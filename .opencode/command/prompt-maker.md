---
description: Prompt'u oturum başlamadan önce işle: keşif → bağlam → 100 soruluk toplu liste → niyet → ilgili talimatlar → otomatik üretim
---

Kullanıcının verdiği prompt'u oturum işi başlamadan ÖNCE işle. Henüz kod yazma, dosya değiştirme.

Tek araç kullan: **`promptmaker`**. İki fazı vardır — `action=gather` (zorunlu bağlam paketi) ve `action=assemble` (şablon doğrulama + üretim). Provider/model asla senin tarafında sabitlenmez; opencode hangi modeli seçiliyse (free modlar dâhil) o kullanılır.

## Boru hattı — 6 aşama

1. **[1] Exploration Gate** — Prompt'a giren kapsamı belirle ve salt-okunur keşfet (`read`, `grep`, `glob`, `vault`). Bu aşamada yazma yok.
2. **[2] Exploration Context** — `promptmaker` çağır: `action=gather`. Root md'ler + `.ai` envanteri bütçe içinde (varsayılan 50.000 karakter) paketlenir. Eksik dosya raporlanır, uydurulmaz (G5). Ardından **`vault` aracıyla `.ai` verilerini yükle**: `action=list` (vault dosyalarını gör) → `action=context` (MEMORY/todos/log oturum durumu) → `action=read` (ilgili spec/rules/decision dosyaları). Okumadığın dosyayı `vaultRefs`'e yazma (G5).
3. **[3] Prompt Maker — Toplu 100 soru listesi.** Önce `.ai` vault'unu `vault` aracıyla yükle: `action=list` ile dosyaları gör, `action=read` ile ilgili dosyaları oku (uydurma — G5). `/eli10` uzun paragrafını ve görevlere bölme taslağını hazırla (bunlar sorulmaz, model üretir). Sonra:
   - **Tek mesajda 100 soru listele** (proje özgü; generic soru seti yasak). Her soru numaralı olsun: `1. [soru]` … `100. [soru]`.
   - **CEVAP BEKLE.** Kullanıcı cevaplarını yazana kadar sonraki aşamaya geçme.
   - Cevaplar geldiğinde: `Netlik: NN%` (0-100, dürüst) yaz ve **hemen [6]'ya geç** (`promptmaker` `action=assemble` çağır; ayrıca "üret" deme, onay isteme).
   - Kurallar: (a) tek mesajda 100 soru listele, teker teker sorma; (b) cevaplar gelmeden assemble çağıрма; (c) kullanıcı "yeter", "üret", "devam" derse cevapları topla ve [6]'ya geç; (d) `mode=questions` modunda en az 1 soru/cevap zorunlu (0 soru ile assemble reddedilir).
4. **[4] Intent Router** — Cevaplara göre niyeti sınıflandır ve yalnızca ilgili `.ai` vault/yapılandırma dosyalarını seç; ilgisiz dosyayı yükleme.
5. **[5] Instruction** — Seçilen ilgili dosyaları oku. Aynı 50K bütçeyi aşma; aşıyorsa ilgisiz dosyayı düşür.
6. **[6] System Prompt** — `promptmaker` çağır: `action=assemble`. Araç şablonu doğrular (boş bölüm, cevap/soru sayısı uyumsuzluğu reddedilir) ve footer'ı ekler. Footer'sız çıktı geçersizdir; bu adımdan sonra oturum işi (kodlama) başlar.

## `promptmaker` girdileri (assemble)

| Alan | Zorunlu | Not |
|------|---------|-----|
| `prompt` | ✅ | Kullanıcının orijinal prompt'u, aynen |
| `eli10` | ✅ | Uzun /eli10 paragraf |
| `summary` | ✅ | İstenilen şey, düz ifade |
| `vaultRefs` / `codeRefs` | – | `.ai` ve proje kodu referansları |
| `questions` / `answers` | eşit sayıda | Sayı uyuşmazsa araç hata döner |
| `tasks` | ✅ (en az 1) | `{ title, estimate }`, örn. `30 dk` — `format`/`questions` modunda atlanabilir |
| `mode` | – | `format` \| `questions` \| `split` \| `full` (varsayılan) \| `resume` — hangi zorunlulukların geçerli olduğunu belirler |
| `budget` | – | Sadece `gather` için, varsayılan 50000 |

## Modlar

| Mod | Ne yapar | Zorunlu alanlar |
|-----|----------|-----------------|
| `format` | Yazım/MD düzeltmesi, tek adımda biter (soru yok) | prompt + eli10 + summary |
| `questions` | Asking Questions: en az 1 proje özgü soru/cevap zorunlu | + questions/answers (≥ 1) |
| `split` | Görevlere bölme | + tasks (≥ 1) |
| `resume` | Kayıtlı prompt'a devam (todos/impl-plani üzerinden) | + tasks (≥ 1) |
| `full` (varsayılan) | Hepsi birlikte | + tasks (≥ 1) |

## Çıktı şablonu (zorunlu)

Araç `assemble` ile bu şablonu üretir; sen elle yazma, alanları doldurup aracı çağır:

```
[eli10 uzun paragraf]

[İstenilen şey nedir — açıklama]

Referanslar (.ai vault):
- [dosya]

Referanslar (proje kodu):
- [dosya/simge]

Kullanıcı kararları doğrultusunda:
Sorulan sorular:
1. [soru]
Soruların cevapları:
1. [cevap]

Orijinal prompt:
[kullanıcının girdiği orijinal prompt'un aynısı]

Görevlere böl:
1. görev — tahmin: X dk

✅ Prompt Cevaplarla İşlendi
Dil: Türkçe | Görev Sayısı: [n] | Cevap: [n] | Mod: [mod]
```

Son satır `✅ Prompt Cevaplarla İşlendi` olmadan iş bitmiş sayılmaz.

## Yasaklar

- Hardcoded provider/model yazma.
- `gather` çıktısında olmayan dosya, API, yöntem veya bağımlılık uydurma; bulamazsan `UNKNOWN` / `DOĞRULAMA GEREKLI` yaz.
- İlk aşamalarda dosya yazma, kod üretme; salt-okunur keşif.
