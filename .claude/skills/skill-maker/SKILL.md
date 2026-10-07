---
name: skill-maker
description: "Use when creating, upgrading, or auditing a SKILL.md skill for CoreMusic — Tetikleyiciler: 'yeni skill', 'skill oluştur', 'skill güncelle', 'SKILL.md yaz'."
license: MIT
metadata:
  version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: meta-skill
  tags: [skill-development, truth-mode, template, v3-authority]
  updated: 2026-10-07
  previous-version: "3.2"
---

# skill-maker — Claude Skill v3.0 Format Otoritesi

> Bu skill, CoreMusic `SKILL.md` skill'lerinin **v3.0 formatının tek otoritesidir (SSOT)**.
> Format kuralları §5 FORMAT SPEC'te **normatiftir** (MUST/NEVER); başka kaynakla çelişirse
> bu dosya lehinedir. Önceki içerik sürümü: 3.2 (→ `references/changelog.md`).

---

## §1 Genel Bakış

**Ne yapar:** Yeni skill üretir, mevcut skill'i v3.0 formatına taşır, skill denetler (audit).

| Trigger (description ile eşleşir) | Ne zaman | Bu skill |
|-----------------------------------|----------|----------|
| `yeni skill` / `skill oluştur` / `beceri yap` | Kullanıcı yeni yetenek ister | ✅ ilk adım |
| `skill güncelle` / `SKILL.md yaz` | Mevcut skill upgrade / rewrite | ✅ |
| `agentic skill üret` / `orchestration skill` | Otomasyon becerisi isteği | ✅ |
| Skill denetimi (audit) | Format/kalite kontrolü | ✅ ADIM 5 checklist |
| Kural/talimat yazımı (skill DEĞİL) | Vault `.md` yazımı | ❌ → `.ai/.templates/` (Guardrail #16) |
| Prompt üretimi | Ham prompt → yapılandırılmış prompt | ❌ → `prompt-maker` |

**Kapsam dışı:** `.ai/` vault dokümanları, prompt arşivleri, skill registry dışı envanter işleri.

---

## §2 Zorunlu Okumalar (references/ + templates/)

> Kural: aşağıdaki dosyalar bu skill'in Katman-3 derinliğidir; **tabloda olmayan references/
> veya examples/ dosyası = orphan hatadır** (§5-N4).

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| `references/overview.md` | 3 katmanlı progressive disclosure + canonical klasör şeması | İlk kez skill öğrenilirken / iskelet kurarken |
| `references/rules.md` | Zero-Hallucination, frontmatter formatı, isimlendirme, güvenlik + v3.0 normatif kurallar | Üretim öncesi kısıt kontrolünde (ADIM 3'ten önce) |
| `references/anti-patterns.md` | Yasaklı kalıplar (halüsinasyon, kök frontmatter, isimlendirme, 2000 satır) | İçerik/kod yazım öncesi |
| `references/checklist.md` | Kalite kontrol listesi (ADIM 5) | Teslim öncesi kalite geçişinde |
| `references/changelog.md` | Format/sürüm geçmişi (eski frontmatter `changelog[]` buraya taşındı) | Sürüm davranışı sorgulandığında |
| `references/examples.md` | Orijinal senaryo arşivi (içeriğinin kopyası `examples/01` + `examples/02`'de; dosya korunur — yetki kararı 2026-10-07) | Hızlı senaryo kalibrasyonu / arşiv karşılaştırması |
| `references/Skills-Olusturma-Rehberi (1).md` | Tam üretim rehberi (586 satır, salt-okunur arşiv — silinmez, yetki kararı 2026-10-07) | Derin üretim gerektiğinde |
| `templates/skill-template.md` | Genel skill şablonu (v3.0 iskelet) | Yeni iskelet üretirken (ADIM 3) |
| `templates/php-skill-template.md` | PHP domain şablonu (v3.0 iskelet) | PHP/Backend skill'inde (ADIM 3) |

---

## §3 Örnekler (examples/)

> `examples/` dizini **zorunludur**; her examples/ dosyası SKILL.md'den linkli olmalıdır (§5-N4/N5).

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/01-basic-skill.md` | Temel skill: ham istek → sql-optimizer SKILL.md çıktısı (references/examples.md taşındı) |
| `examples/02-coremusic-php-skill.md` | CoreMusic PHP domain skill'i: middleware + raw PDO kurallarıyla üretim |
| `examples/full-skill-walkthrough.md` | `php-backend-standards` skill'inin uçtan uca üretim kaydı (gereksinim → şablon → SKILL.md → references → examples → doğrulama) |
| `examples/new-skill-example.md` | ADIM 1 → ADIM 5 tam adım dökümü (sql-optimizer) |
| `examples/pointer-skill-example.md` | Punteri skill üretimi (SSOT başka dizindeyken, kopya drift'i yasak) |

---

## §4 Otonom Çalışma Protokolü (5 adım — pipeline)

```text
ADIM 1 GEREKSİNİM  → kısa soru-cevap: ad (lowercase-hyphen) · ana görev/domain · araçlar/API
ADIM 2 WEB RESEARCH → Truth Mode: güncel doküman oku, 2-3 kaynak teyit, varsayım YOK
ADIM 3 YAPI        → v3.0 iskelet dizini kur (aşağıda) + rules.md kısıtlarını uygula
ADIM 4 İÇERİK      → SKILL.md'ye çekirdek kuralları enjekte et:
                     Zero-Hallucination · SOLID/Clean Code · OWASP Top 10:2025 ·
                     test edilmemiş iddia = kabul yok · vault (CLAUDE §7 / AGENTS §5) çelişmez
ADIM 5 KALITE      → references/checklist.md çalıştır + örneklerle hizala + kullanıcıya rapor ver
```

**v3.0 klasör iskeleti (ADIM 3):**

```text
.claude/skills/{skill-adi}/          ← klasör adı = name (1:1, §5-N6)
├── SKILL.md            ← Zorunlu: v3.0 frontmatter + bu dosyadaki § yapısi (§5-N1)
├── references/         ← Zorunlu: derin kural (overview, rules, anti-patterns, checklist, changelog)
├── examples/           ← Zorunlu: ≥1 çalışmış örnek (girdi → çıktı tam döngü, §5-N5)
└── templates/          ← Opsiyonel: kod/şablon dosyaları (varsa)
```

---

## §5 v3.0 FORMAT SPEC (NORMATİF — bu skill formatın otoritesi)

> Aşağıdaki maddeler **zorunludur**. İhlal = skill geçersiz (audit ADIM 5'te yakalanır).

- **N1 — Kök frontmatter anahtarları SADECE:** `name`, `description`, `license`, `metadata`.
  `title` · `type` · `version` · `authority` · `mode` · `purpose` · `reference` · `triggers` ·
  `changelog` · `format` · `updated` kökte **YASAKTIR** → içerik `metadata.*` altına veya
  SKILL.md gövdesine taşınır (ör. tetikleyiciler description içine, geçmiş sürüm →
  `references/changelog.md`, başlık → gövde H1).
- **N2 — description:** `"Use when …"` ile başlar, **≤1024 karakter**, tetikleyici Türkçe
  kelimeleri (`yeni skill`, `skill oluştur`, `skill güncelle`, `SKILL.md yaz` benzeri) içerir.
- **N3 — Boyut:** SKILL.md hedefi **≤~250 satır**, sert üst sınır **2000 satır**;
  monolit dosya YASAK. SKILL.md = **Katman-2 orkestrasyon dosyasıdır** (kod değil, akış +
  kural + link); derinlik `references/`, `examples/`, `scripts/`, `templates/`e dağıtılır.
- **N4 — Link zorunluluğu:** HER `references/*.md` ve `examples/*.md` dosyası SKILL.md'den
  linkli olmalıdır. **Orphan dosya = hata** (§2/§3 tabloları bu linkin kendisidir).
- **N5 — `examples/` zorunlu:** ≥1 çalışmış örnek; **girdi → çıktı tam döngü**
  (kullanıcı isteği → üretilen SKILL.md/çıktı) göstermelidir.
- **N6 — İsimlendirme:** klasör adı = `name` 1:1; lowercase-hyphen; dosya adı `SKILL.md`
  (BÜYÜK harf); Türkçe karakter, alt çizgi (`_`), boşluk, büyük harf YASAK.
- **N7 — MAX THINKING bloğu ASLA kopyalanmaz.** Skill gövdesine tek satır olarak şu gelir:
  `Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.`
- **N8 — `CLAUDE.md` sidecar YASAK** skill dizinlerinde (kök, references/, templates/,
  scripts/ dahil). Talimat tek kaynaktır: SKILL.md.
- **N9 — Footer (her SKILL.md son satırı):**
  `*CoreMusic Skill v3.0 — metadata.version: X.Y.Z — Updated: YYYY-MM-DD*`
  Sürüm otoritesi **`metadata.version`'dur** (footer'daki X = metadata.version).
- **N10 — Eski `changelog[]` kök alanı yoktur:** sürüm geçmişi `references/changelog.md`de
  tutulur; SKILL.md'de yalnız `metadata.version` + `metadata.previous-version` kalır.

**Frontmatter referansı (v3.0 — tek geçerli şema):**

```yaml
---
name: skill-adi
description: "Use when … — Tetikleyiciler: '…', '…', '…'."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: backend-orchestration
  tags: [php, truth-mode]
  updated: 2026-10-07
---
```

---

## §6 Güvenlik & Truth Mode (çekirdek — bağlayıcı)

1. **Web Research zorunlu:** skill'in kullanacağı kütüphane/API/komut/standart uydurulamaz →
   (1) web'de ara, (2) en az 2-3 resmi kaynaktan teyit et (MDN, OWASP, vendor docs),
   (3) geçersiz/deprecated'i reddet (H001).
2. **Zero Hallucination:** doğrulanamayan iddia → `⚠️ VERIFICATION REQUIRED`; tahmin YASAK.
3. **Vault önce:** yeni skill `.ai/` kurallarıyla (CLAUDE §7 guardrail, AGENTS §5) çelişemez;
   çelişki → **DUR + kullanıcıya sor**.
4. **Security & Isolation:** ağ/dış sistem/yıkıcı eylem → kullanıcı onayı; credential
   (API key/secret) skill içine **asla** yazılmaz (`.env`/vault göster).
5. **Registry notu:** `.ai/.templates/index.md` + proje envanteri (`.claude/CONTEXT.md`)
   güncellemesi raporda not edilir.

**İlgili kurallar:** Guardrail #16 (şablon) → `.ai/.templates/index.md` · Zero-Hallucination
→ `.ai/AGENTS.md` §3 · Vault-First → Guardrail #2 / `.ai/CLAUDE.md` §16 boot ·
Skill envanteri → `.claude/CONTEXT.md`.

---

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 3.0.0 — Updated: 2026-10-07*