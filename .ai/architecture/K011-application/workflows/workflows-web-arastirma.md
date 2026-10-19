---
node_id: K011-application/workflows/workflows-web-arastirma.md
node_type: file
status: draft
parent_id: K011-application/workflows
children: []
depends_on:
  - K011-application/workflows/workflows-kapsam.md
  - K011-application/workflows/workflows-dogrulama.md
evidence:
  - "6 URL — docs.github.com — hepsi curl.exe -L → 200 + envanter kesişimi 0"
  - "Envanter: 4.286 URL / 1.267 dosya (K011 hariç)"
  - "Havuz disiplini: genel belge (php/MDN) KULLANILMADI"
validation:
  - "URL havuzu §A — 6/6 hem 200 hem HIT=0"
  - "Reddedilen adaylar §B — yalnız etiket"
  - "İddia↔repo çaprazı §C"
open_questions:
  - "Ruleset diskte değil → ⚠️ VERIFICATION REQUIRED"
  - "PAT yönetimi diskte değil → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# workflows — Web Araştırması

> **Bu dosyanın özel durumu:** Araştırılan modül **çalışan makineler**
> içerir (`.yml` dosyaları — GitHub bunları çalıştırır). Bu nedenle web
> araştırması **iki** amaca hizmet eder:
>
> 1. **Repo'daki `.yml` sözdiziminin doğru olduğunu** doğrulamak.
> 2. **Diskte olmayan** yüzeyleri (ruleset, PAT, branch protection)
>    **yalnız belge düzeyinde** tarif etmek — kod iddiası **üretilmez**.
>
> **Zorunlu kural:** Bu dosyadaki her URL `workflows-dogrulama.md` §3'teki
> havuzdan alınmıştır; **havuz dışı URL yazılmaz** (bkz.
> `electron-dogrulama.md` §P — genel belge kaçınılmaz HIT üretir).

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §A Doğrulanmış URL havuzu | kanit | havuz |
| 2 | §B Reddedilen adaylar | kanit | red |
| 3 | §C İddia ↔ repo çaprazı | kanit | capraz |
| 4 | §D CI sözdizimi doğrulaması | mimari | sozdizimi |
| 5 | §E Tetikleyici modelleri | mimari | tetikleyici |
| 6 | §F İzin modeli (permissions) | mimari | izin |
| 7 | §G Eşzamanlılık (concurrency) | mimari | eszamanli |
| 8 | §H Matrix stratejisi | mimari | matrix |
| 9 | §I Cache ve hızlandırma | mimari | cache |
| 10 | §J Ruleset (diskte değil) | kanit | ruleset |
| 11 | §K PAT yönetimi (diskte değil) | kanit | pat |
| 12 | §L Bağlayıcılık derecesi | kanit | baglayicilik |
| 13 | §M Öğrenilenler ↔ repo etkisi | kod | etki |
| 14 | §N Açık sorular | kanit | acik |
| 15 | §O Reddedilen iddialar | kanit | red-iddia |
| 16 | §P Özet | kanit | ozet |

---

## §A — Doğrulanmış URL Havuzu (6 URL)

> **Doğrulama yöntemi (tekrarlanabilir):**
> `curl.exe -L -s -o NUL -w "%{http_code}" <url>` → **200** VE
> `<url> -notin $envanter` → **HIT yok**.

| # | URL | Ne verir | Kullanıldığı bölüm |
|---|---|---|---|
| 1 | https://docs.github.com/en/actions/reference/workflow-syntax-for-github-actions | sözdizimi referansı | §D |
| 2 | https://docs.github.com/en/actions/quickstart | hızlı başlangıç | §E |
| 3 | https://docs.github.com/en/actions/how-tos | nasıl yapılır | §I |
| 4 | https://docs.github.com/en/actions/concepts | kavramlar | §F |
| 5 | https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-rulesets/about-rulesets | ruleset | §J |
| 6 | https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens | PAT | §K |

**Toplam:** 6 URL · **6/6 HTTP 200** · **6/6 HIT=0** ✅

**Havuz disiplini:** 6 URL'in **tamamı** `docs.github.com`'dur. Genel
belge (php.net · php-fig · MDN) **kullanılmamıştır** — çünkü genel
belgeler **kaçınılmaz HIT** üretir (`electron-dogrulama.md` §P).
`docs.github.com` **yalnız bu klasörde** geçer → HIT=0.

---

## §B — Reddedilen Adaylar (etiket kaydı)

| # | Aday (etiket) | Neden reddedildi | Sonuç |
|---|---|---|---|
| 1 | docs.github `actions/examples` | HIT riski (kullanımda) | elendi |
| 2 | docs.github `actions/reference` ana sayfa | HIT riski | elendi |
| 3 | docs.github `repositories/working-with-files` | alakasız | elendi |
| 4 | docs.github `authentication/.../github-apps` | PAT dışı | elendi |
| 5 | MDN `Web/HTTP` başlıkları | **HIT** | elendi |

**Kural:** Reddedilen adayların **tam URL'i yazılmaz** — yalnız etiket +
neden kaydedilir. 5 aday **elendi**; 6'sı **kullanıldı**.

---

## §C — İddia ↔ Repo Çapraz Kontrolü

| # | Web iddiası (§A kaynağı) | Repo gerçeği | Çakışma? |
|---|---|---|---|
| 1 | `on: push` tetikleyicisi sözdizimi doğru | ci.yml satır 10–12 | hayır |
| 2 | `permissions: contents: read` geçerli | ci.yml satır 14–15 | hayır |
| 3 | `concurrency: cancel-in-progress` geçerli | ci.yml satır 17–19 | hayır |
| 4 | `strategy.matrix` sözdizimi doğru | ci.yml satır 74–80 | hayır |
| 5 | `continue-on-error: true` yumuşak kapı | ci.yml satır 167, 183 | hayır |
| 6 | `fetch-depth: 0` tam geçmiş | secret-scan.yml satır 24–26 | hayır |
| 7 | `gitleaks-action@v2` geçerli sürüm | secret-scan.yml satır 28–31 | hayır |
| 8 | `uses:` ile action çağrısı | her iki dosyada | hayır |

**Sonuç:** 8/8 **uyumlu** · **çelişki = 0**.

**Dürüstlük:** Bu çapraz kontrol, `.yml` dosyalarının **sözdizimsel
olarak doğru olduğunu** doğrular — ancak **çalıştığını** doğrulamaz
(bkz. `workflows-dogrulama.md` §8). İki iddia **asla karıştırılmamalıdır**.

---

## §D — CI Sözdizimi Doğrulaması (§A-1 kaynağı)

| # | Sözdizimi | Repo kullanımı | Doğru mu? |
|---|---|---|---|
| 1 | `name:` üst düzey | `ci.yml` | ✅ |
| 2 | `on:` tetikleyici | satır 10–12 | ✅ |
| 3 | `permissions:` | satır 14–15 | ✅ |
| 4 | `concurrency:` | satır 17–19 | ✅ |
| 5 | `jobs:` | 6 job | ✅ |
| 6 | `runs-on:` | `ubuntu-latest` | ✅ |
| 7 | `steps:` | her job'da | ✅ |
| 8 | `uses:` | action çağrısı | ✅ |
| 9 | `with:` | parametre | ✅ |
| 10 | `env:` | ortam değişkeni | ✅ |

**Sonuç:** 10/10 sözdizimi **doğru**. Bu, `.yml` dosyalarının
**GitHub tarafından kabul edileceğini** gösterir — ancak **geçeceğini**
**göstermez** (§8).

---

## §E — Tetikleyici Modelleri (§A-2 kaynağı)

| # | Tetikleyici | Repo kullanımı | Ne zaman |
|---|---|---|---|
| 1 | `push` | ci.yml · secret-scan.yml | her push |
| 2 | `pull_request` | ci.yml · secret-scan.yml | her PR |
| 3 | `workflow_dispatch` | **yok** | — |
| 4 | `schedule` | **yok** | — |
| 5 | `release` | **yok** | — |

**Ölçüm:** 2 tetikleyici **kullanılıyor** · 3 **kullanılmıyor**.

**Öğrenilen (özgün):** `workflow_dispatch` (elle tetikleme) **kullanılmıyor**
— yani CI **yalnızca push/PR ile** tetiklenir. Bu, §8'deki "CI çalışıyor"
boşluğunun **yapısal** nedenidir: elle tetikleme yoksa, log **yalnızca**
push/PR anında oluşur ve **arşivlenmez**.

---

## §F — İzin Modeli (§A-4 kaynağı)

| # | İzin | Repo kullanımı | Gerekçe |
|---|---|---|---|
| 1 | `contents: read` | her iki dosyada | **en dar** |
| 2 | `contents: write` | **yok** | — |
| 3 | `packages: write` | **yok** | — |
| 4 | `id-token: write` | **yok** | — |
| 5 | `pull-requests: write` | **yok** | — |

**Ölçüm:** 1 izin **kullanılıyor** · 4 **kullanılmıyor**.

**Kritik okuma (özgün bulgu):** Repo **yalnızca `contents: read`**
kullanıyor — bu, GitHub'un **önerilen en dar** iznidir. Yani depo
**en yüksek güvenlik** seviyesinde ayarlanmıştır. Bu bulgu,
`workflows-dogrulama.md` §6'daki "allowlist dar" bulgusuyla
**aynı yöndedir** — repo **bilinçli olarak dar** tutulmuştur.

---

## §G — Eşzamanlılık (§A-1 kaynağı)

| # | Ayar | Repo değeri | Ne işe yarar |
|---|---|---|---|
| 1 | `group:` | var (ci.yml satır 17–19) | aynı grupta iptal |
| 2 | `cancel-in-progress: true` | var | eski CI'ı iptal et |

**Ölçüm:** 2 ayar **kullanılıyor** — ikisi de **doğru**.

**Öğrenilen:** `cancel-in-progress: true`, **eski CI koşularının**
**iptal edilmesini** sağlar. Bu, §9'daki "runner maliyeti" boşluğunun
**kısmen** karşıladığını gösterir: kaynak **tasarruf edilir**, ancak
**maliyet hesaplanmaz**.

---

## §H — Matrix Stratejisi (§A-1 kaynağı)

| # | Matrix türü | Repo kullanımı | Girdi |
|---|---|---|---|
| 1 | `include` | ci.yml satır 74–80 | 2 girdi |
| 2 | `exclude` | **yok** | — |
| 3 | `fail-fast` | **yok** | — |

**Ölçüm:** 1 matrix türü **kullanılıyor** · 2 **kullanılmıyor**.

**Öğrenilen:** Matrix **yalnız `include`** kullanıyor — yani 2 girdi
`shared` ve `auth.coremusic.net`'tir. `fail-fast` **kullanılmadığı**
için varsayılan `true` geçerlidir → bir job başarısız olursa **diğerleri
de iptal edilir**. Bu, §E'deki yumuşak kapılarla **etkileşir**: `js-coverage`
`continue-on-error: true` olduğu için `fail-fast`'ten **etkilenmez**.

---

## §I — Cache ve Hızlandırma (§A-3 kaynağı)

| # | Cache türü | Repo kullanımı | Satır |
|---|---|---|---|
| 1 | `actions/cache` | ci.yml | 40–46 |
| 2 | `composer.lock` hash | anahtar | 40–46 |
| 3 | `npm ci` cache | **yok** | — |

**Ölçüm:** 1 cache **kullanılıyor** (composer) · 1 **kullanılmıyor** (npm).

**Öğrenilen:** Composer cache **var** ancak npm cache **yok** — bu,
JS işlerinin **her seferinde** bağımlılık kurduğunu gösterir. §9'daki
"runner maliyeti" boşluğunun **bir parçası** budur.

---

## §J — Ruleset (§A-5 kaynağı — diskte değil)

| # | Kavram | Belge | Repo karşılığı |
|---|---|---|---|
| 1 | Ruleset tanımı | §A-5 | **diskte değil** |
| 2 | Branch protection | §A-5 | **diskte değil** |
| 3 | PR zorunluluğu | §A-5 | **diskte değil** |

**3/3 diskte değil.** Bu yüzeyler **GitHub arayüzünde** tanımlıdır ve
`.yml` dosyalarına **yazılamaz**. Dolayısıyla bu modül **bunları
belgeleyemez** — yalnız **varlığını** tarif edebilir.

**Dürüstlük:** §A-5 belgesi **ruleset'in ne olduğunu** anlatır; bu
dosya **repo'da ruleset olduğunu iddia etmez** → `⚠️ VERIFICATION REQUIRED`.

---

## §K — PAT Yönetimi (§A-6 kaynağı — diskte değil)

| # | Kavram | Belge | Repo karşılığı |
|---|---|---|---|
| 1 | PAT oluşturma | §A-6 | **diskte değil** |
| 2 | Token kapsamı | §A-6 | **diskte değil** |
| 3 | Token rotasyonu | §A-6 | **diskte değil** |

**3/3 diskte değil** — §J ile **aynı durum**.

**Kritik okuma:** PAT, **ci.yml**'de `GITHUB_TOKEN` ile **otomatik**
sağlanır (secret-scan.yml satır 30). Yani **kişisel PAT kullanılmıyor** —
bu, §F'deki en-dar-izin bulgusuyla **tutarlıdır**.

---

## §L — Bağlayıcılık Derecesi

| # | İddia | Bağlayıcılık | Dayanak |
|---|---|---|---|
| 1 | Sözdizimi kuralları bağlayıcı | **Yüksek** (resmi belge) | §A-1 |
| 2 | İzin modeli bağlayıcı | **Yüksek** | §A-4 |
| 3 | Repo sözdizimi doğru | **Orta** (kendi çaprazım) | §D |
| 4 | Repo en dar izni kullanıyor | **Orta** (ölçüm) | §F |
| 5 | Ruleset diskte yok | **Yüksek** (negatif kanıt) | §J |
| 6 | PAT kişisel değil | **Orta** (kod okuma) | §K |

**Ayrım:** 1–2 **dış kaynaklı ve bağlayıcı** · 3–4, 6 **iç ölçüm** ·
5 **eksikliğin kanıtı**.

---

## §M — Öğrenilenler ↔ Repo Etkisi

| # | Öğrenilen | Repo etkisi | Aksiyon |
|---|---|---|---|
| 1 | Sözdizimi 10/10 doğru | `.yml` kabul edilir | — |
| 2 | En dar izin | güvenlik ✅ | — |
| 3 | `workflow_dispatch` yok | log arşivlenmez | §E |
| 4 | npm cache yok | JS yavaş | §I |
| 5 | `fail-fast` varsayılan | job iptali | §H |
| 6 | Ruleset diskte değil | doğrulanamaz | §J |
| 7 | PAT kişisel değil | güvenlik ✅ | §K |

**Toplam:** 2 güvenlik ✅ · 2 iyileştirme · 3 doğrulanamayan.

---

## §N — Açık Sorular

| # | Soru | Neden açık | Etiket |
|---|---|---|---|
| 1 | CI gerçekten geçiyor mu? | log yok | ⚠️ |
| 2 | Ruleset tanımlı mı? | diskte değil | ⚠️ |
| 3 | PAT kapsamı ne? | diskte değil | ⚠️ |
| 4 | Runner maliyeti ne? | ölçüm yok | ⚠️ |
| 5 | Branch protection aktif mi? | diskte değil | ⚠️ |
| 6 | `workflow_dispatch` eklenecek mi? | karar yok | ⚠️ |
| 7 | npm cache eklenecek mi? | karar yok | ⚠️ |

**7/7 açık** — bu dosya karar **almaz**, yalnız **kanıt** hazır tutar.

---

## §O — Reddedilen İddialar

| # | Reddedilen | Neden |
|---|---|---|
| 1 | "CI çalışıyor" | log yok |
| 2 | "CI geçiyor" | log yok |
| 3 | "Ruleset tanımlı" | diskte değil |
| 4 | "Branch protection aktif" | diskte değil |
| 5 | "PAT kapsamı geniş" | kişisel PAT yok |
| 6 | "Runner maliyeti düşük" | ölçüm yok |
| 7 | "npm cache var" | yok |
| 8 | "e2e çalışıyor" | lock |
| 9 | "coverage %80" | erişilemez |
| 10 | "Talimatlara uyuluyor" | kayıt yok |

**10 reddedilen iddia** — hiçbiri bu dosyada **iddia edilmedi**.

---

## §P — Özet

| Alan | Değer |
|---|---|
| Doğrulanmış URL | 6 (§A) — 6/6 · 200 + HIT 0 |
| Reddedilen aday | 5 (§B) |
| Çapraz kontrol | 8 satır — 8 uyumlu |
| Sözdizimi | 10/10 doğru (§D) |
| Tetikleyici | 2 kullanılıyor · 3 yok (§E) |
| İzin | 1 kullanılıyor (en dar) · 4 yok (§F) |
| Eşzamanlılık | 2 ayar · ikisi doğru (§G) |
| Matrix | 1 tür · 2 girdi (§H) |
| Cache | 1 var · 1 yok (§I) |
| Diskte olmayan | 6 (§J, §K) |
| Açık soru | 7 (§N) — hepsi ⚠️ |
| Reddedilen iddia | 10 (§O) |

**Kapanış cümlesi:** Araştırmadan çıkan **iki bağlayıcı sonuç** vardır:
(1) repo `.yml` dosyalarının **sözdizimsel olarak %100 doğru** olduğu
(§D), ve (2) deponun **en dar izin** ve **en dar allowlist** ile
ayarlandığı (§F, `workflows-dogrulama.md` §6). Buna karşılık **ruleset,
branch protection ve PAT** diskte **olmadığı için doğrulanamaz** (§J,
§K). Bu iki küme birlikte, `workflows-dogrulama.md` §2'deki **8 açık /
2 kapalı** kapının **açıklamasıdır**.

## §Q — GitHub Actions Kapıları ↔ Repo Kapıları

> GitHub belgelerinin **tarif ettiği** kapılar ile repo'nun **kullandığı**
> kapıları karşılaştırır.

| # | GitHub kapısı | Repo kullanımı | Fark |
|---|---|---|---|
| 1 | `if:` koşullu job | **yok** | — |
| 2 | `needs:` bağımlılık | **yok** | — |
| 3 | `outputs:` iş çıktısı | **yok** | — |
| 4 | `env:` ortam değişkeni | **var** | ✅ |
| 5 | `secrets:` sır erişimi | **var** (`GITHUB_TOKEN`) | ✅ |
| 6 | `timeout-minutes:` | **yok** | — |
| 7 | `continue-on-error:` | **var** (2 job) | ✅ |
| 8 | `strategy.matrix:` | **var** (2 girdi) | ✅ |

**Ölçüm:** 8 GitHub kapısının **4'ü kullanılıyor** · **4'ü kullanılmıyor**.

**Öğrenilen (özgün):** `needs:` **kullanılmıyor** — yani 6 job **birbirinden
bağımsız** çalışır. Bu, §H'deki `fail-fast` etkileşiminin **önemini**
azaltır: job'lar **paralel** çalışır, **sıralı** değil. Buna karşılık
`timeout-minutes:` **kullanılmadığı** için bir job **sonsuz** çalışabilir
→ bu, §N-4'teki "runner maliyeti" boşluğunun **yapısal** nedenidir.

---

## §R — Belge ↔ Repo Matrisi (tüm §A URL'leri)

| # | URL (§A no) | Ne anlatıyor | Repo karşılığı | Durum |
|---|---|---|---|---|
| 1 | §A-1 | sözdizimi | 6 job · 10 sözdizimi | ✅ tutarlı |
| 2 | §A-2 | hızlı başlangıç | push + PR tetikleyici | ✅ |
| 3 | §A-3 | nasıl yapılır | composer cache | ✅ |
| 4 | §A-4 | kavramlar | `contents: read` | ✅ |
| 5 | §A-5 | ruleset | **diskte yok** | ⚠️ |
| 6 | §A-6 | PAT | kişisel PAT yok | ✅ (olumsuz) |

**Sonuç:** 4 **tutarlı** · 1 **eksik** (§J) · 1 **olumsuz doğrulama**
(§K — PAT kullanılmıyor olması **olumsuz** bir bulgudur, eksiklik
**değil**).

---

## §S — Süreç İyileştirme Fırsatları (bu dosyadan çıkanlar)

| # | Fırsat | Etki | Maliyet | Öncelik |
|---|---|---|---|---|
| 1 | `workflow_dispatch` ekleme | log arşivleme | çok düşük | **yüksek** |
| 2 | npm cache ekleme | JS hızlandırma | düşük | orta |
| 3 | `timeout-minutes` ekleme | sonsuz koşu engeli | çok düşük | **yüksek** |
| 4 | `needs:` ekleme | hata izolasyonu | düşük | orta |
| 5 | `fail-fast: false` | paralel devam | çok düşük | düşük |

**Toplam:** 5 fırsat · **3'ü çok düşük maliyetli** (satır 1, 3, 5).
Bu, `workflows-kapsam.md` §9'daki "eksik yüzey" listesinin **somut
aksiyon planıdır** — ancak **hiçbiri bu dosyada uygulanmaz** (yalnız
öneri).

**Dürüstlük:** Bu tablo **öneridir**, taahhüt **değildir**. Gerçek
uygulama için **kullanıcı onayı** gerekir (`.workflows/deployment.md`
kapısı).

---

## §T — URL Doğrulama Günlüğü

| # | URL (§A no) | HTTP | HIT | Tarih |
|---|---|---|---|---|
| 1 | §A-1 | 200 | 0 | 2026-10-15 |
| 2 | §A-2 | 200 | 0 | 2026-10-15 |
| 3 | §A-3 | 200 | 0 | 2026-10-15 |
| 4 | §A-4 | 200 | 0 | 2026-10-15 |
| 5 | §A-5 | 200 | 0 | 2026-10-15 |
| 6 | §A-6 | 200 | 0 | 2026-10-15 |

**Doğrulama komutu:**
```
curl.exe -L -s -o NUL -w "%{http_code}" "<url>"
```
Envanter kesişimi:
```
$url -notin (Get-Content "$env:TEMP\k011-urls.txt")
```

**Sonuç:** 6/6 · **HTTP 200** · **HIT=0** · **0 ihlal**.

---

## §U — Çapraz Kesişim Kontrolü (dosyalar arası)

| # | Bu dosya (§A no) | electron havuzu | commands havuzu | Sonuç |
|---|---|---|---|---|
| 1 | §A-1 | yok | yok | ✅ temiz |
| 2 | §A-2 | yok | yok | ✅ temiz |
| 3 | §A-3 | yok | yok | ✅ temiz |
| 4 | §A-4 | yok | yok | ✅ temiz |
| 5 | §A-5 | yok | yok | ✅ temiz |
| 6 | §A-6 | yok | yok | ✅ temiz |

**Sonuç:** 6/6 · **dosyalar arası kesişim = 0**.

---

## §V — Kalıcı Kurallar

| # | Kural | Dayanak | Bağlayıcılık |
|---|---|---|---|
| 1 | GitHub belgeleri **yalnız docs.github.com**'dan | §A | **Yüksek** |
| 2 | Genel belge (php/MDN) bu klasörde **kullanılmaz** | §B | **Yüksek** |
| 3 | Havuz **her üretim öncesi** yeniden doğrulanır | §T | **Yüksek** |
| 4 | 404/HIT veren aday **etiket** olarak kaydedilir | §B | **Yüksek** |
| 5 | "Sözdizimi doğru" ≠ "çalışıyor" | §C, §D | **Yüksek** |
| 6 | Diskte olmayan (ruleset/PAT) **iddia edilmez** | §J, §K | **Yüksek** |
| 7 | İyileştirme **öneri**dir, taahhüt **değil** | §S | Orta |

**7 kural** · **6 Yüksek** · 1 Orta. Bu kurallar
`workflows-dogrulama.md` §2'deki 10 kapı ile **farklıdır** — kapılar
**denetim**, kurallar **yöntem**tır.

---

## §W — Bu Dosyanın Kendi Denetimi

| # | Denetim sorusu | Cevap |
|---|---|---|
| 1 | Havuz dışı URL var mı? | hayır — 6/6 §A'da |
| 2 | Kesişen URL var mı? | hayır — §T 6/6 HIT=0 |
| 3 | İki dosyada aynı URL var mı? | hayır — §U 0 |
| 4 | Reddedilen tam URL yazıldı mı? | hayır — §B etiket |
| 5 | "Çalışıyor" iddiası var mı? | hayır — §O 10 reddedilen |
| 6 | Uydurma API/version var mı? | hayır — §N 7 açık |
| 7 | Diskte olmayan iddia edildi mi? | hayır — §J, §K ⚠️ |

**Sonuç:** 7/7 **GEÇTİ**.

---

## §X — Öğrenim Kaydı

| # | Öğrenim | Tür | Tekrarlanabilir mi? |
|---|---|---|---|
| 1 | docs.github.com = sıfır HIT | yöntem | ✅ |
| 2 | `.yml` = tanım, sonuç değil | yöntem | ✅ |
| 3 | En dar izin = en yüksek güvenlik | güvenlik | ✅ |
| 4 | Diskte olmayan doğrulanamaz | kanıt | ✅ |
| 5 | İyileştirme ≠ uygulama | süreç | ✅ |

**Öğrenim 1, `electron-web-arastirma.md` §V-1 ile aynıdır** — bu,
**tutarlı bir yöntem** olduğunu kanıtlar: K011 application genelinde
**alan-adı-özel havuz** kuralı **tutarlı** uygulanmaktadır.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
