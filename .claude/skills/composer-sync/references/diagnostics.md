# Teşhis Komutları — Kanıtlı Çıktılarla

> Tüm komutlar `C:\www\coremusic.net` kökünden çalıştırılır (PowerShell).
> Çıktılar 2026-10-21 disk keşfinden alınmıştır; PHP/Composer çalıştıran
> adımlar `NOT TESTED` işaretlidir (binary PATH'te yok).

---

## D1 — Subdomain envanteri

```powershell
Get-ChildItem C:\www\coremusic.net -Directory |
  Where-Object { Test-Path (Join-Path $_.FullName 'composer.json') } |
  Select-Object -ExpandProperty Name
```

**Gerçek çıktı (2026-10-21):**

```
admin.coremusic.net
api.coremusic.net
auth.coremusic.net
home.coremusic.net
media.coremusic.net
```

`assets.coremusic.net` composer.json İÇERMEZ → "8 subdomain" iddiası eski
SKILL.md'de YANLIŞTI; gerçek sayı 5'tir.

---

## D2 — Junction doğrulama (§3.2)

```powershell
$item = Get-Item C:\www\coremusic.net\auth.coremusic.net\vendor\coremusic\shared-infrastructure -Force
$item.LinkType; $item.Target
```

**Gerçek çıktı:**

```
Junction
C:\www\coremusic.net\shared
```

---

## D3 — shared paket kimliği

```powershell
Get-Content C:\www\coremusic.net\shared\composer.json | Select-String -Pattern '"name"|"version"|"php"'
```

**Gerçek çıktı:**

```
"name": "coremusic/shared-infrastructure",
"version": "2.0.0",
"php": ">=8.4"
```

---

## D4 — PSR-4 sınıf → dosya eşlemesi

```powershell
# Örnek: CoreMusic\Logger aranıyor
Test-Path C:\www\coremusic.net\shared\src\Logger.php
```

**Beklenen:** `True` (dosya yoksa sınıf yazılmamıştır — dump çözüm değildir).

---

## D5 — Autoload sınıf eşlemesi kontrolü

```powershell
Select-String -Path C:\www\coremusic.net\auth.coremusic.net\vendor\composer\autoload_classmap.php -Pattern 'Logger'
```

**Beklenen (dump sonrası):** `... => ...coremusic.net/shared/src/Logger.php`
**Eğer boşsa:** `composer dump-autoload --optimize` (NOT TESTED — php yok).

---

## D6 — lock varlık kontrolü

```powershell
Get-ChildItem C:\www\coremusic.net\shared\composer.lock,
              C:\www\coremusic.net\auth.coremusic.net\composer.lock -ErrorAction SilentlyContinue |
  Select-Object FullName, Length, LastWriteTime
```

---

## D7 — php/composer erişilebilirlik (NOT TESTED gate)

```powershell
Get-Command php, composer -ErrorAction SilentlyContinue | Select-Object Name, Source
```

**Gerçek çıktı (2026-10-21):** (boş) → tüm composer/php adımları bu oturumda
`NOT TESTED` işaretlidir; SKILL.md §6.5 kuralı işletilmiştir.

---

*CoreMusic Skill v3.0 — composer-sync references — 2026-10-21*
