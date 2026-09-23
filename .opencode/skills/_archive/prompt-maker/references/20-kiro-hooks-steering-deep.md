# KIRO HOOKS & STEERING — DERİN REHBER
# Prompt Maker v7.2.0 | 2026-06-11 | Kiro IDE Native
# Hook event'leri, steering inclusion modları, skill format, tam örnekler

---

## KIRO HOOK SİSTEMİ — TAM REFERANS

### Hook Dosya Formatı

```json
{
  "name": "Hook Adı (zorunlu)",
  "version": "1.0.0 (zorunlu)",
  "description": "Ne yapar (opsiyonel ama önerilir)",
  "when": {
    "type": "event-tipi (zorunlu)",
    "patterns": ["glob-pattern (file event'lerde zorunlu)"],
    "toolTypes": ["kategori (preToolUse/postToolUse'da zorunlu)"]
  },
  "then": {
    "type": "askAgent veya runCommand (zorunlu)",
    "prompt": "Agent mesajı (askAgent için zorunlu)",
    "command": "Shell komutu (runCommand için zorunlu)"
  }
}
```

### Event Tipleri — Tam Liste

```
fileEdited        → Kullanıcı dosya kaydettiğinde
                    Zorunlu: patterns (glob array)
                    Örnek: ["**/*.php", "**/*.ts"]

fileCreated       → Yeni dosya oluşturulduğunda
                    Zorunlu: patterns

fileDeleted       → Dosya silindiğinde
                    Zorunlu: patterns

userTriggered     → Kullanıcı manuel tetiklediğinde
                    Zorunlu: hiçbir şey
                    Kullanım: Kiro hook UI'dan buton

promptSubmit      → Kullanıcı prompt gönderdiğinde
                    Zorunlu: hiçbir şey
                    Kullanım: Her prompt'a ek bağlam ekle

agentStop         → Agent çalışmayı bitirdiğinde
                    Zorunlu: hiçbir şey
                    Kullanım: Post-task işlemler

preToolUse        → Tool kullanımından ÖNCE
                    Zorunlu: toolTypes
                    Kullanım: Güvenlik kontrolü, onay

postToolUse       → Tool kullanımından SONRA
                    Zorunlu: toolTypes
                    Kullanım: Kalite kontrol, loglama

preTaskExecution  → Spec task başlamadan önce
                    Zorunlu: hiçbir şey
                    Kullanım: Task hazırlık

postTaskExecution → Spec task tamamlandıktan sonra
                    Zorunlu: hiçbir şey
                    Kullanım: Test çalıştır, wiki güncelle
```

### Tool Kategorileri (toolTypes)

```
read    → Dosya okuma araçları (read_file, grep_search, vb.)
write   → Dosya yazma araçları (fs_write, str_replace, vb.)
shell   → Terminal komutları (execute_pwsh, vb.)
web     → Web araçları (web_search, web_fetch, vb.)
spec    → Spec araçları (spec-related tools)
*       → Tüm araçlar

Regex pattern da kullanılabilir:
".*sql.*"  → İsminde "sql" geçen tüm araçlar
".*file.*" → İsminde "file" geçen tüm araçlar
```

---

## HOOK ÖRNEKLERİ — TAM KOLEKSİYON

### 1. PHP Lint on Save

```json
{
  "name": "PHP Lint on Save",
  "version": "1.0.0",
  "description": "PHP dosyası kaydedilince phpcs + phpstan çalıştır",
  "when": {
    "type": "fileEdited",
    "patterns": ["**/*.php"]
  },
  "then": {
    "type": "runCommand",
    "command": "vendor/bin/phpcs --standard=PSR12 {file} && vendor/bin/phpstan analyse {file} --level=8 --no-progress"
  }
}
```

### 2. TypeScript Lint on Save

```json
{
  "name": "TypeScript Lint on Save",
  "version": "1.0.0",
  "description": "TS/TSX dosyası kaydedilince ESLint çalıştır",
  "when": {
    "type": "fileEdited",
    "patterns": ["**/*.ts", "**/*.tsx"]
  },
  "then": {
    "type": "runCommand",
    "command": "npx eslint {file} --fix"
  }
}
```

### 3. Pre-Write Security Guard

```json
{
  "name": "Pre-Write Security Guard",
  "version": "1.0.0",
  "description": "Dosya yazma öncesi güvenlik kontrolü",
  "when": {
    "type": "preToolUse",
    "toolTypes": ["write"]
  },
  "then": {
    "type": "askAgent",
    "prompt": "## GÜVENLİK KONTROLÜ\n\nYazılacak kod için kontrol et:\n1. SQL injection riski? (raw concatenation?)\n2. XSS riski? (innerHTML = userInput?)\n3. Hardcoded credential? (password, key, token?)\n4. unserialize(user_input)?\n5. eval()?\n6. CoreMusic ADR ihlali? (framework, ORM, SELECT *)\n\nSONUÇ: PASS veya FAIL (gerekçe ile)\nFAIL ise: Yazma işlemini durdur, düzeltme öner."
  }
}
```

### 4. Post-Task Wiki Sync

```json
{
  "name": "Post-Task Wiki Sync",
  "version": "1.0.0",
  "description": "Spec task tamamlandıktan sonra wiki ve log güncelle",
  "when": {
    "type": "postTaskExecution"
  },
  "then": {
    "type": "askAgent",
    "prompt": "Task tamamlandı. Şimdi şunları yap:\n\n1. .ai/log.md dosyasına bu task için kayıt ekle (format: ## [YYYY-MM-DD HH:mm:ss] başlığı ile)\n2. Değişen mimari varsa .ai/decisions/ altına ADR oluştur\n3. İlgili wiki sayfalarını güncelle\n4. Stale referans varsa düzelt\n5. IDE steering dosyalarını sync et (.kiro/steering/)\n\nHer adımı tamamladıktan sonra ✅ işaretle."
  }
}
```

### 5. Pre-Task Requirements Check

```json
{
  "name": "Pre-Task Requirements Check",
  "version": "1.0.0",
  "description": "Spec task başlamadan önce gereksinimleri doğrula",
  "when": {
    "type": "preTaskExecution"
  },
  "then": {
    "type": "askAgent",
    "prompt": "Task başlamadan önce kontrol et:\n\n1. Task gereksinimleri net mi?\n2. Bağımlı task'lar tamamlandı mı?\n3. Gerekli dosyalar mevcut mu?\n4. ADR kısıtları var mı? (CoreMusic için: ADR-001 through ADR-007)\n5. Güvenlik gereksinimleri var mı?\n\nHer şey hazırsa: 'READY' yaz ve devam et.\nEksik varsa: Eksikleri listele ve dur."
  }
}
```

### 6. Agent Stop Summary

```json
{
  "name": "Agent Stop Summary",
  "version": "1.0.0",
  "description": "Agent durduğunda özet rapor üret",
  "when": {
    "type": "agentStop"
  },
  "then": {
    "type": "askAgent",
    "prompt": "Agent çalışması tamamlandı. Kısa özet:\n\n1. Ne yapıldı? (1-3 madde)\n2. Hangi dosyalar değişti?\n3. Güvenlik etkisi var mı?\n4. Performance etkisi var mı?\n5. Sonraki adım nedir?\n\nMaksimum 10 satır."
  }
}
```

### 7. Prompt Context Enricher

```json
{
  "name": "CoreMusic Context Enricher",
  "version": "1.0.0",
  "description": "Her prompt'a CoreMusic bağlamı ekle",
  "when": {
    "type": "promptSubmit"
  },
  "then": {
    "type": "askAgent",
    "prompt": "Kullanıcının promptunu işlemeden önce:\n\n1. .ai/brain.md'yi oku (son kararlar)\n2. .ai/log.md son 5 kaydını oku (son değişiklikler)\n3. İlgili ADR'leri kontrol et\n4. Aktif steering dosyalarını kontrol et\n\nBu bağlamı kullanarak kullanıcının promptunu yanıtla.\nCoreMusic ADR'lerini ihlal etme."
  }
}
```

### 8. Shell Command Safety Check

```json
{
  "name": "Shell Command Safety Check",
  "version": "1.0.0",
  "description": "Shell komutu çalıştırmadan önce güvenlik kontrolü",
  "when": {
    "type": "preToolUse",
    "toolTypes": ["shell"]
  },
  "then": {
    "type": "askAgent",
    "prompt": "Shell komutu çalıştırılmak üzere. Kontrol et:\n\n1. Komut destructive mi? (rm -rf, DROP TABLE, vb.)\n2. Production'ı etkiler mi?\n3. Geri alınamaz mı?\n4. Güvenlik riski var mı? (injection, privilege escalation)\n\nGüvenli ise: 'SAFE' yaz ve devam et.\nRiskli ise: Riski açıkla ve kullanıcıdan onay iste."
  }
}
```

---

## KIRO STEERING — TAM REFERANS

### Steering Dosya Formatı

```markdown
---
inclusion: always | auto | fileMatch | manual
priority: low | medium | high | critical
description: "Kısa açıklama (auto modda zorunlu)"
fileMatchPattern: "**/*.php"  (fileMatch modda zorunlu)
---

# Başlık

[İçerik]
```

### Inclusion Modları — Detay

```
always:
  - Her prompt'ta otomatik yüklenir
  - Kullanım: Global kurallar, proje standartları
  - Örnek: coremusic-core.md, ultrathink-rules-core.md

auto:
  - Kiro otomatik karar verir (description'a göre)
  - description zorunlu (Kiro bunu okur)
  - Kullanım: Domain-spesifik kurallar
  - Örnek: php-standards.md (PHP dosyaları açıkken)

fileMatch:
  - Belirli dosyalar açıkken yüklenir
  - fileMatchPattern zorunlu (glob)
  - Kullanım: Dosya tipi bazlı kurallar
  - Örnek: "**/*.php" → PHP kuralları

manual:
  - Kullanıcı # ile manuel yükler
  - Kullanım: Büyük, nadiren kullanılan rehberler
  - Örnek: #coremusic-wiki (wiki rehberi)
```

### Priority Seviyeleri

```
low:      Genel bilgi, opsiyonel
medium:   Önerilen kurallar
high:     Önemli kurallar, uyulmalı
critical: Zorunlu kurallar, ihlal edilemez
```

---

## STEERING ÖRNEKLERİ — TAM KOLEKSİYON

### 1. PHP Güvenlik Steering (fileMatch)

```markdown
---
inclusion: fileMatch
fileMatchPattern: "**/*.php"
priority: critical
description: PHP 8.x security standards — OWASP Top 10:2025 compliant
---

# PHP 8.x Güvenlik Standartları

## Zorunlu
- declare(strict_types=1) her dosyada
- PDO parameterized queries
- Argon2id password hashing
- CSRF token her formda
- Rate limiting: 5 req/15 dk
- session_regenerate_id(true) login sonrası

## Yasak
- md5($password) → Argon2id kullan
- Raw SQL concatenation → PDO kullan
- unserialize(user_input) → JSON decode kullan
- eval() → Hiçbir zaman
- SELECT * → Sadece gerekli kolonlar
- extract($_POST) → Explicit assignment
```

### 2. JavaScript SPA Steering (fileMatch)

```markdown
---
inclusion: fileMatch
fileMatchPattern: "assets.coremusic.net/**/*.js"
priority: high
description: Vanilla JS SPA standards — IIFE, AbortController, DOM-safe
---

# JavaScript SPA Standartları

## Zorunlu
- IIFE pattern: (function(CoreMusic) { ... })(window.CoreMusic = ...)
- AbortController: Her fetch isteğinde
- DOM-safe: textContent (innerHTML = userInput yasak)
- Event cleanup: unmount'ta removeEventListener
- No var: let veya const kullan
- Async/await: callback hell yasak

## Yasak
- var → let/const
- innerHTML = userInput → textContent
- Framework import → Vanilla JS
- Unhandled promise rejection → try/catch
- document.write() → DOM API
```

### 3. CoreMusic ADR Steering (always)

```markdown
---
inclusion: always
priority: critical
description: CoreMusic Architecture Decision Records — değiştirilemez kararlar
---

# CoreMusic ADR Kuralları

## ADR-001: Vanilla JS Only
Framework runtime YASAK: React, Vue, Angular, Svelte, jQuery (yeni kod için)
Vanilla JS + IIFE pattern zorunlu.

## ADR-002: ITCSS Immutable
CSS mimarisi ITCSS, --cm-* token sistemi zorunlu.
Hardcoded renk/boyut değeri yasak.

## ADR-003: PDO No ORM
ORM YASAK: Doctrine, Eloquent.
PDO parameterized query zorunlu.

## ADR-004: Multi-Domain
music.coremusic.net → PHP runtime
assets.coremusic.net → Static assets

## ADR-007: Cache Namespace
CoreMusic\System\Cache namespace zorunlu.
```

### 4. Wiki Maintenance Steering (always)

```markdown
---
inclusion: always
priority: high
description: Wiki ve log güncelleme kuralları — her task sonrası zorunlu
---

# Wiki Maintenance Kuralları

## Her Task Sonrası Zorunlu

1. .ai/log.md → append (timestamp + task + impact)
2. .ai/decisions/ → ADR (mimari karar varsa)
3. .ai/wiki/ → ilgili sayfaları güncelle
4. .kiro/steering/ → steering sync

## Log Format
```
## [YYYY-MM-DD HH:mm:ss]
### Task: [Ne yapıldı?]
### Modified Systems: [Sistemler]
### Updated Files: [Dosyalar]
### Runtime Impact: [Etki]
### Security Impact: [Güvenlik etkisi]
```

## Yasak
- "Documentation sonra yapılır"
- "Wiki outdated kaldı"
- "Kullanıcı docs güncellesin"
```

---

## SKILL TASARIMI — BEST PRACTICES

### SKILL.md Yapısı

```markdown
---
name: skill-adı (kebab-case, max 64 karakter)
version: X.Y.Z
description: >
  Kısa açıklama (bu skill ne yapar, ne zaman aktive olur).
  Keyword'ler burada olmalı — Kiro bu metni okur.
---

# Skill Başlığı

## Rol
[AI'ın bu skill'deki rolü — 2-3 cümle]

## Aktivasyon
[Ne zaman aktive olur, keyword'ler listesi]

## Çalışma Akışı
[Adım adım ne yapılır — numaralı liste]

## Kurallar
### Zorunlu
- [Kural 1]
### Yasak
- [Yasak 1]

## Çıktı Formatı
[Ne üretir, nereye kaydeder]

## Örnekler
[Gerçek kullanım örnekleri]
```

### Skill Adlandırma Kuralları

```
✅ Doğru:
  prompt-maker
  php-security-audit
  spa-router-debug
  code-review-solid

❌ Yanlış:
  PromptMaker (PascalCase yasak)
  prompt_maker (underscore yasak)
  -prompt-maker (başında hyphen yasak)
  prompt-maker- (sonda hyphen yasak)
  bu-skill-adi-cok-uzun-ve-64-karakteri-asiyor-bu-yuzden-gecersiz (max 64)
```

### Progressive Disclosure Pattern

```
SKILL.md → Kısa, özet (max 500 satır önerilir)
  ↓
references/ → Detaylı dokümantasyon
  01-deep-guide.md
  02-examples.md
  03-templates.md
  ...

Neden?
  - Kiro her prompt'ta SKILL.md'yi yükler
  - Çok uzun SKILL.md → token waste
  - references/ → sadece gerektiğinde yüklenir
```

---

## HOOK + STEERING + SKILL ENTEGRASYONU

### Örnek: Tam CoreMusic Workflow

```
Kullanıcı PHP dosyası kaydeder
  ↓
[Hook: PHP Lint on Save] → phpcs + phpstan çalışır
  ↓
Kullanıcı prompt gönderir
  ↓
[Hook: CoreMusic Context Enricher] → brain.md + log.md okunur
  ↓
[Steering: coremusic-core.md] → ADR kuralları yüklenir
[Steering: php-standards.md] → PHP kuralları yüklenir (fileMatch)
[Steering: ultrathink-rules-core.md] → ULTRATHINK kuralları yüklenir
  ↓
AI yanıt üretir
  ↓
[Hook: Pre-Write Security Guard] → Güvenlik kontrolü
  ↓
Dosya yazılır
  ↓
[Hook: Post-Task Wiki Sync] → log.md + wiki güncellenir
```

---

## KIRO HOOK CHECKLIST

```
Hook oluşturmadan önce:
  [ ] Event tipi doğru mu?
  [ ] patterns zorunlu mu? (file event'lerde)
  [ ] toolTypes zorunlu mu? (preToolUse/postToolUse'da)
  [ ] then.type doğru mu? (askAgent veya runCommand)
  [ ] prompt/command dolu mu?
  [ ] Circular dependency riski var mı?

Circular dependency kontrolü:
  preToolUse hook → askAgent → tool çağrısı → preToolUse hook → sonsuz döngü!
  Çözüm: Hook içinde aynı tool'u çağırma
```

---

*Kiro Hooks & Steering Deep Guide v7.0.0 — 2026-05-29 | Kiro IDE Native*
*Hook events, steering inclusion modes, skill format, full examples*
*Kaynak: kiro.dev/docs/hooks/, kiro.dev/docs/steering/, kiro.dev/docs/skills/*
