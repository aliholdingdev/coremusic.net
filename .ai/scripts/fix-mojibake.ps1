$files = @(
    "AGENTS.md", "brain.md", "CLAUDE.md", "engine.md", "glossary.md", 
    "index.md", "keys.md", "log.md", "MEMORY.md", "PROJECTS.md", 
    "ROLE.md", "ULTRA-THINKING.md", "VISION.md", "WORKFLOW.md"
)

$replacements = @{
    "Ã§" = "ç"
    "Ã‡" = "Ç"
    "ÄŸ" = "ğ"
    "Ä°" = "İ"
    "Ä±" = "ı"
    "Ã¶" = "ö"
    "Ã–" = "Ö"
    "ÅŸ" = "ş"
    "Åž" = "Ş"
    "Ã¼" = "ü"
    "Ãœ" = "Ü"
}

foreach ($fileName in $files) {
    $path = "c:\www\coremusic.net\.ai\$fileName"
    if (Test-Path $path) {
        $content = Get-Content -Path $path -Raw
        $original = $content
        
        foreach ($key in $replacements.Keys) {
            $content = $content.Replace($key, $replacements[$key])
        }
        
        if ($content -cne $original) {
            Set-Content -Path $path -Value $content -Encoding UTF8
            Write-Host "Fixed encoding for $fileName"
        }
    }
}
