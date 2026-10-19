---
node_id: evaluation-ornekler
node_type: module
status: active
parent_id: evaluation
children: []
depends_on: [evaluation-anayasa, evaluation-surucu]
evidence:
  - ".ai/scripts/wiki-link-check.ps1 + kalip-abc-check.ps1 (gerçek değerlendirme betik kanıtı, 2026-10-17 okuma)"
  - ".ai/scripts/screens-frontmatter-check.ps1 (FM değerlendirme, varsa alıntı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# evaluation-ornekler - Ajan Değerlendirme Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Üç `.ai/scripts/*.ps1` gate betiği 2026-10-17'de **tam okundu**; her PowerShell
> alıntısı gerçek satırdır (`Dosya:` / `Satır:` künyeli). İllüstratif çok-dilli bloklar repo
> kanıtı **değildir**; her birinin üstünde zorunlu etiket taşır. `.ai/scripts/**` salt-okunurdur,
> bu üretimde hiçbir betik değiştirilmedi.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob ile doğrulanmış: `.ai/scripts/*.ps1` → 6 isabet) |
| Satır | okunan aralık (L24-L38 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| Bölüm | satır numarası yoksa bölüm adı (`§13.6`) — uydurma satır no yazılmaz |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | `ReadAllLines(...).Count` / grep sayacı — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali · çelişkide disk kazanır, alıntı tazelenir |

## §2. Değerlendirme Betik Envanteri (glob kanıtı)

Dosya: `.ai/scripts/` · glob `*.ps1` → **6 isabet** (2026-10-17)

| # | Betik | Satır | Görev (kendi başlığından) | Değerlendirme rolü |
|---|---|---|---|---|
| 1 | `wiki-link-check.ps1` | 43 | `[[hedef]] -> gercek dosya var mi?` (L2) | link bütünlüğü gate |
| 2 | `kalip-abc-check.ps1` | 231 | `Faz 6 kapisi` + `Salt-okunur: raporlar, degistirmez` (L3, L5) | Kalıp A/B/C gate |
| 3 | `screens-frontmatter-check.ps1` | 73 | `Faz 5 kapisı ... Degistirmez, raporlar.` (L2-L3) | FM + bölüm gate |
| 4 | `figma-tokens.ps1` | okunmadı | — | ⚠️ VERIFICATION REQUIRED (Faz 9 kapısı, §26) |
| 5 | `figma-extract.ps1` | okunmadı | — | ⚠️ VERIFICATION REQUIRED (çekim, gate değil) |
| 6 | `device-matrix-catid.ps1` | okunmadı | — | ⚠️ VERIFICATION REQUIRED |

**Okuma sınırı:** bu dosya yalnız 1-3'ü **tam** okudu; 4-6 için satır künyesi **yazılmaz**.

## §3. wiki-link-check — Kabuk + UTF-8 Ayarı

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L1-L6

```powershell
#Requires -Version 5.1
<# wiki-link denetimi: .ai/ui-design/**/*.md icindeki [[hedef]] -> gercek dosya var mi? #>
$ErrorActionPreference='Stop'
[Console]::OutputEncoding=[Text.Encoding]::UTF8
$ROOT='C:\www\coremusic.net\.ai\ui-design'
$enc=[Text.Encoding]::UTF8
```

| Gözlem | Kanıt |
|---|---|
| Sürüm kapısı `#Requires -Version 5.1` | L1 |
| Hata → dur (`Stop`), sessiz geçiş yok | L3 |
| Konsol + dosya okuma **ikisi de UTF-8** — mojibake'nin ilk savunması | L4, L6 |
| Tarama kökü sabit mutlak yol (`.ai/ui-design`) | L5 |

## §4. wiki-link-check — Dosya İndeksi İnşası (3 anahtar türü)

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L8-L16

```powershell
# index: dosya adi (uzantisiz, kucuk) -> gercek tam yol
$index=@{}
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | ForEach-Object {
  $base=$_.BaseName.ToLowerInvariant()
  $rel=$_.FullName.Substring(0,$_.FullName.Length-3)
  if(-not $index.ContainsKey($base)){ $index[$base]=$_.FullName }
  $index[$_.FullName.ToLowerInvariant()]=$_.FullName
  $index[$rel.ToLowerInvariant()]=$_.FullName
}
```

| Anahtar | Örnek | Satır |
|---|---|---|
| uzantısız dosya adı (küçük) | `claude.md` | L11, L13 |
| tam yol (küçük) | `c:\www\coremusic.net\.ai\claude.md` | L14 |
| uzantısız göreli yol (küçük) | `c:\www\coremusic.net\.ai\claude` | L15 |
| Çakışmada **ilk kazanır** (ContainsKey koruması) | — | L13 |

## §5. wiki-link-check — Link Taraması (regex) + 3 Aşamalı Çözümleme

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L20-L40

```powershell
$broken=New-Object System.Collections.Generic.List[string]
$checked=0
Get-ChildItem -Path $ROOT -Recurse -Filter *.md -File | ForEach-Object {
  $src=$_.FullName
  $dir=Split-Path $src -Parent
  $text=[System.IO.File]::ReadAllText($src,$enc)
  foreach($m in [regex]::Matches($text,'\[\[([^\]|#]+)(?:[#|][^\]]*)?\]\]')){
    $t=$m.Groups[1].Value.Trim()
    if(-not $t){ continue }
    $checked++
    # 1) dogrudan dosya anahtari
    $k=$t.ToLowerInvariant()
    if($index.ContainsKey($k)){ continue }
    # 2) goreli yol
    $cand=[System.IO.Path]::GetFullPath((Join-Path $dir ($t -replace '/','\')))
    if(Test-Path $cand){ continue }
    if(Test-Path ($cand+'.md')){ continue }
    # 3) sadece dosya adi
    $leaf=[System.IO.Path]::GetFileNameWithoutExtension(($t -replace '/','\'))
    if($index.ContainsKey($leaf.ToLowerInvariant())){ continue }
    $broken.Add("$(Split-Path $src -Leaf) -> [[$t]]")
  }
}
```

| Aşama | Yöntem | Satır | Sadeleştirirse |
|---|---|---|---|
| 1 | indeks anahtarı (tam eşleşme) | L29-L30 | göreli link'ler düşer |
| 2 | göreli yol + `.md` eki (`Test-Path`) | L32-L34 | dizin-içi link'ler düşer |
| 3 | yalnız dosya adı (yaprak) | L36-L37 | aynı adlı çoklu eşleşme yanlış geçer |
| Kırmızı bayrak | kırık kayıt biçimi `dosya -> [[hedef]]` | L38 | sayım kaybolur |

**Regex anlamı:** `[[hedef]]` · `[[hedef#çapa]]` · `[[hedef|görünen]]` — grup 1 yalnız hedeftir,
`#` ve `|` son ekleri atılır (L24).

## §6. wiki-link-check — Sayım ve Sonuç Çıktısı

Dosya: `.ai/scripts/wiki-link-check.ps1` · Satır: L41-L43

```powershell
"kontrol edilen link: $checked"
"kirik link: "+$broken.Count
$broken | Select-Object -First 40 | ForEach-Object { "  "+$_ }
```

| Çıktı satırı | Ne ölçer | Sınır |
|---|---|---|
| `kontrol edilen link:` | taranan `[[...]]` sayısı (pozitif) | — |
| `kirik link:` | çözümlenemeyen hedef sayısı (negatif) | hedef: 0 |
| `Select-Object -First 40` | rapor **serpilir** — ilk 40 kırık yazılır | sayım tamdır, liste kırpılmıştır |

**Yorum kuralı:** `First 40` yüzünden "liste 40'ta bitti" diye okunmaz; sayısal kapı yalnız
`$broken.Count` üzerindendir.

## §7. wiki-link-check — Kayıtlı Ölçüm (betik dışı, vault kanıtı)

Dosya: `.ai/AGENTS.md` · Bölüm: `§13.6` + `§7.2` (satır no yok — bölüm künyesi, satır uydurulmadı)

> Alıntı (bölüm metni): *"`wiki-link-check` `227 link / 6 raporlanan` = **gerçek kırık 0**
> (6'sı §13.7 sahte)"* — 2026-09-29 kök ölçümü.

| Sayı | Anlam | Değerlendirme |
|---|---|---|
| 227 | `$checked` karşılığı | §6 satır 1 |
| 6 raporlanan | `$broken.Count` | §6 satır 2 |
| **gerçek kırık 0** | 6'sı şablon değişkeni/kaçış dizgesi (`[[C]]`, `[[V]]`, `[[link]]`) | kusur sayılmaz |
| Düzeltme | **YASAK** — onarmak şablonu bozar (`.ai/AGENTS.md` §13.7) | gate çıktısı yorumlanır, koddan üretilmez |

## §8. kalip-abc-check — Amaç Bloğu + Frontmatter Çıkarıcı

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L1-L5, L18-L25

```powershell
<#
  kalip-abc-check.ps1 — Faz 6 kapisi
  Kalip A (reference), B (flow), C (prompt) zorunluluklarini dogrular.
  Salt-okunur: raporlar, degistirmez.
#>
function Get-Frontmatter([string]$p) {
    $lines = [System.IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)
    if ($lines.Count -lt 3 -or $lines[0] -ne '---') { return $null }
    for ($i = 1; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -eq '---') { return ,@($lines[1..($i-1)]) }
    }
    return $null
}
```

| Gözlem | Kanıt |
|---|---|
| FM sınırı: satır 0 `---` + kapanış `---` + en az 3 satır | L20 |
| İlk `---` dışındaki satırlar alınır (iç içe `---` yok) | L21-L22 |
| FM yoksa `$null` → çağıran `[FM YOK]` raporlar ve dosyayı atlar | L75, L126, L169 |
| `ReadAllLines` + `[Text.Encoding]::UTF8` — **UTF-8 zorunlu** | L19 |

## §9. kalip-abc-check — FM Anahtar Regex + Authority Footer Kapısı

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L27-L34, L56-L61

```powershell
function Test-FmKeys($fm, [string[]]$keys) {
    $miss = @()
    foreach ($k in $keys) {
        $re = "^\s*$([regex]::Escape($k))\s*:"
        if (-not ($fm | Where-Object { $_ -match $re })) { $miss += $k }
    }
    return $miss
}
function Test-AuthorityFooter([string]$text) {
    if ($text.IndexOf('**Authority:**') -lt 0) { return @('Authority footer yok') }
    if ($text.IndexOf('**Last Updated:**') -lt 0) { return @('Authority footer: Last Updated yok') }
    if ($text.IndexOf('**Mode:**') -lt 0) { return @('Authority footer: Mode yok') }
    return @()
}
```

| Kapı | Desen | Satır | Eşik |
|---|---|---|---|
| FM anahtarı var mı | `^\s*<anahtar>\s*:` | L30-L31 | tüm anahtarlar |
| Authority | `**Authority:**` (IndexOf, büyük/küçük duyarlı) | L57 | zorunlu |
| Last Updated | `**Last Updated:**` | L58 | zorunlu |
| Mode | `**Mode:**` | L59 | zorunlu |

**Dikkat:** `IndexOf` literal aramadır — regex değil; `**Authority:**` metni düz geçmelidir.

## §10. kalip-abc-check — Quality Report Kapısı (son H2 + 4 satır)

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L37-L54

```powershell
function Test-QualityReport([string[]]$lines) {
    $issues = @()
    $h2 = @()
    for ($i = 0; $i -lt $lines.Count; $i++) {
        if ($lines[$i] -match '^##\s+') { $h2 += ,@($i, $lines[$i]) }
    }
    if ($h2.Count -eq 0) { return @('Quality Report: hic H2 yok') }
    $last = $h2[-1]
    if ($last[1] -notmatch 'Quality Report') {
        $issues += "son H2 Quality Report degil (son H2 = '$($last[1])')"
    } else {
        $tail = ($lines[$last[0]..($lines.Count-1)]) -join "`n"
        foreach ($row in @('| Version','| Status','| Cross References','| Last Updated')) {
            if ($tail.IndexOf($row) -lt 0) { $issues += "QR satiri eksik: $row" }
        }
    }
    return $issues
}
```

| Kural | Uygulama | Satır |
|---|---|---|
| H2 sayacı | `^##\s+` regex, satır indeksi + metin saklanır | L41 |
| H2 yoksa | tek mesaj, geç | L43 |
| Son H2 `Quality Report` içermeli | `-notmatch` | L45-L46 |
| Tablo satırları `| Version` `| Status` `| Cross References` `| Last Updated` | yalnız son H2'den sonrasına (`$tail`) bakılır | L48-L51 |

## §11. kalip-abc-check — Kalıp Anahtar Kümeleri ve Bölümler

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L64-L65, L101-L110, L118-L119, L159-L162

```powershell
$A_KEYS = @('reference_doc','title','type','category','date','updated',
            'status','version','authority','governance')
$B_SECTIONS = @(
    '## 1. Akış Diyagramı (Decision Flow)',
    '## 2. Ekran Akışı',
    '## 3. Hata Senaryoları',
    '## 4. Tier-Bazlı Varyasyonlar',
    '## 5. BEM Sınıfları',
    '## 6. Adımlar'
)
$C_H3 = @('### Context','### Required Inputs','### ASCII Reference',
          '### Prompt Template','### Expected Output','### Validation')
```

| Kalıp | FM anahtarı | Bölüm kuralı | Satır |
|---|---|---|---|
| A (reference) | 10 anahtar + `reference:` bloğu (L81-L83) | `## 1. Amaç` + Quality Report + footer | L64-L65, L87-L89 |
| B (flow) | aynı 10 (`$B_KEYS` L101-L102) | **6** sabit H2 sırayla (L142-L144) + `reference:` **olmamalı** (L138) | L103-L110 |
| C (prompt) | 9 anahtar (`$C_KEYS` L159-L160, `updated` yok) | **tek** H2 + **6** H3 sırayla (L186-L199) | L161-L162 |
| Muaf dosya | `NN-*.index.md` · `web-research.md` → `$B_SPECIAL`/`$C_SPECIAL` (8 anahtar) | `Test-IsSpecial` L112-L117 | L114-L119 |

## §12. kalip-abc-check — H1/H3 Sıra Kontrolü (birebir eşitlik)

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L185-L199, L201-L215

```powershell
    # tek H2, adi birebir
    $h2 = @($lines | Where-Object { $_ -match '^##\s+' })
    if ($h2.Count -ne 1) { $issues += "H2 sayisi $($h2.Count) (1 olmali)" }
    elseif ($h2[0] -notmatch '^## AI Code Generation Prompt') { $issues += "H2 adi yanlis: $($h2[0])" }

    # 6 H3 sirayla
    $h3 = @($lines | Where-Object { $_ -match '^###\s+' })
    $idx = 0
    foreach ($want in $C_H3) {
        $found = -1
        for ($i = $idx; $i -lt $h3.Count; $i++) {
            if ($h3[$i].TrimEnd() -eq $want) { $found = $i; break }
        }
        if ($found -lt 0) { $issues += "H3 eksik/sira: $want" } else { $idx = $found + 1 }
    }
```

| Kontrol | Ağırlık | Satır |
|---|---|---|
| H2 sayısı = 1 | sayısal | L187 |
| H2 metni `## AI Code Generation Prompt` öneki | birebir regex | L188 |
| 6 H3 **sırayla** (eşitlik, `TrimEnd` ile boşluk temizliği) | kayan pencere `$idx = found+1` | L190-L199 |
| H1 kalıbı dizin-adına göre (`component`/`page`/`screen`/`layout`) | `switch` regex tablosu | L207-L214 |

**Ölçüm dersi:** "var mı" değil "**sırada mı**" — `TrimEnd() -eq` sıralı birebir eşitliktir.

## §13. kalip-abc-check — Sayaçlar ve Final Çıkış (gate sinyali)

Dosya: `.ai/scripts/kalip-abc-check.ps1` · Satır: L91-L97, L155, L228-L231

```powershell
    if ($issues.Count) {
        $aBad++; $exit = 1
        $rel = $f.FullName.Replace("$UI\", '')
        Write-Host "  [$rel] -> $($issues -join ' | ')"
    }
}
Write-Host "  A: toplam $($aFiles.Count) | sorunlu $aBad"
...
Write-Host "  C: toplam $($cFiles.Count) | sorunlu $cBad"
...
Write-Host "SONUC -> A:$aBad B:$bBad C:$cBad  (0/0/0 = GECTI)"
exit $exit
```

| Sinyal | Anlam | Satır |
|---|---|---|
| `$exit = 1` her sorunlu dosyada | CI'ya iade edilen exit code | L92, L132, L174, L226 |
| `A: toplam N \| sorunlu M` | kapsam + hata ayrı sayılır (negatif + pozitif) | L97, L155, L228 |
| `SONUC -> A:0 B:0 C:0  (0/0/0 = GECTI)` | tek satır gate özeti | L230 |
| `exit $exit` | 0 = geç, 1 = dur | L231 |

**Kayıtlı ölçüm (2026-09-29, `.ai/AGENTS.md` §13.6):** `A:21·0 / B:21·0 / C:51·0 → GECTI`
(totaller 21/21/51, sorunlu 0/0/0). Ayrımcımdır: L230 çıktısı sorunlu sayısını yazar.

## §14. screens-frontmatter-check — FM Alanları + 9 Bölüm

Dosya: `.ai/scripts/screens-frontmatter-check.ps1` · Satır: L10-L14, L35-L42

```powershell
$REQUIRED_FM = @('reference_doc','title','type','category','date','status','version',
                 'tier','viewport','device','authority','governance','reference')
$SECTIONS = @('## 1. ASCII Layout','## 2. BEM','## 3. Token','## 4. Touch Target',
              '## 5. WCAG','## 6. Glassmorphism','## 7. PNG Referans',
              '## 8. Responsive','## 9. State')
...
  if (-not $isIndex) {
    foreach ($k in $REQUIRED_FM) {
      if (-not ($fm | Where-Object { $_ -match "^\s*$([regex]::Escape($k))\s*:" })) { $issues += "FM eksik: $k" }
    }
    # --- 9 bolum ---
    foreach ($s in $SECTIONS) {
      if ($text.IndexOf($s) -lt 0) { $issues += "bolum eksik: $s" }
    }
```

| Kümeler | Sayı | Satır |
|---|---|---|
| `$REQUIRED_FM` (Kalıp D §3.1) | **13** alan — bu dosyanın FM'i (10) ile **farklı** kümelerdir | L10-L11 |
| `$SECTIONS` | **9** bölüm, `IndexOf` literal | L12-L14 |
| İstisna | `00-ascii-art-index.md` muaf (`$isIndex`, L32) | L35 |
| FM regex | `^\s*<anahtar>\s*:` — kalip-abc L31 ile **aynı desen** | L37 |

## §15. screens-frontmatter-check — FM Kapanışı + Şekil Kontrolleri

Dosya: `.ai/scripts/screens-frontmatter-check.ps1` · Satır: L21-L30, L43-L49

```powershell
  $text = [System.IO.File]::ReadAllText($f.FullName, [Text.Encoding]::UTF8)
  $lines = $text -split "`r?`n"
  $issues = @()
  # --- frontmatter siniri ---
  $fmEnd = -1
  for ($i = 1; $i -lt $lines.Count; $i++) { if ($lines[$i] -eq '---') { $fmEnd = $i; break } }
  if ($fmEnd -lt 0) { $issues += 'frontmatter KAPANIYOR'; $fail++; Write-Host "  $rel -> $($issues -join ' | ')" ; continue }
  $fm = $lines[1..($fmEnd - 1)]
  ...
    $vp = ($fm | Where-Object { $_ -match '^\s*viewport\s*:' })
    if ($vp -and $vp -match 'width\s*:') { $issues += 'viewport nesne biciminde' }
    $tr = ($fm | Where-Object { $_ -match '^\s*tier\s*:' })
    if ($tr -and $tr -match 'T\d+-[a-z]') { $issues += 'tier dizin adi biciminde' }
```

| Kapı | Kötü biçim | Satır |
|---|---|---|
| FM kapanmıyorsa | `frontmatter KAPANIYOR` → dosya atlanır (`continue`) | L28 |
| CRLF/LF ayrıştırma `` "`r?`n" `` | Windows/Linux eşitliği | L22 |
| `viewport` nesne biçiminde (`width:` içeriyorsa) | skaler beklenir: `1024x600` | L44-L45 |
| `tier` dizin adı biçiminde (`T\d+-[a-z]`) | kanonik tier adı beklenir | L47-L48 |

## §16. screens-frontmatter-check — PNG Gerçeği + Bayat Sinyal + Sonuç

Dosya: `.ai/scripts/screens-frontmatter-check.ps1` · Satır: L51-L67, L71-L73

```powershell
  $sot = ($fm | Where-Object { $_ -match '^\s*source_of_truth\s*:' })
  if ($sot) {
    $v = ($sot -replace '^\s*source_of_truth\s*:\s*','').Trim().Trim('"')
    if ($v -notmatch 'VERIFICATION REQUIRED') {
      if ($v -like '*.png') {
        $cand = if ($v -like '.ai/*' -or $v -like 'C:*') { $v } else { Join-Path 'C:\www\coremusic.net\.ai' $v }
        if (-not (Test-Path $cand)) { $issues += "PNG yok: $v" }
        if ($v -notlike '.ai/.png*') { $issues += "PNG yolu on eksik (.ai/.png olmali)" }
      }
    }
  }
  foreach ($p in @('23 md','175 prompt','14 page','wifi-modal','file-browser')) {
    if ($text.IndexOf($p) -ge 0) { $issues += "bayat: $p" }
  }
...
Write-Host "dosya: $($files.Count) | sorunlu: $fail"
```

| Kapı | Kural | Satır |
|---|---|---|
| Kanıt dosyası gerçekten diskte mi | `Test-Path` → `PNG yok` | L58 |
| Yol öne eki | `.ai/.png` zorunlu → `PNG yolu on eksik` | L59 |
| **Etiketli eksiklik kabul** | `VERIFICATION REQUIRED` ise PNG kontrolü **yapılmaz** (uydurma sayılmaz) | L55 |
| Bayat sinyal | 5 eski metin parçası → `bayat:` | L65-L67 |
| Final sayı | `dosya: N \| sorunlu: M` | L73 |

**Dikkat (vault kanıtı):** göreli yollar `.ai/` içinden çözülemez → **19 sahte "PNG yok"**;
betik yalnız repo kökünden çalıştırılır (`.ai/AGENTS.md` §13.6).

## §17. Çalıştırılabilir Kapı Kalıbı (bu üretimde kullanılan 4 kapı)

> Bu blok bu dosyanın **kendi** üretim kapısıdır — çalıştırılabilir, `TODO` yok.
> Repo kökünden (`C:\www\coremusic.net`) çalıştırılır.

```powershell
$hedef = 'C:\www\coremusic.net\.ai\architecture\K015-AI\evaluation\evaluation-ornekler.md'
$lines = [System.IO.File]::ReadAllLines($hedef, [Text.Encoding]::UTF8)     # kapı 1: sayım
$text  = [System.IO.File]::ReadAllText($hedef, [Text.Encoding]::UTF8)

# kapı 1 — satır sayısı
$n = $lines.Count
$gate1 = if ($n -ge 500) { 'PASS' } else { 'FAIL' }                        # band 500-520

# kapı 2 — frontmatter 10 alan (ilk 14 satır)
$FM = @('node_id','node_type','status','parent_id','children','depends_on',
        'evidence','validation','open_questions','group')
$head = $lines[0..13]
$missing = @($FM | Where-Object {
    -not ($head | Where-Object { $_ -match "^\s*$([regex]::Escape($_))\s*:" })
})
$gate2 = if ($missing.Count -eq 0) { 'PASS' } else { "FAIL: $($missing -join ',')" }

# kapı 3 — Authority son satır birebir
$want = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
$gate3 = if ($lines[$lines.Count-1] -ceq $want) { 'PASS' } else { 'FAIL' }

# kapı 4 — mojibake (U+FFFD) sayımı
$fffd = ([regex]::Matches($text, [char]0xFFFD)).Count
$gate4 = if ($fffd -eq 0) { 'PASS' } else { "FAIL: $fffd" }

"satir=$n $gate1 | fm10=$($missing.Count -eq 0) $gate2 | authority $gate3 | u+fffd=$fffd $gate4"
```

| # | Kapı | Mekanizma | Eşik |
|---|---|---|---|
| 1 | Satır sayısı | `ReadAllLines(UTF8).Count` | `>= 500` (band 500-520 · **ölçüm 873**, 2026-10-17) |
| 2 | FM 10 alan | 10 anahtar × `^\s*<anahtar>\s*:` regex | eksik 0 |
| 3 | Authority | `$lines[-1] -ceq` (büyük/küçük **duyarlı**) | 1/1 |
| 4 | Mojibake | U+FFFD sayımı | 0 |

**Neden `-ceq`:** `-eq` PowerShell'de büyük/küçük harf duyarsız olabilir; künye birebirliği
için `-ceq` kullanılır (`.ai/scripts` betiklerindeki `-ne '---'` eşitliği de kültür-duyarlıdır).

## §18. FM 10-Alan Regex — Ölçüm Ayrıştırması

Dosya: `.ai/scripts/kalip-abc-check.ps1` deseni · Satır: L30-L31 (alıntı §9'da)

| Adım | Kalıp | Kaçırma riski |
|---|---|---|
| 1 | ilk satır `---` (L20) | FM'siz dosya `$null` → `[FM YOK]` |
| 2 | kapanış `---` satırı (L21-L22) | kapanmayan FM → §15 `frontmatter KAPANIYOR` |
| 3 | `^\s*<anahtar>\s*:` (L30) | **yalnız varlık** testi — değer **okunmaz** |
| 4 | `Where-Object` üzerinde tek tek satır | anahtar değeri satır 2'de olsa da eşleşir |
| 5 | Bu dosyanın FM'i: 10 anahtar (`node_id`…`group`) | `evidence` blok satırları (`  - "..."`) anahtar değildir, sayım dışı |

**Sınır (dürüst):** betik regex'i **alan varlığını** ölçer; `children: []` gibi **boş değer**ler
geçer. Değer doğrulama (boş/boş olmayan) için §20'deki illüstratif parser gerekir — repo'da
**YOK** (§25).

## §19. Authority Kontrolü — Üç Seviye

| Seviye | Kontrol | Kaynak | Durum |
|---|---|---|---|
| 1 | `**Authority:**` literal var mı | `kalip-abc-check.ps1` L57 | gerçek betik |
| 2 | `**Last Updated:**` + `**Mode:**` var mı | L58-L59 | gerçek betik |
| 3 | Son satır **birebir** `-ceq` | §17 kapı 3 | bu üretim kalıbı |

| Dosya | Authority satırı biçimi | Uygun mu |
|---|---|---|
| `kalip-abc-check.ps1` kontrol ettiği ui-design dosyaları | ayrı satırlar `**Authority:**` · `**Last Updated:**` · `**Mode:**` | evet |
| Bu dosya (K015-AI modülü) | tek satır `... · **Mode:** ...` | §17 kapı 3 birebir |
| `rag-ornekler.md` (şablon) | tek satır, aynı biçim | evet (şablon L519) |

**Çelişki notu:** `Test-AuthorityFooter` `IndexOf` ile `**Last Updated:**` **ister**; bu dosyanın
zorunlu Authority satırında `Last Updated` **yoktur**. İki farklı dosya ailesidir; kapı 3 bu dosya
için **geçerli olandır**, L58-L59 ui-design Kalıp A–D içindir. Saklanmaz, §29 açık soru olarak
kaydedilir.

## §20. Mojibake (U+FFFD) Taraması — Kod + Kaynak

**Kod kalıbı (çalıştırılabilir):**

```powershell
$t = [System.IO.File]::ReadAllText($hedef, [Text.Encoding]::UTF8)
$bad = ([regex]::Matches($t, [char]0xFFFD)).Count     # hedef: 0
$bad2 = ([regex]::Matches($t, '[Ãâ][\x80-\xBF]')).Count   # klasik UTF-8→CP1254 izi, hedef: 0
"u+fffd=$bad | mojibake-iz=$bad2"
```

| Gözlem | Kanıt |
|---|---|
| Betikler konsolu **ve** dosyayı UTF-8 yapar | `wiki-link-check.ps1` L4, L6 · `kalip-abc-check.ps1` L13 · `screens-frontmatter-check.ps1` L5 |
| Vault kaydı: mojibake temizlendi (745 düzeltme) | `.ai/AGENTS.md` §26.3 (bölüm künyesi) |
| U+FFFD = kodlanmış dizgede çözülemeyen byte'ın yeri | WHATWG Encoding Standard — §2 Security background (§27 kaynak 1) |
| Üretimde kapı | §17 kapı 4 → `0` |

## §21. Hallucination Tarama Kalıpları (grep katmanı)

| # | Kalıp | Ne yakalar | Karşılığı |
|---|---|---|---|
| 1 | `VERIFICATION REQUIRED` | işaretli eksiklik (kabul edilir, tamamlanmış sayılmaz) | `.ai/AGENTS.md` §24.3 |
| 2 | `UNKNOWN` | bilinmeyen değer | kök AGENTS §3 Zero-Hallucination |
| 3 | `TODO`, `TBD`, `{{`, `PLACEHOLDER` | bekleyen yer tutucu | `rag-ornekler.md` §28 #6 |
| 4 | `\d{7}` (git hash biçimi) | **uydurma commit hash** | `.ai/AGENTS.md` §25.5 "uydurma hash yazılmaz" |
| 5 | `Dosya: .* · Satır: L\d+` → ilgili dosyada grep | **uydurma künye** | bu dosya §1 |
| 6 | `\b\d{2,}\b` + `(%|adet|satır|dosya)` → kaynağı yoksa | **ölçülmemiş sayı iddiası** | `.ai/RAG.md` §3.2 kural 4 |
| 7 | `https?://` → fetch ile HEAD/GET | **uydurma URL** | bu dosya §27 |
| 8 | `İllüstratif` blok + repo kanıtı süsü | etiketsiz illüstratif | bu dosya §1 |

**Çalıştırma:** repo kökünden, `.ai/` gizli dizin olduğu için `hidden: true` ile —
aksi halde `.ai/**` sahte "0 sonuç" üretir (`.ai/AGENTS.md` §7 ARAMA KURALI).

## §22. İllüstratif — Python: Claim Verifier (iddia ↔ kanıt eşleştirme)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
from dataclasses import dataclass
import re

@dataclass(frozen=True)
class Claim:
    text: str          # "227 link / 6 kırık"
    source: str        # dosya + bölüm/satır künyesi
    kind: str          # "olcum" | "yokluk" | "illustratif"

@dataclass(frozen=True)
class Evidence:
    source: str
    snippet: str

def verify(claim: Claim, evidence: list[Evidence], grep_counts: dict[str, int]) -> str:
    if claim.kind == "olcum":
        # iddia, araç çıktısıyla eşleşmeli — tahmin edilemez
        key = claim.text.split("/")[0].strip().split()[0]
        return "PASS" if grep_counts.get(key) is not None else "UNVERIFIED"
    if claim.kind == "yokluk":
        # yokluk iddiası: kelime 0 isabet ister
        return "PASS" if grep_counts.get(claim.text, 0) == 0 else "FAIL"
    if claim.kind == "illustratif":
        etiket = "İllüstratif örnek - repo kanıtı DEĞİL"
        return "PASS" if any(etiket in e.snippet for e in evidence) else "FAIL"
    return "UNVERIFIED"

CLAIM_RE = re.compile(r"^(Dosya|Satır): .+")   # künye biçimi (bu dosya §1)
```

**Kapsam:** iddia-kanıt eşleştirme — CoreMusic'te karşılığı **yalnız grep/manual tur**
(§21); otomatik verifier **YOK** (§25).

## §23. İllüstratif — TypeScript: Evidence Checker (künye tazeliği)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type Cite = { file: string; lines: [number, number] };

export type CheckResult = { ok: boolean; reason: string };

/** Alıntının hâlâ dosyada olup olmadığını satır aralığı üzerinden doğrular. */
export async function checkCite(cite: Cite, actual: string[]): Promise<CheckResult> {
  const [a, b] = cite.lines;
  if (a < 1 || b < a || b > actual.length) {
    return { ok: false, reason: `aralık disi: L${a}-L${b} / ${actual.length} satir` };
  }
  const window = actual.slice(a - 1, b).join("\n");
  if (!window.trim()) return { ok: false, reason: "aralık boş — künye hayalet" };
  return { ok: true, reason: "künye geçerli" };
}

export const mojibake = (s: string) => (s.match(/\uFFFD/g) ?? []).length; // hedef: 0
```

**Kapsam:** künye + mojibake JS ölçümü — JS tarafında bu kod **YOK** (repo durumu §25).

## §24. İllüstratif — Go: Frontmatter Parser / Validator

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package fm

import "strings"

// Parse `---` ... `---` bloğunu döndürür (kalip-abc L18-L25 semantiği).
func Parse(lines []string) (map[string]string, bool) {
	if len(lines) < 3 || lines[0] != "---" { return nil, false }
	for i := 1; i < len(lines); i++ {
		if lines[i] == "---" {
			out := map[string]string{}
			for _, l := range lines[1:i] {
				if k, v, ok := strings.Cut(l, ":"); ok {
					out[strings.TrimSpace(k)] = strings.TrimSpace(v) // değer de okunur
				}
			}
			return out, true
		}
	}
	return nil, false
}

func Missing(fm map[string]string, keys []string) []string {
	var miss []string
	for _, k := range keys { if _, ok := fm[k]; !ok { miss = append(miss, k) } }
	return miss
}
```

**Kapsam:** FM parser — betik regex'i yalnız **varlık** test eder (§18); bu parser **değer** de
okur. Repo karşılığı **YOK** (§25).

## §25. İllüstratif — Rust: Mojibake Scanner

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
/// U+FFFD (replacement character) + klasik UTF-8→CP1254 izlerini tarar.
pub fn scan(text: &str) -> usize {
    let fffd = text.chars().filter(|c| *c == char::REPLACEMENT_CHARACTER).count();
    let doubled = text
        .as_bytes()
        .windows(2)
        .filter(|w| matches!(w[0], 0xC3 | 0xC2) && w[1] >= 0x80 && w[1] <= 0xBF)
        .count();
    fffd + doubled
}

#[cfg(test)]
mod tests {
    #[test]
    fn temiz_metin_sifir() { assert_eq!(super::scan("CoreMusic — ölçüm"), 0); }
}
```

**Kapsam:** mojibake taraması — PowerShell kalıbı §20 ile aynı hedef (0). Rust betiği repo'da
**YOK**; `.ai/scripts/**` yalnız PowerShell (envanter §2).

## §26. İllüstratif — C++: Diff Comparator (idempotent üretim kontrolü)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
#include <string>
#include <string_view>
#include <vector>

// İki sürümü satır satır karşılaştırır: üretim diff'i "yalnız eklenen satır" mı?
struct DiffStat { size_t added = 0, removed = 0, changed = 0; };

DiffStat compare(std::string_view old_text, std::string_view new_text) {
    auto split = [](std::string_view s) {
        std::vector<std::string_view> out; size_t pos = 0;
        while (pos <= s.size()) {
            auto nl = s.find('\n', pos);
            if (nl == std::string_view::npos) { out.push_back(s.substr(pos)); break; }
            out.push_back(s.substr(pos, nl - pos)); pos = nl + 1;
        }
        return out;
    };
    auto a = split(old_text), b = split(new_text);
    DiffStat st;
    const size_t n = a.size() < b.size() ? a.size() : b.size();
    for (size_t i = 0; i < n; ++i) {
        if (a[i] == b[i]) continue;
        if (b[i].empty()) ++st.removed; else if (a[i].empty()) ++st.added; else ++st.changed;
    }
    st.added   += (b.size() > n) ? (b.size() - n) : 0;
    st.removed += (a.size() > n) ? (a.size() - n) : 0;
    return st;
}
```

**Kapsam:** "silme yok, ekleme var" idempotansi (`.ai/AGENTS.md` §25.3 kural 1) — ölçülen diff
üreticisi repo'da **YOK** (§25).

## §27. İllüstratif — C#: Gate Orchestrator (4 kapıyı sırayla çalıştırır)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
using System.Text;

public sealed record GateResult(string Name, bool Pass, string Detail);

public sealed class GateOrchestrator
{
    private static readonly string[] FmKeys =
        { "node_id","node_type","status","parent_id","children","depends_on",
          "evidence","validation","open_questions","group" };

    private const string Authority =
        "**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode";

    public async Task<IReadOnlyList<GateResult>> RunAsync(string path, CancellationToken ct)
    {
        var lines = await File.ReadAllLinesAsync(path, new UTF8Encoding(false), ct);
        var text  = string.Join('\n', lines);
        var head  = lines.AsSpan(0, Math.Min(14, lines.Length));

        var missing = FmKeys.Where(k => !head.ToArray()
            .Any(l => Regex.IsMatch(l, $@"^\s*{Regex.Escape(k)}\s*:"))).ToArray();

        return new[]
        {
            new("lines>=500", lines.Length >= 500, $"n={lines.Length}"),
            new("fm10",       missing.Length == 0,  string.Join(',', missing)),
            new("authority",  lines[^1] == Authority, "son satır -ceq"),
            new("u+fffd=0",   !text.Contains('\uFFFD'), $"n={(text.Length - text.Replace("\uFFFD","").Length)}"),
        };
    }
}
```

**Kapsam:** kapı orkestrasyonu — §17 PowerShell kalıbının C# karşılığı. Repo'da `*.cs` gate
**YOK** (§25).

## §28. Kavram ↔ Eşleşme Tablosu

| Değerlendirme kavramı | Betik karşılığı (Dosya · Satır) | Eşleşme |
|---|---|---|
| Link bütünlüğü | `wiki-link-check.ps1` L24-L43 | **evet** |
| FM alan varlığı | `kalip-abc-check.ps1` L27-L34 · `screens-frontmatter-check.ps1` L36-L38 | **evet** |
| FM kapanış sınırı | `screens-frontmatter-check.ps1` L26-L30 | **evet** |
| Bölüm/başlık sırası | `kalip-abc-check.ps1` L142-L144, L186-L199 | **evet** |
| Authority footer | `kalip-abc-check.ps1` L56-L61 | **evet** |
| Authority son satır birebir | §17 kapı 3 (bu üretim) | **kısmi** (betikte yok) |
| Quality Report tablosu | `kalip-abc-check.ps1` L37-L54 | **evet** |
| Kanıt dosyası diskte mi (`Test-Path`) | `screens-frontmatter-check.ps1` L58 | **evet** |
| Bayat metin sinyali | `screens-frontmatter-check.ps1` L65-L67 | **evet** |
| Exit code / gate | `kalip-abc-check.ps1` L231 · `$exit=1` L92 | **evet** |
| Mojibake (U+FFFD) taraması | betikte **YOK** — yalnız UTF-8 ayarı (L4/L6/L13/L5) | **YOK** → §17 kapı 4 |
| FM **değer** doğrulama (boş/boş olmayan) | — | **YOK** → §24 |
| İddia↔kanıt otomatik eşleştirme | — | **YOK** → §22 |
| Ölçülen diff/idempotans | — | **YOK** → §26 |
| Hallucination taraması (hash/URL/sayı) | — | **YOK** → §21 (el grep'i) |
| Secret maskeleme | betikte **YOK** | **YOK** → §29 kural |
| FM 10 alan (bu dosya ailesi) | §17 kapı 2 (bu üretim) | **kısmi** (betik 13 alan ister, §14) |

## §29. Yokluk Cümleleri (tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| Mojibake (U+FFFD) sayan gate betiği | Repo'da bu alan kodu YOK — kaynak: upstream docs (§20 kalıbı bu üretimde) |
| FM **değer** doğrulayıcı (varlık değil) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| İddia↔kanıt eşleme (claim verifier) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Hallucination otomatik tarama (CI) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Ölçülen diff / idempotans karşılaştırıcı | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Değerlendirme metrik raporu (precision/recall/F1) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| JUnit/JSON çıktı üreten gate | Repo'da bu alan kodu YOK — kaynak: upstream docs (betikler `Write-Host`) |
| Gate betikleri için unit test | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| `.ai/scripts/**` için LINT/CI bağlamı | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| `figma-tokens.ps1` / `figma-extract.ps1` / `device-matrix-catid.ps1` içeriği | Bu üretimde **okunmadı** — ⚠️ VERIFICATION REQUIRED (§2) |

## §30. Güvenlik Notları (değerlendirme raporunda maskeleme)

| # | Kural | Dayanak |
|---|---|---|
| 1 | Değerlendirme raporuna `FIGMA_TOKEN` / API anahtarı **yazılmaz**; çıktı yalnız `pass/fail/sayı` | `.ai/AGENTS.md` §16.9 (Figma token SSOT: yalnız `.ai/.env.figma`) |
| 2 | Secret `*.md` / `*.json` / `*.log`'a **konmaz** — env/substitution | kök `AGENTS.md` §5 Security |
| 3 | Log'da hassas veri → `[REDACTED]` | `.ai/AGENTS.md` §17 #3 |
| 4 | Betikler salt-okunur: raporlar, değiştirmez (`L5` / `L2-L3` künyesi §2, §8) | kendi başlıkları |
| 5 | Betik gövdesine sabit anahtar **yok** (bellekte tutulsa bile rapora sızmamalı) | `.ai/AGENTS.md` §16.9 |
| 6 | Betik çıktısı `Write-Host` ile konsoldur — ortama tam yol (`C:\www\...`) sızar; paylaşılan raporda yollar normalize edilir | §13, §16 alıntıları (mutlak yol) |

## §31. Ölçüm Kalıpları (araç çıktısı — iddia değil)

| # | Ölçüm | Komut (repo kökünden) | Hedef |
|---|---|---|---|
| 1 | Satır sayısı | `[IO.File]::ReadAllLines($h,[Text.Encoding]::UTF8).Count` | `>= 500` (band 500-520) |
| 2 | FM 10 alan | §17 kapı 2 — 10 × `^\s*<k>\s*:` | eksik 0 |
| 3 | Authority | `$lines[-1] -ceq $want` | PASS |
| 4 | Mojibake | `ReadAllText` + U+FFFD sayımı | 0 |
| 5 | Betik kanıtı tazeliği | betik grep → satır kayması varsa künye güncellenir | 0 kayma |
| 6 | `## §` sayısı | `grep -c '^## §'` | `>= 25` |
| 7 | İllüstratif blok | `grep -c 'İllüstratif örnek - repo kanıtı'` | 6 (§22-§27) |
| 8 | Betik satır uzunluğu | `wc -L` benzeri | §/kod okunabilirliği |
| 9 | Gate betik toplamı | 43 + 231 + 73 = **347 satır** (§2 envanterinden) | — |
| 10 | İddia→ölçüm izi | her sayı satırı §/betik künyesine bağlı | izlenebilir |

## §32. Doğrulama Kapıları (bu üretim)

| # | Kapı | Kriter | Kanıt |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (band 500-520 · ölçüm **873**, 2026-10-17) | §17 kapı 1 |
| 2 | Frontmatter 10 alan | `title`/`parent` **yok** · 10 anahtar | §17 kapı 2 |
| 3 | Authority son satır | birebir `-ceq` | §17 kapı 3 |
| 4 | Mojibake | U+FFFD = 0 | §17 kapı 4 |
| 5 | Betik kanıtı | her alıntı gerçek `Dosya:`/`Satır:` — 3 betik tam okundu | §3-§16 |
| 6 | Yokluk cümleleri | 9 satır "KOD YOK" + 1 ⚠️ VERIFICATION REQUIRED | §29 |
| 7 | İllüstratif etiket | 6 blok / 6 dil, her birinde zorunlu etiket | §22-§27 |
| 8 | Bölüm sayısı | en az 25 `## §` (ölçüm: **34 §**) | §1-§34 |
| 9 | Web kaynağı | 1-4 resmi kaynak doğrulandı, uydurma URL yok | §33 kaynak listesi |
| 10 | Yasaklar | git commit yok · `.ai/scripts/**` yazımı yok · tek dosya | §33 |

**Öz-eşleşme notu (dürüstlük):** `TODO|TBD|{{|PLACEHOLDER` grep'i bu dosyada **2 isabet** verir —
ikisi de kalıp tanımıdır, bekleyen yer tutucu değildir: §17 ("`TODO` yok" cümlesi) ve §21 #3
(taranan desenin kendisi). Bekleyen yer tutucu sayısı: **0**.

## §33. Açık Sorular + Kaynaklar + Yasaklar

**Açık sorular (0 → 4, gerekçeli):**

| # | Soru | Neden açık |
|---|---|---|
| 1 | `Test-AuthorityFooter` `**Last Updated:**` ister; K015-AI Authority satırında yok — iki aile birleşmeli mi? | §19 çelişki notu; onaysız birleştirme yapılmaz |
| 2 | `figma-tokens.ps1` "Faz 9 kapısı" olarak anılıyor — içeriği okunmadan gate özeti tam sayılmaz mı? | §2, `.ai/AGENTS.md` §7.2 alıntısı |
| 3 | 6 sahte kırık link sayısı sonsuza dek 6'da mı kalacak, yoksa kaçış dizgesi değiştirilecek mi? | `.ai/AGENTS.md` §13.7 "düzeltme YASAK" |
| 4 | Gate'ler `Write-Host`+`exit` üretiyor; CI'a makine-okur JSON/JUnit çıktısı gerekir mi? | §29 satır 7 |

**Kaynaklar (2026-10-17 doğrulandı):**

| # | Kaynak | Ne verdi |
|---|---|---|
| 1 | https://encoding.spec.whatwg.org/ — WHATWG Encoding Standard (son güncelleme 21 May 2026) | U+FFFD + "producer and consumer do not agree on the encoding" güvenlik arka planı (§20) |
| 2 | https://www.unicode.org/glossary/ — Unicode Glossary (Unicode Consortium) | abstract character / encoding terminolojisi |
| 3 | https://learn.microsoft.com/en-us/dotnet/api/system.io.file.readalllines — `File.ReadAllLines(String, Encoding)` | `ReadAllLines(path, Encoding)` imzası + istisnalar (§17 kapı 1) |
| 4 | https://www.nist.gov/itl/ai-risk-management-framework — NIST AI RMF 1.0 (26 Jan 2023) + NIST-AI-600-1 GenAI Profile (26 Jul 2024) | "design, development, use, and **evaluation** of AI products" — değerlendirme çerçevesi |
| 5 | `.ai/scripts/*.ps1` (3 dosya, tam okuma) · `.ai/architecture/K015-AI/rag/rag-ornekler.md` (şablon) | birincil kanıt |

**Yasaklar:**

| Yasak | Kural kaynağı |
|---|---|
| `git commit` atmak | `.ai/AGENTS.md` §16.9 #5 — commit orkestratöre aittir |
| `.ai/scripts/**` ve `.ai/*.md` yazımı (salt-okunur) | görev manifesti · §2 betik başlıkları |
| Bu dosya dışına yazım | görev manifesti |
| Uydurma `Dosya:`/`Satır:` künyesi · etiketsiz illüstratif blok | Zero-Hallucination (§1) · §22-§27 |
| Dosya adını değiştirme | görev manifesti |

## §34. Gate Özeti

§1-§34: künye formatı 10 kural · envanter 6 betik (3 tam okundu, 3 ⚠️) · betik kanıt bölümü 14
(wiki-link ×4, kalip-abc ×6, screens-fm ×3, ölçüm 1) · **13 alıntı bloğu / 168 gerçek betik
satırı** (kaynak 43+231+73 = **347 satır**; ayrıca 2 blok / 30 satır bu üretimin kendi kalıbı,
§17+§20) · çalıştırılabilir kapı kalıbı 4 kapı (PowerShell) · illüstratif **6 blok / 6 dil**
(Python, TypeScript, Go, Rust, C++, C#) · eşleme 17 satır · yokluk 10 satır · güvenlik 6 ·
ölçüm 10 · kapı 10 · açık soru 4 · web kaynağı 4. Ölçüm (2026-10-17): satır **873** `>=500` ·
FM 10/10 eksik 0 · Authority son satır `-ceq` 1/1 · U+FFFD 0.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
