---
name: skill-maker
description: "Use when creating, upgrading, or auditing a SKILL.md skill for CoreMusic — Claude Skill v3.0 format otoritesi (N1-N10), Truth Mode research ve kalite kapısı. Tetikleyiciler: 'yeni skill', 'skill oluştur', 'skill güncelle', 'SKILL.md yaz', 'beceri yap', 'skill denetle', 'skill audit'."
license: MIT
metadata:
  version: 4.0.0
  previous-version: "3.0.0"
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: meta-skill
  tags: [skill-development, truth-mode, template, v3-authority, format-spec, n1-n10]
  updated: 2026-10-21
---

# skill-maker — Claude Skill v3.0 Format Otoritesi

> Bu skill, CoreMusic `SKILL.md` skill'lerinin **v3.0 formatının tek otoritesidir (SSOT)**.
> Format kuralları §5 FORMAT SPEC'te **normatiftir** (MUST/NEVER); başka kaynakla çelişirse
> bu dosya lehinedir. İçerik sürümü 3.0.0 → **4.0.0** (2026-10-21): N3 boyut kuralı
> revize edildi, upstream (resmi Agent Skills spec) karşılaştırmalı doğrulama eklendi,
> `references/sources.md` zorunlu hale getirildi.

**Bu sürümde değişenler (özet — tam kayıt `references/changelog.md`):**

| Alan | 3.0.0 | 4.0.0 |
|------|-------|-------|
| N3 SKILL.md alt sınırı | yok (eski ≤~250 hedefi) | **min 500 satır** (kullanıcı onaylı revizyon 2026-10-21) |
| N3 dizin toplamı | yok | **min 1000 satır** (SKILL.md ≥500 + references/examples ≥500) |
| N3 üst sınır | 2000 satır | **2000 satır (korundu)** |
| Upstream karşılaştırma | yok | §5.3 — resmi spec ile bilinçli sapmalar tablosu |
| `references/sources.md` | yok | **zorunlu** (Truth Mode kaynak kaydı, N4 linkli) |

---

## §1 Genel Bakış

**Ne yapar:** Yeni skill üretir, mevcut skill'i v3.0 formatına taşır, skill denetler (audit).

| Trigger (description ile eşleşir) | Ne zaman | Bu skill |
|-----------------------------------|----------|----------|
| `yeni skill` / `skill oluştur` / `beceri yap` | Kullanıcı yeni yetenek ister | ✅ ilk adım |
| `skill güncelle` / `SKILL.md yaz` | Mevcut skill upgrade / rewrite | ✅ |
| `agentic skill üret` / `orchestration skill` | Otomasyon becerisi isteği | ✅ |
| `skill denetle` / `skill audit` / `format kontrol` | Format/kalite kontrolü | ✅ ADIM 5 checklist |
| Kural/talimat yazımı (skill DEĞİL) | Vault `.md` yazımı | ❌ → `.ai/.templates/` (Guardrail #16) |
| Prompt üretimi | Ham prompt → yapılandırılmış prompt | ❌ → `prompt-maker` |
| Subagent tanımı | Uzman rol tarifi | ❌ → `agent/` (onaylı) |
| Slash komut | Kullanıcı kısayolu | ❌ → `command/` (onaylı) |

**Kapsam dışı:** `.ai/` vault dokümanları, prompt arşivleri, skill registry dışı envanter işleri.

**Sahiplik (`.claude/AGENTS.md` §3.1):** skill içeriği ilgili domain agent'ının, iskelet
şablondandır (Guardrail #16); bu skill **format** otoritesidir, **içerik** otoritesi değil.
Bir PHP skill'inin PHP kurallarını bu dosya yazmaz — o kurallar `.ai/` vault'undan ve ilgili
domain kaynaklarından gelir; bu dosya yalnız *nasıl paketlendiğini* bağlar.

---

## §2 Zorunlu Okumalar (references/ + templates/)

> Kural: aşağıdaki dosyalar bu skill'in Katman-3 derinliğidir; **tabloda olmayan references/
> veya examples/ dosyası = orphan hatadır** (§5-N4). `references/sources.md` her üretimde
> bir kez okunur (ADIM 2 Truth Mode kayıtları oradadır).

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| `references/overview.md` | 3 katmanlı progressive disclosure + canonical klasör şeması | İlk kez skill öğrenilirken / iskelet kurarken |
| `references/rules.md` | Zero-Hallucination, frontmatter formatı, isimlendirme, güvenlik + v3.0 normatif kurallar | Üretim öncesi kısıt kontrolünde (ADIM 3'ten önce) |
| `references/anti-patterns.md` | Yasaklı kalıplar (halüsinasyon, kök frontmatter, isimlendirme, monolit, dolgu) | İçerik/kod yazım öncesi |
| `references/checklist.md` | Kalite kontrol listesi (ADIM 5) — N3 500/1000/2000 kapıları dahil | Teslim öncesi kalite geçişinde |
| `references/sources.md` | **Zorunlu** — Truth Mode kaynak kaydı (ad · URL · erişim tarihi · ne doğrulandı) | ADIM 2'de kaynak teyidi sırasında + üretim sonrası denetimde |
| `references/changelog.md` | Format/sürüm geçmişi (eski frontmatter `changelog[]` buraya taşındı — N10) | Sürüm davranışı sorgulandığında |
| `references/examples.md` | Orijinal senaryo arşivi (içeriğinin kopyası `examples/01` + `examples/02`'de; dosya korunur — yetki kararı 2026-10-07) | Hızlı senaryo kalibrasyonu / arşiv karşılaştırması |
| `references/Skills-Olusturma-Rehberi (1).md` | Tam üretim rehberi (586 satır, salt-okunur arşiv — silinmez, yetki kararı 2026-10-07) | Derin üretim gerektiğinde |
| `templates/skill-template.md` | Genel skill şablonu (v3.0 iskelet + N3 revize kapılar) | Yeni iskelet üretirken (ADIM 3) |
| `templates/php-skill-template.md` | PHP domain şablonu (v3.0 iskelet + CoreMusic PHP kuralları) | PHP/Backend skill'inde (ADIM 3) |

**Şablon otoritesi (A2 kararı):** `.ai/.templates/` içinde **skill şablonu YOKTUR**
(ölçüm 2026-10-21: 66 dosya, hiçbiri SKILL.md iskeleti değil — yalnız `prompt-maker/`,
`documentation/`, `frontend/`, `backend/` vb. vault şablonları var). Bu nedenle bu dizindeki
`templates/skill-template.md` + `templates/php-skill-template.md` **kanoniktir** ve Guardrail #16
"şablon zorunlu" kuralının bu skill sınıfındaki karşılığıdır. Vault'a skill şablonu eklenmesi
önerisi raporda not edilir (vault yazımı bu skill'in yetkisi değildir).

---

## §3 Örnekler (examples/)

> `examples/` dizini **zorunludur** (N5); her examples/ dosyası SKILL.md'den linkli olmalıdır (N4).

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/01-basic-skill.md` | Temel skill: ham istek → `sql-optimizer` SKILL.md çıktısı (girdi → çıktı tam döngü) |
| `examples/02-coremusic-php-skill.md` | CoreMusic PHP domain skill'i: middleware + raw PDO kurallarıyla üretim |
| `examples/full-skill-walkthrough.md` | `php-backend-standards` skill'inin uçtan uca üretim kaydı (gereksinim → şablon → SKILL.md → references → examples → doğrulama) |
| `examples/trigger-quickref.md` | **Tetikleyici quickref** — description yazımı, keşfedilebilirlik (N2/N6) ve trigger kaybı teşhisi |
| `examples/pointer-skill-example.md` | Punteri skill üretimi (SSOT başka dizindeyken, kopya drift'i yasak) |

**Not (düzeltme 2026-10-21):** `examples/new-skill-example.md` dosyası silinmiştir — içeriği
`examples/01-basic-skill.md` (ADIM 1→5 dökümü) ve `examples/full-skill-walkthrough.md`
(gerçek üretim kaydı) ile birebir örtüşüyordu; iki dosyada tutmak N4 bakım yükü ve bilgi
drift'i yaratırdı. Bilgi kaybı YOKTUR.

---

## §4 Otonom Çalışma Protokolü (5 adım — pipeline)

```text
ADIM 1 GEREKSİNİM  → kısa soru-cevap: ad (lowercase-hyphen) · ana görev/domain · araçlar/API
ADIM 2 WEB RESEARCH → Truth Mode: güncel doküman oku, 2-3 kaynak teyit, varsayım YOK,
                     kayıtlar references/sources.md'ye yazılır
ADIM 3 YAPI        → v3.0 iskelet dizini kur (aşağıda) + rules.md kısıtlarını uygula
ADIM 4 İÇERİK      → SKILL.md'ye çekirdek kuralları enjekte et:
                     Zero-Hallucination · SOLID/Clean Code · OWASP Top 10:2025 ·
                     test edilmemiş iddia = kabul yok · vault (CLAUDE §7 / AGENTS §5) çelişmez
ADIM 5 KALITE      → references/checklist.md çalıştır + ölçümleri raporla + örneklerle hizala
```

### ADIM 1 — Gereksinim (soru-cevap, atlanmaz)

Minimum 3 cevap alınır; cevapsız ADIM 2'ye geçilmez (tahmin = halüsinasyon kaynağı):

| Soru | Neden zorunlu | Örnek cevap |
|------|---------------|-------------|
| Skill adı | N6: klasör = `name` 1:1; yanlış ad = keşif + envanter kırılır | `sql-optimizer` |
| Ana görev/domain | Kapsam sınırını çizer; "her şeyi yapan skill" (YAGNI ihlali) doğmasını önler | DB performans analizi |
| Araçlar/API'ler | ADIM 2'de hangi kaynağın taranacağını belirler | MySQL `EXPLAIN` |
| Çıktı biçimi | Kabul kriteri yazılmazsa ADIM 5'te "başarılı" ölçülemez | Markdown ihlal raporu |

### ADIM 2 — Web Research (Truth Mode)

1. Skill'in kullanacağı **kütüphane / API / komut / sürüm / standart** model içinden yazılmaz.
2. En az **2-3 ayrı resmi kaynak** taranır (vendor docs, standart gövdesi, resmi blog).
3. Her doğrulanan iddia `references/sources.md`'ye **ad · URL · erişim tarihi · ne doğrulandı**
   satırı olarak yazılır (kanıtsız doğrulama = doğrulanmamış sayılır).
4. Tek kalan / çelişkili iddia → `⚠️ VERIFICATION REQUIRED` etiketi; kesin yazılmaz (H001).

### ADIM 3 — Yapı

```text
.claude/skills/{skill-adi}/          ← klasör adı = name (1:1, §5-N6)
├── SKILL.md            ← Zorunlu: v3.0 frontmatter + bu dosyadaki § yapısı (§5-N1)
├── references/         ← Zorunlu: derin kural (≥1 dosya; bu skill'de 8 dosya)
├── examples/           ← Zorunlu: ≥1 çalışmış örnek (girdi → çıktı tam döngü, §5-N5)
├── scripts/            ← Opsiyonel: çalıştırılabilir betik
└── templates/          ← Opsiyonel: kod/şablon dosyaları (varsa)
```

İskelet önce kurulur ve **gövde iskeleti onaylanmadan derinlik yazılmaz** (yapı → içerik sırası).

### ADIM 4 — İçerik enjeksiyonu

Şablondan türetilen SKILL.md'ye şu çekirdekler enjekte edilir (ilgi alanı varsa):
Zero-Hallucination · OWASP Top 10:2025 · test edilmemiş iddia = tamamlanmış değil ·
vault çelişkisi = DUR + kullanıcıya sor · credential kod içine asla yazılmaz.

### ADIM 5 — Kalite geçişi

`references/checklist.md` baştan sona çalıştırılır; **ölçüm değerleri rapora yazılır**
(satır sayıları tahmin değil, `(Get-Content dosya).Count` çıktısıdır). Eksik varsa
skill "hazır" ilan edilmez — BLOCK yazılır ve nedeni tek satırda belirtilir.

---

## §5 v3.0 FORMAT SPEC (NORMATİF — bu skill formatın otoritesi)

> Aşağıdaki maddeler **zorunludur**. İhlal = skill geçersiz (ADIM 5'te yakalanır).
> Bu bölüm `references/rules.md` §5 ile **birebir aynı** normatiftir; ikisi çelişirse
> bu dosya lehinedir.

- **N1 — Kök frontmatter anahtarları SADECE:** `name`, `description`, `license`, `metadata`.
  `title` · `type` · `version` · `authority` · `mode` · `purpose` · `reference` · `triggers` ·
  `changelog` · `format` · `updated` kökte **YASAKTIR** → içerik `metadata.*` altına veya
  SKILL.md gövdesine taşınır (ör. tetikleyiciler description içine, geçmiş sürüm →
  `references/changelog.md`, başlık → gövde H1).
  *Gerekçe:* kök anahtar seti sabitlenmezse her skill kendi şemasını icat eder; araç
  (Claude Code) bilinmeyen kök anahtarı reddeder ve skill keşfedilemez hale gelir.
  *Upstream notu:* resmi liste `compatibility` ve `allowed-tools` da içerir — biz **daha
  dar** tutuyoruz (bilinçli sıkılaştırma, §5.3).

- **N2 — description:** `"Use when …"` ile başlar, **≤1024 karakter**, üçüncü şahıs ağzında
  ("I can help…" YASAK), tetikleyici Türkçe kelimeleri (`yeni skill`, `skill oluştur`,
  `skill güncelle`, `SKILL.md yaz` benzeri) içerir, XML etiketi içermez.
  *Gerekçe:* description **tek keşif yüzeyidir** — boot'ta tüm skill'lerin name+description'ı
  system prompt'a ön yüklenir; trigger kelimesi yoksa skill hiç tetiklenmez ve kimse
  varlığını fark etmez. Kötü description = görünmez skill.

- **N3 — Boyut (revize 2026-10-21, kullanıcı onaylı):**
  - SKILL.md **min 500 satır** (artık hedef taban — 2026-10-21 revizyonu),
  - SKILL.md **maks 2000 satır** (sert üst sınır — korundu),
  - skill dizini toplamı **min 1000 satır** (SKILL.md ≥500 + `references/`/`examples/` ≥500).
  - Monolit YASAK: SKILL.md = **Katman-2 orkestrasyon dosyasıdır** (akış + kural + link),
    derinlik `references/`, `examples/`, `scripts/`, `templates/`e dağıtılır.
  - Dolgu YASAK: 500 satır, **gerçek kural/gerekçe/örnek/hata-modu** ile doldurulur;
    tekrar, süsleme, "boş satır doldurma" ile sayıma ulaşılmaz (tespit = BLOCK).
  - Eski "≤~250 satır hedefi" 2026-10-21'de kaldırılmıştır; artık hiçbir yerde hedef
    olarak geçmez (yalnız tarihsel/changelog notu olarak anılabilir).
  *Upstream çelişkisi — gizlenmez:* resmi Agent Skills best-practice dokümanı SKILL.md
  gövdesi için **"under 500 lines"** önerir (token bütçesi). CoreMusic N3 bunun **alt
  sınırını** 500'e çekmiştir: CoreMusic skill'leri token ucuzluğundan değil **denetim
  ve devir teslim** için yazılır, kural metni gerekçesiyle birlikte kayıt altında olmalıdır.
  Bu bilinçli yerel sapmadır; upstream'e dönüş istenirse ADR gerekir (§5.3).

- **N4 — Link zorunluluğu:** HER `references/*.md` ve `examples/*.md` dosyası SKILL.md'den
  linkli olmalıdır. **Orphan dosya = hata** (§2/§3 tabloları bu linkin kendisidir).
  *Gerekçe:* dosya linklenmezse agent onu hiç okumaz; ölü metin ağırlık ve bakım maliyetidir.

- **N5 — `examples/` zorunlu:** ≥1 çalışmış örnek; **girdi → çıktı tam döngü**
  (kullanıcı isteği → üretilen SKILL.md/çıktı) göstermelidir.
  *Gerekçe:* tarif + örnek olmadan agent'ın ürettiği format tahmine dayalıdır; örnek
  formatın canlı kanıtıdır.

- **N6 — İsimlendirme:** klasör adı = `name` 1:1; lowercase-hyphen; dosya adı `SKILL.md`
  (BÜYÜK harf); Türkçe karakter, alt çizgi (`_`), boşluk, büyük harf YASAK;
  tire ile başlamaz/bitmez, çift tire (`--`) içermez; `name` ≤64 karakter.
  *Gerekçe:* keşif zinciri (system prompt → dosya yolu) bu adla kurulur; ad saparsa
  araç dosyayı bulamaz. *Upstream teyidi:* spec adı "parent directory name ile eşleşmeli"
  der — N6 bununla birebir örtüşür (§5.3).

- **N7 — (boşta — 3.0.0'da N7 ayrı bir madde olarak kullanılmadı; numara korunmuştur,
  yeni madde eklenmesi numara kaymasını önler).**

- **N8 — `CLAUDE.md` sidecar YASAK** skill dizinlerinde (kök, references/, templates/,
  scripts/ dahil). Talimat tek kaynaktır: SKILL.md.
  *Gerekçe:* iki kural dosyası = iki SSOT; biri güncellenince diğeri susar ve agent
  eski kuralı uygular. Kural ile derinlik ayrımı SKILL.md ↔ references/ ile kurulur.

- **N9 — Footer (her SKILL.md son satırı):**
  `*CoreMusic Skill v3.0 — metadata.version: X.Y.Z — Updated: YYYY-MM-DD*`
  Sürüm otoritesi **`metadata.version`'dur** (footer'daki X = metadata.version; footer
  ile metadata çelişirse metadata kazanır ve footer düzeltilir).

- **N10 — Eski `changelog[]` kök alanı yoktur:** sürüm geçmişi `references/changelog.md`de
  tutulur; SKILL.md'de yalnız `metadata.version` + `metadata.previous-version` kalır.

### 5.1 Frontmatter referansı (v3.0 — tek geçerli şema)

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

`metadata` için bu skill'in kendi sürüm kaydı (4.0.0) şablonudur:

```yaml
metadata:
  version: 4.0.0            # sürüm otoritesi (N9 footer buna uyar)
  previous-version: "3.0.0" # bir önceki sürüm (N10)
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: meta-skill
  tags: [skill-development, truth-mode, template]
  updated: 2026-10-21
```

### 5.2 N3 boyut kapısı — nasıl ölçülür

| Kapı | Ölçüm | Kabul |
|------|-------|-------|
| SKILL.md alt sınır | `(Get-Content SKILL.md).Count` | ≥500 |
| SKILL.md üst sınır | `(Get-Content SKILL.md).Count` | ≤2000 |
| Dizin toplamı | `Get-ChildItem -Recurse -File -Filter *.md` + satır toplamı | ≥1000 |
| Dolgu kontrolü | Aynı tekrar eden blok / süsleme taraması | 0 |

### 5.3 Upstream karşılaştırması (bilinçli sapmalar — gizlenmez)

Kaynaklar: `references/sources.md` (3 resmi kaynak, erişim 2026-10-21).

| Konu | Resmi kaynak | CoreMusic (bu otorite) | Karar |
|------|--------------|------------------------|-------|
| `description` maks uzunluk | 1024 karakter (3 kaynakta teyitli) | ≤1024 karakter (N2) | **UYUMLU** |
| `name` formatı | ≤64, lowercase/numara/tire, tire ile başlamaz/bitmez, `--` yok, klasör = ad | aynısı (N6) | **UYUMLU** |
| XML etiketi | description/name içinde yasak | yasak (N2/N6) | **UYUMLU** |
| Kök anahtar izni | `name`, `description`, `license`, `metadata`, `compatibility`, `allowed-tools` | yalnız `name`, `description`, `license`, `metadata` (N1) | **BİLİNÇLİ SIKILAŞTIRMA** |
| SKILL.md gövde uzunluğu | "under 500 lines" önerisi (token bütçesi) | min 500 / maks 2000 (N3) | **BİLİNÇLİ SAPMA** — gerekçe N3 |
| Referans dosyası derinliği | SKILL.md'den 1 seviye derin | aynı (§2/§3 tek seviye tablolar) | **UYUMLU** |
| Yol ayıracı | İleri bölü (`/`) — Windows ters bölü yasak | aynı (§7.5) | **UYUMLU** |

**Çelişki işleme kuralı:** upstream ile CoreMusic kuralı çelişirse CoreMusic lehine çalışılır
(bu dosya format otoritesidir) **ama çelişki raporda açıkça yazılır** — sessizce
yutulması yasaktır. Vault (`.ai/`) ile çelişki ise vault kazanır ve iş DURUR (§6.3).

---

## §6 Truth Mode & Güvenlik (çekirdek — bağlayıcı)

### 6.1 Web Research zorunluluğu

Skill'in kullanacağı kütühane/API/komut/standart uydurulamaz → (1) web'de ara, (2) en az
2-3 resmi kaynaktan teyit et, (3) geçersiz/deprecated'i reddet (H001), (4) kaydı
`references/sources.md`'ye yaz. Teyitsiz iddia skill'e **kesin** olarak girmez; `⚠️
VERIFICATION REQUIRED` etiketiyle girer.

### 6.2 Zero-Hallucination

Doğrulanamayan iddia → `⚠️ VERIFICATION REQUIRED`; tahmin YASAK. "Muhtemelen", "sanırım",
"eğer hafızam doğruysa" kalıpları = hata. Disk kanıtı (gerçek dosya yolu + satır) olmadan
sayısal iddia (dosya sayısı, satır sayısı, sürüm) yazılmaz.

### 6.3 Vault önce

Yeni skill `.ai/` kurallarıyla (CLAUDE §7 guardrail, AGENTS §5) çelişemez; çelişki →
**DUR + kullanıcıya sor**. Vault'a yazma yetkisi bu skill'de YOKTUR (sadece oku).

### 6.4 Security & Isolation

Ağ/dış sistem/yıkıcı eylem → kullanıcı onayı. Credential (API key/secret/token) skill
içine **asla** yazılmaz (`.env` / vault göster). `.md`/`.json`/`.log` içine secret yazımı
kesin yasaktır.

### 6.5 Registry notu

`.ai/.templates/index.md` + proje envanteri (`.claude/CONTEXT.md`) güncellemesi raporda
not edilir — **bu skill envanter dosyasını değiştirmez**, yalnızca "güncellenmeli" der
(yazma yetkisi MO/ownerdadır).

**İlgili kurallar:** Guardrail #16 (şablon) → `.ai/.templates/index.md` · Zero-Hallucination
→ `.ai/AGENTS.md` §3 · Vault-First → Guardrail #2 / `.ai/CLAUDE.md` §16 boot ·
Skill envanteri → `.claude/CONTEXT.md` · Sahiplik/yasaklar → `.claude/AGENTS.md` §3.3.

---

## §7 Neden bu kurallar? (tasarım gerekçeleri — 50 yıllık denetim perspektifi)

Kural ezberi yetmez; kural **neden var** bilinmezse doğru genişlikte uygulanamaz ve
ihlal edildiğinde farkedilmez.

### 7.1 Keşfedilebilirlik (N2/N6) — en pahalı hata sessizliktir

Bir skill tetiklenmezse **hiçbir hata vermez**; sadece yapılmaz. Bu yüzden tetikleme
hataları "sessiz hata" sınıfındadır ve test edilmeden yakalanmaz.

| Hata | Belirti | Teşhis |
|------|---------|--------|
| Trigger kelimesi yok | Skill hiç yüklenmez | description'da kullanıcının söyleyeceği kelime var mı? |
| Description 2. şahıs ağzında | Keşif tutarsız | "I can help…" → üçüncü şahıs (§5.3 upstream kuralı) |
| `name` ≠ klasör adı | Dosya bulunamaz | `name` ve dizin adı diff = 0 olmalı |
| Türkçe karakter / `_` / boşluk | Araç adı çözümleyemez | N6 karakter kümesi |
| Description >1024 | Kırpılma → trigger kaybı | `(description.Length)` ölçümü |

### 7.2 Orphan dosya (N4) — linklenmeyen metin ölü metindir

Agent, SKILL.md'den link görmeyen references dosyasını **okumaz**. Sonuç: emek verilmiş
içerik sessizce görünmez olur, bakım maliyeti sürer, birisi onu "kullanılmıyor" diye siler.
Kontrol: `references/` ve `examples/` altındaki her `.md`, SKILL.md §2/§3 tablosunda geçmiyor
mu? Geçmiyorsa orphan = hata.

### 7.3 Sidecar (N8) — iki SSOT, biri susar

`CLAUDE.md` sidecar'ı olan bir skill'de kural iki yerde yaşar. Bir güncellenir, diğeri
kalır → agent eski kuralı uygular ve **kimse hangisinin doğru olduğunu bilmez**. 2026-10-07'de
tüm skill sidecar'ları bu yüzden kaldırıldı; kural ↔ derinlik ayrımı SKILL.md ↔ references/
olarak kuruldu.

### 7.4 Monolit ve dolgu (N3) — iki zıt uç, tek kapı

| Uç | Ne olur | Savunma |
|----|---------|---------|
| Çok kısa (dolgu olmayan 120 satır) | Kural yetmez, agent tahmin eder → halüsinasyon | min 500 + min 1000 dizin |
| Çok uzun (4000 satır monolit) | Token bütçesi taşar, ilgili bölüm kaybolur | maks 2000 + derinlik references/ |
| Sahte uzun (tekrarla 500 satır) | Denetim yanılgısı; okunmaz | dolgu tespiti = BLOCK |

**500 satırı doldurmak zorunlu değildir — 500 satır GERÇEK olmak zorundadır.** Gerçek
gerekçe, gerçek hata modu, gerçek ölçüm ve gerçek örnek varsa sayı kendiliğinden gelir;
yoksa BLOCK yazılır ve "skill henüz 500 satırlık olgunluğa ulaşmadı" denir.

### 7.5 Yol ayıracı ve taşınabilirlik

Skill dosyaları ileri bölü (`/`) ile yazılır: `references/rules.md`, `scripts/check.py`.
Ters bölü (`\`) Unix ortamında hata üretir; skill taşınabilir olmalıdır.

### 7.6 Test edilmemiş iddia

"EXPLAIN çıktısı olmadan performans iddiası yok", "ölçüm satırı olmadan 'hızlı' deme".
Skill üretiminin de kendi testi vardır: ADIM 5 checklist + satır ölçümü. Ölçülmüş sayı
yazılmayan rapor = kanıtsız rapor.

---

## §8 Yaygın Hata Modları ve Düzeltmeleri

| # | Hata modu | Neden olur | Düzeltme |
|---|-----------|------------|----------|
| 1 | Dolgu SKILL.md (tekrar/süsleme) | N3 min 500 sayısal sanıldı | Gerçek gerekçe+örnek+hata-modu yaz; yoksa BLOCK |
| 2 | Orphan references dosyası | N4 link tablosu unutuldu | §2/§3 tablosuna ekle |
| 3 | Trigger kaybı (skill görünmez) | N2 tetikleyiciler silindi/ kırpıldı | description'ı ≤1024'ün altında trigger'lı yaz |
| 4 | Sidecar CLAUDE.md | Eski alışkanlık | Sil; kuralı SKILL.md'ye, derinliği references/ |
| 5 | Uydurma sürüm/API | Web research atlandı | ADIM 2'ye dön; `⚠️ VERIFICATION REQUIRED` |
| 6 | Klasör ≠ name | N6 | Klasörü yeniden adlandır (ad sabit, klasör uyar — N6.1) |
| 7 | Frontmatter'da fazla kök anahtarı | N1 | `metadata.*` altına taşı |
| 8 | Vault çelişkisi | `.ai/` okunmadan üretim | DUR + `.ai/` oku + kullanıcıya sor |
| 9 | Şablonsuz iskelet | Guardrail #16 atlandı | `templates/skill-template.md`'den türet |
| 10 | Kopya drift (punteri yerine kopya) | SSOT başka dizinde | Punteri üret; içeriği kopyalama |

---

## §9 Üretim Sonrası Rapor Şablonu (ADIM 5 çıktısı)

Rapor şu sırayla verilir; her satır **ölçümle** desteklenir (tahmin sayı yok):

```text
1. Dosya tablosu: dosya yolu → satır sayısı ((Get-Content).Count) + dizin toplamı
2. Silinen / yeni dosya listesi
3. sources.md kaynakları (ad · URL · ne doğrulandı)
4. checklist sonuçları (PASS / BLOCK — gerekçesiyle)
5. N3 revizyonunun geçtiği yerler (kanıt satırları)
6. Sapmalar + ⚠️ VERIFICATION REQUIRED kalemleri
7. Kısa not: ne değişti / ne çalışıyor / sonraki adım
```

**Yasaklar (bu skill'in kendi çalışma kuralları):** `git commit` / `git add` YASAK
(orkestratöre aittir) · `.ai/` vault dosyalarına YAZMA YALNIZ OKU · hedef dizine
`CLAUDE.md` sidecar OLUŞTURMA YASAK (N8) · klasör adı / frontmatter `name` DEĞİŞTİRİLEMEZ
(N6) · mevcut `description` tetikleyicileri SİLİNMEZ (N2) · N1-N10 diğer maddeleri
GEVŞETİLEMEZ · uydurma sürüm/API/format numarası YASAK (`⚠️ VERIFICATION REQUIRED`) ·
başka skill dizinine YAZMA YASAK.

---

## §10 Mevcut Skill Envanteri (ölçüm kanıtı — 2026-10-21)

Format otoritesi, denetlediği envanteri bilir. Sayılar tahmin değil, disk ölçümüdür
(`(Get-Content dosya).Count`). Yalnız `.claude/skills/*/SKILL.md` satır sayıları:

| Skill | SKILL.md satır | N3 alt (500) | Not |
|-------|---------------:|:------------:|-----|
| `agent-orchestrator` | 662 | ✅ | |
| `audio-engine-cpp` | 98 | ❌ | 4. dalga adayı (N3 alt sınır altı) |
| `composer-sync` | 377 | ❌ | 4. dalga adayı |
| `database-normalize-maker` | 1021 | ✅ | |
| `hardware-electronics` | 98 | ❌ | 4. dalga adayı |
| `learning-prompts` | 520 | ✅ | |
| `php-backend-standards` | 652 | ✅ | |
| `prompt-maker` | 205 | ❌ | `.archive/` hariç; 4. dalga adayı |
| `security-hardening` | 645 | ✅ | |
| `skill-maker` | bu dosya (≥500) | ✅ | format otoritesi v4.0.0 |
| `ui-analyzer` | 120 | ❌ | 4. dalga adayı |
| `ui-code-generator` | 239 | ❌ | 4. dalga adayı |
| `vault-sync-post` | 668 | ✅ | |

**Envanter özeti:** 13 `SKILL.md` (12 skill + bu otorite). N3 alt sınırı tutan: 6/13.
**Önemli sınır:** mevcut 12 skill'in N3'e uyumu BU görevin **kapsamı dışındadır** — bunlar
farklı bir üretim dalgasının (Dalga 1/3) çıktısıdır; buradaki tablo yalnız **durum
kaydıdır**, o skill'lere bu oturumda YAZMA YAPILMAZ (yasak: başka skill dizinine yazma).

**N1-N10 hızlı denetim matrisi (audit yaparken tek tek taranır):**

| Madde | Ne taranır | Hızlı komut / yöntem | İhlal sonucu |
|-------|-----------|----------------------|--------------|
| N1 | Kökte name/description/license/metadata dışı anahtar | Frontmatter bloğunu gözle tara (10 satır) | Skill geçersiz |
| N2 | "Use when" başlangıcı · ≤1024 · tetikleyici var · XML yok | Karakter sayımı + tetikleyici listesi karşılaştırma | Keşif kırığı |
| N3 | ≥500 · ≤2000 · dizin ≥1000 · dolgu yok | `(Get-Content).Count` + tekrar taraması | BLOCK |
| N4 | §2/§3 tablosu ↔ disk dosya listesi diff | `Get-ChildItem -Filter *.md` + tablo karşılaştırma | Orphan = hata |
| N5 | ≥1 example, girdi→çıktı döngüsü | Örnek dosyayı oku, "girdi/çıktı" başlıklarını ara | Üretim eksik |
| N6 | `name` = klasör · lowercase-hyphen · `SKILL.md` büyük harf | `Split-Path` + ad karşılaştırma | Keşif kırığı |
| N8 | Dizinde `CLAUDE.md` var mı | `Get-ChildItem -Recurse -Filter CLAUDE.md` | Sidecar = hata |
| N9 | Son satır footer · sürüm = metadata.version | Son satırı oku + metadata ile karşılaştır | Kayıt tutarsız |
| N10 | `changelog[]` kökte yok · sürüm geçmişi references/ | Frontmatter + `references/changelog.md` | Kayıt tutarsız |

---

## §11 Sürüm Yükseltme Kuralı (bu skill'e özgü)

Bu skill **format otoritesi** olduğu için kendi sürümü de denetime tabidir:

| Değişiklik | Sürüm etkisi | Örnek |
|------------|--------------|-------|
| N1-N10 maddesi **gevşetildi/gevşetilemez oldu** | major +1 | 3.0.0 → 4.0.0 (N3 revizyonu) |
| Yeni normatif madde / yeni zorunlu dosya | major +1 | `references/sources.md` zorunluluğu |
| Yalnız yazımda/düzendeki düzeltme | patch +1 | typo, tablo genişletme |
| Yeni örnek / yeni açıklama (normatif değil) | minor +1 | yeni examples/ dosyası |

**metadata ↔ footer ↔ changelog üçlüsü** her yükseltmede birlikte güncellenir;
üçünden biri eksik = N9/N10 ihlali. `previous-version` mutlaka yazılır (N10).

---

## §12 Teşhis Komutları (ADIM 5'i hızlandırır — repo kökünden çalıştır)

Denetim iddia ile değil ölçümle biter. Aşağıdaki komutlar Windows PowerShell içindir
(`(Get-Content).Count` ölçüm standardı — `.claude/AGENTS.md` §5/§6 ile aynı):

```powershell
# 1) N3 kapıları — SKILL.md satır sayısı (min 500 / maks 2000)
(Get-Content .claude/skills/<ad>/SKILL.md).Count

# 2) N3 dizin toplamı — tüm .md satırları (min 1000)
(Get-ChildItem .claude/skills/<ad> -Recurse -Filter *.md |
 ForEach-Object { (Get-Content $_.FullName).Count } | Measure-Object -Sum).Sum

# 3) N8 sidecar denetimi — çıktı boş = PASS
Get-ChildItem .claude/skills/<ad> -Recurse -Filter CLAUDE.md

# 4) N4 orphan denetimi — diskteki her .md §2/§3 tablosunda geçiyor mu?
Get-ChildItem .claude/skills/<ad> -Recurse -Filter *.md | Select-Object -ExpandProperty Name

# 5) N6 ad eşleşmesi — name ve klasör adı diff almalı (boş = PASS)
(Get-Item .claude/skills/<ad>).Name

# 6) N2 uzunluk — description karakter sayısı (maks 1024)
(Get-Content .claude/skills/<ad>/SKILL.md -Raw) -match '(?s)description:\s*(.+?)\r?\n[a-z]'
$Matches[1].Length

# 7) UTF-8 BOM denetimi — ilk 3 bayt EF BB BF ise BOM var = hata
Get-Content .claude/skills/<ad>/SKILL.md -Encoding Byte -TotalCount 3 -ErrorAction SilentlyContinue
```

**Dolgu tespiti (N3) otomatik değil, elle yapılır:** SKILL.md'yi satır satır tara;
aynı cümlenin tekrarı, "harika", "unutulmaz" gibi süsleme, kural tekrarı = dolgu
şüphesi. Şüpheli blok silinir; sayı kaybı varsa gerçek gerekçe/hata-modu/örnek ile
geri konur. **Dolgu ile sayı hedeflemek BLOCK sebebidir** (§5.2).

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
