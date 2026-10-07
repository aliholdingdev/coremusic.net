# 🌐 skill-maker — Agentic Rules & Kiro Formatı

## 1. Zero Hallucination & Truth Mode Kuralları
- **Web Research Zorunluluğu:** Skill içerisine yazılacak hiçbir teknoloji, kütüphane veya config tahmini (halüsinasyon) olamaz. MDN, OWASP, W3C gibi otoriter kaynaklardan doğrulanmalıdır.
- **Doğrulanmamış Bilgi İşareti:** Araştırmaya rağmen %100 emin olunamayan durumlarda kod satırının üstüne `// ⚠️ VERIFICATION REQUIRED` eklenmelidir.
- **Kritik Reddi (H001):** Mimariye ters düşen, eski veya güvensiz yapılar reddedilir ve kullanıcı uyarılır.

## 2. YAML Frontmatter Formatı (Zorunlu)
Oluşturulan her `SKILL.md` dosyasının başında bu yapı KESİNLİKLE bulunmalıdır:

```yaml
---
name: skill-adi              # Sadece küçük harf, tire (-). Örn: php-security-analyzer
description: açıklama        # Otonom tetikleme için net tanım. Tetikleyici kelimeleri içerir. (Max 1024 char)
license: MIT
metadata:
  version: 1.0.0
  author: Bayram Ali (ULTRATHINK Engineering)
  compatibility: Kiro IDE
  category: agentic-orchestration
  tags: [tag1, tag2]
---
```

## 3. Dosya ve İsimlendirme Kuralları
- **Dosya Adı:** Mutlaka `SKILL.md` (Büyük harflerle, `.md` küçük). `skill.md` YASAKTIR.
- **Klasör Adı = Skill Name:** YAML `name` alanı ile klasör adı 1:1 aynı olmalıdır.
- **İzin Verilmeyen Karakterler:** Boşluk, alt çizgi (`_`), Türkçe karakterler (ı, ş, ğ, ü, ö, ç), büyük harfler.

## 4. Otonom Güvenlik ve Kısıtlamalar (Security Enforcements)
- API anahtarı, token veya herhangi bir secret `SKILL.md` içine hardcode EDİLEMEZ.
- Zararlı olabilecek terminal komutları (`rm -rf`, vb.) için "Kullanıcı Onayı" prosedürü zorunludur.
- Skill çıktısı her zaman Kiro IDE ve CoreMusic kuralları ile tam uyumlu (SOLID, OWASP Top10:2025) olmak zorundadır.

## 5. Claude Skill v3.0 — Normatif Format Kuralları (N1-N10)

> **Otorite:** Bu bölüm `SKILL.md` §5 (v3.0 FORMAT SPEC) ile **birebir aynı** normatif
> kuralları taşır — ikisi çelişirse SKILL.md lehinedir. Format: `claude-skill-v3`.

- **N1 — Kök frontmatter anahtarları SADECE:** `name`, `description`, `license`, `metadata`.
  Kökte `title` · `type` · `version` · `authority` · `mode` · `purpose` · `reference` ·
  `triggers` · `changelog` · `format` · `updated` **YASAKTIR** → `metadata.*` altına veya
  gövdeye taşınır (tetikleyiciler → description; geçmiş → `references/changelog.md`).
- **N2 — description:** `"Use when …"` ile başlar, ≤1024 karakter, tetikleyici Türkçe
  kelimeleri içerir.
- **N3 — Boyut:** SKILL.md ≤~250 satır hedef, 2000 satır sert üst sınır; monolit YASAK;
  SKILL.md = Katman-2 orkestrasyon dosyası, derinlik `references/`/`examples/`e dağılır.
- **N4 — Link zorunluluğu:** HER `references/*.md` ve `examples/*.md` SKILL.md'den linkli
  olmalıdır. **Orphan dosya = hata.**
- **N5 — `examples/` zorunlu:** ≥1 çalışmış örnek (girdi → çıktı tam döngü).
- **N6 — İsimlendirme:** klasör adı = `name` 1:1; lowercase-hyphen; `SKILL.md` büyük harf;
  Türkçe karakter / alt çizgi / boşluk / büyük harf YASAK.
- **N7 — MAX THINKING bloğu ASLA kopyalanmaz** → tek satır:
  `Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.`
- **N8 — `CLAUDE.md` sidecar YASAK** skill dizinlerinde (kök, references/, templates/,
  scripts/ dahil).
- **N9 — Footer:** `*CoreMusic Skill v3.0 — metadata.version: X.Y.Z — Updated: YYYY-MM-DD*`
  (sürüm otoritesi `metadata.version`).
- **N10 — `changelog[]` kök alanı yok** → sürüm geçmişi `references/changelog.md`de.

### 5.1 Çelişki Düzeltme Notu (2026-10-07)

Önceki durum: `anti-patterns.md` §2 kök `version`'ı yasaklarken **tüm mevcut skill'ler**
kökte `version` / `format` / `updated` / `title` / `type` / `authority` / `mode[]` /
`purpose[]` / `reference{}` / `triggers[]` / `changelog[]` taşıyordu → kural-gerçeklik
çelişkisi. **Düzeltme:** `skill-maker` SKILL.md v3.0 formatına alındı (yalnız
`name`/`description`/`license`/`metadata` kökte) ve yasak listesi N1 ile bilinçli olarak
tüm non-standart kök anahtarları içerecek şekilde genişletildi. anti-patterns.md §2 ile
uyumlu; tekrarlayan eski kök alanlar hatalıdır.
