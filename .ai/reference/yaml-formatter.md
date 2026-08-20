---
title: "CoreMusic — YAML Formatter & Validation Standards"
type: reference
category: yaml-format
date: 2026-08-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
source: ".ai/WORKFLOW.md §8.8"
---

# YAML Formatter & Doğrulama

YAML dosyalarının (`.yml`, `.yaml`) tutarlı, geçerli ve okunabilir olmasını sağlamak için kullanılan formatter workflow'u.

## Amaç

- Tüm YAML dosyalarını standart formata getirme (2 boşluk girinti, single-quote string, key sırası)
- Syntactic doğrulama (geçerli YAML syntax)
- Schema uyumluluğu kontrolü (CI/CD, Docker, GitHub Actions)
- Vault YAML dosyalarının format tutarlılığını koruma

## YAML Format Standartları

| Kural | Değer |
|-------|-------|
| Girinti | 2 boşluk (tab yasak) |
| String Quote | Single-quote (`'`) tercih edilir, double-quote sadece escape gerektiğinde |
| Key Sırası | Alfabetik (CI/CD dosyaları hariç — mantıksal gruplama) |
| Max Satır Uzunluğu | 120 karakter |
| Boş Satır | Maksimum 1 arka arkaya |
| Comment Style | `# ` prefix ile (boşluk zorunlu) |
| Trailing Comma | YAML'de desteklenmez — kaldırılmalı |
| Boolean Format | `true` / `false` (lowercase) |
| Null Format | `null` (lowercase) |
| Date Format | ISO 8601 (`YYYY-MM-DDTHH:MM:SSZ`) |

## YAML Doğrulama Adımları

| # | Adım | Araç | Kontrol |
|---|------|------|---------|
| 1 | Syntactic doğrulama | `yamllint` veya Python `yaml.safe_load()` | Geçerli YAML syntax |
| 2 | Format kontrolü | `prettier --parser yaml` veya `yaml-formatter` | Girinti, quote, spacing |
| 3 | Schema doğrulama | `yaml-schema-validator` (JSON Schema) | Zorunlu alanlar, tipler |
| 4 | Duplicate key kontrolü | YAML parser | Tekrarlayan anahtar yok |
| 5 | Anchors & Aliases kontrolü | YAML parser | Döngüsel referans yok |
| 6 | Comment kalitesi | Manuel / linter | Anlamlı, güncel yorumlar |

## Dosya Kategorileri & Format Kuralları

| Kategori | Dosya Pattern | Özel Kurallar |
|----------|---------------|---------------|
| CI/CD | `.github/workflows/*.yml` | Step isimleri zorunlu, `on:` trigger tanımlı |
| Docker | `docker-compose*.yml` | Service isimleri lowercase, `version:` zorunlu |
| Vault | `.ai/**/*.yml` | Wiki-link formatı, frontmatter zorunlu |
| Config | `*.config.yml` | Environment-specific section'lar ayrıştırılmış |
| Schema | `*.schema.yml` | JSON Schema uyumlu, `$schema` reference |

## Otomatik Format Düzeltme (PowerShell)

```powershell
# Tüm YAML dosyalarını bul ve formatla
Get-ChildItem -Recurse -Include *.yml,*.yaml |
  ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    # 1. Tab'ları 2 boşluğa çevir
    $content = $content -replace "`t", "  "
    # 2. Trailing whitespace kaldır
    $content = $content -replace "\s+$", ""
    # 3. Boolean标准化
    $content = $content -replace ":\s*True\s*$", ": true"
    $content = $content -replace ":\s*False\s*$", ": false"
    $content = $content -replace ":\s*Yes\s*$", ": true"
    $content = $content -replace ":\s*No\s*$", ": false"
    Set-Content $_.FullName $content -NoNewline
  }
```

## YAML Hata Türleri & Düzeltmeleri

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Indentation error | `mapping values are not allowed here` | Girintiyi 2 boşluğa düzelt |
| Duplicate key | `duplicate key found` | Tekrarlayan key'i kaldır veya birleştir |
| Invalid boolean | `True/False/Yes/No` | `true/false` formatına çevir |
| Missing separator | `while scanning a simple key` | `:`后面 boşluk ekle |
| Tab character | `tab characters are not allowed` | Tab'ı 2 boşluğa çevir |
| Trailing spaces | Linter uyarısı | Trailing whitespace kaldır |

## CI/CD Entegrasyonu

```yaml
# .github/workflows/yaml-lint.yml
name: YAML Lint
on: [push, pull_request]
jobs:
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Install yamllint
        run: pip install yamllint
      - name: Run yamllint
        run: yamllint -c .yamllint.yml .
```

## `.yamllint.yml` Konfigürasyonu

```yaml
---
extends: default
rules:
  indentation:
    spaces: 2
    indent-sequences: true
  line-length:
    max: 120
    allow-non-breakable-words: true
    allow-non-breakable-inline-mappings: true
  comments:
    min-spaces-from-content: 1
    require-starting-space: true
  truthy:
    allowed-values: ['true', 'false']
  document-start: disable
  comments-indentation: disable
```

---

**Authority:** Bayram Ali / Vault Steward
**Source:** WORKFLOW.md §8.8 (extracted)
**Mode:** Red Team · Human Mode · Truth Mode
