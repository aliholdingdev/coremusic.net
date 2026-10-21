# Kalite Kapısı (Quality Gate) — PASS / BLOCK

> Bu skill'in teslim kapısı. Şablon: `skill-maker/references/checklist.md` kalıbı.
> Her çıktı bu kapıdan geçmeden “tamam” sayılmaz. Tek bir BLOCK bile varsa teslim **durdurulur**.

## Şekil (N1–N10)

```text
[ ] SKILL.md dosya adı tam: SKILL.md
[ ] Frontmatter yalnız: name / description / license / metadata
[ ] name = klasör adı birebir (database-normalize-maker), lowercase-hyphen
[ ] description "Use when…" ile başlıyor, Türkçe tetikleyiciler korunmuş, ≤1024 karakter
[ ] Body ≥6 bölüm + en az 1 kod bloğu/şema/çizelge
[ ] Footer = metadata.version + "Updated 2026-10-21"
[ ] SKILL.md satır sayısı 500–2000 (Get-Content .Count ile ölçülmüş)
[ ] Dizin toplam satır ≥1000
[ ] UTF-8 BOM'suz, mojibake yok (Türkçe karakterler doğru)
[ ] CLAUDE.md sidecar yazılmadı (N8 yasağı)
```

## Orphan (orphan=0)

```text
[ ] references/ içindeki HER .md SKILL.md'den linkli
[ ] scripts/ HER dosya SKILL.md'den linkli
[ ] templates/ HER dosya SKILL.md'den linkli (korunduysa)
```

## İçerik doğruluğu (zero-hallucination)

```text
[ ] MySQL 8.0 hedefi açık; "MySQL 9" farkı VERIFICATION REQUIRED ile işaretli
[ ] Partial index (… WHERE) MySQL'de desteklenmez — doğru öğretiliyor
[ ] EXPLAIN ANALYZE=8.0.18+, JSON multi-valued index=8.0.17+, CHECK=8.0.16+ notları var
[ ] Fonksiyonel bağımlılık + aday anahtar deterministik anlatılmış
[ ] Doğrulanamayan her iddia VERIFICATION REQUIRED etiketli
[ ] Uydurma tablo/kolon/ADR/sürüm yok
```

## CoreMusic domain (ADR uyumu)

```text
[ ] SELECT * yasak (ADR-014) — üretilen SQL'de yok
[ ] ORM yok (ADR-002); prepared statement disiplini vurgulu
[ ] Hard delete yok (soft delete zorunlu)
[ ] DECIMAL para; FLOAT para yasak
[ ] Prod DDL root onayı; subagent commit atmıyor
```

## Script'ler

```text
[ ] 3 PHP strict_types, saf analiz, --help destekli, mojibake yok
[ ] Çalıştırma kanıtı raporlandı (bu ortamda php YOK → VERIFICATION REQUIRED)
```

## Kaynak

```text
[ ] references/sources.md: 2-3 resmi kaynak, her biri URL + erişim tarihi 2026-10-21
[ ] ISO/IEC 9075 doğrulanmadıysa VERIFICATION REQUIRED (uydurulmadı)
```

## KARAR

- **PASS:** tüm şekil + orphan + içerik + domain maddeleri işaretli; BLOCK yok.
- **BLOCK:** herhangi bir madde eksik/yanlış → teslim durur, düzeltilir, tekrar ölçülür.
