# 2026-10-18 - Figma Veri Kurulumu: 462:5874 tazeleme + 3175:13501 (Linux 3840) yeni sayfa

**Talep:** "figma dataı kur ve yeni bütün verileri çek api den... kaydet başla eskili olanları da güncelle yenileri başla .png den referans ver"
**Durum:** ✅ Tamamlandı (working tree - **commit YOK**, orkestratöre ait)

## Kapsam

| Node | Sayfa | Karar | Sonuç |
|---|---|---|---|
| `462:5874` | Linux 1920 (güncellendi) | tazeleme | sürüm `2411290726990257863` → `2411746660332637475` · lastModified `2026-10-18T18:01:58Z` |
| `3175:13501` | **Core Music - Linux 3840** (yeni) | yeni doc | 1 frame `3175:13502` 3840×2160 · 13 çocuk · 814 node (CANVAS dahil) |

**Node sayım zinciri**
- 1920: 3654 node (CANVAS dahil) · 3649 ağaç satırı · TEXT mismatch 488 · AL 98 · efekt 1464 (gizli 3) · INSTANCE 287 → toplam 3644 → **3654 (+10, yalnız Home `2831:13747` 803 → 813)**
- 3840: 814 node · 812 ağaç satırı · TEXT 222 · INSTANCE 53 · VECTOR 152 · TEXT mismatch 196 · AL 47 · efekt 363

## Üretilen / güncellenen (13 yüzey)

1. **Ham JSON** — `reference/figma/raw/page-462-5874.json` (3,424,959 B · 3653 + CANVAS) + `raw/page-3175-13501.json` (814,298 B · 814); eski 462 yedekte `C:\temp\opencode\page-462-5874-OLD-20261017.json`.
2. **PNG export 5/5 OK** — `.png/home-1920/` 4 dosya (**scale=2**) + `.png/home-3840/Linux 3840 - Home.png` (6,665,774 B · **scale=1**); id-prefixed arşive 5 dosya eklendi → `reference/figma/png` **149 → 154**.
3. **Token** — `tokens/tokens-3840.json` ilk kez doldu (322,643 B) · `tokens-1920.json` tazelendi; yedek `C:\temp\opencode\tokens-backup-20261018/`.
4. **`gen-extracted.mjs`** — parametrik (`--file/--node/--page/--title/--viewport/--created`), dinamik bölüm no (`SN = 4+frames.length`, `QRN = SN+1`), Ham-JSON SSOT satırı `PAGEFILE`'e bağlandı; `node --check` SYNTAX OK.
5. **`extracted-462-5874.md` v1.2.0** — 713,086 B · 4101 satır · wiki-link 16 · PNG 4/4 · §2.2 diff tablosu · statik prosa id'lerin tamamı mevcut; eski doc yedekte `C:\temp\opencode\extracted-462-5874-OLD-20261018.md` (710,478 B); **verify OK**.
6. **`extracted-3175-13501.md` v1.0.0 (yeni)** — 1055 satır · wiki-link 16 · PNG 1/1 · §2.2 boş-öncül ilişkisi `sec22-3175.md`'den · §5.3'te 1920 hygiene notları basılmadı; **verify OK**.
7. **`.png/home-3840/CLAUDE.md`** (v1.0.0, yeni) · **`.png/CLAUDE.md`** envanter 19 → **26 PNG / 5 dizin** · **`.png/home-1920/CLAUDE.md`** Last Updated.
8. **`01-mockup-index.md` v6.4.0** (271 satır) — başlık 26 PNG · `source_of_truth` + §2 ağacı `home-3840` · §3.5 "Home Dashboard — Linux 4K (1 PNG)" · CatID tablosuna `3175:13501` · Quality Report (26 PNG · arşiv 154 · 3 viewport · 4 spec'siz).
9. **`screens/00-ascii-art-index.md` v6.4.0** — `total_png_mockups: 26` · changelog · §7 madde 6 spec'siz PNG 3 → **4** · §7 QR "PNG eşleştirme 26/26".
10. **`reference/04-verification.md`** — §6.5'e 2026-10-18 gate bloğu · §8 #4 satırı kapandı (3840 doldu; `tv` hâlâ boş).
11. **`reference/figma/_extraction-notes.md`** — 2026-10-18 extract kaydı append edildi (+4,964 B → 26,278 B / 369 satır), verify OK.
12. **`reference/legacy-inventory.md` v1.1.0** — PNG envanteri 19 → **26** (+`admin-1920` 3 + `home-3840` 1), `01-mockup-index` satır 183 → 271, `.png/CLAUDE.md` "eski ad" notu gerçeğe göre kapatıldı; **14 eklenen / 8 silinen satır** (numstat ile doğrulandı).
13. **Genel envanter notu** — `tokens/design-tokens-master.md` ve `tokens/color-palettes.md` grep ile tarandı, "3840 boş" iddiası **içermiyor** → değişiklik gerekmedi.

## Gate'ler (repo kökünden, 2026-10-18)

| Gate | Sonuç | Kanıt |
|---|---|---|
| `screens-frontmatter-check.ps1` | ✅ PASS | `dosya: 24 | sorunlu: 0` |
| `wiki-link-check.ps1` | ✅ PASS | `kontrol edilen link: 363 | kırık: 6` = §13.7 bilinen 6 sahte (exit 0) |
| `figma-tokens.ps1` (Faz 9) | ✅ PASS | `toplam=7 | pass=6 | bos=1 | fail=0 | bos_bp=tv` + `BITTI` — **3840 ilk kez PASS** (node=815 · c=278 t=168 s=153 r=64 sp=5 sz=634); 1920 PASS (node=28874 · korunan eski=2469) |
| `kalip-abc-check.ps1` | ❌ **FAIL (exit 1)** | `A:0 B:0 C:1` — C:1 = `prompt/component/aaaa.md` (H2 8/1, H3 sıra, H1 kalıp dışı) |
| `vault-utf8-writer.mjs scan` | ✅ `.ai/ui-design` **0 kirli** | `.ai` geneli 105 kirli → tümü önceden var (aşağıda) |

## Bulgular (kapsam dışı - onay/karar bekliyor)

1. **`kalip-abc` C:1 önceden var** — `prompt/component/aaaa.md` 2026-09-30 tarihli, HEAD'de tracked ve **değişmemiş**; baseline (2026-09-29, C:51/0) sonrası eklenmiş. Bu görevin manifestinde olmadığı için **dokunulmadı**. Not: script'in `SONUC ... (0/0/0 = GECTI)` satırı statik lejanttır, gerçek karar `exit $exit` = 1.
2. **`.ai/log.md` 194 literal U+FFFD + CJK 2** — `git show HEAD:.ai/log.md` (raw bayt) ile de **194** → bozulma önceden commit edilmiş; append-only kuralı gereği dokunulmadı.
3. **`.ai/MEMORY.md` 1279 mojibake · `architecture/**` 100+ dosya** (BOM 7 · mojibake · 3 CJK) — önceden var, kapsam dışı.
4. **`.ai/AGENTS.md` §7.2 "(19 PNG)"** iddiası bayat (gerçek 26) — SSOT boot dosyası olduğu için yalnızca işaretlendi, **sahip onayı ister**.
5. **`.ai/.templates/ui-design/screen-spec-template.md` L42** "19 PNG / home-1920 1" — **sahip onayı iletmedi, değiştirilmedi**.
6. **Paralel oturum uyarısı** — `.png/home-3840/` ve `.png/home-1920/Home.png` bu oturumdan önce (20:58) başkası tarafından yazılmış; PNG'ler sürümle senkron yeniden export edildi.
7. **Teknik not (aracı davranışı)** — `edit` tool'u dosyayı UTF-8 **BOM'lu** yazabiliyor (`legacy-inventory.md` → `EF BB BF`); `vault-utf8-writer.mjs repair` ile temizlendi ve scan'e takılmadı. PowerShell 5.1 `>` redirect'i çıktıyı **UTF-16LE** olarak yazar → bayt/encoding ölçümünde `git show ... > dosya` **kullanılmaz** (bu oturumda 2 kez yanılttı); ham bayt için `node -e "child_process.execSync('git show ...')"`.
8. **`legacy-inventory.md` UTF-16 iddiası RED** — PowerShell artifactsi; `git diff --numstat` = `14 8` yani diff yalnızca kasıtlı editler.

## Dosya yedekleri (`C:\temp\opencode\`)

`page-462-5874-OLD-20261017.json` · `extracted-462-5874-OLD-20261018.md` · `tokens-backup-20261018/` · `exnotes-20261018.md` · `sec22-3175.md` · `gen-extracted.mjs` · `vault-backups/legacy-inventory.md.bak`

## Sonraki adım

Commit **atılmadı** (orkestratöre ait). Onay bekleyenler: (a) bu görevin commit'i, (b) `screen-spec-template` L42 + `.ai/AGENTS.md` §7.2 envanter düzeltmesi, (c) `prompt/component/aaaa.md` Kalıp C uyumu, (d) `.ai/log.md` + `MEMORY.md` + `architecture/**` mojibake onarımı.
