# Windows Junction — Derinlemesine

> SKILL.md §3.1'in genişletilmiş hali. Bu dosya yalnız arka plan verir;
> karar tabloları SKILL.md'dedir.

---

## 1. Reparse point ailesi (Windows NTFS)

| Tür | Create flag | Yetki | Kullanım alanı |
|-----|-------------|-------|----------------|
| Directory Junction | `SYMBOLIC_LINK_FLAG_DIRECTORY` | admin gerekmez | Composer path-repo (CoreMusic) |
| Directory Symlink | `SYMBOLIC_LINK_FLAG_DIRECTORY \| SYMBOLIC_LINK_FLAG_ALLOW_UNPRIVILEGED_CREATE` | Developer Mode | genel amaçlı |
| File Symlink | `SYMBOLIC_LINK_FLAG_ALLOW_UNPRIVILEGED_CREATE` | Developer Mode | tek dosya |
| Hardlink | `CreateHardLinkW` | admin gerekmez | dosya düzeyi, inode paylaşır |

> Kaynak: Microsoft Learn — Symbolic Links (S3, erişim 2026-10-21).

---

## 2. Composer path-repo seçimi

`auth.coremusic.net/composer.json`:

```json
{ "type": "path", "url": "../shared", "options": { "symlink": true } }
```

Composer Windows'ta `symlink: true` ister; junction'un symlink'ten farkı:

1. Junction yalnız **mutlak** hedef kabul eder → `shared/` taşınırsa
   `LinkType=Junction` kalır ama `Target` yanlış gösterir; `realpath()`
   yanlış veya `false` döner.
2. Junction için yetki gerekmez → CI'da sorun çıkarmaz.
3. Junction `is_link()` ile yakalanmaz → JS/PHP dosya sistemi kontrollerinde
   `LinkType` özelliği şarttır.

---

## 3. Doğrulama ve onarım özet

```powershell
# Doğrula
$item = Get-Item "C:\www\coremusic.net\auth.coremusic.net\vendor\coremusic\shared-infrastructure" -Force
$item.LinkType   # Junction beklenir
$item.Target     # C:\www\coremusic.net\shared beklenir

# Onar (dikkat: junction silmeden önce LinkType teyidi — SKILL.md §6.4 UYARISI)
```

---

## 4. SSS

**S: Junction'ı silmek `shared/`'i siler mi?**
C: Junction girişi tek başına silinirse hedef DOKUNULMAZ; ancak
`Remove-Item -Recurse` bazı Windows sürümlerinde junction'ın İÇERİĞİNE
yazabilir → `LinkType` teyidi olmadan recurse silme YASAK.

**S: `Test-Path` junction'da ne döner?**
C: Junction + hedef mevcut → `true`; hedef silinmiş → `false`. `Test-Path`
junction'ı hedef üzerinden değerlendirir.

**S: `--classmap-authoritative` junction'ı etkiler mi?**
C: Hayır — autoload katmanı junction'dan bağımsızdır; sınıf → dosya eşlemesi
`vendor/composer/autoload_classmap.php` içindedir (gerçek yol, junction'ın
hedefini çözdükten sonra).

---

*CoreMusic Skill v3.0 — composer-sync references — 2026-10-21*
