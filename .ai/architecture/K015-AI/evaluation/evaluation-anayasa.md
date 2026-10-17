---
node_id: evaluation-anayasa
node_type: module
status: active
parent_id: evaluation
children: []
depends_on: [evaluation-index]
evidence:
  - ".ai/AGENTS.md §24.4-§24.5 + §16 (kalite checklist + standartlar, okundu 2026-10-17)"
  - "root AGENTS.md §3 Zero-Hallucination (değerlendirme çekirdeği)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# evaluation-anayasa - Ajan Değerlendirme Kuralları

> **Bu dosya ne değildir:** ajan çıktılarının **skorlandığı otomatik değerlendirme motorunun kodu**
> (LLM-judge, rubrik skorlayıcı, benchmark altyapısı) ve **test/lint/security-audit** süreçlerinin
> yerine geçmez; burada **değerlendirme anayasası** vardır — bir ajan çıktısı nasıl değerlendirilir,
> hangi kapılar zorunlu, PASS/FAIL nasıl verilir, bulgu nereye yazılır.

## §1 Rol (ELI15)

Evaluation alt-domaini, "bu ajan çıktısı güvenilir mi?" sorusunun **tek kural setinden**
cevaplanmasını sağlar. Değerlendirme, çıktının **iddialarını disk kanıtıyla eşleştirmeyi**
(bkz. §3), kalite kapılarını ölçmeyi (§4) ve sonucu PASS/FAIL + retry/escalation olarak
raporlamayı (§12-§13) kapsar. Dosya bir **süreç dosyası değildir**; kod yazmaz, yalnız
kural koyar ve ölçüm yöntemini tarif eder.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Kim değerlendirir? | Değerlendiren ajan (reviewer/qa) + orkestratör | `.ai/AGENTS.md` §7.6 (Verification) |
| Ne değerlendirilir? | Ajan çıktısı: iddia, dosya, sayı, URL, FM, Authority | bu dosya §3-§5 |
| Çıktı biçimi | PASS/FAIL + kanıt künyesi + retry/escalation | bu dosya §12-§13 |
| Kim karar verir? | 3 başarısız turdan sonra üst karar (insan) | kök `AGENTS.md` §10 (escalation) |
| Otomatik skorlayıcı var mı? | **YOK** — bugün elle kapı ölçümü | bu dosya §22 (repo kanıtı 0) |

> **eli10:** Bu dosya, "değerlendirmeyi nasıl yaparım" sorusunun cevabını ve "sistem henüz
> otomatik değil" dürüstlüğünü aynı yerde tutar. > **eli15:** Değerlendirme = kanıt karşılaştırması,
> zevk değil (`Dosya: root AGENTS.md · Satır: 111`).

## §2 Kapsam (in / out)

| Kapsam | Kapsam Dışı | Kanıt |
|--------|-------------|-------|
| Ajan çıktısı değerlendirme kuralları (iddia ↔ disk kanıtı) | Kod çalıştırma, test suite'i çalıştırma | root `AGENTS.md` §3 |
| Kalite kapıları: ≥500 satır, FM 10 alan, Authority, mojibake 0 | `.ai/CLAUDE.md` §6 LINT'inin tam metni (yalnız atıf) | bu dosya §4 |
| PASS/FAIL metrikleri, retry, escalation | Model/LLM benchmark'ları (K006 kapsamı) | `evaluation/index.md:27` |
| §24.5 checklist değerlendirmesi + §16 standart yorumu | Standartların kendisini değiştirme (SSOT `.ai/AGENTS.md`) | `.ai/AGENTS.md:555-581` |
| Değerlendirme → vault-sync bulgu akışı | Vault dosyalarına yazım (salt-okunur; yazan orkestratör) | root `AGENTS.md` §16.5 |
| Değerlendirme raporu güvenliği (secret redaction) | OWASP denetiminin kendisi (security-engineer işi) | `.ai/AGENTS.md:562` |

**Sınır notu:** Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün)
kapsamı **DEĞİLDİR** (`evaluation/index.md:27`). Kademe-3 (Module) bu üretimde AÇILMADI;
children bilerek boştur (`evaluation/index.md:6`).

## §3 Değerlendirme Çekirdeği — İddia ↔ Disk Kanıtı Eşleşmesi

Zero-Hallucination bir **değerlendirme kuralıdır**: her iddia disk kanıtıyla eşleşmeden PASS alamaz.
Kök anayasa 4 madde verir (`Dosya: root AGENTS.md · Satır: 111-117`): repo/URL/API/class/method/
dependency/version/config/skill/agent/dosya/dizin/mimari kural **asla uydurulmaz**; bilinmeyen = `UNKNOWN`;
kanıtlanamayan = `⚠️ VERIFICATION REQUIRED`; her iddia disk kanıtı olmadan yazılmaz.

| # | İddia tipi | Eşleşme kuralı | Eşleşmezse | Kanıt |
|---|-----------|----------------|------------|-------|
| 1 | "Dosya var" | `Test-Path` / glob → diskte gerçek | Hayalet iddia — FAIL | root `AGENTS.md:113` |
| 2 | "Dosyada şu satır var" | `ReadAllLines` + satır no birebir | Uydurma alıntı — FAIL | root `AGENTS.md:115` |
| 3 | "URL şudur" | webfetch ile erişim + içerik eşleşmesi | Uydurma URL — FAIL | root `AGENTS.md:112` |
| 4 | "Sayı budur" (165 php, 19 png …) | Ölçüm tarihi + yeniden sayım | Ölçüsüz sayı — FAIL | `.ai/RAG.md:105` (rag-anayasa §5) |
| 5 | "Sistem çalışıyor" | IMPLEMENTED etiketi + kod kanıtı | PLANNED etiketi zorunlu | `.ai/AGENTS.md:630` |

**Değerlendirme formülü (bağlayıcı):** `PASS = tüm iddialar eşleşti` · `FAIL = en az 1 eşleşmeyen iddia` ·
`PARTIAL kabul edilmez` — eşleşmeyen iddia `⚠️ VERIFICATION REQUIRED` olarak etiketlenir ve çıktı
tamamlanmış sayılmaz (`.ai/AGENTS.md:551`: Hallüsinasyon → "VERIFICATION REQUIRED" etiketi).

## §4 Kalite Kapıları Tablosu (5 kapı)

Her kapı **ölçülür**; göz kararı PASS verilmez. Ölçüm betikleri bu üretimde fiilen çalıştırılmıştır (§5).

| # | Kapı | Ne ölçülür | Nasıl ölçülür | PASS eşiği | FAIL |
|---|------|-----------|---------------|-----------|------|
| 1 | Satır uzunluğu | Tam satır sayısı | `[IO.File]::ReadAllLines($p,[Text.Encoding]::UTF8).Count` | **≥500** | 499 ve altı |
| 2 | FM 10 alan | Frontmatter anahtar sayısı + sıra | `---` bloğu regex + `^[a-z_]+:` sayımı | **=10**, `title`/`parent` yok | 9/11 alan, yasaklı anahtar |
| 3 | Authority son satır | Son satır birebir eşleşme | `$lines[-1] -ceq '<Authority satırı>'` | **$true** (durable karşılaştırma) | herhangi 1 fark |
| 4 | Mojibake | U+FFFD (bozuk karakter) | `$text.Contains([char]0xFFFD)` | **0 adet** | ≥1 adet |
| 5 | Placeholder | şablon artığı — iki ardışık `{` | `$text.Contains('{' + '{')` | **0 adet** | ≥1 adet |

**Sıra kuralı:** kapılar **hepsi** yeşil olmadan dosya teslim sayılmaz — tek FAIL tüm dosyayı FAIL eder
(hard gate mantığı, `.ai/WORKFLOW.md` §5 Faz 7 "✅ HARD GATE" ile aynı semantik). Ölçüm betiği yalnız
**repo kökünden** çalıştırılır (`.ai/AGENTS.md` §13.6 kök kuralı — `.ai/` içinden çalıştırma geçersizdir).

## §5 Ölçüm Betikleri (PowerShell — bu üretimde kullanılan gerçek kapı kalıpları)

```powershell
# Dosya: evaluation-anayasa · Kapı 1+4+5 — satır / mojibake / placeholder ölçümü
$hedef = 'C:\www\coremusic.net\.ai\architecture\K015-AI\evaluation\evaluation-anayasa.md'
$lines = [IO.File]::ReadAllLines($hedef, [Text.Encoding]::UTF8)
$text  = [IO.File]::ReadAllText($hedef, [Text.Encoding]::UTF8)

$gate1 = if ($lines.Count -ge 500) { "PASS ($($lines.Count))" } else { "FAIL ($($lines.Count))" }
$gate4 = ([regex]::Matches($text, [char]0xFFFD)).Count   # U+FFFD → 0 olmalı
$gate5 = ([regex]::Matches($text, [regex]::Escape('{' + '{'))).Count   # iki ardışık açılı parantez
```

```powershell
# Dosya: evaluation-anayasa · Kapı 2 — FM 10 alan (anahtar regex)
$fm = [regex]::Match($text, '(?s)\A---\r?\n(.*?)\r?\n---')
$keys = @($fm.Groups[1].Value -split "`r?`n" | Where-Object { $_ -match '^[a-z_]+:' } |
          ForEach-Object { ($_ -split ':')[0].Trim() })
$gate2 = if ($keys.Count -eq 10 -and $keys -notcontains 'title' -and $keys -notcontains 'parent') `
         { "PASS ($($keys.Count))" } else { "FAIL ($($keys.Count))" }
```

```powershell
# Dosya: evaluation-anayasa · Kapı 3 — Authority son satır (-ceq, büyük/küçük harf duyarlı)
$expected = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
$last = $lines[$lines.Count - 1]
$gate3 = if ($last -ceq $expected) { 'PASS' } else { "FAIL: <$last>" }
```

**Neden `-ceq`?** PowerShell `-eq` varsayılan olarak kültüre duyarlıdır; Authority satırındaki
`·` (U+00B7) ve büyük/küçük harf farkları **birebir** kanıtlanmalıdır → `-ceq` (case-sensitive)
zorunludur. Betikler `C:\www\coremusic.net` kökünden çalıştırılır (§20 kök kuralı).

| # | Blok | Kapı(lar) | Ne döndürür |
|---|------|-----------|-------------|
| 1 | 1. blok | Kapı 1 (≥500) + Kapı 4 (U+FFFD 0) + Kapı 5 (placeholder 0) | `$gate1` · `$gate4` · `$gate5` |
| 2 | 2. blok | Kapı 2 (FM 10 alan, `title`/`parent` yasağı) | `$gate2` = PASS/FAIL + anahtar listesi |
| 3 | 3. blok | Kapı 3 (Authority son satır) | `$gate3` = PASS veya hatalı satırın kendisi |

Ölçüm çıktısı **rapora sayı olarak** yazılır (§12 `gates` alanı) — "geçti" denip sayı yazılmaz.

## §6 §24.4 Automatic Cleanup — Değerlendirme Karşılığı

`.ai/AGENTS.md` §24.4 (`Dosya: .ai/AGENTS.md · Satır: 546-553`) 4 durum tanımlar; her durumun
değerlendirme karşılığı vardır ve **değerlendirici bu 4 durumdan birini zorundadır** — "sorun yok"
varsayılamaz.

| # | Tetiklenen durum | §24.4 aksiyonu | Değerlendirme sonucu | Kanıt |
|---|------------------|----------------|----------------------|-------|
| 1 | LSP hata tespit | "FIX IMMEDIATELY", devam yasak | **FAIL** — değerlendirme durur | `.ai/AGENTS.md:550` |
| 2 | Hallüsinasyon | "VERIFICATION REQUIRED" etiketi | **FAIL** (etiketli) — teslim sayılmaz | `.ai/AGENTS.md:551` |
| 3 | Çelişki | DUR + kullanıcıya sor | **BLOCKED** — üst karar (§13) | `.ai/AGENTS.md:552` |
| 4 | Eksik dosya | Hemen tamamla veya sil | **RETRY** (§13 — 3 hakkından biri) | `.ai/AGENTS.md:553` |

**Sıra kuralı:** LSP/çelişki tespit edildiğinde kapı ölçümüne (§4) devam edilmez — önce temizlenir,
sonra ölçülür. Aksi halde ölçüm, bozuk dosya üzerinde yapılmış sahte PASS üretir (§19 sahte-PASS
uyarısı ile aynı aile).

## §7 §24.5 Quality Checklist Değerlendirmesi (disk ölçümü: 8 madde)

> **Sayım düzeltmesi (zero-hallucination):** brifingde "7 madde" denir; diskte ise §24.5'te
> **8 checkbox satırı** vardır (`Dosya: .ai/AGENTS.md · Satır: 558-565`). 7 sayımı yalnızca
> son maddedeki "(7 zorunlu alan)" notundan gelir. **Doğrusu 8/8'dir — sayı uydurulmaz.**

| # | Madde (birebir) | Değerlendirme yöntemi | Sonuç |
|---|-----------------|------------------------|-------|
| 1 | Syntax doğru mu? | LSP / parser hatası yok | `.ai/AGENTS.md:558` |
| 2 | Import'lar mevcut mu? | Her import diskte | `.ai/AGENTS.md:559` |
| 3 | Types uyumlu mu? | Typecheck uyarısı 0 | `.ai/AGENTS.md:560` |
| 4 | Style tutarlı mı? | Şablon + mevcut stille uyum | `.ai/AGENTS.md:561` |
| 5 | Security riski yok mu? | secret/redaction taraması (§15) | `.ai/AGENTS.md:562` |
| 6 | Template'e uygun mu? | Guardrail #16 şablon okundu mu | `.ai/AGENTS.md:563` |
| 7 | Cross-reference'lar geçerli mi? | `wiki-link-check.ps1` (§20) | `.ai/AGENTS.md:564` |
| 8 | Frontmatter tam mı? (7 zorunlu alan) | FM sayımı (§4 Kapı 2 — bu anayasada 10 alan) | `.ai/AGENTS.md:565` |

**Değerlendirme kuralı:** 8/8 yeşil → checklist PASS; herhangi biri FAIL → dosya teslim edilmez,
§13 retry döngüsüne girilir. Frontmatter maddesindeki "7 zorunlu alan" `.ai/` boot dosyaları içindir;
K015-AI architecture düğümleri **10 alanlı** FM kullanır (§17) — iki şema birbirine çevrilmez.

## §8 §16 Quality Standards — Değerlendirme Hedefleri Farklıdır

`.ai/AGENTS.md` §16 (`Satır: 571-581`) agent-bazlı **hedef** tanımlar. Değerlendirici bu hedefleri
**kendi kapısıyla karıştırmaz**: Backend `%100 strict_types` ile QA `≥%80 coverage` aynı ölçekte
değerlendirilemez; her agent'ın çıktısı **kendi standardıyla** ölçülür.

| Agent | Standart (§16) | Hedef | Değerlendirici ne ölçer | Kanıt |
|-------|----------------|-------|-------------------------|-------|
| Backend | strict_types, PSR-12, prepared statement | %100 | Dosyada `declare(strict_types=1)` + prepared yasağı ihlali | `.ai/AGENTS.md:575` |
| UI | ITCSS, BEM, WCAG 2.2 AA, C01-C16 | %100 | Katman/şablon uyumu (mockup gate ile birlikte) | `.ai/AGENTS.md:576` |
| Security | OWASP Top 10, `csrf_token`, Argon2id | %100 | Politika ihlali → veto (security-engineer) | `.ai/AGENTS.md:577` |
| Data | BCNF, no ORM, no SELECT *, prepared | %100 | SQL yüzeyi taraması | `.ai/AGENTS.md:578` |
| Embedded | Zero-allocation, lock-free, noexcept | %100 | RT kural ihlali | `.ai/AGENTS.md:579` |
| QA | Test coverage ≥%80, flaky %0 | ≥%80 | Coverage raporu + flaky sayımı | `.ai/AGENTS.md:580` |
| DevOps | CI/CD success ≥%95, GitLeaks clean | ≥%95 | Pipeline sonucu + secret taraması | `.ai/AGENTS.md:581` |

**Çapraz kural:** hedefler **değiştirilemez** (SSOT `.ai/AGENTS.md`); değerlendirme anayasası yalnızca
"nasıl ölçüleceğini" ekler. Hedef ile ölçüm sonucu çelişirse SSOT kazanır, bu dosya değil.

## §9 Değerlendirme ≠ Test ≠ Lint ≠ Security Audit (ayrım matrisi)

Dördü farklı soruları yanıtlar; biri diğerinin yerine kullanılamaz — karıştırma **sahte PASS** üretir.

| # | Aktivite | Sorusu | Kanıtı | Değerlendirmeye girer mi? |
|---|----------|--------|--------|---------------------------|
| 1 | **Değerlendirme** | Çıktı iddiaları diskle eşleşiyor mu? | Dosya/satır/ölçüm künyesi | ✅ bu dosyanın konusu |
| 2 | **Test** | Kod doğru çalışıyor mu? | Test suite çıktısı (QA) | ❌ ayrı süreç — sonuç **girdi** olarak alınır |
| 3 | **Lint** | Stil/syntax kuralları ihlal edildi mi? | LSP/PHPStan çıktısı | ❌ §6-§7'ye girdi; tek başına yeterli değil |
| 4 | **Security audit** | Güvenlik açığı var mı? | security-engineer raporu | ❌ veto yetkisiyle ayrı akış (§15) |

**Birlikte çalışma kuralı:** teslim kapısı = (değerlendirme PASS) ∧ (test PASS) ∧ (lint temiz) ∧
(veto yok). Değerlendirme **yalnız** birinci bileşenin sahibidir; "test geçti" diyerek iddia-kanıt
eşleşmesi **atlanamaz** (root `AGENTS.md` §2: `Verify` zincirde **iki kez** yer alır —
`Satır: 103`: `Understand → … → Test → Review → Secure → Verify → Document`).

## §10 Hallüsinasyon Değerlendirme Senaryoları (3 tip)

Her senaryonun **tespit adımı** vardır; "sanırım uydurma" ile tespit edilmez — ölçümle edilir.

| # | Senaryo | Tipik uydurma | Tespit adımı | Sonuç |
|---|---------|---------------|--------------|-------|
| 1 | **Uydurma dosya** | `.ai/wiki/foo.md` var denir | `Test-Path` / glob → 0 sonuç | FAIL + hayalet satır silinir (`.ai/RAG.md:102`) |
| 2 | **Uydurma satır / sayı** | "Satır 123'te şöyledir" | `ReadAllLines` → o satır farklı içerik | FAIL + `⚠️ VERIFICATION REQUIRED` |
| 3 | **Uydurma URL** | Doküman linki / API adresi | webfetch 404 veya içerik farklı | FAIL — URL uydurmak yasak (§3.3) |

**Ek senaryo (4): uydurma sürüm/eklenti** — "X paketi v9 kullanılıyor" iddiası `composer.json` /
lock dosyasıyla eşleşmezse FAIL (root `AGENTS.md:113` dependency/version maddesi). **Bilinen
gerçek:** `.ai/wiki/` diskte **0 dosyadır** — orada "wiki sayfası buldum" iddiası otomatik FAIL'tir
(`Dosya: root AGENTS.md · Satır: 190` — `.ai/wiki/` diskte YOK).

**Ölçü (bu üretim, 2026-10-17):** uydurma-senaryo tespiti için gerekli araçların tamamı mevcuttur
(`Test-Path`, `ReadAllLines`, `webfetch`); **otomatik hallüsinasyon skorlayıcı kodu repo'da YOK**
(§22).

## §11 İddia Tipi → Doğrulama Aracı Eşlemesi

Değerlendirici iddia tipine göre **tek** doğru aracı kullanır; yanlış araç geçersiz kanıt üretir.

| # | İddia tipi | Doğru araç | Yanlış araç (geçersiz) |
|---|-----------|-----------|------------------------|
| 1 | Dosya/dizin varlığı | `Test-Path` · `glob` (hidden: true için `.ai/`) | model hafızası |
| 2 | Dosya içeriği / satır | `read` + `ReadAllLines` künyesi | "hatırlıyorum" |
| 3 | Satır sayısı / ölçüm | `ReadAllLines(...).Count` | tahmini sayı |
| 4 | Web iddiası (URL/API) | `webfetch` / `websearch` | eğitim verisi |
| 5 | Kod davranışı | Repo'da okuma (`Dosya:`/`Satır:` künyeli) | illüstratif örnek (§23-§25) |

**Zorunlu ayrım:** illüstratif örnek **kanıt değildir** — o bloklar `⚠️ İllüstratif örnek - repo
kanıtı DEĞİL - upstream/docs referansı` etiketi taşır ve hiçbir PASS gerekçesi olarak kullanılamaz.

## §12 Değerlendirme Metrikleri — PASS/FAIL Skorlama

Tek metrik: **eşleşen iddia oranı kapıyı geçmez** — kapılar **hard gate**tir (§4). Buna ek
değerlendirme raporu şu alanları taşır:

| # | Alan | Değer | Yorum |
|---|------|-------|-------|
| 1 | `verdict` | `PASS` / `FAIL` / `BLOCKED` | PARTIAL yok (§3) |
| 2 | `gates` | 5/5 yeşil mi (§4) | 4.9 → FAIL |
| 3 | `claims_checked` | eşleşen/eşleşmeyen iddia sayısı | sayı = ölçümle |
| 4 | `retries` | kullanılan deneme (0-3) | §13 |
| 5 | `evidence` | `Dosya: … · Satır: …` künyeleri | künyesiz iddia sayılmaz |

**Örnek rapor satırı:** `verdict: PASS · gates 5/5 · claims 12/12 · retries 0 · mojibake 0`.
Eşikler: kapılar **≥/=/0** biçiminde sabittir (§4 tablosu); iddia eşleşme oranı **12/12 = %100**
zorunludur — %95 "neredeyse PASS" diye bir durum yoktur (root `AGENTS.md` §4: zayıf başarı
kriteri sürekli netlik gerektirir).

## §13 Retry / Escalation Değerlendirme Döngüsü

3 başarısız tur → **dur + üst karar**. Kaynaklar: `.ai/AGENTS.md` §8 (Max Retry 3),
§10.2 (escalation seviyeleri), kök `AGENTS.md` (döngü kuralı: 3 başarısız turda dur).

| # | Tur | Aksiyon | Sonuç |
|---|-----|---------|-------|
| 1 | İlk FAIL | Düzelticiye geri dön — düzeltme gerekçesi kanıtla | retry 1/3 |
| 2 | İkinci FAIL | Şüpheli varsayımı **açıkça yaz** | retry 2/3 |
| 3 | Üçüncü FAIL | **DUR** — şüpheli varsayımı raporla, üst karar iste | escalation |
| — | BLOCKED (çelişki) | Doğrudan üst karar — retry sayılmaz (§6.3) | L1 → L2 → L3 → İnsan |

**Escalation zinciri** (`Dosya: .ai/AGENTS.md · Satır` §10.1): `L1 (Domain Lead) → L2 (Tech Lead) →
L3 (Arch Lead) → İnsan`; timeout'lar L1 30s · L2 60s · L3 120s, **her seviyede max retry 3**
(`.ai/AGENTS.md` §10.2). **Asla** 4. tur denenmez ve aynı hata 2 kez tekrarlanmaz (root
`AGENTS.md` Final rule: "aynı hatayı tekrarlama").

**Değerlendirici yasağı:** subagent bu döngüde `git commit` **atmaz** — retry sonucu ne olursa olsun
commit kararı orkestratöre aittir (root `AGENTS.md` §16.5).

## §14 Değerlendirme → Vault-Sync Bulgu Akışı

Bulgu **tek doğru yere** yazılır; her şey her yere yazılmaz (aksi halde vault kirlenir).

| # | Bulgu tipi | Nereye yazılır | Sorumlu | Kanıt |
|---|-----------|----------------|---------|-------|
| 1 | Açık soru / karar bekliyor | Bu dosyanın `open_questions` alanı + ilgili `index.md` | orkestratör | `evaluation/index.md:13` |
| 2 | Session boyunca tekrarlanacak gözlem | `.ai/MEMORY.md` (session state) | MO (§7.7) | `.ai/AGENTS.md` §7.7 |
| 3 | Kalıcı mimari karar (değişiklik taşır) | `.ai/.decisions/` → ADR (onay akışı) | Vault Steward | `.ai/RAG.md` (ADR tetikleyicisi) |
| 4 | Denetim kaydı (neyi ne zaman ölçtük) | `.ai/log.md` — **append-only** | tüm ajanlar | `.ai/AGENTS.md:636` (log.md append-only) |
| 5 | Dosya eklendi/taşındı | `vault-sync-post` ile §3/indeks yeniden ölçülür | MO | `.ai/RAG.md:121` |

**Akış:** `Değerlendirme FAIL → düzelt → yeniden ölç → PASS → bulgu kararı (1-4) → vault-sync`.
**Yasak:** bu anayasa salt-okunur sürecinde **yalnız bu dosya** yazılır; `.ai/*.md` ve `shared/**`
başka dosyalarına yazım yasaktır — bulgu varsa raporlanır, o dosyaya **orkestratör** yazar.

## §15 Güvenlik — Değerlendirme Raporunda Secret Sızıntısı Olmaz

Değerlendirme raporu da bir **çıktıdır** → §3 kapsamındadır ve OWASP redaction kuralına tabidir.

| # | Risk | Kontrol | Sonuç |
|---|------|---------|-------|
| 1 | Rapor içine `.env` / `FIGMA_TOKEN` kopyalanması | Token/yazma yalnız `.ai/.env.figma`'dan okunur; rapora **hiç girmez** | root `AGENTS.md` §16.3 |
| 2 | Prompt/log içine `password\|secret\|api_key\|token` sızması | `PromptEngine::validatePrompt` regex taraması | `Dosya: shared/src/AI/PromptEngine.php · Satır: 176-179` |
| 3 | Log'da hassas veri | `[REDACTED]` maskeleme | `.ai/AGENTS.md` §17.3 |
| 4 | Raporun commit edilmesi | subagent commit yasak; onay orkestratörde | root `AGENTS.md` §16.5 |
| 5 | Kanıt olarak `.env` içeriği gösterilmesi | Kanıt = yol + satır no; değer asla | `.ai/RAG.md:137` |

**Kapı:** değerlendirme raporunda secret deseni bulunduysa **FAIL + BLOCKED** (§6.3 ile aynı muamele) —
rapor, secret içeriyorsa düzeltilmeden hiçbir yere iletilmez.

## §16 Token Ekonomisi — Değerlendirme Bütçesi

Değerlendirme de context harcar; **gereksiz okuma yasaktır** (root `AGENTS.md`: "gereksiz dosya/
skill/agent/context/plan üretme").

| # | Kural | Uygulama | Kanıt |
|---|-------|----------|-------|
| 1 | Okuma sırası P0→P1→P2→P3 | Vault'ta gereksiz dosya okunmaz | root `AGENTS.md` §13.1 |
| 2 | On-demand vault | "tüm vault'u oku" geçersiz; yalnız ilgili § | root `AGENTS.md` §9 |
| 3 | Boot'ta toplu okuma yok | Boot paketi 14 .ai kök dosyası ile sabit | `.ai/AGENTS.md` §25.4 |
| 4 | Prompt token bütçesi | `countTokens` > `MAX_TOKENS_DEFAULT` → risk kaydı | `Dosya: shared/src/AI/PromptEngine.php · Satır: 169-174` |
| 5 | Token overflow | Görev başarısız sayılır (retry tetiklenir) | `.ai/AGENTS.md` §18.4 |

**Değerlendirme bütçesi kuralı:** tek turda en fazla **5 dosya okunur** (hedef + SSOT + şablon +
kanıt kaynağı + indeks); 6. dosya okunacaksa gerekçesi rapora yazılır. PromptEngine'deki token
sayımı **tahmindir** (`ceil(karakter / 3.5)`) — kesin token iddiası yazılmaz
(`Dosya: shared/src/AI/PromptEngine.php · Satır: 90-96` — rag-anayasa §19 atfıyla).

## §17 Frontmatter (FM 10 Alan) Değerlendirmesi

| # | Alan | Zorunlu | Değerlendirme | Not |
|---|------|---------|----------------|-----|
| 1 | `node_id` | ✅ | dosya adıyla birebir | K015-AI düğüm şeması |
| 2 | `node_type` | ✅ | `module` / `index` / `subdomain` tutarlı | — |
| 3 | `status` | ✅ | `active` / `draft` gerçek durumla uyumlu | — |
| 4 | `parent_id` | ✅ | `evaluation` (klasör adıyla eş) | — |
| 5 | `children` | ✅ | kademe-3 açılmadıysa `[]` | `evaluation/index.md:6` |
| 6 | `depends_on` | ✅ | `[evaluation-index]` | — |
| 7 | `evidence` | ✅ | her satır disk kanıtlı, tarihli | — |
| 8 | `validation` | ✅ | ölçüm yöntemi yazılı | — |
| 9 | `open_questions` | ✅ | `[]` veya gerçek sorular | — |
| 10 | `group` | ✅ | `mimari` (iki-eksen gruplama) | `evaluation/index.md:41` |

**Yasak anahtarlar:** `title` ve `parent` **anahtarı YOKTUR** (brifing şeması — kapı 2 bunu ölçer).
FM bloğu dosyanın **ilk** `---` çifti arasında olur; `---` içinde `---` bulunmaz (regex §5/Kapı 2).

## §18 Authority / Mode Son Satır Değerlendirmesi

| # | Kural | Ayrıntı | Sonuç |
|---|-------|---------|-------|
| 1 | Son satır **birebir** | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | -ceq PASS |
| 2 | Ek satır yasak | Authority'den sonra **hiçbir** satır bulunmaz (blank dahi) | son satır ≠ Authority → FAIL |
| 3 | Biçim | `·` (U+00B7) ayraç, iki **bold** alan | U+002E ile değiştirilirse FAIL |
| 4 | Büyük/küçük harf | `Authority`, `Mode`, isimler birebir | `-eq` değil `-ceq` |
| 5 | Dosya sonu newline | Son satır `ReadAllLines[-1]` ile okunur; boş son satır sayıyı şişirmez | — |

**Örnek K015-AI kardeş dosyaları** bu satırı aynen taşır (ör. `rag-anayasa.md:516`,
`evaluation/index.md:48`) — çelişki çıkarsa **brifingdeki sabit metin** kazanır, kopya değil.

## §19 Mojibake (U+FFFD) Değerlendirmesi

| # | Tip | Örnek | Tespit | Sonuç |
|---|-----|-------|--------|-------|
| 1 | Bozuk karakter | `U+FFFD` () | `[regex]::Matches($text,[char]0xFFFD).Count` | >0 → FAIL |
| 2 | Çift kodlama | `Ã¼`, `Ã§` | gözden geçirme + tarama | FAIL |
| 3 | Kopuk Türkçe | `GÃ¶rev`, `UYGUNL` | §7-§8 checklist kapsamı | FAIL |
| 4 | Emoji/karakter kaybı | `⚠` yerine `?` | `⚠️ VERIFICATION REQUIRED` etiketi doğrulaması | FAIL |
| 5 | Unicode ayraç kaybı | `·` → `-` | Authority `-ceq` (§18) | FAIL |

**Tarihli kanıt:** vault genelinde 745 mojibake düzeltmesi yapılmıştır
(`Dosya: .ai/AGENTS.md · Satır` §26.3 — "Mojibake temizlendi (vault geneli 745 düzeltme)") —
yani kapı **geçmişte ihlal edildi** ve kalıcıdır; her üretimde yeniden ölçülür (§4 Kapı 4).

## §20 Wiki-Link & Cross-Reference Değerlendirmesi

| # | Kontrol | Yöntem | Bilinen durum |
|---|---------|--------|---------------|
| 1 | Wiki-link hedefleri diskte | `.ai/scripts/wiki-link-check.ps1` **kökten** | 2026-09-29: 227 link / 6 raporlanan = gerçek kırık **0** |
| 2 | 6 sahte kırık link | `[[C]]`/`[[V]]`/`[[link]]` şablon değişkenleri — **düzeltme YASAK** | `.ai/AGENTS.md` §13.7 |
| 3 | Betik çalıştırma yeri | yalnız repo kökünden | `.ai/AGENTS.md` §13.6 |
| 4 | `.ai/` arama gizliliği | glob/grep'te `hidden: true` | `.ai/` nokta dizin |
| 5 | Yeni `.md` şablonu | Guardrail #16 — şablon okunmadan üretim yasak | `.ai/AGENTS.md` §14 |

**Değerlendirme sonucu:** 6 sahte kırık **kusur sayılmaz**; "onarmaya" kalkışan çıktı FAIL'tir
(şablon bozulur). Gerçek kırık >0 ise FAIL → §13 retry.

## §21 PHP Repo Kanıtı — Değerlendirme Yüzeyi

Repo'da **değerlendirmeye komponent** niteliğinde iki gerçek kod parçası vardır (güven skoru +
çıktı doğrulama); her ikisi de künyelidir:

```php
// Dosya: shared/src/AI/KnowledgeBase.php · Satır: 223-231
private function calculateConfidence(array $retrieved): float
{
    if (empty($retrieved)) {
        return 0.0;
    }

    $avgScore = array_sum(array_column($retrieved, 'score')) / count($retrieved);
    return round(min($avgScore * 1.2, 1.0), 2);
}
```

```php
// Dosya: shared/src/AI/PromptEngine.php · Satır: 156-186 (validatePrompt — risk listesi döndürür)
public function validatePrompt(string $prompt): array
{
    $risks = [];
    // ... INJECTION_PATTERNS (Satır: 161-167) · token bütçesi (169-174) ·
    // hassas veri (176-179): password|secret|api[_-]?key|token
    return ['valid' => empty($risks), 'risks' => $risks, 'sanitized' => $sanitized];
}
```

**Sözleşme kanıtı:** çıktı şeması `answer/sources/confidence` üçlüsüdür —
`Dosya: shared/src/Contracts/AI/KnowledgeBaseInterface.php · Satır: 30`
(`array{answer: string, sources: array{…}, confidence: float}`). **Kısıt:** bu iki fonksiyon
değerlendirme **motoru değildir** — güven skoru arama skorunun 1.2 katıdır (tavan 1.0) ve
`validatePrompt` ADR-030'a göre **bağlı değildir** (rag-anayasa §19: "validatePrompt kodda,
bağlı değil → ❌ PLANNED").

| # | Dosya | Satır | Değerlendirmedeki rolü |
|---|-------|-------|------------------------|
| 1 | `shared/src/AI/KnowledgeBase.php` | 223-231 | `calculateConfidence` — güven skoru (0.0-1.0) |
| 2 | `shared/src/AI/KnowledgeBase.php` | 89 | çıktıya `confidence` alanının yazılması |
| 3 | `shared/src/AI/PromptEngine.php` | 156-186 | `validatePrompt` — risk listesi (`valid`/`risks`/`sanitized`) |
| 4 | `shared/src/Contracts/AI/KnowledgeBaseInterface.php` | 30 | çıktı şeması sözleşmesi (`answer/sources/confidence`) |

## §22 Repo'da Olmayan Yüzey (Açık Beyan)

- **Ajan çıktısı değerlendiren / hallüsinasyon skorlayan kod repo'da YOK** — `shared/src` içinde
  `evaluat|hallucin` taraması **0 eşleşir** (2026-10-17 grep ölçümü; yalnız `confidence` yorumu
  `KnowledgeBase.php:89` ve `JwtService.php:13` "zero-hallucination" yorum metni vardır).
- **LLM-judge / rubrik skorlayıcı yoktur** — ADR-030 kapsamında AI özellikleri `PLANNED`
  (`Dosya: ADR-030 · Satır: 305/356`: "LLM API/RAG/embedding/yerel model/öneri verisi 0").
- **Bugünkü değerlendirme = insan + agent kapı ölçümü** (§5 betikleri + gözden geçirme).
- Vault indeksini okuyup skor üreten kod **0 dosyadır** (rag-anayasa §20 ile aynı sonuç).
- Bu bölümde "değerlendirme sistemi çalışıyor" ifadesi **geçmez**; geçerse ihlaldir.

| # | Aranan yüzey | Ölçüm (2026-10-17) | Durum | Doğru etiket |
|---|--------------|---------------------|-------|--------------|
| 1 | `shared/src/**` içinde `evaluat\|hallucin` | 0 eşleşme (grep) | YOK | `PLANNED` |
| 2 | LLM-judge / rubrik skorlayıcı | 0 dosya | YOK | `PLANNED` (ADR-030:356) |
| 3 | Kapı ölçümü (satır/FM/Authority) | §5 PowerShell betikleri | **VAR (elle)** | `IMPLEMENTED` (süreç) |
| 4 | Vault indeksini skorlayan kod | 0 dosya | YOK | `PLANNED` |

## §23 İllüstratif Örnek — Python (claim-verifier)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# iddia ↔ disk kanıtı eşleştirici — upstream docs (claim verification) referansı; repo'da YOK
from pathlib import Path

def verify_claim(claim: str, evidence_path: str, expected: str) -> bool:
    """'Dosya: X · Satır: N' künyeli iddia; içerik birebir eşleşmeli."""
    p = Path(evidence_path)
    if not p.is_file():
        return False                      # uydurma dosya (§10.1)
    for line in p.read_text(encoding="utf-8").splitlines():
        if expected in line:
            return True                   # eşleşme = PASS gerekçesi
    return False                          # uydurma satır (§10.2)

verdict = "PASS" if verify_claim("c", ".ai/AGENTS.md", "§24.5 Quality Checklist") else "FAIL"
```

Kural: **eşleşmeyen iddia PASS üretemez** — fonksiyon tek yönlüdür (kanıt → sonuç).
`⚠️ VERIFICATION REQUIRED` etiketi, `False` durumunda **sonucun kendisidir** (§3.5).

| # | Girdi (`evidence_path`) | Beklenen (`expected`) | Beklenen sonuç | Karşılığı |
|---|-------------------------|------------------------|----------------|-----------|
| 1 | `.ai/AGENTS.md` (var) | gerçek satır metni | `True` → PASS | §11.2 |
| 2 | `.ai/wiki/x.md` (yok) | herhangi biri | `False` → FAIL | §10.1 uydurma dosya |
| 3 | `.ai/AGENTS.md` (var) | uydurma satır metni | `False` → FAIL | §10.2 uydurma satır |

## §24 İllüstratif Örnek — TypeScript (faithfulness skoru)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// bağlam-sadakat (faithfulness) skoru — upstream docs (Phoenix evals) referansı; repo'da YOK
type Claim = { text: string; supported: boolean };

export function faithfulness(claims: Claim[]): { verdict: "PASS" | "FAIL"; ratio: number } {
  const ok = claims.filter(c => c.supported).length;
  const ratio = claims.length === 0 ? 0 : ok / claims.length;
  return { verdict: ratio === 1 ? "PASS" : "FAIL", ratio };   // kısmi kabul yok (§12)
}
```

Upstream karşılığı: Phoenix `FaithfulnessEvaluator` 1.0/0.0 iki değerli çalışır
("faithful / unfaithful" — kaynak: arize.com/docs/phoenix, §27/1). CoreMusic'te bu **eşik değildir**;
karşılığı hard gate §4'tür — oran ≠ 1 → FAIL (§12).

| # | Upstream kavram | Kaynak (§27) | CoreMusic karşılığı | Fark |
|---|-----------------|--------------|---------------------|------|
| 1 | `score` 1.0 / 0.0 | Phoenix faithfulness | `verdict` PASS/FAIL (§12) | eşikli oran yok |
| 2 | `context` (reference) | DeepEval hallüsinasyon | **disk kanıtı** (§3) | context = dosya, retriever değil |
| 3 | `threshold` min | DeepEval | 5 **hard gate** (§4) | kısmi geçiş yok |
| 4 | evaluate → metrics pipeline | Google Cloud eval overview | elle kapı ölçümü (§5) | otomatik pipeline YOK (§22) |

## §25 İllüstratif Örnek — Go (gate runner)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// 5 kapılı değerlendirme koşucusu — upstream docs referansı; repo'da karşılığı YOK
package gate

type Result struct{ Name string; Pass bool; Detail string }

func RunAll(lines []string, text string, fmKeys int, authority string) []Result {
    last := lines[len(lines)-1]
    return []Result{
        {"satir>=500", len(lines) >= 500, fmt.Sprint(len(lines))},
        {"fm==10", fmKeys == 10, fmt.Sprint(fmKeys)},
        {"authority", last == authority, last},          // -ceq karşılığı (§18)
        {"mojibake==0", !strings.ContainsRune(text, rune(0xFFFD)), ""},
        {"placeholder==0", !strings.Contains(text, "{"+"{"), ""},
    }
}
```

`rune(0xFFFD)` = U+FFFD (§19). Çıktı **hard gate**: 5/5 PASS yoksa dosya teslim edilmez (§4).

| # | Go satırı | Kapı | PowerShell karşılığı |
|---|-----------|------|----------------------|
| 1 | `len(lines) >= 500` | Kapı 1 | `ReadAllLines(...).Count` |
| 2 | `fmKeys == 10` | Kapı 2 | FM regex + anahtar sayımı |
| 3 | `last == authority` | Kapı 3 | `$lines[-1] -ceq ...` |
| 4 | `!ContainsRune(text, rune(0xFFFD))` | Kapı 4 | `[char]0xFFFD` sayımı |
| 5 | `!Contains(text, "{"+"{")` | Kapı 5 | `Escape('{' + '{')` |

## §26 Doğrulama Kapıları (bu dosyanın üretim kapısı)

| # | Kapı | Kriter | Yöntem |
|---|------|--------|--------|
| 1 | Satır sayısı | ≥500 (500-520 bandı) | `ReadAllLines` UTF8 |
| 2 | FM 10 alan | 10 anahtar, `title`/`parent` yok | regex (§5) |
| 3 | Authority son satır | `-ceq` birebir | §5/Kapı 3 |
| 4 | Mojibake / placeholder | 0 / 0 | §5/Kapı 4-5 |
| 5 | Yapı | H1 + ≥25 § · her § tablo içerir | gözden geçirme |
| 6 | Kanıt künyesi | `Dosya: … · Satır: …` biçimli alıntılar | §21-§22 |
| 7 | İllüstratif bloklar | her birinin üstünde zorunlu `⚠️` etiketi | §23-§25 |
| 8 | Web kaynakları | 1-3 resmi kaynak, uydurma URL yok | §27 |

**Ölçüm tarihi:** 2026-10-17 üretim; kapılar bu üretimde fiilen çalıştırılmıştır (§5).

## §27 Web Kaynakları (3 resmi kaynak — doğrulandı)

| # | Kaynak | URL | Ne doğrulandı |
|---|--------|-----|---------------|
| 1 | Arize Phoenix — Faithfulness evaluator | `https://arize.com/docs/phoenix/evaluation/pre-built-metrics/faithfulness` | `input/output/context` 3 girdi; `score` 1.0/0.0; `label` faithful/unfaithful (2026-10-17 webfetch) |
| 2 | DeepEval — Hallucination metric | `https://deepeval.com/docs/metrics-hallucination` | `Hallucination = uyumlu context / toplam context`; `threshold` min; RAG'ta faithfulness'a geçiş (2026-10-17 webfetch) |
| 3 | Google Cloud — Gen AI evaluation overview | `https://cloud.google.com/vertex-ai/generative-ai/docs/models/evaluation-overview` | Evaluate bölüm yapısı: define metrics → dataset → run → view results → evaluate agents (2026-10-17 webfetch) |

**Kullanım sınırı:** bu kaynaklar **terminoloji çapraz kontrolü** içindir (§24'teki 1.0/0.0 karşılığı
gibi); CoreMusic kapı eşikleri (§4) **önceeldir** — literatür eşiği bunları **ezmez**. Uydurma URL
yoktur; üçü de bu üretimde erişilmiştir.

## §28 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | `evaluation/index.md` "kanıt bekliyor" satırı ne zaman `IMPLEMENTED` olur? | HIGH | ⏳ bu anayasa disk kanıtı üretti; index güncellemesi orkestratöre ait (`evaluation/index.md:10`) |
| 2 | Otomatik claim-verifier kodu ne zaman yazılacak? | HIGH | `⚠️ VERIFICATION REQUIRED` — ADR kararı yok (§22) |
| 3 | LLM-judge kullanılacak mı (literatürde yaygın)? | MEDIUM | `UNKNOWN` — ADR-030 AI özellikleri PLANNED |
| 4 | §16 hedefleri (Backend %100 vs QA ≥%80) tek raporda nasıl normalize edilir? | MEDIUM | çözülmedi — §8 "karıştırma yasak" kuralı geçici çözüm |
| 5 | 5 kapı dışına yeni kapı (ör. wiki-link) eklenecek mi? | LOW | `open_questions: []` — ekleme onayı orkestratörde |
| 6 | Değerlendirme bütçesi (§16: 5 dosya/tur) ölçülebilir mi? | LOW | `⚠️ VERIFICATION REQUIRED` — ölçüm altyapısı yok |

## §29 Kaynaklar

| # | Kaynak | Yol | Kullanım |
|---|--------|-----|----------|
| 1 | Agent Registry SSOT | `.ai/AGENTS.md` (v22.2.0) §24.4 · §24.5 · §16 · §8 · §10 · §17-§18 | §6-§8, §13, §15 |
| 2 | Vault süreç SSOT | `.ai/WORKFLOW.md` (v22.3.0, ilk 120 satır) §5 Hard Gate | §4, §14 |
| 3 | Kök kurallar | `AGENTS.md` (root) §2 (Satır: 100-109) · §3 (Satır: 111-117) | §3, §9, §10 |
| 4 | Anayasa şablonu (yapı referansı) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` (516 satır) | yapı: H1 + § + tablo + Authority |
| 5 | Alt-dümğüm künyesi | `.ai/architecture/K015-AI/evaluation/index.md` (48 satır) | §1-§2, §14, §28 |
| 6 | PHP kanıtı | `shared/src/AI/KnowledgeBase.php` · `PromptEngine.php` · `Contracts/AI/KnowledgeBaseInterface.php` | §21 |
| 7 | Ölçüm betiği (kapılar) | §5 (bu dosya) — kökten çalıştırılır | §4, §26 |
| 8 | Web kaynakları | §27 (3 resmi kaynak) | §24 |

## §30 Hızlı Referans + Ölçüm Notu

| # | Soru | Cevap |
|---|------|-------|
| 1 | Çıktı nasıl değerlendirilir? | İddia ↔ disk kanıtı eşleşmesi (§3) → 5 kapı (§4) → PASS/FAIL (§12) |
| 2 | Kapılar neler? | ≥500 satır · FM 10 alan · Authority son satır · U+FFFD 0 · placeholder 0 |
| 3 | Hallüsinasyon nasıl tespit? | Test-Path / ReadAllLines / webfetch (§10-§11) |
| 4 | 3 kez başarısız oldum? | DUR + üst karar (§13) — 4. tur yasak |
| 5 | Bulguyu nereye yazarım? | open_questions / MEMORY / ADR / log.md (§14) — dosyalara orkestratör yazar |
| 6 | Ölçüm betiği nereden? | Repo kökünden; `.ai/` içinden çalıştırma geçersiz (§20) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
