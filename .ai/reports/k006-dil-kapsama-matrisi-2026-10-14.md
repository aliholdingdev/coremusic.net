---
title: "K006 Yapay Zeka — Dil Kapsama Matrisi Raporu"
type: report
category: reports
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/K006-yapay-zeka/ — tarama 2026-10-14"
updated: 2026-10-14
tier: 2
domain: architecture
ssot: false
risk: medium
owner: "Vault Steward"
depends-on: [".ai/architecture/K006-yapay-zeka/index.md"]
---

# K006 — 13 Dil Kapsama Matrisi (uygulama sonrası ölçüm)

> **Amaç:** Kullanıcı kapsama kararı (2026-10-14): **13 dil yüzeyi** — gruplar 1–4 onaylandı
> (C/C++/Rust · Python/Go/C# · PHP/JS-Node · Zig/Swift/Java-Kotlin) + eksik tamamlama →
> **Bash · PowerShell** eklendi = standart 13 dil (K000 planı §Girdi kararı ile birebir).
> **Yöntem:** açılış-fence (`^```dil`) sayımı — öncesi/sonrası disk ölçümü, tahmin yok.
> **Üretim planı:** `C:\Users\MARHAN\.opencode\plan\K006-13-dil-kapsama-plan.md` (Faz 0–3).

## §1 Kapsam

| Öğe | Değer |
|---|---|
| Taranan dizin | `.ai/architecture/K006-yapay-zeka/` (9 çocuk düğüm + 1 kök) |
| Yazım yüzeyi | 9 × `*ornekler.md` (çocuk) — kök `yapay-zeka-kod-ornekleri.md` **dokunulmaz** (§4 G4) |
| Ölçüm birimi | Kod bloğu (açılış fence) sayısı · 2026-10-14 |
| Öncesi → sonrası | **30 → 86 fence** (+56 blok) · git: **1526 ekleme / 0 silme** (9 dosya) |
| Fazlar | Faz 1 (5 dosya) + Faz 2 (4 dosya) — batch ≤ 8 kuralına uygun, paralel subagent |

## §2 ANA MATRİS — 13 dil × 10 dosya (uygulama SONRASI blok sayısı)

| # | Dosya | C | C++ | Rust | Py | Go | C# | PHP | JS | Zig | Swift | J/K | Bash | PS | Dil |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | `local-inference-ornekler` | 0 | 1 | 0 | 1 | 0 | 1 | 1 | 1 | 0 | 0 | 1 | 0 | 0 | 6 |
| 2 | `inference-ornekler` | 1 | 1 | 1 | 1 | 0 | 1 | 2 | 1 | 0 | 0 | 1 | 0 | 0 | 8 |
| 3 | `model-management-ornekler` | 0 | 0 | 0 | 1 | 1 | 1 | 1 | 1 | 0 | 0 | 0 | 1 | 1 | 7 |
| 4 | `evaluation-ornekler` | 0 | 0 | 0 | 1 | 1 | 1 | 1 | 1 | 0 | 0 | 1 | 1 | 0 | 7 |
| 5 | `audio-intelligence-ornekler` | 1 | 1 | 1 | 1 | 0 | 1 | 3 | 1 | 0 | 1 | 1 | 0 | 0 | 9 |
| 6 | `testing-ornekler` | 0 | 0 | 0 | 1 | 1 | 2 | 1 | 1 | 0 | 0 | 1 | 1 | 0 | 7 |
| 7 | `recommendation-ornekler` | 0 | 0 | 0 | 1 | 1 | 1 | 3 | 1 | 0 | 0 | 1 | 1 | 0 | 7 |
| 8 | `personalization-ornekler` | 0 | 0 | 0 | 1 | 1 | 1 | 3 | 1 | 0 | 0 | 1 | 0 | 1 | 7 |
| 9 | `music-analysis-ornekler` | 1 | 1 | 1 | 1 | 1 | 1 | 2 | 1 | 0 | 1 | 0 | 1 | 0 | 10 |
| — | `yapay-zeka-kod-ornekleri` (kök) | 0 | 0 | 0 | 0 | 0 | 0 | 9 | 0 | 0 | 0 | 0 | 0 | 0 | 1 |
| — | **TOPLAM (blok)** | **3** | **4** | **3** | **9** | **6** | **10** | **26** | **9** | **0** | **2** | **7** | **5** | **2** | — |

> J/K = Java/Kotlin (dosyalarda `java` fence'i; Kotlin-özel blok yok — G3). Kök satırı
> **kapsama dışı, dokunulmaz** (0 değişiklik). C# Testing'de 2 blok (ONNX doğrulama çifti).

## §3 EKLEME KIRILIMI — 56 yeni blok (dosya × dil)

| Dosya | Eklenen bloklar (dil) | Adet |
|---|---|---|
| local-inference | Python · C# · Java · JS · C++ | 5* |
| inference | C · C++ · C# · Java · JS · Rust | 6 |
| model-management | Python · Bash · PowerShell · Go · C# · JS | 6 |
| evaluation | Python · C# · Java · JS · Go · Bash | 6 |
| audio-intelligence | C++ · Rust · JS · Java · C# · Swift · C | 7 |
| testing | Python · C# · Java · JS · Go · Bash | 6 |
| recommendation | Python · C# · Java · JS · Go · Bash | 6 |
| personalization | Python · JS · C# · Java · Go · PowerShell | 6 |
| music-analysis | C++ · C · Rust · JS · Swift · C# · Go | 7 |
| — | **TOPLAM** | **55 + 1\*** |

> \* local-inference subagent'ı C için "adımlar metinsel" notuyla blok raporladı; grep
> sayımında `c` fence'i **yok** (yalnız onnxruntime C API adımları metin olarak eklendi) —
> disk ölçümü esastır: dosya 6 blok/5 yeni dil. Toplam yeni blok = **56** (86 − 30).
> Her blok: resmi URL (webfetch **HTTP 200** doğrulamalı, 2026-10-14) +
> `[ORİJİNAL ÖRNEK · derlenmedi]` etiketi; 404→alternatif resmi kaynak dönüşleri subagent
> raporlarında kayıtlı (ör. `with-javascript/node.html`, DL4J `quickstart.md`, ML.NET
> `movie-recommendation`).

## §4 TEMEL BULGU (zero-hallucination — disk kanıtlı)

| # | Bulgu | Kanıt |
|---|---|---|
| **G1** | **Zig = 0 blok / 9 dosyanın tamamında** | ` ```zig ` = 0 · N/A gerekçesi: ZIG AI/ML DOĞRULANMIŞ RESMİ KAYNAK YOK (onnxruntime.ai resmi API dizininde Zig binding'i listelenmiyor) — uydurma yazılmadı |
| **G2** | **Kotlin = 0 blok** — JVM yüzeyi Java bloklarıyla kapsandı (7 dosyada `java`) | Kotlin-özel resmi doğrulanmış AI örneği bulunamadı; Java = JVM binding |
| **G3** | **C# ve JS 9/9 çocuk dosyada** — en geniş iki dil (ONNX Runtime binding yoğunluğu) | §2 sütunları |
| **G4** | Kök `yapay-zeka-kod-ornekleri.md` = 9 php, **0 değişiklik** | Dosya sözleşmesi `status: verified` + "her blok gerçek disk içeriğidir" (yalnız repo kodu) — dış resmi örnek ihlal olurdu (plan §3) |
| **G5** | En zengin dosyalar: music-analysis (10/13) · audio-intelligence (9/13) · inference (8/13) | §2 "Dil" sütunu |
| **G6** | 0 silme / 0 mevcut satır değişikliği | `git diff --numstat`: 9 dosya, 1526 eklemesı, **0 silme** |

## §5 DİL BAZINDA ÖNCESİ → SONRASI

| Dil | Önce | Sonra | Durum |
|---|---|---|---|
| C | 0 | 3 | ✓ eklendi (inference · audio · music) |
| C++ | 0 | 4 | ✓ eklendi (local · inference · audio · music) |
| Rust | 0 | 3 | ✓ eklendi (inference `ort` · audio `cpal` · music `spectrum-analyzer`) |
| Python | 3 | 9 | ✓ +6 (dosyada olmayan 6 dosyaya) |
| Go | 0 | 6 | ✓ eklendi (gonum · net/http · mlflow REST) |
| C# | 0 | 10 | ✓ eklendi (9 dosya, testing 2) |
| PHP | 26 | 26 | — mevcut (repo-kanıtı, değişmedi) |
| JavaScript/Node | 0 | 9 | ✓ eklendi (9 dosya, onnxruntime-node/transformers.js/Web Audio) |
| Zig | 0 | 0 | **N/A** — doğrulanmış resmi AI kaynak yok (G1) |
| Swift | 0 | 2 | ✓ eklendi (audio · music — AVAudioEngine) |
| Java/Kotlin | 0 | 7 | ✓ Java eklendi (7 dosya) · Kotlin N/A = JVM (G2) |
| Bash | 1 | 5 | ✓ +4 (mlflow CLI · CI test scripti) |
| PowerShell | 0 | 2 | ✓ eklendi (model-management · personalization) |

**Dil başına asgari görünürlük (K000 standardı):** 13 dilin **11'i ≥ 1 blokla temsil**;
Zig ve Kotlin gerekçeli N/A (G1/G2) — sessiz fallback yok, her N/A dosya-raporunda kayıtlı.

## §6 Ekleme sözleşmesi (uygulanan — K000 §4 ile aynı)

Her blok: HTTP 200 doğrulanmış **resmi** URL + `[ORİJİNAL ÖRNEK · derlenmedi]` + tarih
`2026-10-14` · yer tutucu yasak · sessiz fallback yasak · 0 silme · `git commit` subagent
tarafından ATILMADI (orkestratöre ait). Kaynak havuzu: `ai-master.md` §11/§16 +
onnxruntime.ai · mlflow.org · scikit-learn.org · essentia.upf.edu · librosa.org ·
docs.rs · pkg.go.dev · learn.microsoft.com · developer.apple.com · deeplearning4j.konduit.ai ·
huggingface.co/docs/transformers.js · docs.github.com.

## §7 Açık sorular

| # | Soru | Sahip |
|---|---|---|
| O1 | Kotlin-özel blok (ör. ktor/djl-kotlin Kotlin API'si) sonradan eklenir mi, yoksa JVM=Java kararı kalıcı mı? | Vault Steward |
| O2 | Zig ekosolgunlaşıp resmi doğrulanmış AI binding'i çıktığında bu matris yeniden açılır mı (periyodik tarama)? | Vault Steward |
| O3 | Commit: 9 dosya + bu rapor + plan (`~/.opencode/plan/K006-13-dil-kapsama-plan.md`) tek seferde atılır — onay? | orchestrator |

## §8 İlişkili dosyalar

- Üretim planı: `C:\Users\MARHAN\.opencode\plan\K006-13-dil-kapsama-plan.md`
- K000 öncülü rapor: [`k000-dil-kapsama-matrisi-2026-10-14.md`](k000-dil-kapsama-matrisi-2026-10-14.md)
- Kapsam dizini: [`../architecture/K006-yapay-zeka/index.md`](../architecture/K006-yapay-zeka/index.md)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
