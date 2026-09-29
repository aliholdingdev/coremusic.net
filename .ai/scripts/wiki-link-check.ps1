#Requires -Version 5.1
<# wiki-link denetimi: .ai/ui-design/**/*.md icindeki [[hedef]] -> gercek dosya var mi? #>
$ErrorActionPreference='Stop'
[Console]::OutputEncoding=[Text.Encoding]::UTF8
$ROOT='C:\www\coremusic.net\.ai\ui-design'
$enc=[Text.Encoding]::UTF8

# index: dosya adi (uzantisiz, kucuk) -> gercek tam yol
$index=@{}
Get-ChildItem -Path 'C:\www\coremusic.net\.ai' -Recurse -Filter *.md -File | ForEach-Object {
  $base=$_.BaseName.ToLowerInvariant()
  $rel=$_.FullName.Substring(0,$_.FullName.Length-3)
  if(-not $index.ContainsKey($base)){ $index[$base]=$_.FullName }
  $index[$_.FullName.ToLowerInvariant()]=$_.FullName
  $index[$rel.ToLowerInvariant()]=$_.FullName
}

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
"kontrol edilen link: $checked"
"kirik link: "+$broken.Count
$broken | Select-Object -First 40 | ForEach-Object { "  "+$_ }
