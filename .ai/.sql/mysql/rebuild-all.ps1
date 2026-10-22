# rebuild-all.ps1 — CoreMusic MySQL'i .ai/.sql/mysql dump'larından yeniden kurar
# Kullanım (repo kökünden):
#   powershell -ExecutionPolicy Bypass -File .ai\.sql\mysql\rebuild-all.ps1
#   powershell -ExecutionPolicy Bypass -File .ai\.sql\mysql\rebuild-all.ps1 -Target coremusic_logs
# DİKKAT: hedef DB'deki MEVCUT şema ve veri DÜŞER (dump DROP TABLE içerir).
#         Önce yedek alın: mysqldump --databases <db> > yedek.sql
# Doğrulama: her dosya yüklenir, tablo|view|sutun|index sayisi raporlanir.

param(
  [string]$Target = '',              # bos = 18 coremusic + media_catalog hepsi
  [string]$MysqlPath = 'C:\Program Files\MySQL\MySQL Server 9.7\bin\mysql.exe',
  [string]$Host_ = 'localhost',
  [string]$User = 'Ali',
  [string]$Pass = 'ali**',
  [string]$DumpDir = $PSScriptRoot
)

$ErrorActionPreference = 'Continue'
$order = @(
  'coremusic_patch',        # migration/patch metadata — once hazir
  'coremusic_system',
  'coremusic_auth',
  'coremusic_user',
  'coremusic_catalog',
  'coremusic_musics',
  'coremusic_albums',
  'coremusic_media',
  'coremusic_cms',
  'coremusic_api',
  'coremusic_download',
  'coremusic_logs',
  'coremusic_neva',
  'coremusic_playlist',
  'coremusic_social',
  'coremusic_studio',
  'coremusic_ai',
  'coremusic_wireless',
  'media_catalog'
)
if ($Target) { $order = @($Target) }

$mAuth = @('-h', $Host_, '-u', $User, ('-' + 'p' + $Pass), '--default-character-set=utf8mb4')
$report = @()

foreach ($db in $order) {
  $file = Join-Path $DumpDir "$db.sql"
  if (-not (Test-Path $file)) { $report += "$db | FAIL | dosya yok: $file"; continue }

  $out = cmd /c "`"$MysqlPath`" -h $Host_ -u $User -p$Pass --default-character-set=utf8mb4 < `"$file`" 2>&1"
  if ($LASTEXITCODE -ne 0) {
    $report += "$db | FAIL | import: $(($out | Select-Object -First 2) -join ' ')"
    continue
  }

  $cq = "SELECT CONCAT((SELECT COUNT(*) FROM information_schema.TABLES t WHERE t.TABLE_SCHEMA='$db' AND t.TABLE_TYPE='BASE TABLE'), '|', (SELECT COUNT(*) FROM information_schema.VIEWS v WHERE v.TABLE_SCHEMA='$db'), '|', (SELECT COUNT(*) FROM information_schema.COLUMNS c WHERE c.TABLE_SCHEMA='$db'), '|', (SELECT COUNT(*) FROM information_schema.STATISTICS s WHERE s.TABLE_SCHEMA='$db'))"
  $cnt = (& $MysqlPath @mAuth -N -e $cq 2>$null | Select-Object -First 1)
  $report += "$db | OK | $cnt (tablo|view|sutun|index)"
}

Write-Output "===== REBUILD RAPOR ====="
$report | ForEach-Object { Write-Output $_ }
$ok = ($report | Where-Object { $_ -match '\| OK \|' }).Count
Write-Output "SONUC: $ok / $($order.Count) DB kuruldu"
