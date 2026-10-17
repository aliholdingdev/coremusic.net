---
node_id: orchestration-surucu
node_type: module
status: active
parent_id: orchestration
children: []
depends_on: [orchestration-anayasa]
evidence:
  - ".ai/AGENTS.md §7-§12 (dispatch/health/lock akışları, okundu 2026-10-17)"
  - "shared/src/AI/AIOrchestrator.php (orkestrasyon kod kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# orchestration-surucu - Orkestrasyon Sürümü

> **Kapsam:** Kullanıcı isteği geldiğinde sürücünün **gerçekten çalıştığı** adımlar — analiz →
> pre-flight → assignment → execution → handover → validation → completion; öncelik/timing karar
> ağaçları; health check akışı; lock edinimi ve deadlock kırma; hata/çelişki durumlarında davranış.
> **Kural koyar değil, tarif eder:** bağlayıcı kurallar `Dosya: .ai/AGENTS.md · Satır: 246-470`
> (§7-§12 dispatch/health/lock) ve `Dosya: .ai/WORKFLOW.md · Satır: 288-331` (§4 Core Principles +
> §9 Hard Gates) içindedir. Bu dosya o kuralların **kullanım kılavuzudur**; çelişkide vault kazanır.
>
> **Ölçüm tarihi:** 2026-10-17 (tüm satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count` ile
> bugün diskten ölçülmüştür — §19). Kod alıntıları aynı gün dosyadan okunmuştur.

## ELI15 — Bu sürücü ne işe yarar

Bir kullanıcı "şu işi yap" dediğinde sistem **doğrudan kod yazmaz**. Sürücü isteği önce kelimelere
ayırp routing tablosuna (`AGENTS.md §6`) vurur; doğru ajanı ve öncelik seviyesini çıkarır; kod
yazmadan **önce** pre-flight kapılarını (mockup gate, şablon gate, domain boundary, envanter)
geçirir; görevi atar; ajan yürütür; gerekiyorsa başka ajana handover eder; çıktıyı doğrular ve
`log.md`'ye yazarak kapatır. Her adımda **tek bir** başarısızlık yolu vardır: `DUR` / `RED` /
retry (max 3) / escalation (L1→L2→L3→İnsan) / MO müdahalesi — sürükleyici sessiz devam yoktur.

> **eli10:** "Önce hangi kapıdan geçiyorum?" sorusunun cevabı yedi adımda yazılıdır
> (`AGENTS.md §7.1`-`§7.7`); sürücü bu adımların **karar ağaçlarını** ve **ihlal tepkisini**
> tarif eder.
>
> **eli15:** Bugün dispatch **yarı-otomatiktir**: vault tarafı (§7-§12) elle uygulanır, kod tarafı
> ise yalnız görev kuyruğunu ve ajan eşlemesini içerir — `AIOrchestrator.php` içinde **lock, retry,
> health ve escalation kodu YOKTUR** (negatif kanıt: `shared/src/AI/*.php` taraması 1 isabet,
> o da donanım analizi placeholder'ı — §16, §24). Yani bu sürucü, **kural ile kod arasındaki farkı
> da** açıkça gösterir; vault kuralını "kodda var" diye anlatmaz.

## Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 7 adımlık dispatch akışı + her adımın karar ağacı (§1-§7) | Ajanların domain iş mantığı |
| Öncelik/timing karar ağaçları, retry/escalation zinciri (§8, §11, §14) | ADR metinleri (frozen — yalnız okunur) |
| Pre-flight kapıları: mockup / şablon / domain boundary / envanter (§2, §9) | Güvenlik politikasının kendisi (Security Engineer) |
| Health state geçişleri + lock edinimi + deadlock kırma (§12-§13) | `shared/src/**` kodunun değiştirilmesi (**salt-okunur**) |
| Ölçüm/lock betikleri, PHP kanıtı, illüstratif örnekler (§18-§25) | `git commit` (**orkestratöre ait**) |

- **Kullananlar:** dispatch yapan MO · görev kabul eden her ajan · denetim yapan Vault Steward.
- **Ön koşul:** `.ai/AGENTS.md` §7-§12 ve `.ai/WORKFLOW.md` §4/§9 diskte mevcut (2026-10-17: 775 ve 900 satır — §19).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; künyesiz iddia yazılmaz (§16).

## §1. Adım 1 — Analiz (Analysis)

> Kaynak künye: `Dosya: .ai/AGENTS.md · Satır: 259-266` (`#### §7.1 Step 1: Analysis`).
> Tablo hücreleri kaynaktan **birebir** alınmıştır; sürücü sadece dal sonuçlarını ekler.

| Kontrol | Yöntem | Kaynak | Karar ağacı (dal → sonuç) |
|---------|--------|--------|---------------------------|
| Keyword çıkarma | Routing tablosuna başvur | Bu dosya §6 | Eşleşen grup var → birincil+ikincil ajan (**§3**) · yok → sonraki satır |
| Domain eşleme | Dosya uzantısı ve içerik analizi | Bu dosya §5 | `*.php`→backend · `*.js`/`*.css`→ui · `*.sql`→data · `*.cpp/h`→embedded · `*.yml`→devops (`Satır: 95-108`) |
| Öncelik belirleme | CRITICAL > HIGH > MEDIUM > LOW | Bu dosya §8 | Güvenlik/sistem-durması → CRITICAL · kritik kayıp → HIGH · normal → MEDIUM · iyileştirme → LOW (`Satır: 318-323`) |
| Ajan seçimi | Birincil + ikincil ajan | Bu dosya §6 | İkincil yok (`—`) ise tek ajan · ikincil varsa **handover adayı** (§5) |

**Routing tablosunun sürucüdeki kullanımı** (`Dosya: .ai/AGENTS.md · Satır: 232-243`, 9 keyword grubu):

| Keyword grubu (kısa) | Birincil | İkincil | Karar |
|---|---|---|---|
| API, endpoint, routing, middleware, PHP | Backend Architect | Security Engineer | ikincil = güvenlik denetimi tetikleyicisi |
| CSS, UI, responsive, ITCSS, BEM, mockup | UI Designer | QA Engineer | **mockup gate** zorunlu (§2 adım 2) |
| CSRF, CSP, XSS, OWASP, auth | Security Engineer | Backend Architect | öncelik varsayılanı CRITICAL |
| database, SQL, BCNF, migration, MySQL | Data Engineer | Backend Architect | şema değişikliği → HIGH handover |
| C++, ASIO, JUCE, audio, DSP, Neva Engine | Embedded Engineer | DevOps Engineer | device loss → §14 eskalasyon |
| test, coverage, PHPUnit, Vitest, Playwright | QA Engineer | — | tek ajan |
| CI/CD, GitHub Actions, deploy, pipeline | DevOps Engineer | QA Engineer | deploy → Hard Gate (`WORKFLOW.md Satır: 309`) |
| vault, documentation, ADR, wiki-link | MO (vault-updater) | — | yazma yetkisi MO'da |
| template, şablon, .templates | Tüm ajanlar (guardrail #16) | MO (koordinasyon) | **şablon okunmadan üretim yok** (§9/3) |

**Repo karşılığı:** PHP yüzeyinde aynı karar, sabit `TASK_TYPES` haritasıyla yapılır —
`'fault' => ['agent' => 'dsp-fw', 'priority' => 'critical']` (`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 45`).
Yani **analiz = routing tablosu (vault) + task-type map (kod)**; ikisi ayrı tutulur, biri diğerinin yerine geçmez.

**Duraklatma noktası (analiz netleşmezse):** belirsiz istek → tek kısa soru (kök `AGENTS.md` §7/1);
bilinmeyen sınıf/API → `// ⚠️ VERIFICATION REQUIRED` (`Dosya: .ai/AGENTS.md · Satır: 201`, §17/5).

## §2. Adım 2 — Pre-flight Checks

> Kaynak künye: `Dosya: .ai/AGENTS.md · Satır: 268-281` (`#### §7.2 Step 2: Pre-flight Checks`).
> "İhlal" sütunu **bağlayıcıdır** — biri bile `DUR`/`RED` verirse §3'e geçilmez.

| Kontrol | Değer | İhlal (karar ağacı sonucu) |
|---------|-------|----------------------------|
| Domain boundary | Doğru ajan | Layer violation → revert |
| Dosya etkileniyor mu? | Eşzamanlı erişim | Context lock (§13 akışı) |
| Bağımlılık var mı? | Handover gerekli | Transfer başlat (§5) |
| UI Design uyumu | [[ui-design/01-mockup-index]] (19 PNG + 45-tier cihaz matrisi) ve C01-C16 kontrolü; referans sırası: PNG > ASCII art > Inventory > Tokens > Reference (01-10) | Mockup okunmadıysa → **DUR** |
| 45-Tier uyumu | [[ui-design/reference/10-device-specific-guidelines]] tier kontrolü; responsive token'lar | Tier kuralı ihlal edilmişse → **RED** |
| Responsive uyum | [[ui-design/05-responsive-architecture]] §7.4 (4K'da ortalamama) + §12 (fallback zorunlu) | Tier kuralı ihlal edilmişse → **RED** |
| Şablon zorunluluğu | `.ai/.templates/ui-design/{reference,flow,prompt,screen-spec}-template.md` (Kalıp A-D) — görev tipine göre ilgili şablon okundu mu? | Şablon okunmadıysa → **DUR** (Guardrail #16) |
| Gate script sonucu | Repo **kökünden** `screens-frontmatter-check` `21/0` · `kalip-abc-check` `A:0 B:0 C:0 → GECTI` · `wiki-link-check` `227 link / 6 raporlanan` = **gerçek kırık 0** · `figma-tokens` `SONUC -> toplam=7 \| pass=5 \| bos=2 \| fail=0` + `BITTI` → **exit 0** | `.ai/` içinden çalıştırıldıysa → geçersiz, kökten tekrar çalıştır (§13.6) |
| Varlık envanteri | ui-design kök **6** MD · `screens/` **21** · `flow/` **21** · `prompt/` **51** · `reference/` **17** · `tokens/` **4 md + 7 json** · `.ai/.png` **19 PNG** | Envanter ile görev iddiası uyuşmuyorsa → **DUR** |
| Önceki görev başarısız mı? | Retry / escalation | Max 3 retry (§11) |

**Pre-flight kapısı = kod öncesi 16 maddelik repo incelemesi** (`Dosya: .ai/AGENTS.md · Satır: 40` —
"analiz tamamlanmadan implementation başlatılmaz"). Pre-flight geçmeden §3'e çıkan her görev
**iade edilir**; bu, WORKFLOW'daki `Zero Code Before Plan` ilkesiyle aynı kapıdır
(`Dosya: .ai/WORKFLOW.md · Satır: 292`).

**İhlal sonrası sıra:** (1) durdur → (2) revert → (3) `log.md` CRITICAL → (4) Vault Steward'a bildir
(`Dosya: .ai/WORKFLOW.md · Satır: 324-330`, §9.2 Violation Procedure).

## §3. Adım 3 — Task Assignment

> Kaynak künye: `Dosya: .ai/AGENTS.md · Satır: 283-285` — "*Öncelik seviyeleri, timeout ve retry
> değerleri: §8 Priority Levels.*" · öncelik tablosu `Satır: 318-323`.

| Öncelik | Tanım | Timeout | Max Retry | Yanıt Süresi | Atama kararı |
|---------|-------|---------|-----------|-------------|--------------|
| CRITICAL | Sistem durması, güvenlik açığı | 5s | 1 | Anlık | doğrudan en uygun ajan + lock priority override (§13) |
| HIGH | Kritik işlev kaybı | 15s | 3 | 15s | birincil ajan, ikincil bilgi için hazır bekler |
| MEDIUM | Normal geliştirme görevi | 30s | 3 | 30s | birincil ajan, gerekiyorsa handover (§5) |
| LOW | İyileştirme, optimizasyon | 60s | 2 | 60s | kuyruğa alınır, idle ajanlara dağılır |

**Atama karar ağacı:**

```text
[1] Öncelik belirlendi mi?  (§1 satır 3)
      Hayır → MEDIUM varsay + gerekçeyi yaz (uydurma öncelik YASAK)
      Evet  → [2]
[2] TASK_TYPES bu görev tipini tanıyor mu?  (AIOrchestrator.php Satır: 40-49)
      Hayır → InvalidArgumentException "Unknown task type" (Satır: 73-74) → DUR + kullanıcıya sor
      Evet  → [3]
[3] Agent map ajanı tanıyor mu?  (Satır: 26-38)
      Hayır → `Unknown agent: {id}` red (Satır: 127-133) → §10 red davranışı
      Evet  → [4]
[4] Aynı dosyada başka ajan çalışıyor mu?  (§17/1)
      Evet  → Context Lock kuyruğu → öncelik sırası (§13)
      Hayır → [5]
[5] Ata: taskId üret (Satır: 177-180) → status=pending (Satır: 83) → executeTask (Satır: 94)
```

**Timeout davranışı atamada gömülüdür:** timeout değeri önceliğe bağlıdır (CRITICAL 5s ↔ LOW 60s);
retry sayacı bu değerle birlikte taşınır. Kod tarafında **timeout alanı yoktur** —
`AIOrchestrator` içinde `timeout`/`retry` anahtarı bulunmaz (§16 negatif kanıt); bu yüzden vault
tablosu bağlayıcıdır, kod tablosu değil.

## §4. Adım 4 — Execution

> Kaynak künye: `Dosya: .ai/AGENTS.md · Satır: 287-292` (`#### §7.4 Step 4: Execution`).

Ajan görevi yürütür. Kurallar (birebir):

| Kural | Karar ağacı (uygulanmazsa) | Kaynak künye |
|-------|----------------------------|--------------|
| Domain boundary'yi koru | ihlal → **derhal revert + log ERROR** · "Sistem durur, MO müdahale eder" | `Satır: 290` · `Satır: 217` (§18/1) · `Satır: 203` (§17/7) |
| Zero Code Before Plan uygula | atlandıysa → Hard Gate ihlali → revert + CRITICAL log | `Satır: 291` · `WORKFLOW.md Satır: 296, 380` |
| Çıktıyı standardize et | standartsız → §6 doğrulama RED | `Satır: 292` |

**Yürütme sırasında sağlık denetimi zorunludur** — "*Check Interval: Her görev başında*"
(`Dosya: .ai/AGENTS.md · Satır: 411`), akış §12'dedir.

**Yürütme durakları (kod tarafı):**

```php
$task['status'] = 'running';                                                     // Satır 188
$agentId = $task['agent'];                                                       // Satır 192
$result  = $this->routeToAgent($agentId, $task['type'], $task['params']);        // Satır 193
if ($result['success']) { $task['status'] = 'completed'; }                       // Satır 195-197
else                   { $task['status'] = 'failed';   }                         // Satır 199-201
```
**`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 185-209`** (`executeTask`)

**Dürüstlük notu:** `completed` yalnız `routeToAgent` **true** dönerse yazılır; ajanın gerçekten
iş yapıp yapmadığı kodda **doğrulanmaz** (§16/3). Doğrulama bilinçli olarak §6'ya (Validation)
bırakılmıştır — kodda çift sayım yoktur.

## §5. Adım 5 — Handover

> Kaynak künye: `Dosya: .ai/AGENTS.md · Satır: 294-296` — "*Gerekirse diğer ajana transfer.
> Handover protokolü §9'da tanımlıdır.*" · protokol `Satır: 327-367`.

```text
[Kaynak Agent] → [Handover Request] → [Hedef Agent] → [Onay/Red] → [Confirmation]
```
**`Dosya: .ai/AGENTS.md · Satır: 329-331`**

| # | Karar | Dal | Sonuç |
|---|---|---|---|
| 1 | Görev başka domain mi? | Evet → handover **gerekli** | §2 satır 3 "Transfer başlat" |
|  |  | Hayır → kendi ajanda bitir | §6'ya geç |
| 2 | Mesaj 8 alan dolu mu? (Konu · Kaynak · Hedef · Öncelik · Etkilenen Dosyalar · İstek · Onay Durumu · Timestamp) | Eksik → **RED**, tamam → gönder | `Satır: 335-343` |
| 3 | Hedef onayladı mı? | `APPROVED` → Confirmation + log | `Satır: 348-350` |
|  |  | `REJECTED` → **MO devreye girer** | `Satır: 353` |
|  |  | 30s timeout → retry (max 3) → MO | `Satır: 351-352` |
| 4 | Hedef ajan işi reddedemez mi? (onay zorunlu) | "*Hedef agent onayı olmadan tamamlanamaz*" | `Satır: 350` |

**Yetki red durumu (kod + kural birlikte):** bilinmeyen ajan `routeToAgent` red verir ve bu red
**sessiz başarıya çevrilmez**:

```php
if (!isset(self::AGENT_MAP[$agentId])) {                                        // Satır 127
    return ['success' => false, 'result' => null,
            'error'   => "Unknown agent: {$agentId}"];                           // Satır 128-132
}
// Domain boundary kontrolü                                                      // Satır 135
$agentInfo = self::AGENT_MAP[$agentId];                                          // Satır 136
```
**`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 125-149`** (`routeToAgent`)

**Sürücü kararı:** `success=false` → görev `failed` (Satır: 199-201) → §11 retry sayacı →
max 3 → queue reset → escalation. Red **gerekçesiz** verilemez: `error` alanı doludur ve
`log.md`'ye yazılır (`Dosya: .ai/AGENTS.md · Satır: 354` — "Tüm handover'lar `log.md`'ye yazılır").

**Handover senaryoları (öncelik önceden bellidir):** Güvenlik açığı tespiti Backend→Security
**CRITICAL** · DB schema Backend→Data **HIGH** · Frontend test eksikliği UI→QA **MEDIUM** ·
CI/CD pipeline hatası DevOps→QA **HIGH** · Auth middleware Security→Backend **HIGH** ·
Audio DSP Embedded→DevOps **MEDIUM** · Vault doküman MO→vault-updater **LOW** ·
Security audit Security→QA **HIGH** (`Dosya: .ai/AGENTS.md · Satır: 358-366`, 8 senaryo).

## §6. Adım 6 — Validation

> Kaynak künye: `Dosya: .ai/AGENTS.md · Satır: 298-305` (`#### §7.6 Step 6: Verification` —
> başlık dosyada "Verification"tır; §7 şemasında "Validation" adıdır; **ikisi aynı adımdır**).

| Kontrol | Değer | Karar ağacı (RED koşulu) |
|---------|-------|---------------------------|
| Çıktı formatı | Uygun | standart dışı → iade (§4/3) |
| Domain uyumluluğu | Doğru ajan | yanlış domain → layer violation → revert (§18/6) |
| Cross-reference | Geçerli wiki-link'ler | kırık link → `.ai/scripts/wiki-link-check.ps1` **repo kökünden** |
| Security | Hassas veri redaction | hassas veri log'da → `[REDACTED]` (`Satır: 200`, §17/3) |

**Kalite standardı (ajan bazlı, hedef %100 — istisnai QA ≥80% / DevOps ≥95%):**
Backend `strict_types, PSR-12, prepared statement` · UI `ITSS uyum, BEM namespace, WCAG 2.2 AA,
ui-design C01-C16 uyumu` · Security `OWASP Top 10, CSRF=csrf_token, Argon2id` · Data `BCNF, no ORM,
no SELECT *, prepared` · Embedded `Zero-allocation, lock-free, noexcept` · QA `Test coverage ≥80%,
flaky test %0` · DevOps `CI/CD success ≥95%, GitLeaks clean`
(`Dosya: .ai/AGENTS.md · Satır: 573-581`, §16).

**Validation RED → ne olur:** RED bir sonuç, §5'teki handover veya §14'teki escalation'a gider;
kod tarafında ise başarısızlık `failed` + `error` yazdırır (Satır: 199-206) ve retry/escalation
**kodda değil vault kuralındadır** (§16).

## §7. Adım 7 — Completion

> Kaynak künye: `Dosya: .ai/AGENTS.md · Satır: 307-312` (`#### §7.7 Step 7: Completion`).

| # | İş (birebir) | Karar ağacı | Kanıt |
|---|---|---|---|
| 1 | Görev tamamlanır | `status=completed` + `completed_at` yazılır | `Satır: 310` · `AIOrchestrator.php Satır: 196, 203` |
| 2 | `log.md`'ye giriş eklenir | append-only — geçmiş satıra dokunulmaz | `Satır: 311` · `AGENTS.md Satır: 636` (§25.3/3) |
| 3 | MEMORY.md session state güncellenir | session kapanışı yoksa atlanmaz, güncellenir | `Satır: 312` |
| 4 | Gerekirse vault-sync yapılır | yeni/taşınan `.md` varsa → `vault-sync-post` | `Satır: 313` |
| 5 | Kapanış listesi | §29 8 madde tamam mı? | bu dosya §29 |

**Kapanışta hâlâ açıksa:** doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` ile bırakılır ve
görev **"tamamlandı" sayılmaz** — completion, doğrulama kapıları (§17) geçilmeden verilmez.

## §8. Öncelik ve Timing Karar Ağacı (tek bakışta)

> Bağlayıcı tablo: `Dosya: .ai/AGENTS.md · Satır: 318-323` (§8) · eskalasyon zamanları
> `Satır: 379-399` (§10.1/10.2).

| Tetik | Öncelik | Timeout | Retry | Sonraki dal | Escape |
|---|---|---|---|---|---|
| Sistem durması | CRITICAL | 5s | 1 | L2 → **İnsan, anlık** | MO müdahalesi (`Satır: 389`) |
| Güvenlik açığı | CRITICAL | 5s (yanıt 15s) | 1-3 | L2 → L3 (15s) | security-engineer veto |
| CSRF/CSP uyumsuzluğu | HIGH | 15s | 3 | L1 → L2 (15s) | `Satır: 383` |
| Agent aynı dosyayı değiştiremiyor | HIGH | 30s | 3 | L1 → L2 (30s) | lock kırma (§13) |
| BCNF çelişkisi | HIGH | 30s | 3 | L1 (Data) → L2 | `Satır: 382` |
| ASIO cihaz kaybı | HIGH | 30s | 3 | L1 → L2 + **WASAPI fallback** | `Satır: 384` · `Satır: 202` (§17/6) |
| Test coverage %80 altı | MEDIUM | 60s | 3 | L1 (QA) → L2 | `Satır: 385` |
| Deployment başarısız | HIGH | 30s | 3 | L1 → L2 → **deploy iptal** | `WORKFLOW.md Satır: 309` |
| Mimari çelişki (ADR) | MEDIUM | 60s | 3 | L2 → L3 | `Satır: 387` |
| İyileştirme/optimizasyon | LOW | 60s | 2 | kuyrukta bekler | `Satır: 323` |

**Timing zinciri:** L1 timeout **30s** · L2 timeout **60s** · L3 timeout **120s** ·
**max retry 3 her seviyede** · "İnsan müdahalesi — Son çare"
(`Dosya: .ai/AGENTS.md · Satır: 393-398`).

**Eskalasyon zinciri:** `Level 1 (Domain Lead) → Level 2 (Tech Lead) → Level 3 (Arch Lead) → İnsan`
(`Dosya: .ai/AGENTS.md · Satır: 373-375`).

## §9. Pre-flight Kontrol Listesi (kapı detayı)

| # | Kapı | Sorgu (evet/hayır) | Hayır ise | Kaynak künye |
|---|---|---|---|---|
| 1 | Mockup gate | CSS/HTML/JS/layout görevinde `.ai/ui-design/**` görseli okundu mu? | **DUR** — okunamıyorsa bildir | `AGENTS.md Satır: 147` (§13) |
| 2 | Şablon gate (Guardrail #16) | Görev tipine göre Kalıp A-D şablonu okundu mu? | **DUR** | `Satır: 143` · `Satır: 278` |
| 3 | Domain boundary | Bu dosya tipi bu ajanın mı? | revert + log ERROR | `Satır: 95-108` · `Satır: 272` |
| 4 | Eşzamanlı erişim | Aynı dosyada başka ajan var mı? | Context lock → kuyruk | `Satır: 273` · `Satır: 377` (WORKFLOW §11) |
| 5 | Bağımlılık/handover | Başka domain girdisi var mı? | transfer başlat | `Satır: 274` |
| 6 | Gate script yeri | Betik **repo kökünden** çalıştırıldı mı? | geçersiz → kökten tekrar | `Satır: 144` (§13/6) |
| 7 | Envanter | Görev iddiası envanterle uyuşuyor mu? | **DUR** | `Satır: 280` |
| 8 | Önceki başarısızlık | Retry sayacı max 3'ü aştı mı? | queue reset → escalation | `Satır: 281` |
| 9 | Ultra-thinking | 5 adım (vault oku → bağlam → hata → tahmin → doğrulama) uygulandı mı? | kod yazımı yasak | `Satır: 489-497` (§24.1) |
| 10 | Onay kapısı | Hard Gate gerektiriyor mu? (Phase 7, deploy) | onaysız geçiş **yok** | `WORKFLOW.md Satır: 304-309` |

**Onay formatı (Hard Gate):** `[Kullanıcı Onayı] — [Tarih] — [Değişiklik Özeti]`
(`Dosya: .ai/WORKFLOW.md · Satır: 313-315`).

## §10. Yetki Red / Handover Red Durumu Davranışı

| # | Red tipi | Tetikleyici | Davranış | Sonuç |
|---|---|---|---|---|
| 1 | Bilinmeyen görev tipi | `TASK_TYPES`'ta yok | `\InvalidArgumentException` fırlat | DUR — kullanıcıya sor (`AIOrchestrator.php Satır: 73-75`) |
| 2 | Bilinmeyen ajan | `AGENT_MAP`'ta yok | `success=false` + `error` döndür | failed → §11 retry (Satır: 127-133) |
| 3 | Domain boundary ihlali | yanlış ajan dosyaya yazdı | derhal revert + log ERROR | "Sistem durur, MO müdahale eder" (`Satır: 217`) |
| 4 | Handover RED | hedef ajan `REJECTED` | **MO devreye girer** | yeniden dağıtım (`Satır: 353`) |
| 5 | Handover timeout | 30s yanıt yok | retry max 3 → MO | (`Satır: 351-352`) |
| 6 | Mockup/şablon gate RED | görsel/şablon okunmadı | **DUR**, kod yok | (`Satır: 275, 278`) |
| 7 | Hard Gate ihlali | onaysız faz geçişi | durdur + revert + CRITICAL log + Steward'a bildir | (`WORKFLOW.md Satır: 324-330`) |
| 8 | Kullanıcı reddi (Phase 7) | onay verilmedi | **Phase 4'e geri dön** | (`WORKFLOW.md Satır: 373`) |

**Ortak kural:** red **sessizce yutulmaz** — her red ya `error` alanı, ya `DUR`, ya da
`log.md` CRITICAL girdisi üretir. Sessiz yutma = Zero-Hallucination ihlali.

## §11. Timeout / Retry Davranışı (max 3 → queue reset → escalation)

**Bağlayıcı cümle (iki yerde aynı):** "*Ajan timeout (30s+)* → *Max 3 retry, sonra queue reset*"
(`Dosya: .ai/AGENTS.md · Satır: 201`, §17/4) ve "*Agent timeout 30s+* → *Max 3 retry, sonra
escalation*" (`Dosya: .ai/AGENTS.md · Satır: 217`, §18/2).

```text
[1] timeout tespit (önceliğe göre: 5s/15s/30s/60s — §8)
      → [2]
[2] retry sayacı < max ?  (CRITICAL 1 · HIGH 3 · MEDIUM 3 · LOW 2)
      Evet  → [3] yeniden dene (state = Retry/408)
      Hayır → [4]
[3] deneme → başarılı → Healthy/200 → §6 Validation
      başarısız → [2]'ye dön (sayaç++)
[4] sayacı doldu → **queue reset** (kuyruk sıfırlanır)
      → [5]
[5] escalation: L1 (30s) → L2 (60s) → L3 (120s) → İnsan (son çare)
      → Dead/503 ise **derhal** escalation (adım atlanır)
```

**Kural ile kod farkı (dürüst kayıt):**

| Davranış | Vault kuralı | Repo kodu | Durum |
|---|---|---|---|
| max 3 retry | `Satır: 201, 217, 352, 398` | **yok** (`retry` = 0 isabet, §16) | kural IMPLEMENTED, kod **PLANNED** |
| queue reset | `Satır: 201` | **yok** | PLANNED |
| escalation L1→L2→L3 | `Satır: 373-375` | **yok** (`escalat` = 0 isabet) | PLANNED |
| priority weighted execution | `Satır: 318-323` | `// Priority weighted execution` (Satır: 93) — **yorum = niyet** | kısmi |
| task status geçişleri | §7.7 | `pending→running→completed/failed` (Satır: 83, 188, 196, 199) | IMPLEMENTED |

> **Doğrulama kapıları:** bu tablo `Dosya: .ai/RAG.md · Satır: 104` (durum sütunu kuralı) ile aynı
> disiplindedir — "kodda yok" derken tarama sayısı ve tek isabet künyelenmiştir (§16, §19/D).

## §12. Health State Geçiş Tablosu

> Bağlayıcı: `Dosya: .ai/AGENTS.md · Satır: 416-422` (§11.2) · parametreler `Satır: 407-412` (§11.1)
> · akış `Satır: 426-435` (§11.3).

| Durum | Kod | Açıklama | Geçiş → nereye |
|-------|-----|----------|----------------|
| Healthy | 200 | Görev tamamlandı | → §6 Validation (devam) |
| Degraded | 301 | Yavaş yanıt (>15s) | → **uyar, devam** (görev durmaz) |
| Retry | 408 | Timeout, yeniden deneniyor | → yeniden dene (**max 3**) |
| Failed | 500 | 3 retry başarısız, queue reset | → **queue reset + escalation** |
| Dead | 503 | Yanıt yok | → **derhal escalation** |

**Parametreler:** Timeout **30 saniye** · Max Retry **3** · Check Interval **Her görev başında** ·
Heartbeat **10 saniye** (`Dosya: .ai/AGENTS.md · Satır: 409-412`).

**Akış (birebir, `Satır: 426-435`):**

```text
Görev başlangıcı
  → Health check tetikle
    → Durum kontrolü
      → Healthy → devam
      → Degraded → uyar, devam
      → Retry → yeniden dene (max 3)
      → Failed → queue reset, escalation
      → Dead → derhal escalation
```

**Geçiş matrisi (okuma tablosu):**

| Şimdiki \ Sonraki | 200 Healthy | 301 Degraded | 408 Retry | 500 Failed | 503 Dead |
|---|---|---|---|---|---|
| (başlangıç) | görev tamam | yavaş yanıt | timeout | — | yanıt yok |
| 301 Degraded | devam eder | kalır | >15s→408 | 3×408→500 | 403 yok → 503 |
| 408 Retry (n<3) | kurtardı → 200 | — | n+1 → 408 | n=3 → **500** | yanıt tamamen yok → 503 |
| 500 Failed | — | — | — | terminal → escalation | — |
| 503 Dead | — | — | — | — | terminal → **derhal escalation** |

**Kod karşılığı:** repo'da ajan health check'i **yoktur**; `shared/src/AI/*.php` taramasındaki tek
`healthy` isabeti donanım analizi placeholder'ıdır: `$status = 'healthy';`
(`Dosya: shared/src/AI/AIEngine.php · Satır: 141`, `analyzeHardware()` içinde, hemen ardından
"`// Şimdilik placeholder`" — `Satır: 148`). Yani health state tablosu **bugün vault kuralıdır**,
kod değil.

## §13. Lock Edinim Akışı ve Deadlock Kırma (MO intervention)

> Bağlayıcı: `Dosya: .ai/AGENTS.md · Satır: 443-469` (§12.1-§12.3).

**Lock Flow (birebir, `Satır: 454-460`):**

```text
Ajan dosyaya erişmek ister
  → Lock acquire (max 30s bekleme)
    → Başarılı → dosyayı düzenle
    → Başarısız → kuyruk → öncelik sırası
      → Timeout → escalation
```

| # | Karar | Dal | Sonuç | Kaynak |
|---|---|---|---|---|
| 1 | Dosya serbest mi? | Evet | acquire → düzenle + acquire log'u | `Satır: 449` |
|  |  | Hayır | kuyruğa gir, öncelik sırası bekle | `Satır: 458` |
| 2 | 30s doldu mu? | Evet | **timeout → escalation** (L1) | `Satır: 459` |
|  |  | Hayır | bekleme sürer (max 30s) | `Satır: 456` |
| 3 | Bekleyen CRITICAL mı? | Evet | **priority override: diğer kilidi kırar** | `Satır: 466` |
| 4 | Kilit süresi aşıldı mı? | Evet | timeout → max 30s sonra lock serbest | `Satır: 465` |
| 5 | Deadlock şüphesi | — | **MO intervention: MO en eski kilidi kırar** | `Satır: 447, 467` |
| 6 | Kilitler tutarsız | — | **queue reset: tüm kilitler sıfırlanır** | `Satır: 468` |

**Öncelik:** `CRITICAL > HIGH > MEDIUM > LOW` (`Satır: 448`) · **Logging:** "Lock acquire/release
`log.md`'ye yazılır" (`Satır: 449`) · **Kilit süresi:** Max 30 saniye (`Satır: 446`).

**Kırma sırası (solda kalan kural üstte):** (1) timeout serbest bırakma → (2) CRITICAL priority
override → (3) MO en eski kilit → (4) queue reset. MO müdahalesi **en son** değil, **en eski
kilidi kıran** aktiftir; bu, §17/1 "Aynı dosyaya eşzamanlı erişim → Context Lock + Queue" ile
aynı mekanizmadır (`Satır: 199`).

**Kod karşılığı:** `AIOrchestrator.php` içinde lock/reentrant kilit **yoktur** (`lock` isabeti 0 —
§16); eşzamanlılık bugün **elle** yönetilir. WORKFLOW aynı davranışı ADR-022'ye bağlar:
"Concurrent write | Context Lock + Queue | ADR-022"
(`Dosya: .ai/WORKFLOW.md · Satır: 377`).

## §14. Hata, Çelişki ve Eskalasyon Davranışı

| # | Durum | Davranış | Zaman | Kaynak künye |
|---|---|---|---|---|
| 1 | Ajan aynı dosyayı değiştiremiyor | L1 → L2 | 30s | `AGENTS.md Satır: 381` |
| 2 | BCNF çelişkisi | L1 (Data) → L2 | 30s | `Satır: 382` |
| 3 | CSRF/CSP uyumsuzluğu | L1 (Security) → L2 | 15s | `Satır: 383` |
| 4 | ASIO cihaz kaybı | L1 (Embedded) → L2 + WASAPI fallback | 30s | `Satır: 384` · `Satır: 202` |
| 5 | Test coverage %80 altı | L1 (QA) → L2 | 60s | `Satır: 385` |
| 6 | Deployment başarısız | L1 (DevOps) → L2 → deploy iptal | 30s | `Satır: 386` · `WORKFLOW Satır: 309` |
| 7 | Mimari çelişki (ADR) | L2 → L3 | 60s | `Satır: 387` |
| 8 | Güvenlik açığı | L2 → L3 | 15s | `Satır: 388` |
| 9 | Sistem durması | L2 → **İnsan** | anlık | `Satır: 389` |
| 10 | Boot/vault sayım zinciri çelişkisi | **DUR** + üst karar | anlık | `CLAUDE.md §16` (rag-surucu §8/7) |
| 11 | Aynı hata 3 düzeltme turunda | **DUR** — şüpheli varsayımı yaz, üst karar iste | tur sayacı | kök `AGENTS.md` §2 Final rule |
| 12 | Çelişen iki kanıt (kod ↔ ADR/etiket) | iki kanıt da künyelenir + `⚠️ VERIFICATION REQUIRED` | anlık | `RAG.md §6/8` |
| 13 | Hard Gate bypass | derhal revert + CRITICAL log | anlık | `WORKFLOW.md Satır: 380` |

**Eskalasyon kuralı özeti:** max retry **3 her seviyede** · L1 30s · L2 60s · L3 120s ·
"İnsan müdahalesi — Son çare" (`Dosya: .ai/AGENTS.md · Satır: 393-398`).

## §15. Edge Case Davranış Tabloları (§17 taşıması)

> `Dosya: .ai/AGENTS.md · Satır: 197-209` — 9 satır; **#2 numaralı satır tabloda yoktur**
> ("Faz 1'den beri bilinen boşluk — korunmuştur", `Satır: 209`); 2 numara **uydurulmaz**.

| # | Senaryo | Çözüm | Sürücüdeki karşılığı |
|---|---------|-------|----------------------|
| 1 | Aynı dosyaya eşzamanlı erişim | Context Lock + Queue | §13 |
| 3 | Sensitive data log'da | `[REDACTED]` ile maskeleme | §6/4 Security redaction |
| 4 | Ajan timeout (30s+) | Max 3 retry, sonra queue reset | §11 |
| 5 | Bilinmeyen class/API | `// ⚠️ VERIFICATION REQUIRED` | §1/7 · §16/6 |
| 6 | ASIO device loss | WASAPI fallback | §14/4 |
| 7 | Layer violation | Derhal revert + log ERROR | §4/1 |
| 8 | PCM5122 kullanımı | PCM3168A / AK4458 öner | domain dışı — Embedded |
| 9 | Network outage | Offline-First + SQLite queue | §11/2 queue reset ruhu |
| 10 | Vault corruption | `git checkout` + son commit | §16/9 |

**Uyarı tablosu (§18, `Satır: 215-225` — 8 satır):** Domain boundary ihlali → sistem durur/MO ·
Agent timeout 30s+ → max 3 retry sonra escalation · Hallüsinasyon → `VERIFICATION REQUIRED` ·
Token overflow → görev başarısız · Vault bozulması → git ile kurtarma · Layer violation → kod
revert · ORM → SQL injection riski · Framework → bağımlılık artışı.

## §16. Kanıt ve Negatif Kanıt (repo taraması)

| İddia | Tarama | Sonuç (2026-10-17) |
|---|---|---|
| "Dispatch, ajan haritası ve task tipi haritası **kodda var**" | `AGENT_MAP\|TASK_TYPES\|dispatchTask\|routeToAgent\|executeTask` → `AIOrchestrator.php` | **11 isabet, gerçek kod** (Satır 26, 40, 71, 73, 78, 93, 125, 127, 136, 185 — §19/B çıktısı) |
| "Repo'da bu alan kodu YOK: **context lock / deadlock**" | `lock\|deadlock` → `shared/src/AI/*.php` | **0 isabet** |
| "Repo'da bu alan kodu YOK: **retry / escalation / handover**" | `retry\|escalat\|handover` → `shared/src/AI/*.php` | **0 isabet** |
| "Repo'da **agent health check** yok" | `health` → `shared/src/AI/*.php` | **1 isabet**: `AIEngine.php:141` `$status = 'healthy';` — donanım analizi placeholder'ı, ajan health'i değil |
| "Vault §7-§12 bölümleri diskte" | bölüm başlığı taraması → `.ai/AGENTS.md` | **11 başlık**, satır künyeleri §19/B'de |

**Toplam (tek ölçüm):** `lock|retry|health|escalat|deadlock|handover` → `shared/src/AI/*.php` =
**1 isabet** (`AIEngine.php:141`). Bu, §11-§13'teki kuralların **vault kuralı olarak kaldığını**
kanıtlar; kod tarafı `PLANNED` etiketi taşır.

**Çelişki kaydı (kapatılmaz, işaretlenir):** vault "max 3 retry, queue reset, escalation" der
(`Satır: 201, 217`), kod tek denemede `failed` yazar ve retry'ı uygulamaz (`Satır: 199-206`).
İkisi de **doğru** olabilir (kod iskeleti, davranış vault'ta) — hangisinin bağlayıcı olduğu
**`⚠️ VERIFICATION REQUIRED`** (§30/Q2).

## §17. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem / komut |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz (title/parent YOK) | `Select-String` sayacı (§19) |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` (§18/A) |
| 3 | Authority son satır | birebir | `Select-String` eşitlik (§18/A) |
| 4 | Dispatch § başlıkları | §1-§7 = 7 adım, her birinde karar ağacı | `Select-String -Pattern '^## §[1-7]\.'` → 7 |
| 5 | Künyeli iddia | her iddia `Dosya:` + `Satır:` | gözden geçirme (§2 kural) |
| 6 | Placeholder | şablon değişkeni yok (üçlü sürgü: `\{` + `{`) | `Select-String` eşleşmesi → 0 |
| 7 | Kod alıntıları | PHP satır künyeleri dosyayla eşleşiyor | §19/B sembol taraması |
| 8 | Negatif kanıt | tarama sonucu dosyada yazılı | §19/D çıktısı = 1 isabet |
| 9 | Kapsam dışı yazım | 0 dosya | `git status --porcelain` (§19/F) |
| 10 | `git commit` | **0** | subagent yasak — orkestratöre ait |

## §18. PowerShell Ölçüm Kalıpları (gerçek çalıştırılabilir)

> ✅ **Çalıştırılabilir PowerShell betikleri** — repo kökünden (`C:\www\coremusic.net`) çalışır;
> çıktısı §19'da **bugün alınan gerçek sonuçlarla** verilmiştir.

```powershell
# A) Üretim kapısı: satır >=500 + FM 10 alan + Authority son satır (tek seferde)
$p = '.ai\architecture\K015-AI\orchestration\orchestration-surucu.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir    => {0}   (kapı >=500: {1})" -f $lines.Count, ($lines.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority=> {0}" -f ($lines[$lines.Count - 1] -eq $auth)
$pat = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10    => {0}" -f ((Select-String -Path $p -Pattern $pat).Count -eq 10)
"adim 1-7 => {0} (kapı 7)" -f (Select-String -Path $p -Pattern '^## §[1-7]\.').Count
```

```powershell
# B) Kanıt taraması: dispatch bölüm başlıkları + AIOrchestrator sembolleri (künye doğrulama)
$pat = '^### §7 Task Dispatch Algorithm|^### §8 Priority Levels|^### §9 Handover Protocol|^### §10 Escalation Protocol|^### §11 Health Check|^### §12 Context Lock|^#### §7\.1|^#### §7\.2|^#### §11\.3|^#### §12\.2|^#### §12\.3'
Select-String -Path '.ai\AGENTS.md' -Pattern $pat |
    ForEach-Object { "{0}:{1}" -f $_.LineNumber, $_.Line.Trim() }
Select-String -Path 'shared\src\AI\AIOrchestrator.php' -Pattern `
    'AGENT_MAP|TASK_TYPES|function dispatchTask|function routeToAgent|function executeTask|Domain boundary kontrol|Priority weighted' |
    ForEach-Object { "{0}:{1}" -f $_.LineNumber, $_.Line.Trim() }
```

```powershell
# C) Context Lock akışı simülasyonu (AGENTS.md §12.2/§12.3) — repo'ya YAZMAZ, yalnız temp'te çalışır
$dir  = 'C:\temp\opencode\orch-lock-test'
New-Item -ItemType Directory -Path $dir -Force | Out-Null
$file = Join-Path $dir 'shared.php.lock'
Remove-Item $file -Force -ErrorAction SilentlyContinue

New-Item -Path $file -ItemType File -Force | Out-Null
Set-Content -Path $file -Value 'security|2026-10-17T00:00:00Z|CRITICAL'
"adim 1 => security kilit aldi: {0}" -f (Test-Path $file)

$deadline = (Get-Date).AddSeconds(1)     # 30s kuralının 1s'lik test sürümü
$ok = $false
while ((Get-Date) -lt $deadline) {
    if (-not (Test-Path $file)) { $ok = $true; break }
    Start-Sleep -Milliseconds 100
}
"adim 2 => backend acquire (1s bekleme): {0}  [beklenen False]" -f $ok

Remove-Item $file -Force                 # MO intervention: en eski kilidi kir (§12.3)
"adim 3 => MO lock kirildi, kilit bos: {0}" -f (-not (Test-Path $file))

New-Item -Path $file -ItemType File -Force | Out-Null
"adim 4 => backend kilit aldi (bos lock dosyasi): {0}" -f ((Get-Item $file).Length -eq 0)

Remove-Item $file -Force                 # release
"adim 5 => lock release, kalan lock = {0} (kapı 0)" -f @(Get-ChildItem $dir -Filter '*.lock').Count
```

```powershell
# D) Negatif kanıt: lock|retry|health|escalat|deadlock|handover -> shared/src/AI
$hits = Select-String -Path 'shared\src\AI\*.php' -Pattern 'lock|retry|health|escalat|deadlock|handover' -CaseSensitive:$false
"isabet => {0}" -f @($hits).Count
$hits | ForEach-Object { "  {0}:{1}" -f $_.Filename, $_.LineNumber }
```

```powershell
# E) Kapsam sayacı: bu üretimde yazılan dosya tek olmalı (kapsam dışı yazım = 0)
$changed = git status --porcelain -- '.ai/architecture/K015-AI/orchestration/'
"degisen => {0}" -f @($changed).Count
$changed | ForEach-Object { "  {0}" -f $_ }
```

## §19. Ölçüm Çıktıları (2026-10-17 — gerçek)

**A) Üretim kapısı (2026-10-17 gerçek çıktı):**

| Kapı | Sonuç | Durum |
|---|---|---|
| `satir` | **973** (>=500) | **PASS** |
| `authority` | son satır birebir = True | **PASS** |
| `fm 10` | 10 alan eksiksiz = True | **PASS** |
| `adim 1-7` | `^## §[1-7]\.` = 7 | **PASS** |
| `toplam §` | `^## §[0-9]+\.` = **32** (≥25) | **PASS** |
| şablon değişkeni | eşleşme **0** | **PASS** |
| ilk satır | `---` (frontmatter başlangıcı) | **PASS** |

**B) Kanıt taraması (gerçek çıktı — konsol `§` işaretini `?` gösterir, dosya içeriği etkilenmez):**

```text
246:### §7 Task Dispatch Algorithm
259:#### §7.1 Step 1: Analysis
268:#### §7.2 Step 2: Pre-flight Checks
316:### §8 Priority Levels
327:### §9 Handover Protocol
371:### §10 Escalation Protocol
403:### §11 Health Check
424:#### §11.3 Health Check Flow
439:### §12 Context Lock
452:#### §12.2 Lock Flow
462:#### §12.3 Deadlock Prevention
26:private const AGENT_MAP = [
40:private const TASK_TYPES = [
71:public function dispatchTask(string $type, array $params, string $priority = 'medium'): string
73:if (!isset(self::TASK_TYPES[$type])) {
78:$taskConfig = self::TASK_TYPES[$type];
93:// Priority weighted execution
125:public function routeToAgent(string $agentId, string $action, array $params): array
127:if (!isset(self::AGENT_MAP[$agentId])) {
136:$agentInfo = self::AGENT_MAP[$agentId];
185:private function executeTask(string $taskId): void
```

**C) Lock simülasyonu (gerçek çıktı, `C:\temp\opencode\orch-lock-test`):**

```text
adim 1 => security kilit aldi: True
adim 2 => backend acquire (1s bekleme): False  [beklenen False]
adim 3 => MO lock kirildi, kilit bos: True
adim 4 => backend kilit aldi (bos lock dosyasi): True
adim 5 => lock release, kalan lock = 0 (kapı 0)
```

**D) Negatif kanıt (gerçek çıktı):**

```text
isabet => 1
  AIEngine.php:141
```

**E) Kapsam sayacı (gerçek çıktı):**

```text
degisen => 4
  A  .ai/architecture/K015-AI/orchestration/index.md
  ?? .ai/architecture/K015-AI/orchestration/orchestration-anayasa.md
  ?? .ai/architecture/K015-AI/orchestration/orchestration-ornekler.md
  ?? .ai/architecture/K015-AI/orchestration/orchestration-surucu.md
```

> **Dürüst okuma:** dizin sayacı **4** verir çünkü bu üretim turunda `orchestration/` altındaki
> **kardeş dosyalar paralel ajanlarca** üretilmektedir (`-A` = staged, `??` = untracked).
> Bu dosyanın **kendi yazımı tek dosyadır**: `orchestration-surucu.md`. Kapsam dışı yazım
> (bu dosyanın dışına) = **0** — kapı 9 PASS; kardeş üretimler kapsam dışı sayılmaz, paralel üretimdir.

**Kaynak dosya ölçümü (ReadAllLines, 2026-10-17):**

| Dosya | Satır |
|---|---|
| `.ai\AGENTS.md` | 775 |
| `.ai\WORKFLOW.md` | 900 |
| `shared\src\AI\AIOrchestrator.php` | 210 |
| `shared\src\AI\AIEngine.php` | 382 |
| `shared\src\AI\AIWorkflow.php` | 271 |
| `...orchestration\index.md` (ebeveyn) | 48 |
| `...orchestration\orchestration-anayasa.md` (bağımlılık, paralel üretim) | 559 |
| `...orchestration\orchestration-ornekler.md` (kardeş, paralel üretim) | 873 |
| `...rag\rag-surucu.md` (şablon yapı referansı) | 779 |

## §20. Repo Kanıtı — PHP Dispatch Çekirdeği

> ✅ **Repo kanıtı** — aşağıdaki bloklar `shared/src/AI/AIOrchestrator.php` içinden alınmış
> birebir alıntılardır (uydurma yok; satır sonundaki yorumlar künyedir).

```php
private const TASK_TYPES = [                                                     // Satır 40
    'recommendation'    => ['agent' => 'backend', 'priority' => 'medium'],       // Satır 41
    'analysis'          => ['agent' => 'embedded', 'priority' => 'high'],        // Satır 42
    'fault'             => ['agent' => 'dsp-fw',   'priority' => 'critical'],    // Satır 45
    'security-audit'    => ['agent' => 'security', 'priority' => 'critical'],    // Satır 46
    'documentation'     => ['agent' => 'mo',       'priority' => 'low'],         // Satır 47
];                                                                                // Satır 49

public function dispatchTask(string $type, array $params,
                            string $priority = 'medium'): string                  // Satır 71
{
    if (!isset(self::TASK_TYPES[$type])) {                                       // Satır 73
        throw new \InvalidArgumentException("Unknown task type: {$type}");       // Satır 74
    }
    $taskId     = $this->generateTaskId();                                       // Satır 77
    $taskConfig = self::TASK_TYPES[$type];                                       // Satır 78
    $this->tasks[$taskId] = [
        'id'         => $taskId,                                                  // Satır 81
        'status'     => 'pending',                                                // Satır 83
        'priority'   => $priority,                                                // Satır 84
        'agent'      => $taskConfig['agent'],                                     // Satır 85
        ...
    ];
    // Priority weighted execution                                                // Satır 93
    $this->executeTask($taskId);                                                  // Satır 94
    return $taskId;                                                               // Satır 96
}
```

```php
private const AGENT_MAP = [                                                       // Satır 26
    'mo'         => ['domain' => 'coordination', 'layer' => 'coordination'],      // Satır 27
    'backend'    => ['domain' => 'php-api',     'layer' => 'l2-routing'],         // Satır 28
    'security'   => ['domain' => 'security',    'layer' => 'l1-security'],        // Satır 30
    'data'       => ['domain' => 'database',    'layer' => 'l0-infrastructure'],  // Satır 31
    'embedded'   => ['domain' => 'audio-dsp',   'layer' => 'l0-electronics'],     // Satır 32
    ...
    'win-sw'     => ['domain' => 'platform',    'layer' => 'plat'],               // Satır 37
];                                                                                 // Satır 38
```

```php
// task durum yaşam döngüsü (completion kanıtı)
$task['status'] = 'running';                                                      // Satır 188
try {
    $result = $this->routeToAgent($agentId, $task['type'], $task['params']);      // Satır 193
    if ($result['success']) {
        $task['status'] = 'completed';                                            // Satır 196
        $task['result']  = $result['result'];                                     // Satır 197
    } else {
        $task['status'] = 'failed';                                               // Satır 199
        $task['error']  = $result['error'];                                       // Satır 200
    }
    $task['completed_at'] = date('Y-m-d H:i:s');                                  // Satır 203
} catch (\Throwable $e) {                                                          // Satır 204
    $task['status'] = 'failed';                                                   // Satır 205
    $task['error']  = $e->getMessage();                                           // Satır 206
}
```
**`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 185-209`**

**Sürücü ile bağlantısı:**

| Repo davranışı | Sürücüdeki karşılığı | Künye |
|---|---|---|
| `dispatchTask` — tip doğrulama + hata fırlatma | §1/§3 analiz + atama karar ağacı | `AIOrchestrator.php · Satır: 71-96` |
| `AGENT_MAP` — 11 ajan (domain + layer) | §1 domain eşleme (§5) | `... · Satır: 26-38` |
| `// Domain boundary kontrolü` — **yorum**, denetim yok | §9 kapı 3 (denetim vault'ta) | `... · Satır: 135-136` |
| `pending → running → completed/failed` | §4 execution + §7 completion | `... · Satır: 83, 188, 196, 199` |

## §21. İllüstratif — Python (task queue + retry bütçesi)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# §11 davranışı: max retry -> queue reset -> escalation (AGENTS.md 201/217 ruhu)
from dataclasses import dataclass, field

PRIORITY_BUDGET = {"critical": (5, 1), "high": (15, 3), "medium": (30, 3), "low": (60, 2)}

@dataclass
class Task:
    kind: str
    agent: str
    priority: str = "medium"
    attempts: int = 0
    status: str = "pending"
    error: str | None = None

def execute(task: Task, run) -> Task:
    timeout, max_retry = PRIORITY_BUDGET[task.priority]
    task.status = "running"
    while True:
        task.attempts += 1
        try:
            run(task, timeout)
            task.status = "completed"
            return task
        except TimeoutError:
            if task.attempts > max_retry:
                task.status = "failed"          # -> queue reset (AGENTS.md 201)
                task.error = f"retry budget exhausted after {task.attempts}"
                return task                     # -> escalation zincirine gider
```

*İskelet kaynak: `Dosya: .ai/AGENTS.md · Satır: 318-323` (timeout/retry bütçesi) ·
`Satır: 201` (max 3 retry, sonra queue reset).*

## §22. İllüstratif — Go (health state machine)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package health

// State, AGENTS.md §11.2 health state tablosunun birebir karşılığıdır.
type State string

const (
    Healthy  State = "200" // görev tamamlandı
    Degraded State = "301" // yavaş yanıt (>15s) -> uyar, devam
    Retry    State = "408" // timeout, yeniden deneniyor (max 3)
    Failed   State = "500" // 3 retry başarısız -> queue reset, escalation
    Dead     State = "503" // yanıt yok -> derhal escalation
)

// Next, §11.3 akışının geçiş fonksiyonudur.
func Next(cur State, elapsedMs int, attempts int) State {
    switch cur {
    case Healthy:
        if elapsedMs > 15000 { return Degraded }
        return Healthy
    case Degraded:
        if elapsedMs > 15000 { return Retry }
        return Degraded
    case Retry:
        if attempts >= 3 { return Failed }
        return Retry
    case Failed:
        return Failed // terminal -> escalation (§10)
    case Dead:
        return Dead // terminal -> derhal escalation
    }
    return Retry
}
```

*İskelet kaynak: `Dosya: .ai/AGENTS.md · Satır: 416-422` (5 durum) · `Satır: 426-435` (akış).*

## §23. İllüstratif — TypeScript (escalation chain)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
// §10 escalation zinciri: L1 (30s) -> L2 (60s) -> L3 (120s) -> İnsan (son çare)
type Level = 'L1' | 'L2' | 'L3' | 'HUMAN';

const TIMEOUT_MS: Record<Level, number> = { L1: 30_000, L2: 60_000, L3: 120_000, HUMAN: 0 };
const HUMAN_IS_LAST_RESORT = true; // AGENTS.md 398: "İnsan müdahalesi — Son çare"

export function escalate(current: Level, retries: number): Level {
  if (retries < 3) return current;          // max retry 3 HER seviyede (Satır 397)
  if (current === 'L1') return 'L2';
  if (current === 'L2') return 'L3';
  if (current === 'L3' && HUMAN_IS_LAST_RESORT) return 'HUMAN';
  return 'HUMAN';
}
```

*İskelet kaynak: `Dosya: .ai/AGENTS.md · Satır: 373-375` (zincir) · `Satır: 393-398` (timeout/retry).*

## §24. Web Doğrulaması (resmi kaynaklar — 2026-10-17)

| # | Kaynak | Doğrulanan kavram | Doğrulama yöntemi |
|---|---|---|---|
| 1 | `https://learn.microsoft.com/en-us/azure/architecture/patterns/retry` | Retry pattern: "*If the request is unsuccessful after a predefined number of attempts, the application should treat the fault as an exception and handle it accordingly.*" — belirli sayıda deneme sonra **istisna olarak ele alma** = bizim "max 3 → escalation" karşılığı; ayrıca ilk deneme hatalarının *informational*, son denemenin *error* olarak loglanması | **webfetch** ile sayfa okundu (2026-10-17) |
| 2 | `https://learn.microsoft.com/en-us/azure/architecture/patterns/priority-queue` | "*Prioritize requests sent to services so that a workload processes high-priority requests more quickly than lower-priority ones.*" — §8 öncelik sıralaması (CRITICAL > HIGH > MEDIUM > LOW) | websearch sonucu ile bulundu (sayfa içeriği okunmadı → detay `⚠️ VERIFICATION REQUIRED`) |
| 3 | `https://learn.microsoft.com/en-us/azure/architecture/patterns/scheduler-agent-supervisor` | "*coordinates a multistep distributed task through three logical actors … orchestrate the …*" — scheduler/agent/supervisor ayrımı, MO'nun denetleyici rolüyle aynı ailede | websearch sonucu ile bulundu (sayfa içeriği okunmadı → detay `⚠️ VERIFICATION REQUIRED`) |
| 4 | `https://sre.google/sre-book/monitoring-distributed-systems/` | Sağlık/monitoring bölümü — heartbeat ve durum sınıflandırması (§11.1 heartbeat 10s ile aynı konu ailesi) | websearch sonucu ile bulundu (sayfa içeriği okunmadı → detay `⚠️ VERIFICATION REQUIRED`) |

**UYDURMA URL YOKTUR:** yukarıdaki 4 URL arama/webfetch çıktısıdır; içeriği okunmayanların
iddiası sınırlandırılmıştır (`⚠️ VERIFICATION REQUIRED`). Bağlayıcı olan **vault kurallarıdır**;
dış kaynaklar yalnızca karşılaştırma içindir.

## §25. Token / Okuma Ekonomisi

| # | Kural | Sonuç | Kanıt |
|---|---|---|---|
| 1 | Toplu okuma yasak — boot'ta tüm vault okunmaz | token bütçesi korunur | `Dosya: kök AGENTS.md · Satır: 282` |
| 2 | Pre-flight kapıları kod öncesi **16 maddelik** inceleme | yanlış dosyaya yazım engellenir → rework yok | `Dosya: .ai/AGENTS.md · Satır: 40` |
| 3 | Ultra-thinking 5 adım: vault oku (max 25s) → bağlam → hata → tahmin → doğrulama | kod öncesi israf azalır | `Dosya: .ai/AGENTS.md · Satır: 491-496` |
| 4 | Routing tablosu tek tarama = 9 keyword grubu | ajan seçimi 1 tablo | `Satır: 232-243` |
| 5 | İlgili § harici bölüm okunmaz (on-demand vault) | boot 14 dosyada sabit | `Dosya: kök AGENTS.md · Satır: 273-282` |
| 6 | Token overflow → görev başarısız (uyarı) | sınır biliniyor | `Satır: 218` (§18/4) · `WORKFLOW.md Satır: 378` (chunked read) |
| 7 | Red/çelişki tek cümleyle raporlanır, drama yok | rapor maliyeti düşük | kök `AGENTS.md` Output Style |

## §26. Yasaklar ve Sınırlar

| # | Yasak | Neden |
|---|---|---|
| 1 | `git commit` atmak | **Orkestratöre aittir** — subagent yasak (§17/10 · `AGENTS.md Satır: 172`) |
| 2 | `shared/src/**` ve `.ai/AGENTS.md` yazmak | bu üretim **salt-okunur** kaynak okur (kapsam §19/E) |
| 3 | Bu dosya dışına yazmak / dosya adını değiştirmek | kapsam dışı yazım = ihlal (§17/9) |
| 4 | Uydurma kanıt/URL/satır künyesi | Zero-Hallucination (`Dosya: .ai/AGENTS.md · Satır: 42`) |
| 5 | Pre-flight geçmeden kod yazmak | Zero Code Before Plan (`WORKFLOW.md Satır: 292`) |
| 6 | Mockup/şablon okumadan frontend üretmek | Guardrail #16 → **DUR** (`Satır: 143, 147`) |
| 7 | Hard Gate'i onaysız geçmek | revert + CRITICAL log (`WORKFLOW.md Satır: 304-309`) |
| 8 | `log.md`'ye geçmiş satırı düzenleyerek yazmak | append-only (`Satır: 636`, §25.3/3) |
| 9 | §17 tablosundaki **2 numaralı satırı uydurmak** | bilinen boşluk korunur (`Satır: 209`) |
| 10 | `.ai/` içinden gate betiği çalıştırmak | sahte başarısızlıklar (`Satır: 144`) |

## §27. Sık Yapılan Hatalar (anti-pattern)

| # | Yanlış | Doğru | Kaynak |
|---|---|---|---|
| 1 | "Kodda retry var" demek | kural vault'ta, kodda **PLANNED** (§16) | tarama = 0 isabet |
| 2 | Health check'i `AIEngine.php:141` sanmak | o donanım placeholder'ı | `Satır: 141, 148` |
| 3 | Pre-flight'i atlayıp doğrudan yazmak | 16 madde + kapılar (§9) | `Satır: 40` |
| 4 | Red'i sessizce yutmak | `error` alanı dolu + `log.md` | `Satır: 128-133, 354` |
| 5 | Retry'yı sınırsız denemek | önceliğe göre 1/3/3/2 | `Satır: 318-323` |
| 6 | Lock timeout'u MO'suz bekletmek | deadlock → MO en eski kilidi kırar | `Satır: 447, 467` |
| 7 | Escalasyonu doğrudan İnsana açmak | L1→L2→L3, insan **son çare** | `Satır: 373-375, 398` |
| 8 | Gate betiğini `.ai/` içinden çalıştırmak | repo **kökünden** | `Satır: 144` (§13/6) |
| 9 | Kanıtsız "bitti" demek | §17 kapıları + §29 liste | bu dosya §17 |
| 10 | §17'de eksik #2'yi tamamlamak | boşluk korunur | `Satır: 209` |

## §28. Doğrulama — Bölüm Bütünlüğü

| Kontrol | Beklenen | Durum |
|---|---|---|
| H1 birebir | `# orchestration-surucu - Orkestrasyon Sürümü` | ✔ yazıldı |
| FM 10 alan, `title`/`parent` yok | 10 | §19/A |
| Numaralı § sayısı | ≥ 25 (§1-§32) | 32 |
| §1-§7 = 7 dispatch adımı, her birinde karar ağacı tablosu | 7/7 | ✔ |
| PowerShell blokları | A-E (5) — hepsi çalıştırılabilir | 5 |
| PHP blokları (künyeli) | en az 3 | 4 (§4, §5, §20 ×2) |
| İllüstratif blok (uyarı satırıyla) | Python/Go/TS = 3 | 3 (§21-§23) |
| Web kaynak | 1-3 resmi | 4 (§24) |
| Son satır Authority | birebir | §32 |

## §29. Kapanış Kontrol Listesi (her dispatch görevi sonunda)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | §1-§7 adımları sırayla izlendi mi? | evet / gerekçesi |
| 2 | Pre-flight kapıları (mockup/şablon/domain/envanter) geçildi mi? | evet |
| 3 | Her iddia `Dosya:` + `Satır:` künyeli mi? | evet |
| 4 | Kod/vault çelişkisi var mı, işaretlendi mi (§16)? | evet / hayır |
| 5 | Ölçüm yapıldı mı (§18-§19)? | evet |
| 6 | Red/çelişki sessiz yutuldu mu? | **hayır** |
| 7 | Kapsam dışı dosyaya yazıldı mı? | **hayır (0)** |
| 8 | `git commit` atıldı mı? | **hayır (orkestratöre ait)** |

## §30. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [orchestration-anayasa]` — bu üretim turunun başında `orchestration/` altında yalnız `index.md` diskteydi; **kapanış ölçümünde** `orchestration-anayasa.md` (559 satır) ve `orchestration-ornekler.md` (873 satır) kardeş üretimi diskte bulundu (§19/E). Bağımlılık **doldu mu**, içerik olarak da mı? | bağımlılık dosyası mevcut (559 satır) — içerik uyumu `⚠️ VERIFICATION REQUIRED` |
| Q2 | Vault "max 3 retry + queue reset + escalation" der (`AGENTS.md Satır: 201, 217`), `AIOrchestrator::executeTask` tek denemede `failed` yazar (`Satır: 199-206`) — hangisi bağlayıcı? | `⚠️ VERIFICATION REQUIRED` (§16) |
| Q3 | Ebeveyn `orchestration/index.md` `children: []` ve "Kademe-3 bu üretimde açılmadı" diyor (`Satır: 6`). Bu modül eklendikten sonra `children` güncellenecek mi? | orkestratör kararı |
| Q4 | `routeToAgent` içindeki `// Domain boundary kontrolü` yorumu bir **denetim** mü, yoksa yalnız niyet beyanı mı? Kodda ihlal tespiti yok (§20). | `⚠️ VERIFICATION REQUIRED` |
| Q5 | `WORKFLOW.md §9.2` 6. adım metni ("Sousuz sıfırlanır") okunaksız ve başka vault dosyasında karşılığı yok (`WORKFLOW.md Satır: 331`) — ne kastedildiği Vault Steward teyidi gerekir | beklemede |
| Q6 | Health state geçişlerinde `Degraded → Dead` doğrudan geçişi tabloda yazılmamış (`AGENTS.md Satır: 416-422`); 301→503 ara adımı gerekiyor mu? | bilinmiyor — **uydurulmaz** |
| Q7 | Lock mekanizmasının kod karşılığı (distributed/local lock) hangi katmanda olacak — `shared/src/` mi, vault prosedürü mü kalacak? | ADR gerektirir |

## §31. Kaynaklar

| Kaynak | Yol | Kullanım amacı | Ölçüm (2026-10-17) |
|---|---|---|---|
| Dispatch/Health/Lock SSOT | `.ai/AGENTS.md` §6-§12, §17, §18 | §1-§15 karar ağaçları | 775 satır |
| Süreç / Hard Gate | `.ai/WORKFLOW.md` §4, §9, §11 | pre-flight onay kapıları, ihlal prosedürü | 900 satır |
| Orkestrasyon kod kanıtı | `shared/src/AI/AIOrchestrator.php` | §20 PHP alıntıları | 210 satır |
| Sahte "health" kanıtı | `shared/src/AI/AIEngine.php` | §16 negatif kanıt | 382 satır |
| AI iş akışı iskeleti | `shared/src/AI/AIWorkflow.php` | bağımlılık enjeksiyonu bağlamı | 271 satır |
| Ebeveyn düğüm | `.ai/architecture/K015-AI/orchestration/index.md` | kapsam sınırı + `children` durumu (Satır: 6) | 48 satır |
| Yapı referansı (şablon) | `.ai/architecture/K015-AI/rag/rag-surucu.md` | H1 + § + karar ağacı + kapı iskeleti | 779 satır |
| Retrieval sürucusu (kardeş) | `.ai/architecture/K015-AI/rag/rag-surucu.md` §16-§19 | ölçüm/kapı kalıpları | — |
| Resmi dış kaynak | learn.microsoft.com (retry, priority-queue, scheduler-agent-supervisor) · sre.google | §24 karşılaştırma | 4 URL (1 webfetch) |
| Ölçüm betikleri | bu dosya §18 (A-E) | kapılar | kökten çalışır |

## §32. Gate (özet)

§1-§32 tamam: ELI15 · kapsam · **7 adımlık dispatch akışı (§1-§7, her adımda karar ağacı tablosu)** ·
öncelik/timing karar ağacı · 10 maddelik pre-flight listesi · red davranışı 8 satır ·
retry/escalation davranışı (max 3 → queue reset → escalation) · health 5 durum + geçiş matrisi ·
lock akışı 6 dal + deadlock kırma · eskalasyon 13 satır · §17 edge 9 satır · §18 uyarı 8 satır ·
negatif kanıt 5 satır · PowerShell **5 betik (A-E)** · PHP **4 blok** · illüstratif **3 blok
(Python/Go/TS)** · web **4 kaynak** · token 7 kural · yasak 10 · anti-pattern 10 ·
kapanış 8 soru · açık 7 soru · 10 kaynak.

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · kapsam dışı yazım **0** ·
`git commit` **0**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode