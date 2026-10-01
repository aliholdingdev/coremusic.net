Add-Type -AssemblyName System.Drawing
$roots = @(
  'C:\www\coremusic.net\assets.coremusic.net\Image',
  'C:\www\coremusic.net\.ai\ui-design\reference\figma\png'
)
foreach ($r in $roots) {
  if (-not (Test-Path $r)) { continue }
  Get-ChildItem $r -Recurse -Include *.png, *.jpg, *.webp -File | ForEach-Object {
    try {
      $i = [System.Drawing.Image]::FromFile($_.FullName)
      $a = [math]::Round($i.Width / [double]$i.Height, 2)
      if ($_.Name -match 'banner|girl|bg|welcome|home' -or ($a -gt 2.4 -and $a -lt 3.0 -and $i.Width -gt 400)) {
        Write-Host ("{0}`t{1}x{2}`ta={3}`t{4}" -f $_.Name, $i.Width, $i.Height, $a, $_.FullName)
      }
      $i.Dispose()
    } catch { }
  }
}
