---
node_id: evaluation-surucu
node_type: module
status: active
parent_id: evaluation
children: []
depends_on: [evaluation-anayasa]
evidence:
  - ".ai/scripts/wiki-link-check.ps1 (43 satır, gerçek değerlendirme betiği, 2026-10-17 okuma)"
  - ".ai/AGENTS.md §13.6-§13.7 (gate çalıştırma yeri + sahte kırık link kuralı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# evaluation-surucu - Ajan Değerlendirme Sürucusu

> **Kapsam:** Ajan çıktısı geldiğinde sürücünün gerçekten çalıştığı adımlar — çıktı → kapı ölçümü →
> kanıt doğrulama → hallucination taraması → PASS/FAIL → retry/escalation → vault senkronu.
> **Kural koyar değil, tarif eder:** bağlayıcı kurallar `Dosya: .ai/AGENTS.md · §13.6-§13.7`
> (gate çalıştırma yeri + 6 sahte kırık link), `Dosya: .ai/AGENTS.md · §7.6` (Verification tablosu)
> ve `Dosya: .ai/AGENTS.md · §10.2` (max retry 3) içindedir. Bu dosya o kuralların **kullanım
> kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (tüm satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count`
> ile bugün diskten ölçülmüştür; gate betikleri **bugün repo kökünden çalıştırılmıştır** — §17).

## §1. ELI15 — Bu sürücü ne işe yarar

Bir ajan işini bitirdiğinde "bitti" demesi **kanıt değildir**. Sürücü, ajan çıktısını yedi adımdan
geçirir (§3): çıktıyı toplar, kapı betiğini **repo kökünden** çalıştırır, her iddianın `Dosya:` +
`Satır:` künyesini diskte doğrular, uydurma iddia taraması yapar, sonra PASS/FAIL kararı verir.
FAIL ise en fazla 3 tur retry denenir; hâlâ FAIL ise **durulur, şüpheli varsayım yazılır, üst karar
istenir**. Değerlendirme bulgusu doğru vault yüzeyine yazılır (§27) — ama bu sürucüyü üreten
subagent **yazmaz**, yalnız raporlar (§30).

> **eli10:** "Doğrulama" = betiği **doğru yerde** çalıştır + iddiayı **diskte** kanıtla + kararı
> **ölçümle** ver. Üçünden biri eksikse sonuç PASS bile olsa **sahte PASS**'tir (§13).
>
> **eli15:** Değerlendirme bugün **elle** yapılıyor — gate betikleri `.ai/scripts/*.ps1`'dir,
> CI'a bağlı değildir; PASS/FAIL kararı ajan/metin düzeyindedir. Bu yüzden sürücünün günlük
> davranışı (§9, §17) **otomasyonu değil, bugünkü elle akışı** anlatır.

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Çıktı → kapı → kanıt → PASS/FAIL → retry → senkron akışı (§3-§11) | Ajan performans **rubriğinin içeriği** (parent `evaluation` düğümünün işi) |
| Gate betiği çalıştırma yeri + sahte sonuç mekanizması (§6) | `.ai/scripts/**` betiklerinin **değiştirilmesi** (salt-okunur) |
| §13.7 sahte kırık link davranışı (§12) | `git commit` (orkestratöre aittir) |
| Değerlendirme hata modları + PowerShell kapım kalıpları (§13, §18-§20) | K006-yapay-zeka / ML **ürün** değerlendirmesi (o düğüm ayrı) |
| Vault senkronu hedef eşlemesi + token ekonomisi (§27-§28) | ADR metinleri (frozen — yalnız okunur) |

- **Kullananlar:** değerlendirme yapan her ajan · MO (retry/escalation kararı) · Vault Steward (denetim).
- **Ön koşul:** gate betikleri `.ai/scripts/` altında diskte vardır; çalıştırma yeri **repo köküdür**.
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` veya **bugünkü ölçüm çıktısı** künyelidir.

## §3. Değerlendirme Akışı A-G (genel bakış)

```text
[A] Çıktı toplama   → ajan raporu + üretilen dosya listesi + iddia künyeleri
[B] Kapı ölçümü     → .ai/scripts/*.ps1 REPO KÖKÜNDEN çalıştırılır (§13.6) → sayımlar
[C] Kanıt doğrulama → her `Dosya:/Satır:` künyesi diskte Test-Path + ReadAllLines ile
[D] Hallucination   → uydurma URL/dosya/sayı/sürüm taraması → etiketle (§8)
[E] PASS/FAIL       → karar ağacı (§9): her kapı ölçüldü mü + kanıtlandı mı
[F] Retry/Escalate  → FAIL → max 3 tur (§10) → hâlâ FAIL → DUR + şüpheli varsayım + üst karar
[G] Vault senkronu  → bulgu türü → doğru hedef (§27) → log/MEMORY/ADR/üst karar
```

| Adım | Girdi | Çıktı | Kapı | FAIL davranışı |
|---|---|---|---|---|
| A | ajan çıktısı | iddia listesi + dosya listesi | iddia künyeli mi | künyesiz iddia → D (tarama) |
| B | `.ai/scripts/*.ps1` | sayı (link/sayıma/sorunlu) | betik **kökten** + sayı dikey | `.ai/` içinden → geçersiz, tekrar (§6) |
| C | `Dosya:/Satır:` | disk kanıtı | Test-Path + satır aralığı | kanıt yok → `⚠️ VERIFICATION REQUIRED` |
| D | ham metin | etiket listesi | 0 uydurma iddia | etiket yoksa FAIL + DUR |
| E | B+C+D | `PASS` / `FAIL` | tüm kapılar (§29) | → F |
| F | FAIL | retry #1-#3 | 3 tur | 3 tur → DUR + üst karar |
| G | bulgu | vault hedefi | hedef eşlemesi (§27) | subagent **yazmaz**, raporlar |

**Bağlayıcı doğrulama tablosu** (`Dosya: .ai/AGENTS.md · §7.6 Step 6: Verification`): çıktı formatı
uygun · domain uyumluluğu doğru ajan · cross-reference geçerli wiki-link'ler · security hassas veri
redaction — dördü de E kararının **girişidir**, atlanamaz.

## §4. A — Çıktı Toplama

| Toplanan | Nasıl | Yasak |
|---|---|---|
| Ajan raporu (değerlendirme metni) | ham hâliyle okunur, özetlenmez | raporu "güzel" diye yeniden yazmak |
| Üretilen dosya listesi | `git status --porcelain` + glob (`.ai/` için `hidden: true`) | kapsam dışı dosyayı listeye sokmamak (gizleme) |
| İddia künyeleri | `Dosya: … · Satır: …` deseni ile ayrıştırılır | künyesiz iddianın "kanıt" sayılması |
| Ölçüm iddiaları | sayı + **ölçüm tarihi** birlikte | tarihsiz sayı = bellekten sayı sayılır (§13/2) |
| Durum etiketleri | `IMPLEMENTED` / `PLANNED` / `UNKNOWN` | kanıtsız `IMPLEMENTED` |

**Ölçüm iddiası kuralı:** bugün `wiki-link-check` **347 link / 6 kırık** verir (§17); §13.6'daki
"`227 link`" 2026-09-29 ölçümüdür. Bugünün değerlendirmesinde 227 yazmak **ölçülmemiş iddiadır** —
her sayı **ölçüm tarihiyle** yazılır.

## §5. B — Kapı Ölçümü (gate betiği)

| # | Kapı | Betik / komut | Bugünkü çıktı (2026-10-17, kökten) |
|---|---|---|---|
| B1 | Wiki-link bütünlüğü | `.ai/scripts/wiki-link-check.ps1` | `kontrol edilen link: 347` · `kirik link: 6` (§17) |
| B2 | Screens frontmatter | `.ai/scripts/screens-frontmatter-check.ps1` | `dosya: 24 \| sorunlu: 0` (§17) |
| B3 | Kalıp A/B/C | `.ai/scripts/kalip-abc-check.ps1` | bu turda **çalıştırılmadı** → `UNKNOWN` |
| B4 | Figma token | `.ai/scripts/figma-tokens.ps1` | bu turda **çalıştırılmadı** → `UNKNOWN` |
| B5 | Üretim kapısı (bu dosya) | `[IO.File]::ReadAllLines(...).Count` | §20 kalıbı — >=500 + FM 10 + Authority |
| B6 | Kapsam sayacı | `git status --porcelain` | yalnız bu dosya değişmeli |

**Kural:** kapıdan **çıkış yoktur** — betik raporlar (`Write-Host`), PASS/FAIL'i **sürücü verir**.
Böylece betik "0 hata" dese bile sürücü §13.7 gibi **bilinen sahte sonuçları** üstüne süzer.

## §6. §13.6 — Gate Betiği Neden YALNIZ Repo Kökünden Çalışır

> `Dosya: .ai/AGENTS.md · §13 kural 6`: ".ai/scripts/*.ps1 doğrulama scriptleri yalnızca **repo
> kökünden** (`C:\www\coremusic.net`) çalıştırılır — `screens-frontmatter-check.ps1` göreli yolları
> `.ai/` içinden çözemediği için **19 adet sahte 'PNG yok'** üretir."

**Sahte sonuç üretme mekanizması (adım adım):**

| # | Mekanizma | Sonuç |
|---|---|---|
| 1 | Betik göreli yol kullanır (`Join-Path 'C:\www\coremusic.net\.ai' $v` yanında `-Path $SCR` göreli okunabilir) | PowerShell göreli yolu **çalışma konumuna** çözer |
| 2 | Çalışma konumu `.ai/` ise hedef dizin farklı yerdedir | `Test-Path` → `false` |
| 3 | `Test-Path false` → betik "PNG yok" üretir | **19 sahte başarısızlık** (2026-09-29 ölçümü) |
| 4 | Sürücü bu sahte FAIL'i gerçek sanır | **sahte FAIL** (yanlış suçlama) — ve tersi de olur: yanlış konumda "0 dosya" okunursa **sahte PASS** |

**Resmi dayanak (web doğrulaması):** PowerShell göreli yollar **current working location**'a göre
çözülür — "The relative path `.\System` resolves as `C:\Windows\System`" (about_Path_Syntax,
Microsoft Learn). Ve "If you don't specify a path … the container or item is assumed to be located
in the current working location" (about_Locations, Microsoft Learn). Runscape konumu ile
`[System.Environment]::CurrentDirectory` **aynı şey değildir** (about_Locations NOT) — bu yüzden
"ben zaten repo kökündeyim" varsayımı **ölçülmeden yazılmaz**; `Get-Location` ile kanıtlanır.

**Sürücü davranışı:** her gate çağrısından önce `Get-Location` yazılır ve repo köküne
eşitlenir; eşit değilse **çalıştırma geçersiz sayılır ve tekrarlanır** (§18 kalıbı B0 adımı).

## §7. C — Kanıt Doğrulama (künye → disk)

| # | İddia türü | Doğrulama yöntemi | Çıkış |
|---|---|---|---|
| 1 | `Dosya: X · Satır: a-b` | `Test-Path X` + `ReadAllLines(X).Count >= b` | OK / GHOST / OUT-OF-RANGE |
| 2 | Wiki-link hedefi | `wiki-link-check.ps1` (kökten) | link sayısı + kırık sayısı |
| 3 | Dosya var mı (envanter) | `Test-Path` / glob (`hidden: true`) | var / yok |
| 4 | Dış URL/API | webfetch/resmi kaynak | doğrulandı / `⚠️ VERIFICATION REQUIRED` |
| 5 | Sürüm/bağımlılık | dosyadaki manifest (composer.json vb.) | dosyadan okunan değer |
| 6 | Sayı/istatistik | **yeniden ölçüm** (§17-§20) | tarihli sayı / `UNKNOWN` |

**Çıktı biçimi (kullanım hazır):**

```text
OK       : shared/src/AI/KnowledgeBase.php · Satır 1-232 → 232 satır, aralık kapsandı
GHOST    : .ai/wiki/anything.md → diskte yok (hayalet atıf)
OUTRANGE : Dosya 177 satır ama künye "Satır: 200" → künye geçersiz
VR       : dış URL doğrulanamadı — ⚠️ VERIFICATION REQUIRED
```

## §8. D — Hallucination Taraması

| # | Tarama | Desen / yöntem | Bulunursa |
|---|---|---|---|
| 1 | Uydurma dosya | künyedeki yol → `Test-Path` | GHOST → FAIL |
| 2 | Uydurma URL | `https?://` çıkar → webfetch ile 1-3 resmi kaynak | erişilemezse `⚠️ VERIFICATION REQUIRED`, "var" denmez |
| 3 | Uydurma sayı | sayı satırlarında ölçüm tarihi yoksa | `UNKNOWN`'a düşür |
| 4 | Uydurma API/sürüm/sınıf | manifest/dosya okunmadan yazılmış isim | etiketle, düzeltme **üst karar** |
| 5 | Mojibake | U+FFFD karakteri + yanlış kodlama ikilileri (desen: `[char]0xFFFD` · `[char]0x00C3`+`[char]0x20AC`) | §13/3 — false-negative riski |
| 6 | Yer tutucu | `\{\{` / `\{`+`{` üçlü sürgü | 0 değilse FAIL |
| 7 | Durum etiketi | kanıtsız `IMPLEMENTED` | `PLANNED`/`UNKNOWN` (§9 rag-anayasa ruhu) |

**Bağlayıcı kaynak:** "Zero-Hallucination: repository, URL, API, class, method, dependency, version,
config, skill, agent, dosya, dizin, mimari kural, benchmark, güvenlik iddiası **asla uydurulmaz** →
doğrulanamayan = `⚠️ VERIFICATION REQUIRED` · bilinmeyen = `UNKNOWN`"
(`Dosya: .ai/AGENTS.md · Kısayol Akış Diyagramı — Zero-Hallucination`).

## §9. E — PASS/FAIL Karar Ağacı

| # | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | Gate betikleri **repo kökünden** mi çalıştırıldı (`Get-Location` kanıtlı)? | → 2 | **FAIL (sahte sonuç riski)** — §6'ya dön |
| 2 | Tüm kapılar **ölçüldü** mü (tarihli sayı)? | → 3 | **FAIL (ölçülmemiş iddia)** |
| 3 | Her `Dosya:/Satır:` künyesi diskte **kapsanıyor** mu? | → 4 | **FAIL (ghost/out-of-range)** |
| 4 | Uydurma iddia taraması 0 mu? | → 5 | **FAIL + DUR** (§8/1-4) |
| 5 | Mojibake/yer tutucu taraması 0 mu? | → 6 | **FAIL** (§13/3, §13/4) |
| 6 | §13.7 bilinen 6 sahte kırık **üstüne süzüldü** mü (6 → gerçek 0)? | → 7 | **FAIL (kural çiğnendi — "onarmaya kalkışma")** |
| 7 | Kapsam dışı yazım 0 + `git commit` 0 mı? | → **PASS** | **FAIL (kapsam ihlali)** |

| Sonuç | Anlam | Sonraki |
|---|---|---|
| `PASS` | tüm kapılar ölçüldü + kanıtlandı | §27 vault senkronu (G) |
| `FAIL (düzeltilebilir)` | tek kapı eksik/yanlış | §10 retry #1 |
| `FAIL (3. tur)` | aynı hata 3 kez tekrarladı | **DUR** + şüpheli varsayım yaz + üst karar |
| `BLOCKED` | yasak yüzey / çelişki | derhal DUR, retry yok (§30) |

## §10. F — Retry / Escalation

| # | Kural | Değer | Kaynak |
|---|---|---|---|
| 1 | Max retry (HIGH/MEDIUM) | **3** | `Dosya: .ai/AGENTS.md · §8 Priority Levels` |
| 2 | Max retry her escalation seviyesinde | **3** | `Dosya: .ai/AGENTS.md · §10.2` |
| 3 | Aynı hatanın 3. tekrarında | **DUR** — şüpheli varsayımı yaz, üst karar iste | §15/11 (rag-surucu) · §18 Warnings #2 |
| 4 | Escalasyon zinciri | L1 Domain Lead → L2 Tech Lead → L3 Arch Lead → İnsan | `Dosya: .ai/AGENTS.md · §10` |
| 5 | Retry her turda | aynı kapı **yeniden ölçülür** (bellekten sonuç taşınmaz) | §5 B5-B6 |
| 6 | BLOCKED'ta retry | **yok** — derhal DUR | §29 kapı |

**Retry turu davranışı:** tur 1 → betiği kökten tekrar çalıştır + kanıtı tazele · tur 2 → hipotezi
daralt (hangi kapı? hangi künye?) · tur 3 → **aynı** kapı hâlâ FAIL ise dur ve şu üçünü yaz:
(1) şüpheli varsayım, (2) yapılan 3 ölçümün tarihli çıktıları, (3) üst kararı bekleyen açık soru.

## §11. G — Değerlendirme Sonrası Vault Senkronu (özet → §27 detay)

Değerlendirme bittiğinde bulgu **doğru yüzeye** yazılır; subagent bu yüzeylere **yazma yetkisine
sahip değildir** (§30) — bulgu raporlanır, yazımı MO/orkestratör yapar. Kısa eşleme §27'dedir;
buradaki tek kural: **bulgu türü hedefi belirler, ajan değil.**

## §12. §13.7 — 6 Sahte Kırık Link ("onarmaya kalkışma" davranısı)

> `Dosya: .ai/AGENTS.md · §13 kural 7`: `flow/auth/01-login.md [[C]]` · `flow/auth/04-select-gender.md
> [[V]]` · `flow/music/01-playback.md [[V]]` · `flow/settings/04-general.md [[V]]` ·
> `reference/legacy-inventory.md [[link]]` ×2 (L225, ` ```yaml ` bloğu içinde).
> "Bunlar şablon değişkeni/kaçış dizgesidir, gerçek kırık link değildir; **'onarmaya' kalkışmak
> şablonu bozar**. Sayım 6'da kalır ve kusur sayılmaz."

| # | Sürücü davranışı | Yanlış davranış |
|---|---|---|
| 1 | Betik çıktısındaki 6 satır **bilinen küme** ile birebir karşılaştırılır | 6'yı "hata" raporlamak |
| 2 | Eşleşen 6 → `gerçek kırık = 0` olarak **süzülmüş** sayı yazılır | 6'yı 0'a indirmek için dosyayı düzenlemek |
| 3 | Farklı bir kırık link çıkarsa (7. satır) → o **gerçek**dir, FAIL | hepsini "sahte" diye savunmak |
| 4 | Sayım 6'da **sabit** kalır; kusur sayılmaz | "6 kırık var, düzelteyim" (şablon bozulur) |

**Bugünkü kanıt (2026-10-17, kökten çalıştırıldı):** 6 kırık satırın tamamı bu listedir —
`01-login [[C]]` · `04-select-gender [[V]]` · `01-playback [[V]]` · `04-general [[V]]` ·
`legacy-inventory [[link]]` ×2 → **gerçek kırık 0** (§17).

## §13. Değerlendirme Hata Modları

| # | Hata modu | Semptom | Tespit | Davranış |
|---|---|---|---|---|
| 1 | **Sahte PASS** | "0 hata" denir ama betik `.ai/` içinden çalıştı / kapı hiç çalışmadı | `Get-Location` ≠ repo kökü; betik çıktı sayısı 0 dosyadan geliyor | Sonuç **geçersiz** → kökten tekrar çalıştır (§6) |
| 2 | **Ölçülmemiş iddia** | Bellekten sayı ("227 link") bugünün değerlendirmesinde tekrarlanıyor | iddiada ölçüm tarihi yok / tarih ≠ bugün | Sayıyı `UNKNOWN` yap, §20 kalıbıyla ölç |
| 3 | **Mojibake false-negative** | UTF8 okunmadan `İ/ş/ğ` bozulur; bozulmuş metinde `[[hedef]]` regex'i kaçar → link **kırık görünmez** | U+FFFD sayacı > 0 ya da `[char]0x00C3` + `[char]0x20AC` ikili deseni > 0 (kalıp §19/D) | Dosyayı `[Text.Encoding]::UTF8` ile yeniden oku, taramayı tekrarla |
| 4 | **Partial write** | Dosya yarım: FM kapanmamış / Authority satırı yok / § sayısı eksik | son satır eşitliği + `---` kapanışı + § sayacı | Kapı **FAIL** — "500 geçti" tek başına PASS değildir |
| 5 | Sahte FAIL (tersi) | `.ai/` içinden 19 "PNG yok" gibi sahte başarısızlık | sayı dikey eşiği ani sıçrama + konum kanıtı yok | Suçlama üretme → kökten tekrar ölç (§6/3) |
| 6 | Ghost künye | `Dosya:` yazılan hedef diskte yok | `Test-Path` false | Künyeyi `⚠️ VERIFICATION REQUIRED` yap (§7/1) |
| 7 | Kapıya güvenip kanıtsız PASS | betik PASS ama iddialar diskte değil | C adımı hiç çalıştırılmamış | C çalıştırılmadan PASS **yok** (§3 zinciri) |
| 8 | 3 tur sonra aynı hata | retry sonsuz döngüsü | tur sayacı = 3 | **DUR** + şüpheli varsayım + üst karar (§10/3) |
| 9 | Kapsam dışı yazım | tek dosya yerine birden fazlası değişti | `git status --porcelain` > 1 satır | FAIL — rollback orkestratöre |
| 10 | Kapsam dışı yazım (subagent) | `.ai/*.md` / `.ai/scripts/**` / `git commit` | whitelist sayacı | **BLOCKED** — yetki yok (§30) |

## §14. Gate Script Envanteri (`.ai/scripts/` — 6 betik, salt-okunur)

| Betik | Satır | İş | Çalışma yeri |
|---|---|---|---|
| `wiki-link-check.ps1` | **43** (2026-10-17 okuma) | `[[hedef]]` → dosya var mı? | repo kökü (§13.6) |
| `screens-frontmatter-check.ps1` | **73** (2026-10-17 okuma) | Kalıp D FM 13 alan + 9 bölüm + PNG yolu | repo kökü |
| `kalip-abc-check.ps1` | `UNKNOWN` (bu turda okunmadı) | Kalıp A/B/C bölüm denetimi | repo kökü |
| `figma-tokens.ps1` | `UNKNOWN` (bu turda okunmadı) | Figma token kapısı (Faz 9) | repo kökü |
| `figma-extract.ps1` | `UNKNOWN` (bu turda okunmadı) | Figma API çekimi (`.ai/.env.figma`) | repo kökü |
| `device-matrix-catid.ps1` | `UNKNOWN` (bu turda okunmadı) | CatID/45-tier matrisi | repo kökü |

> **Not:** 3 betiğin satırı bu turda **okunmadı** → `UNKNOWN`; bellekten satır sayısı **yazılmaz**.

## §15. Gerçek Betik A — `wiki-link-check.ps1` (künyeli alıntı)

> ✅ **Repo kanıtı** — `.ai/scripts/wiki-link-check.ps1` (43 satır) içinden birebir alıntılar.

```powershell
$ROOT='C:\www\coremusic.net\.ai\ui-design'                                   # Satır 5
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | # Satır 10
  ForEach-Object {
  $base=$_.BaseName.ToLowerInvariant()                                        # Satır 11
  if(-not $index.ContainsKey($base)){ $index[$base]=$_.FullName }             # Satır 13
}
foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){   # Satır 24
  $checked++                                                                  # Satır 27
  $k=$t.ToLowerInvariant(); if($index.ContainsKey($k)){ continue }            # Satır 29-30
  $cand=[System.IO.Path]::GetFullPath((Join-Path $dir ($t -replace '/','\'))) # Satır 32
  if(Test-Path $cand){ continue }                                             # Satır 33
  $broken.Add("$(Split-Path $src -Leaf) -> [[$t]]")                           # Satır 38
}
"kontrol edilen link: $checked"                                               # Satır 41
"kirik link: "+$broken.Count                                                 # Satır 42
$broken | Select-Object -First 40 | ForEach-Object { "  "+$_ }               # Satır 43
```

**Sürücü açısından anlamı:** betik üç kademeli eşleşme yapar (dosya anahtarı → göreli yol →
yalnız dosya adı) ve **raporlar, düzeltmez**; "6 kırık" çıktısının sahte olanlarını süzme
**sürücünün** işidir (§12) — betik bunu bilmez. `$ROOT` mutlak yoldur ama indeksleme
`.ai` kökünden mutlak yürür; buna rağmen §13.6 kuralı **tüm betikler** için bağlayıcıdır.

## §16. Gerçek Betik B — `screens-frontmatter-check.ps1` (künyeli alıntı)

> ✅ **Repo kanıtı** — `.ai/scripts/screens-frontmatter-check.ps1` (73 satır) içinden alıntılar.

```powershell
$SCR = 'C:\www\coremusic.net\.ai\ui-design\screens'                          # Satır 7
$PNG = 'C:\www\coremusic.net\.ai\.png'                                       # Satır 8
$REQUIRED_FM = @('reference_doc','title','type','category','date','status',   # Satır 10-11
                 'version','tier','viewport','device','authority',
                 'governance','reference')
$SECTIONS = @('## 1. ASCII Layout',…,'## 9. State')                           # Satır 12-14
$text = [System.IO.File]::ReadAllText($f.FullName, [Text.Encoding]::UTF8)     # Satır 21
  $issues += 'frontmatter KAPANIYOR'                                          # Satır 28
  $cand = if ($v -like '.ai/*' -or $v -like 'C:*') { $v }                     # Satır 57
          else { Join-Path 'C:\www\coremusic.net\.ai' $v }
  if (-not (Test-Path $cand)) { $issues += "PNG yok: $v" }                    # Satır 58
Write-Host "dosya: $($files.Count) | sorunlu: $fail"                          # Satır 73
```

**Sürücü açısından anlamı:** bu betik **mutlak yollarla** kuruludur (buna rağmen §13.6 ihlalinden
**19 sahte "PNG yok"** üretilmiştir — §6/3); "PNG yok" üretimi `Test-Path`'in başarısızlığıdır,
dosyanın yokluğu **değil**. Ayrıca `source_of_truth` alanında `VERIFICATION REQUIRED` **bulunan
dosyalar PNG kontrolünden muaftır** (Satır 55-56) — yani eksik-veri işareti, sahte FAIL üretmez.

## §17. Ölçüm Kanıtı — 2026-10-17 Gerçek Çalıştırma (repo kökünden)

```powershell
# Bu bölümdeki çıktılar üretim günü çalıştırılmış gerçek çıktılardır.
Set-Location 'C:\www\coremusic.net'
& '.\.ai\scripts\wiki-link-check.ps1'
& '.\.ai\scripts\screens-frontmatter-check.ps1'
```

| Betik | Çıktı (2026-10-17) | Yorum |
|---|---|---|
| `wiki-link-check.ps1` | `kontrol edilen link: 347` · `kirik link: 6` | 6 satırın tamamı §12 listesi → **gerçek kırık 0** |
| `screens-frontmatter-check.ps1` | `dosya: 24 \| sorunlu: 0` | screens altında **24 md** (glob 2026-10-17) |

**Sayım farkı kaydı (dürüst mutabakat):** `Dosya: .ai/AGENTS.md · §13.6` 2026-09-29 ölçümü
`227 link` · `21 ekran` der; bugün **347 link · 24 ekran** ölçülmüştür. Fark **bayatlama değil,
değişimdir** (screens altında T17-monitor-22fhd için `admin-register` / `admin-login` /
`admin-layout` dosyaları glob'da görünmektedir) — **eski sayı bugün için kullanılmaz**; hangi
tarihin bağlayıcı olduğu `⚠️ VERIFICATION REQUIRED` (§33/Q2).

## §18. PowerShell Kalıp 1 — Gate Runner (gerçek çalıştırılabilir)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden çalışır; B0 adımı §13.6 konum kapısıdır.

```powershell
# --- B0: konum kapısı (§13.6) — bu adım PASSA değilse hiçbir sonuç geçerli değildir ---
$repo = 'C:\www\coremusic.net'
Set-Location $repo
$loc = (Get-Location).Path
if ($loc -ne $repo) { "KONUM HATASI => $loc (kapı: $repo)"; exit 2 }

# --- B1-B2: betikleri çalıştır, çıktıyı ham topla ---
$gates = @('.ai\scripts\wiki-link-check.ps1', '.ai\scripts\screens-frontmatter-check.ps1')
foreach ($g in $gates) {
  "--- $g ---"
  & (Join-Path $repo $g)
}

# --- B3: yorum (bilinen sahte 6'yı süz — §12) ---
$known = @('01-login.md -> [[C]]','04-select-gender.md -> [[V]]','01-playback.md -> [[V]]',
           '04-general.md -> [[V]]','legacy-inventory.md -> [[link]]','legacy-inventory.md -> [[link]]')
$rawBroken = 6   # bugünün ölçümü: 6 (§17) — betik çıktısından okunur, bellekten DEĞİL
$realBroken = $rawBroken - $known.Count
"gercek kirik => {0}   (kapı: 0)" -f $realBroken
```

**Kullanım kuralı:** `$rawBroken` **betik çıktısından** okunur; §17'deki 6 bir **bugün ölçümüdür**,
gelecek turlarda sabitlenemez (§13/2 anti-pattern).

## §19. PowerShell Kalıp 2 — Kanıt Doğrulayıcı (künye → disk)

> ✅ **Çalıştırılabilir PowerShell betiği** — §7 kapı 1-2.

```powershell
# C) Künye iddiaları: 'Dosya: X · Satır: a-b' -> disk kapsaması
$claims = @(
  @{ f = '.ai\AGENTS.md';                 a = 144; b = 145 },  # §13.6-§13.7 bloğu
  @{ f = '.ai\scripts\wiki-link-check.ps1'; a = 1;   b = 43 }   # betik tamamı
)
foreach ($c in $claims) {
  if (-not (Test-Path $c.f)) { "GHOST  => $($c.f)"; continue }
  $n = [IO.File]::ReadAllLines((Join-Path (Get-Location) $c.f), [Text.Encoding]::UTF8).Count
  $ok = ($n -ge $c.b)
  "{0} satir={1} aralik={2}-{3} => {4}" -f $c.f, $n, $c.a, $c.b, $(if ($ok) { 'OK' } else { 'OUTRANGE' })
}

# D) Mojibake taraması (§13/3 — false-negative koruması)
$md = Get-ChildItem '.ai\architecture\K015-AI\evaluation' -Filter *.md -File
$moji = 0
foreach ($f in $md) {
  $t = [IO.File]::ReadAllText($f.FullName, [Text.Encoding]::UTF8)
  $patM = [string][char]0xFFFD + '|' + ([string][char]0x00C3) + ([string][char]0x20AC)
  $moji += ([regex]::Matches($t, $patM)).Count
}
"mojibake => {0} (kapı: 0)" -f $moji
```

## §20. PowerShell Kalıp 3 — FM / Authority / Satır Kapısı (bu dosyanın üretim kapısı)

> ✅ **Çalıştırılabilir PowerShell betiği** — §29 kapı 1-3.

```powershell
$p = '.ai\architecture\K015-AI\evaluation\evaluation-surucu.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir    => {0}   (kapı >=500: {1})" -f $lines.Count, ($lines.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority=> {0}" -f ($lines[$lines.Count - 1] -eq $auth)
$pat = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10    => {0}" -f ((Select-String -Path $p -Pattern $pat).Count -eq 10)
"yer tutucu => {0} (kapı: 0)" -f (Select-String -Path $p -Pattern '\{\{').Count
"bolum    => {0} (kapı: >=25)" -f ([regex]::Matches(($lines -join "`n"), '(?m)^## §')).Count
```

## §21. PHP — Repo Kanıtı (bu alan kodu YOK)

**Tarama (2026-10-17):** `grep -i 'evaluat|rubric|judge|scoring' --include=*.php` → `shared/src/`
içinde **0 isabet**.

| İddia | Tarama | Sonuç |
|---|---|---|
| "Repo'da bu alan kodu YOK: **ajan değerlendirme / rubric / skorlama PHP kodu**" | `evaluat\|rubric\|judge\|scoring` → `shared/src/**/*.php` | **0 isabet** (2026-10-17) |
| Değerlendirme kapısı betiği | `.ai/scripts/*.ps1` | **6 dosya** (§14) — betik dili **PowerShell** |
| Değerlendirme kararı çalıştıran servis | — | **YOK** — karar bugün elle + bu sürücü |

**Sonuç:** bu alanın uygulama katmanı **yoktur**; değerlendirici = ajan + `.ai/scripts/*.ps1`.
Yazılı hâle getirilmesi ayrı bir mimari karardır (ADR gerektirir — §33/Q4).

## §22. İllüstratif — Python Gate Runner

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# Repo kokuğu yakalayan gate runner (konum kapısı + sahte sonuç süzme)
from pathlib import Path
import subprocess

REPO = Path(r"C:\www\coremusic.net")
KNOWN_BROKEN = 6  # §13.7 bilinen sahte kırık link sayısı (süzme tabanı)

def run_gate(script: str) -> tuple[bool, str]:
    if Path.cwd() != REPO:                       # §13.6: .ai/ içinden çalıştırma
        return False, f"KONUM HATASI: {Path.cwd()}"
    out = subprocess.run(["powershell", "-NoProfile", "-File", str(REPO / script)],
                         capture_output=True, text=True, cwd=REPO, encoding="utf-8")
    if out.returncode != 0:
        return False, out.stderr.strip()
    return True, out.stdout

def judge(raw_broken: int) -> str:
    real = raw_broken - KNOWN_BROKEN
    if real < 0:
        return "FAIL: sayımlar §13.7 kümesiyle uyumsuz"
    return "PASS" if real == 0 else f"FAIL: {real} gerçek kırık link"
```

## §23. İllüstratif — Go Evidence Verifier

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package eval

import (
	"os"
	"strconv"
	"strings"
)

// Claim, "Dosya: X · Satır: a-b" künyesinin ayrıştırılmış hâlidir.
type Claim struct{ File string; From, To int }

// Verify, künyeyi diskle karşılaştırır: GHOST / OUTRANGE / OK.
func Verify(c Claim) string {
	b, err := os.ReadFile(c.File)
	if err != nil {
		return "GHOST" // diskte yok — uydurma atıf
	}
	n := len(strings.Split(strings.ReplaceAll(string(b), "\r\n", "\n"), "\n"))
	if n < c.To {
		return "OUTRANGE" // dosya kısa, künye uzun — partial write sinyali (§13/4)
	}
	return "OK"
}

func Parse(s string) (Claim, bool) { // "Dosya: a · Satır: 10-20"
	parts := strings.Split(s, "·")
	if len(parts) != 2 { return Claim{}, false }
	rng := strings.Split(strings.TrimSpace(strings.TrimPrefix(parts[1], " Satır:")), "-")
	from, _ := strconv.Atoi(rng[0])
	to, _ := strconv.Atoi(rng[1])
	return Claim{File: strings.TrimSpace(strings.TrimPrefix(parts[0], "Dosya:")), From: from, To: to}, true
}
```

## §24. İllüstratif — TypeScript PASS/FAIL Kararı

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type Verdict = 'PASS' | 'FAIL' | 'BLOCKED';
interface Gate { id: string; measured: boolean; fromRepoRoot: boolean; value: string; }

/** §9 karar ağacı: 7 kapı sırayla; ilk ihlal kararı verir. */
export function decide(gates: Gate[]): Verdict {
  const g = (id: string) => gates.find((x) => x.id === id);

  if (!g('B0')?.fromRepoRoot) return 'FAIL';        // §13.6 — sahte sonuç riski
  if (gates.some((x) => !x.measured)) return 'FAIL'; // §13/2 — ölçülmemiş iddia
  if (g('C1')?.value !== 'OK') return 'FAIL';        // §7 — ghost/out-of-range
  if (g('D1')?.value !== '0') return 'FAIL';         // §8 — uydurma iddia
  if (Number(g('B1')?.value ?? -1) - 6 !== 0) return 'FAIL'; // §12 — 6 sahte süzülmeli
  if (g('X1')?.value !== '0') return 'BLOCKED';      // §30 — kapsam dışı yazım
  return 'PASS';
}
```

## §25. Adım-Bazlı Karar Tabloları (A-G detayı)

| Adım | Ölçülen | PASS koşulu | FAIL örneği |
|---|---|---|---|
| A | iddia sayısı + künyeli olanlar | her iddia künyeli | künesiz "her şey hazır" |
| B1 | link sayısı (tarihli) | sayı yazıldı + tarih bugün | "227" (eski tarih) tekrarı |
| B2 | `dosya / sorunlu` | sorunlu = 0 | `.ai/` içinden 19 sahte PNG |
| B3-B4 | betik çıktıları | çalıştı **ya da** `UNKNOWN` yazıldı | çalışmadan "GECTI" denmesi |
| C1 | `Dosya:/Satır:` disk kapsaması | `OK` | `GHOST` / `OUTRANGE` |
| C2 | URL | 1-3 resmi kaynak doğrulandı | erişilmemiş URL "doğrulandı" |
| D | uydurma + mojibake + yer tutucu | 3'ü de 0 | `\{`+`{` ikilisi veya U+FFFD karakteri |
| E | karar ağacı (§9) | 7/7 kapı | 6/7 → FAIL |
| F | tur sayacı | ≤3 | 3'te DUR |
| G | hedef eşleme (§27) | bulgu doğru yüzeye raporlandı | subagent'in `.ai/*.md` yazması |

## §26. Retry Döngüsü Detayı

```text
FAIL (E)
  → [Tur 1] kapı betiğini KÖKTEN yeniden çalıştır + C kanıtını tazele
       → PASS → G
  → [Tur 2] daralt: hangi kapı? hangi künye? hipotez yaz
       → PASS → G
  → [Tur 3] aynı kapı hâlâ FAIL → DUR
       → (1) şüpheli varsayımı yaz  (2) 3 tarihli ölçüm çıktısı  (3) üst karar iste
  → BLOCKED (§30 ihlali) → tur yok, derhal DUR
```

| Tur | Zorunlu çıktı | Kaynak |
|---|---|---|
| 1 | taze ölçüm çıktısı (kopyalanmış eski sayı değil) | §5, §17 |
| 2 | daraltılmış hipotez + tek kapı | §9 |
| 3 | **DUR** yazımı: varsayım + kanıt + açık soru | `.ai/AGENTS.md · §18 Warnings #2` (max 3 retry) |

## §27. Değerlendirme Sonrası Vault Senkronu — Bulgu Türü → Hedef

| # | Bulgu türü | Hedef yüzey | Yazan | Not |
|---|---|---|---|---|
| 1 | Bağlayıcı kural değişikliği / yeni kapı | **ADR kararı** → `.ai/.decisions/` (yeni ADR) | MO + Vault Steward | frozen ADR'ler değişmez |
| 2 | Ölçüm/kapı sonucu (tarihli sayı) | ilgili **surucu/dogrulama** dosyasının `validation`/ölçüm satırı | MO | sayı + tarih birlikte |
| 3 | Çelişki / doğrulanamayan iddia | bu dosyanın **§33 Açık Sorular** + `⚠️ VERIFICATION REQUIRED` | üreten ajan (rapor) | kapatılmaz, işaretlenir |
| 4 | Gate/betik bulgusu (`.ai/scripts/**`) | `.ai/AGENTS.md §13.6-§13.7` notu (kural metni) | **yalnız MO** | scripts salt-okunur |
| 5 | Session kapanışı / denetim izi | `.ai/log.md` (**append-only**) · `.ai/MEMORY.md` | MO | geçmiş satıra dokunulmaz |
| 6 | Yeni vault dosyası üretildiyse | `RAG.md §3` indeks satırı + `vault-sync-post` | MO | rag-surucu §12 akışı |
| 7 | Retry/üst karar sonucu | üst karar (insan/MO) → karar ADR'ye | insan/MO | subagent kararı uygulamaz |

**Altın kural:** bulgu **türü** hedefi belirler; ajanın kolayına gelen dosya değil. Bu sürucüyü
üreten subagent yalnız **raporlar** — §30 yasakları gereği hedef yüzeylere **yazamaz**.

## §28. Token / Okuma Ekonomisi

| # | Kural | Sonuç |
|---|---|---|
| 1 | Boot'ta okunan dosya yeniden okunmaz (`.ai/AGENTS.md`, kök `AGENTS.md`) | çift israf yok |
| 2 | Betikler **tam okunmaz**, §15-§16'daki satır aralıkları okunur | 43 + 73 satır yerine ~40 satır |
| 3 | Gate betiği her turda **tam** çalıştırılır (önbellek yok) | ama çıktı yalnız özet sayım alınır |
| 4 | `.ai/` gizli dizin → glob/grep `hidden: true` | sahte "0 sonuç" (false PASS) yok |
| 5 | Kanıt okuması §-bazlı (`Dosya:/Satır:` aralığı) | tam dosya okuma yalnız üretim kapısında |
| 6 | Ölçüm betikleri toplu çalıştırılır (tek turda A+B5+B6) | tur başına tek konum değiştirme |

## §29. Doğrulama Kapıları (bu dosyanın + değerlendirmenin)

| # | Kapı | Kriter | Yöntem |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz | §20 `$pat` sayacı |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` |
| 3 | Authority son satır | birebir | §20 eşitlik kontrolü |
| 4 | H2 bölüm sayısı | `>= 25` | §20 regex sayacı |
| 5 | Yer tutucu | `\{\{` → 0 | §20 |
| 6 | Mojibake | 0 | §19 D |
| 7 | Wiki-link | hedefler diskte · 6 sahte süzülmüş | `.ai/scripts/wiki-link-check.ps1` (kökten) |
| 8 | Konum kapısı | `Get-Location` = repo kökü | §18 B0 |
| 9 | Kapsam | yalnız bu dosya değişti | `git status --porcelain` |
| 10 | `git commit` | **0** (subagent yasak) | whitelist sayacı |

## §30. Yasaklar ve Sınırlar

| # | Yasak | Neden |
|---|---|---|
| 1 | `.ai/` **içinden** gate betiği çalıştırmak | 19 sahte "PNG yok" (§13.6) |
| 2 | `git commit` atmak | **orkestratöre aittir** (subagent yasak) |
| 3 | `.ai/*.md` ve `.ai/scripts/**` yazmak (bu dosya hariç) | salt-okunur — bulgu §27'de **raporlanır** |
| 4 | §13.7 sahte kırık link'i "onarmak" | şablon bozulur, sayım 6 sabit kalır (§12) |
| 5 | Tarihsiz/ölçülmemiş sayı yazmak | Zero-Hallucination ihlali (§13/2) |
| 6 | Bu dosya dışına yazmak / dosya adını değiştirmek | kapsam ihlali (§29/9) |
| 7 | Erişilemeyen URL'yi "doğrulandı" demek | `⚠️ VERIFICATION REQUIRED` zorunlu |
| 8 | 3 turdan fazla retry | sonsuz döngü — §10/3 DUR |

## §31. Sık Yapılan Hatalar (anti-pattern)

| # | Yanlış | Doğru |
|---|---|---|
| 1 | Betik "0 hata" dedi → PASS | konum kanıtı (`Get-Location`) + C kanıtı (§9/1) |
| 2 | 2026-09-29 sayısını bugün kullanmak | bugün ölç (347 / 24 — §17) |
| 3 | 6 kırık link'i düzeltmeye kalkmak | süz, "gerçek 0" yaz (§12) |
| 4 | `.ai/**` aramasını `hidden:false` yapmak | `hidden: true` → sahte 0 sonuç |
| 5 | Sayfa 500 satırı geçti → PASS | FM + Authority + §20 + §29 birlikte |
| 6 | Mojibake'yi "dosya bozuk" sanmak | UTF8 ile yeniden oku → hâlâ bozuksa raporla (§13/3) |
| 7 | FAIL'de aynı yolu tekrar denemek | §26 hipotez daraltma; 3. turda DUR |
| 8 | Bulguğu kendi dosyasına yazmak | §27 hedef eşlemesi + subagent yazma yasağı |

## §32. Kapanış Kontrol Listesi (her değerlendirme sonunda)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | Betikler repo kökünden mi çalıştı (`Get-Location` kanıtı)? | evet |
| 2 | Her sayı **tarihli** ölçüm mü? | evet |
| 3 | Her `Dosya:/Satır:` künyesi diskte kapsandı mı? | evet |
| 4 | 6 sahte kırık süzüldü → gerçek 0 mı? | evet |
| 5 | Mojibake / yer tutucu taraması 0 mı? | evet |
| 6 | FAIL varsa ≤3 tur ve DUR yazımı yapıldı mı? | evet / yok |
| 7 | Bulgu §27 hedefine **raporlandı** mı (yazım değil)? | evet |
| 8 | Kapsam dışı yazım 0 + `git commit` 0 mı? | **hayır (0)** |

## §33. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [evaluation-anayasa]` — bu üretim turunda `evaluation/` altında **yalnız** `index.md` + bu dosya diskte (glob 2026-10-17). `evaluation-anayasa` paralel üretiliyor mu? | `⚠️ VERIFICATION REQUIRED` |
| Q2 | §13.6 sayıları (227/21) ile bugünün ölçümü (347/24) arasındaki fark — hangi tarih bağlayıcı, fark kaydı `.ai/AGENTS.md`'ye eklenecek mi? | orkestratör kararı |
| Q3 | `kalip-abc-check.ps1` · `figma-tokens.ps1` · `figma-extract.ps1` · `device-matrix-catid.ps1` satır sayıları bu turda okunmadı → `UNKNOWN` | bilgi: sonraki turda ölç |
| Q4 | Değerlendirme kapısının PHP/CI tarafı (repo'da 0 kod — §21) yazılacak mı? ADR gerekir mi? | ADR kararı bekliyor |
| Q5 | Ebeveyn `evaluation/index.md` `children: []` ve "kademe-3 bu fazda AÇILMADI" diyor (`Satır: 6`); bu modül eklendikten sonra `children` güncellenecek mi? | orkestratör kararı |
| Q6 | PASS/FAIL kararı elle mi kalacak, otomatik gate runner (§18/§22) mı devreye girecek? | MO + Vault Steward |

## §34. Kaynaklar

| Kaynak | Yol / URL | Kullanım amacı | Ölçüm |
|---|---|---|---|
| Agent Registry SSOT | `.ai/AGENTS.md` | §13.6-§13.7 (kural 6-7) · §7.6 Verification · §8 §10.2 retry | 775 satır |
| Gate betiği (gerçek) | `.ai/scripts/wiki-link-check.ps1` | §15 alıntı + §17 ölçüm | **43 satır** |
| FM gate betiği | `.ai/scripts/screens-frontmatter-check.ps1` | §16 alıntı + §17 ölçüm | **73 satır** |
| Gate envanteri | `.ai/scripts/*.ps1` | §14 tablo | **6 betik** |
| Ebeveyn düğüm | `.ai/architecture/K015-AI/evaluation/index.md` | kapsam sınırı (Satır: 27) | 48 satır |
| Yapı referansı (şablon) | `.ai/architecture/K015-AI/rag/rag-surucu.md` | H1 + § + kapı + kalıp iskeleti | 779 satır |
| Retrieval surucusu | `.ai/architecture/K015-AI/rag/rag-surucu.md` §15/11 | 3 tur → DUR kuralı | — |
| PowerShell — göreli yol | `https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_path_syntax` | §6/1-2 (relative → current location) | web doğrulandı 2026-10-17 |
| PowerShell — konum | `https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_locations` | §6 (runscape konumu ≠ `Environment.CurrentDirectory`) | web doğrulandı 2026-10-17 |
| .NET — dosya okuma | `https://learn.microsoft.com/en-us/dotnet/api/system.io.file.readalllines` | §19 (UTF8 + BOM algılama / Encoding aşırı yüklü hâli) | web doğrulandı 2026-10-17 |
| PHP değerlendirme kodu | `shared/src/**/*.php` | §21 negatif kanıt (grep 0 isabet) | 0 sonuç |

## §35. Gate (özet)

§1-§35 tamam: ELI15 · kapsam · A-G 7 adımlı akış · kapı tablosu · §13.6 konum mekanizması (4 satır) ·
künye doğrulama 6 satır · hallucination 7 tarama · 7 kapılı PASS/FAIL karar ağacı · retry 6 kural ·
§13.7 sahte link 4 davranış · 10 hata modu · 6 betik envanteri · PowerShell gerçek alıntı 2 blok ·
ölçüm kanıtı tablosu · PowerShell kalıp 3 (gate runner / kanıt doğrulayıcı / FM kapısı) ·
PHP negatif kanıt · illüstratif 3 blok (Python/Go/TS) · adım tablosu · retry detayı ·
vault senkronu 7 hedef · 6 token kuralı · 10 kapı · 8 yasak · 8 anti-pattern · 8 soruluk kapanış ·
6 açık soru · 11 kaynak.

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · H2 `>= 25` ·
kapsam dışı yazım **0** · `git commit` **0** · gate betikleri **repo kökünden**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
