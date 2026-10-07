#Requires -Version 5.1
<#
  figma-tokens.ps1 — CoreMusic design token üretimi (Figma ham JSON -> tokens-*.json)
  Kural: Figma taze degeri SSOT; eski anahtarlar korunur (veri kaybi yok);
         farklar reference/figma/token-conflicts.md dosyasina loglanir.
#>
$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [Text.Encoding]::UTF8

$ROOT = 'C:\www\coremusic.net\.ai'
$FIG  = "$ROOT\ui-design\reference\figma"
$RAW  = "$FIG\raw"
$TOK  = "$ROOT\ui-design\tokens"
$CONFLICTS = @()

# --- SSOT: yalniz .ai/.env.figma (betik icinde sabit token/key YOK) ---
$ENVF = "$ROOT\.env.figma"
if (-not (Test-Path $ENVF)) { throw "ENV YOK: $ENVF" }
$ENVRAW = Get-Content -Raw $ENVF -Encoding UTF8
$KEY = ([regex]::Match($ENVRAW, 'FIGMA_FILE_KEY=(\S+)')).Groups[1].Value
if (-not $KEY) { throw 'FIGMA_FILE_KEY bos' }
$stamp = Get-Date -Format 'yyyy-MM-dd'
$script:CONFLICT_OVERFLOW = 0

# --- Faz 9: dogrulama kapasi (global durum sayaclari) ---
# Kural seti (R1-R6) ana dongu icinde uygulanir; token uretimi, Merge-Old, cakisma logu
# ve SSOT (.env.figma regex'i) AYNEN KALIR - yalnizca dogrulama + raporlama eklenir.
$SECTIONS     = @('colors','typography','shadows','radii','spacing','sizes')
$EXPECT_EMPTY = @('3840','tv')   # Figma'da bos sayfalar (olcum: raw 326:3386 / 16:106 -> 0 token)
$passCount = 0; $bosCount = 0; $failCount = 0
$bosBps = @(); $failBps = @()

New-Item -ItemType Directory -Force -Path $TOK | Out-Null

# ---------- kaynak haritasi: breakpoint -> ham dosyalar ----------
$SRC = @{
  '1024'   = @('page-1047-15802.json','page-2135-19832.json','nodes-user-12.json')
  '1920'   = @('page-462-5874.json','page-319-2789.json','page-608-11052.json','page-2161-12438.json','page-1988-14156.json','page-15-403.json','page-1801-12472.json','page-1801-12473.json','nodes-user-12.json')
  '3840'   = @('page-326-3386.json')
  'mobile' = @('page-2003-24752.json')
  'tv'     = @('page-16-106.json')
  'system' = @('page-18-2907.json')
  'plan'   = @('page-1991-12056.json')
}
# nodes-user-12.json icerisinden hangi breakpoint hangi node'lari alir
$NODEBP = @{
  '1024' = @('1639:10160','1646:17727','1639:9775','1639:9773','1639:9904','1639:9892','1639:9910','2831:10267')
  '1920' = @('2831:13747','2849:21489','2850:21494','2831:10267')
}

function Hex([double]$r,[double]$g,[double]$b) {
  '{0:X2}{1:X2}{2:X2}' -f [int][Math]::Round($r*255), [int][Math]::Round($g*255), [int][Math]::Round($b*255)
}
function Put($bag, $section, $key, $value, $nodeId) {
  # seen map'i lowercase normalize: PS 5.1 ConvertFrom-Json case-folding ile okur,
  # aksi halde "ABC / fill" ve "abc / fill" (farkli Figma node'lari) okunamaz dosya uretir.
  $seen = $bag['_seen'][$section]
  $lk = $key.ToLowerInvariant()
  if (-not $seen.Contains($lk)) {
    $seen[$lk] = $key
    $bag[$section][$key] = $value
    return
  }
  $existKey = $seen[$lk]
  $cur = $bag[$section][$existKey]
  $a = if ($cur -is [string]) { $cur } else { ($cur | ConvertTo-Json -Compress -Depth 4) }
  $b = if ($value -is [string]) { $value } else { ($value | ConvertTo-Json -Compress -Depth 4) }
  if ($a -ne $b) {
    $newKey = "$key [$nodeId]"
    $nlk = $newKey.ToLowerInvariant()
    if (-not $seen.Contains($nlk)) {
      $seen[$nlk] = $newKey
      $bag[$section][$newKey] = $value
      if ($script:CONFLICTS.Count -lt 400) {
        $script:CONFLICTS += ('| ' + $section + ' | `' + $existKey + '` | `' + $newKey + '` | mevcut: ' + $a + ' | gelen: ' + $b + ' |')
      } else { $script:CONFLICT_OVERFLOW++ }
    }
  }
}

function New-Bag {
  [ordered]@{
    colors     = [ordered]@{}
    typography = [ordered]@{}
    shadows    = [ordered]@{}
    radii      = [ordered]@{}
    spacing    = [ordered]@{}
    sizes      = [ordered]@{}
    _counts    = [ordered]@{ nodes=0; imageFills=0; autoLayout=0; layoutGrids=0 }
    _seen      = @{ colors=@{}; typography=@{}; shadows=@{}; radii=@{}; spacing=@{}; sizes=@{} }
  }
}

function Walk($bag, $node, $depth) {
  if ($null -eq $node) { return }
  $bag._counts.nodes++
  $id  = $node.id
  $nm  = if ($node.name) { [string]$node.name } else { $id }

  # ---- fills / strokes ----
  foreach ($kind in 'fills','strokes') {
    $arr = $node.$kind
    if ($arr) { foreach ($f in $arr) {
      if ($f.visible -eq $false) { continue }
      if ($f.type -eq 'SOLID' -and $f.color) {
        $hex = '#' + (Hex $f.color.r $f.color.g $f.color.b)
        if ($f.opacity -ne $null -and $f.opacity -lt 1) { $hex = $hex + ' @' + [Math]::Round($f.opacity,3) }
        $suffix = if ($kind -eq 'fills') { '/ fill' } else { '/ stroke' }
        Put $bag 'colors' "$nm $suffix" $hex $id
      } elseif ($f.type -like 'GRADIENT_*') {
        $i = 0
        foreach ($st in $f.gradientStops) {
          $hex = '#' + (Hex $st.color.r $st.color.g $st.color.b)
          if ($st.color.a -ne $null -and $st.color.a -lt 1) { $hex = $hex + ' @' + [Math]::Round($st.color.a,3) }
          Put $bag 'colors' "$nm / gradient stop $i" $hex $id
          $i++
        }
      } elseif ($f.type -eq 'IMAGE') { $bag._counts.imageFills++ }
    }}
  }

  # ---- text style ----
  $st = $node.style
  if ($st -and $st.fontFamily) {
    $key = "$($st.fontFamily) $($st.fontSize)px w$($st.fontWeight)"
    $val = [ordered]@{
      fontFamily=$st.fontFamily; fontSize=$st.fontSize; fontWeight=$st.fontWeight
      lineHeight=$st.lineHeight; lineHeightUnit=$st.lineHeightUnit
      letterSpacing=$st.letterSpacing; letterSpacingUnit=$(if($st.letterSpacingUnit){$st.letterSpacingUnit}else{'API-den-gelmedi'})
      textCase=$st.textCase; textDecoration=$st.textDecoration
    }
    Put $bag 'typography' $key $val $id
  }

  # ---- effects (shadow / blur) ----
  if ($node.effects) { foreach ($e in $node.effects) {
    if ($e.visible -eq $false) { continue }
    if ($e.type -eq 'DROP_SHADOW' -or $e.type -eq 'INNER_SHADOW') {
      $c = $e.color
      $val = [ordered]@{
        type=$e.type
        x=$e.offset.x; y=$e.offset.y; blur=$e.radius
        spread=$(if($e.spread -ne $null){$e.spread}else{$null})
        spreadNote=$(if($e.spread -eq $null){'spread: API-den-gelmedi'}else{''})
        color=('#' + (Hex $c.r $c.g $c.b) + $(if($c.a -lt 1){' @'+[Math]::Round($c.a,3)}else{''}))
      }
      Put $bag 'shadows' "$nm shadow" $val $id
    } elseif ($e.type -like '*BLUR') {
      Put $bag 'shadows' "$nm blur($($e.type))" "radius=$($e.radius)" $id
    }
  }}

  # ---- radius ----
  if ($node.cornerRadius -ne $null -and $node.cornerRadius -ne 0) {
    Put $bag 'radii' "$nm radius" $node.cornerRadius $id
  } elseif ($node.rectangleCornerRadii) {
    Put $bag 'radii' "$nm radius" ($node.rectangleCornerRadii -join ',') $id
  }

  # ---- auto-layout spacing ----
  if ($node.layoutMode -and $node.layoutMode -ne 'NONE') {
    $bag._counts.autoLayout++
    foreach ($v in @($node.itemSpacing,$node.paddingLeft,$node.paddingRight,$node.paddingTop,$node.paddingBottom)) {
      if ($v -ne $null -and $v -gt 0) { Put $bag 'spacing' "space-$v" "$v px" $id }
    }
  }

  # ---- size ----
  $bb = $node.absoluteBoundingBox
  if ($bb) { Put $bag 'sizes' $nm "$([Math]::Round($bb.width,2))x$([Math]::Round($bb.height,2))" $id }

  if ($node.layoutGrids -and $node.layoutGrids.Count -gt 0) { $bag._counts.layoutGrids += $node.layoutGrids.Count }

  if ($node.children) { foreach ($ch in $node.children) { Walk $bag $ch ($depth+1) } }
}

function Read-JsonFile($path) {
  $raw = [System.IO.File]::ReadAllText($path, [Text.Encoding]::UTF8)
  return $raw | ConvertFrom-Json
}
function Save-Json($path, $obj) {
  $enc = New-Object System.Text.UTF8Encoding($false)
  [System.IO.File]::WriteAllText($path, ($obj | ConvertTo-Json -Depth 8), $enc)
}
function Merge-Old($newBag, $oldPath) {
  if (-not (Test-Path $oldPath)) { return 0 }
  $old = Read-JsonFile $oldPath
  $kept = 0
  foreach ($sec in @('colors','typography','shadows','radii','spacing','sizes')) {
    $o = $old.$sec
    if (-not $o) { continue }
    foreach ($p in $o.PSObject.Properties) {
      $seen = $newBag['_seen'][$sec]
      $lk = $p.Name.ToLowerInvariant()
      if (-not $seen.Contains($lk)) {
        $seen[$lk] = $p.Name
        $newBag[$sec][$p.Name] = $p.Value
        $kept++
      }
    }
  }
  return $kept
}

# ================= ANA DONGU =================
$report = @()
foreach ($bp in $SRC.Keys) {
  $bag = New-Bag
  $filesUsed = @()
  $fails = @()   # bu breakpoint icin tetiklenen kurallar (bosssa ve ihlal yoksa -> BOS)
  foreach ($f in $SRC[$bp]) {
    $p = Join-Path $RAW $f
    if (-not (Test-Path $p)) { $fails += "R1 EKSIK KAYNAK: $f"; Write-Host "EKSIK KAYNAK: $f ($bp)" -ForegroundColor Yellow; continue }
    try { $j = Read-JsonFile $p }
    catch { $fails += "R1 JSON OKUMA HATASI: $f -> $($_.Exception.Message)"; Write-Host "JSON OKUMA HATASI: $f ($bp)" -ForegroundColor Yellow; continue }
    if (-not $j.nodes) { continue }
    foreach ($k in $j.nodes.PSObject.Properties.Name) {
      # nodes-user-12.json sadece ilgili breakpoint node'larini verir
      if ($f -eq 'nodes-user-12.json') {
        if ($bp -eq '1024' -and $NODEBP['1024'] -notcontains $k) { continue }
        if ($bp -eq '1920' -and $NODEBP['1920'] -notcontains $k) { continue }
        if ($bp -notin @('1024','1920')) { continue }
      }
      Walk $bag $j.nodes.$k.document 0
    }
    $filesUsed += $f
  }

  $oldPath = Join-Path $TOK "tokens-$bp.json"
  $kept = 0
  try { $kept = Merge-Old $bag $oldPath } catch { $fails += "R1 ESKI TOKEN OKUMA HATASI: $($_.Exception.Message)" }

  $meta = [ordered]@{
    generated = $stamp
    breakpoint = $bp
    source = "Figma $KEY (API, taze cekim)"
    files = $filesUsed
    merge_rule = "Figma taze degeri SSOT; eski eksik anahtarlar korundu; farklar token-conflicts.md"
    counts = $bag._counts
    sections = [ordered]@{
      colors=$bag.colors.Count; typography=$bag.typography.Count; shadows=$bag.shadows.Count
      radii=$bag.radii.Count; spacing=$bag.spacing.Count; sizes=$bag.sizes.Count
    }
    legacy_keys_kept = $kept
  }
  $out = [ordered]@{ _meta = $meta }
  foreach ($sec in @('colors','typography','shadows','radii','spacing','sizes')) { $out[$sec] = $bag[$sec] }

  # ---- R2: token yazildi mi? geri okunup parse edilebildi mi? _meta / _meta.counts var mi? ----
  try { Save-Json $oldPath $out } catch { $fails += "R2 token yazma hatasi: $($_.Exception.Message)" }

  $rj = $null
  if (-not (Test-Path $oldPath)) { $fails += 'R2 token dosyasi diskte yok' }
  else {
    try { $rj = Read-JsonFile $oldPath } catch { $fails += "R2 geri okuma/parse hatasi: $($_.Exception.Message)" }
    if ($null -eq $rj) { $fails += 'R2 geri okunamadi' }
  }
  if ($null -ne $rj) {
    if (-not $rj.PSObject.Properties['_meta']) { $fails += 'R2 _meta yok' }
    else {
      if (-not $rj._meta.PSObject.Properties['counts']) { $fails += 'R2 _meta.counts yok' }
      # ---- R3: _meta.sections <> diskteki gercek bolum sayimi ----
      foreach ($sec in $SECTIONS) {
        $decl = $null
        if ($rj._meta.PSObject.Properties['sections'] -and $rj._meta.sections.PSObject.Properties[$sec]) { $decl = $rj._meta.sections.$sec }
        $disk = 0
        if ($rj.PSObject.Properties[$sec] -and $null -ne $rj.$sec) { $disk = @($rj.$sec.PSObject.Properties).Count }
        if ($null -eq $decl) { $fails += "R3 _meta.sections.$sec yok" }
        elseif ([int]$decl -ne [int]$disk) { $fails += "R3 $sec sayim uyusmazligi: meta=$decl disk=$disk" }
      }
    }
    # ---- R4: colors degeri 6 haneli hex (Hex() AABBCC uretir, disa '#'+deger yazilir; '@opaklik' eki serbest) ----
    if ($rj.PSObject.Properties['colors'] -and $null -ne $rj.colors) {
      foreach ($p2 in $rj.colors.PSObject.Properties) {
        $v = [string]$p2.Value
        if ($v -notmatch '^#[0-9A-Fa-f]{6}( @[0-9.]+)?$') { $fails += "R4 gecersiz hex: $($p2.Name) = [$v]"; break }
      }
    }
  }

  # ---- R5: bos siniflandirmasi (olcum: sayfa kok node sayildigi icin 3840/tv nodes=1, bolum toplami 0) ----
  $nodeCount = [int]$bag._counts.nodes
  $secSum = 0
  foreach ($sec in $SECTIONS) { $secSum += [int]$bag.$sec.Count }
  $emptyDesign = ($nodeCount -eq 0 -or $secSum -eq 0)
  if ($emptyDesign -and ($EXPECT_EMPTY -notcontains $bp)) {
    $fails += "R5 bos uretim: nodes=$nodeCount bolum_toplami=$secSum (Figma'da tasarim var)"
  }

  # ---- R6: hicbir kural tetiklenmediyse PASS ----
  $status = if ($fails.Count -gt 0) { 'FAIL' } elseif ($emptyDesign) { 'BOS' } else { 'PASS' }
  if ($status -eq 'FAIL') { $failCount++; $failBps += $bp }
  elseif ($status -eq 'BOS') { $bosCount++; $bosBps += $bp }
  else { $passCount++ }

  $line = "$bp -> tokens-$bp.json | node=$($bag._counts.nodes) | c=$($bag.colors.Count) t=$($bag.typography.Count) s=$($bag.shadows.Count) r=$($bag.radii.Count) sp=$($bag.spacing.Count) sz=$($bag.sizes.Count) | korunan eski=$kept" + $(if($emptyDesign){' | BOS: tasarim yok'}else{''}) + " | DURUM: $status" + $(if($fails.Count -gt 0){' | HATA: ' + ($fails -join ' ; ')}else{''})
  $report += $line
  Write-Host $line
}

# ---- conflict log ----
if ($script:CONFLICTS.Count -gt 0) {
  $md = "# Token Cakisma Logu ($stamp)`n`n> Figma taze degeri SSOT olarak alindi; asagidaki anahtarlarda eski ve yeni deger farkliydi (eski anahtar korundu, yeni node-id'li anahtar eklendi).`n`n| Section | Anahtar | Yeni Anahtar | Mevcut | Gelen |`n|---|---|---|---|---|`n" + ($script:CONFLICTS -join "`n") + "`n"
  $enc = New-Object System.Text.UTF8Encoding($false)
  [System.IO.File]::WriteAllText("$FIG\token-conflicts.md", $md, $enc)
  Write-Host "`nCakisma logu: $FIG\token-conflicts.md ($($script:CONFLICTS.Count) satir)"
}
# ---- Faz 9: ozet satiri (BITTI'den hemen once) + exit kodu ----
$bosList = if ($bosBps.Count -gt 0) { $bosBps -join ',' } else { '-' }
$sonuc = "SONUC -> toplam=$($SRC.Keys.Count) | pass=$passCount | bos=$bosCount | fail=$failCount | bos_bp=$bosList"
Write-Host $sonuc -ForegroundColor $(if ($failCount -gt 0) { 'Red' } else { 'Green' })
Write-Host "`nBITTI" -ForegroundColor Green
if ($failCount -gt 0) { exit 1 } else { exit 0 }
