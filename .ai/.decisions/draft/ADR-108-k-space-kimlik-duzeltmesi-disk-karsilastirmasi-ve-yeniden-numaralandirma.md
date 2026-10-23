---
reference_doc: .ai/.templates/adr/adr-nygard-template.md (v1.0.1 — Guardrail #16)
title: "CoreMusic — K-Space Band-1 Kimlik Düzeltmesi: Disk↔Registry↔Frontmatter Çelişkisi ve Yeniden Adlandırma/Numaralandırma Kararı"
type: adr
category: decisions
date: 2026-10-17
updated: 2026-10-17
status: draft
version: 1.2.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  related_adr: [".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md"]
  source_of_truth: ".ai/architecture/index.md §5 (registry) + disk Test-Path 2026-10-17 + frontmatter node_id okuması"
group: mimari
---

# CoreMusic — K-Space Band-1 Kimlik Düzeltmesi ve Yeniden Adlandırma Kararı

**Durum:** draft — kullanıcı onayı bekliyor (onay → `accepted/`, redd → `rejected/`)
**Tarih:** 2026-10-17
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili:** ADR-097 (K-Space V3 kurulum + isimlendirme) · ADR-097 K3/K6 (imla korunumu + çakışma kayıtları)

---

## §1 Bağlam (Context)

### §1.1 Tetikleyici
`.ai/architecture/K014-media/` klasör adı ile içeriğindeki `node_id: K015-media` arasındaki çelişki kullanıcı tarafından raporlandı. Keşif sırasında çelişkinin tek olmadığını, **Band-1'de 4 klasörde ve registry'de** sistematik bir kopukluk olduğu tespit edildi.

### §1.2 Gerçek durum (ground truth — 2026-10-17 disk ölçümü)

Yöntem: `.ai/architecture/` altı K* klasörleri listelendi; her klasörün `index.md` frontmatter'ındaki `node_id` okundu; `index.md` §5 registry tablosuyla karşılaştırıldı.

| # | Disk klasörü (gerçek ad) | İçerik `node_id` (frontmatter) | Registry §5 (index.md) | Uyuşmazlık |
|---|---|---|---|---|
| 1 | `K002-electroncis` | K002-electroncis | *"K002 GAP — hiç yok"* | Registry'de disk'te olan yok sayılıyor |
| 2 | `K010-network` | **K014-network** | K014-network | **Klasör ≠ içerik** (içerik+registry aynı) |
| 3 | `K0100-identitiy` | **K010-identitiy** | K010-identitiy | **Klasör 4 hane (şema ihlali) ≠ içerik** |
| 4 | `K014-media` | **K015-media** | K015-media | **Klasör ≠ içerik** (içerik+registry aynı) |
| 5 | `K015-AI` | K015-AI | **K016-AI** | **Frontmatter+klasör ≠ registry** |
| 6 | `K016-**` (yok) | — | K016-AI | Registry'de disk'te olmayan düğüm |
| 7 | `K020-adr` · `K020-comere` (yok) | — | §5 satır 20-22 | Registry'de disk'te olmayan 2 klasör |

### §1.3 Analiz bulgusu (kritik)

- **Frontmatter (`node_id`) + registry + 100+ çapraz referans aynı numaralarda birleşir:**
  - `K014-network` · `K015-media` · `K010-identitiy` — hem frontmatter hem registry hem de komşu çapraz referanslar (örn. `media-bagimliliklar.md` §19'da tek tek sayılan K014↔K015 satırları, `network-*.md`, `K013-*/synchronization`) bu numaraları kullanır.
- **Yalnız 4 disk KLASÖR ADI** bu gerçeğe aykırı: `K010-network`, `K014-media`, `K0100-identitiy`, ve AI için `K015-AI` (registry `K016-AI` diyor).
- **Sonuç:** Bu bir "tüm ağacı yeniden numarandırma" değil; **yarım kalmış bir klasör yeniden adlandırması**. Birisi klasörleri taşımayı başlamış (`network→K010`, `media→K014`, `identity→K0100`) ama frontmatter'ı, registry'yi ve çapraz referansları güncellememiş. `K0100-identitiy` bu yarı geçişin izidir: `K010` network'e verildiği için identity sıkıştırılmış.

### §1.4 Kullanıcı direktifi (2026-10-17)
Kullanıcı önce "K014-media doğru", sonra "tüm ağacı yeniden numaralandır (B)", ardından eşleme olarak `k14 media · k15 ai · k10 network` verdi. Bu eşleme **disk klasör adlarını** takip eder. Ancak keşif, disk klasör adlarının azınlıkta (4 klasör) olduğunu, frontmatter+registry+referansların çoğunlukta olduğunu gösterdi. Bu ADR, iki yolu da kanıtla sunar ve Vault Steward onayına bırakır.

---

## §2 Karar (Decision) — İKİ SEÇENEK (onay bekliyor)

### §2.1 Seçenek A — Klasör adlarını içeriğe hizala (ÖNERİLEN · ucuz · düşük risk)

4 disk klasör adını, zaten her yerde (frontmatter + registry + çapraz referanslar) geçerli olan numaralara çevirir. **Hiçbir `node_id`, hiçbir çapraz referans, hiçbir registry satırı DEĞİŞMEZ.** Yalnız `git mv` ile 4 klasör adı.

| Eski disk adı | Yeni disk adı (frontmatter/registry ile aynı) | Gerekçe |
|---|---|---|
| `K010-network` | `K014-network` | frontmatter `node_id: K014-network` + registry §5 |
| `K014-media` | `K015-media` | frontmatter `node_id: K015-media` + registry §5 |
| `K0100-identitiy` | `K010-identitiy` | frontmatter `node_id: K010-identitiy` + registry §5 (4 hane şema ihlali düzeltilir) |
| `K015-AI` | `K016-AI` | registry §5 `K016-AI` — **frontmatter K015-AI → K016-AI olarak güncellenir** (tek frontmatter değişikliği) |

**Ek düzeltmeler (A seçeneği):**
- `K002-electroncis`: disk'te var → registry §5 ve §6'daki "K002 GAP" satırı **disk gerçeğiyle** düzeltilir (GAP kaydı arşivlenir, K002 artık tanımlı).
- `K020-adr` · `K020-comere`: registry'de var, disk'te yok → ya klasörler üretilir ya da registry satırı `⚠️ disk'te yok` olarak işaretlenir (Vault Steward kararı — §6 açık soru).

**Etki alanı:** 4 klasör adı (`git mv`) + 1 frontmatter `node_id` (K015-AI→K016-AI) + registry §5/§6 düzeltmeleri. **0 çapraz referans değişir.**

### §2.2 Seçenek B — İçeriği disk klasörlerine göre yeniden numaralandır (pahalı · yüksek risk)

Kullanıcının `k14 media · k15 ai · k10 network` eşlemesini esas alır. Bu, disk klasör adlarını doğru kabul edip **frontmatter + registry + 100+ çapraz referansı** yeniden yazar.

| Hedef (disk esaslı) | Değişecek yüzey |
|---|---|
| media → `K014-media` | 15 dosyadaki 100+ `K015-media`/`K015` satırı + komşu `K014↔K015` çapraz referanslar (media-bagimliliklar §19: network-*.md, K013-*/index.md, K014 index.md …) |
| network → `K010-network` | frontmatter `K014-network` → `K010-network` + tüm `K014-network` bağımlılık satırları (media · K013 · K011 · K012 …) |
| AI → `K015-AI` | registry `K016-AI` → `K015-AI` + tüm K016 referansları |
| identity → `K0100-identitiy`? | `K010` artık network'te; identity 4 hane `K0100`'de mi kalır (şema ihlali sürer) — **belirsiz** |

**Risk:** Yüzlerce bağımlılık satırı, ADR-097 K3 KURAL 1 (imla/CONFLICT korunumu) ihlali, ve `K0100` şema anomalisinin ya kalıcılaşması ya da yeni bir numara (örn. identity için boş bir slot) bulmayı gerektirmesi. Tersine çevirme maliyeti yüksek.

---

## §3 Alternatifler

| # | Alternatif | Sonuç |
|---|---|---|
| A | **Klasör adlarını içeriğe hizala (4 `git mv`)** | ✅ ÖNERİLEN — 0 çapraz referans değişir; en ucuz, en geri alınabilir |
| B | İçeriği disk klasörüne göre yeniden numaralandır (100+ satır) | ❌ pahalı; ADR-097 K3 ihlali; `K0100` anomalisini çözmez |
| C | Hiçbir şey yapma, yalnız bu ADR'yi kayıt olarak tut | ⚠️ kabul edilebilir ama 4 klasör + 1 registry tutarsızlığı kalır |
| D | Karışık: yalnız `K014-media→K015-media` klasörünü düzelt, gerisini bırak | ⚠️ kısmi; `K010-network`/`K0100`/AI çakışmaları açık kalır |

---

## §4 Sonuçlar (Consequences)

**Seçenek A (öneri) — Olumlu:**
- 100+ mevcut çapraz referans, frontmatter ve registry **bozulmaz**; tek tutarlı kimlik (`K014-network` · `K015-media` · `K010-identitiy` · `K016-AI`) tescillenir.
- `K0100-identitiy` 4 hane şema ihlali düzeltilir (→ `K010-identitiy`).
- `K002-electroncis` registry'de "GAP" yanlışlığı düzeltülür.
- İş 1 oturumda, geri alınabilir (`git mv`), ADR-097 K3'e uygun.

**Seçenek A — Olumsuz / risk:**
- Kullanıcının baştaki "K014-media doğru" içgüdüsü ile ters düşer — bu ADR'nin amacı, kanıtla (frontmatter+registry+referans çoğunluğu) bu tercihi yeniden değerlendirmek.
- `K020-adr`/`K020-comere` disk'te yokluğu (§6/4) A'da da ayrı karar ister.

**Seçenek B — Risk:** yüzlerce satır değişikliği · ADR-097 K3 ihlali · `K0100` anomalisinin çözümü belirsiz · yüksek geri alma maliyeti.

---

## §5 Açık Sorular (VERIFY REQUIRED — Vault Steward onayı)

| # | Soru | Etiket |
|---|---|---|
| 1 | Seçenek A mı B mi? (bu ADR'nin ana kararı) | ✅ **ÇÖZÜLDÜ → Seçenek B** (§9 — Vault Steward override: `k14 media · k15 ai · k10 network`, disk doğrudur; §8'deki A uygulaması geri alındı) |
| 2 | `identity` düğümü hangi numarada kalmalı: `K010` (A) · `K0100` (4 hane, B) · yeni boş slot (örn. `K002`/`K021`)? | ✅ **ÇÖZÜLDÜ → `K0100-identitiy`** (§9.1 — B; `K010` network'e verildi; 4-hane anomalisinin korunması bilinçli kabul §9.3/1) |
| 3 | `K015-AI` (AI altyapısı) mi `K016-AI` mı? Frontmatter `K015-AI`, registry `K016-AI`. Ayrıca `K006-yapay-zeka` (ML ürün) de var → 3 AI adayı; ama kapsam ayrımı v2 §2.6/§2.16'da tanımlı (K006=ML ürün, AI altyapısı ayrı). | ✅ **ÇÖZÜLDÜ → `K015-AI`** (§9.1 — B; `K015` slotu media'dan AI'a geçti, media `K014`'e taşındı; K006/K015 ayrımı v2 §2.6/§2.16'da meşru) |
| 4 | `K020-adr` · `K020-comere` registry'de var, disk'te yok → klasörler üretilsin mi, registry satırı `⚠️ yok` mu olsun? | ⚠️ VERIFY REQUIRED |
| 5 | `K002-electroncis` disk'te var, registry "GAP" diyor → registry gerçeğe uydurulsun mu (imla `electroncis` korunur)? | ⚠️ VERIFY REQUIRED |
| 6 | `parent_id` alanındaki `"K000–K5000 Architecture Registry"` yazım hatası (birçok index'te `K5000?K5000` gibi bozuk) ve `parent=null` tutarsızlığı → ADR-097 şemasına göre normalize edilsin mi? | ⚠️ VERIFY REQUIRED |

---

## §6 Kapsam Dışı (bu ADR'de yapılmaz)

1. Alt kademelerdeki (kademe-2/3) `node_id` tutarsızlıkları (örn. `K005-firmware` → `node_id: …/index.md`, `K000` → `…/index`) yalnız raporlanır; normalize edilmez (Vault Steward kararı).
2. `donanım` · `identitiy` · `integraion` · `comilance` · `recomenataon` · `electroncis` imla yazımları **dokunulmaz** (ADR-097 K3 KURAL 1).
3. `.ai/architecture/K014-media/` içindeki mimari içeriğin (master/bagimliliklar/dogrulama) genişletilmesi — ayrı iş (bu ADR yalnız kimlik/numaralandırma kararını taşır).

---

## §7 Kanıt Komut Seti (tekrarlanabilir)

```powershell
# 1. Disk klasörleri + frontmatter node_id (§1.2 tablosu)
foreach ($d in (Get-ChildItem -LiteralPath ".ai\architecture" -Directory | Where-Object { $_.Name -like "K0*" })) {
  $idx = Join-Path $d.FullName "index.md"; $node = "?"
  if (Test-Path -LiteralPath $idx) {
    $n = Get-Content -LiteralPath $idx -TotalCount 8 | Where-Object { $_ -match "^node_id:" } | Select-Object -First 1
    if ($n) { $node = ($n -replace "^node_id:\s*","").Trim() }
  }
  "{0,-22} | node_id={1}" -f $d.Name, $node
}

# 2. Registry §5 karşılaştırması
Select-String -LiteralPath ".ai\architecture\index.md" -Pattern "K014-network|K015-media|K010-identitiy|K016-AI|K002"

# 3. Çapraz referans sayımı (B seçeneğinin maliyeti)
Get-ChildItem -LiteralPath ".ai\architecture" -Recurse -Filter "*.md" |
  Select-String -Pattern "K015-media|K014-network" | Measure-Object | Select-Object -ExpandProperty Count
```

---

## §8 Karar Sonucu (2026-10-17 — uygulandı)

**Karar: Seçenek A (klasör adlarını içeriğe hizala) — Vault Steward onayladı, uygulandı.**

### §8.1 Kesin maliyet kanıtı (§2'ye ek — sayımla)

`.ai/**` genelinde satır sayısı ölçümü (2026-10-17):

| node_id | Mevcut (çoğunluk) | Eski/sapma | A maliyeti | B maliyeti |
|---|---|---|---|---|
| identity | `K010-identitiy` **396** | `K0100-identitiy` 23 | 17 (K009-security path ref) | **396** (4-hane ihlali yayılır!) |
| network | `K014-network` **293** | `K010-network` 5 | 0 (yalnız `git mv`) | 293 |
| media | `K015-media` **185** | `K014-media` 9 | 0 (yalnız `git mv`) | 185 |
| AI | `K016-AI` 154 + `K015-AI` 172 | — | 166 (klasör-içi normalize) | registry K016→K015 |
| **Toplam içerik** | | | **~183 satır** | **~874 satır** |

**Kritik bulgu 1:** Registry §5 sırası `K010 kimlik → K011 uygulama → K012 servis → K013 integraion → K014 network → K015 media → K016 AI` = **kesintisiz tasarım dizisi**. Disk'teki kayma (network→10, media→14, AI→15) bu diziyi bozar; **B bu bozuk diziyi kalıcılaştırır**.
**Kritik bulgu 2:** B, `K0100` 4-hane şema ihlalini identity'nin **396 satırına** yayar — şema ihlali yaymak mimari kusurdur.
**Kritik bulgu 3 (git):** HEAD `K015-media` + `K016-AI`'yı **commit'li** taşır; diskteki `K014-media`/`K015-AI` **commit edilmemiş bir yeniden adlandırma denemesidir** (42 ` D` + 2 `??`). `K014-media` saf rename (15=15, kayıpsız); `K015-AI`'da **3 yeni dosya** (`rag-anayasa` · `rag-ornekler` · `rag-surucu`) → HEAD restore edilemez, **taşınarak korundu**.

### §8.2 Uygulanan değişiklikler

| # | İşlem | Detay |
|---|---|---|
| 1 | `git mv` / rename | `K015-AI→K016-AI` · `K014-media→K015-media` · `K010-network→K014-network` · `K0100-identitiy→K010-identitiy` (çakışmasız sıralama) |
| 2 | İçerik normalize (AI) | `K016-AI/` altında `K015-AI→K016-AI` — **23 dosya** |
| 3 | İçerik normalize (Security) | `K009-security/` altında `K0100-identitiy→K010-identitiy` — **6 dosya** (17 satır) |
| 4 | git index senkron | `git add -A .ai/architecture` — rename'ler temiz `R` olarak algılandı |

### §8.3 Doğrulama (kanıt)

- **24/24 klasör** `index.md` `node_id` ile uyumlu (`K001-donanım` konsol `ı` gösterim bozukluğu — klasör=node_id, gerçek uyumsuzluk yok).
- Eski string'ler (`K0100-identitiy` · `K010-network` · `K014-media` · `K015-AI`) → **ADR-098 dışı 0 satır** (yalnız bu ADR'nin tarihsel §1.2/§8.1 tablolarında — kasıtlı).
- Yeni string'ler: `K010-identitiy` 413 · `K014-network` 293 · `K015-media` 185 · `K016-AI` 327.
- **0 çapraz referans kırıldı** (§3 Seçenek A iddiası doğrulandı).

### §8.4 Açıktır (Vault Steward kararı — §5/4 · §5/5)

1. `K002-electroncis`: disk'te **var** (node_id uyumlu), registry §5 L46/L94/L119 hâlâ "**K002 GAP / hiç yok / ID GAP korunmuş**" diyor — registry gerçeğe uydurulacak mı? (imla `electroncis` korunur)
2. `K020-adr` · `K020-comere`: registry'de var, **disk'te yok** → klasörler üretilsin mi, registry satırı `⚠️ disk'te yok` mu olsun?

Bu ikisi §8.2 kapsamı **DIŞINDA** tutuldu (tek başına karar ister); 4 klasör kimlik düzeltmesinden **bağımsızdır** ve ana işi (K015-media içerik üretimi) **bloklamaz**.

## §9 Karar Override — Vault Steward Seçenek B'yi Seçti (2026-10-17)

**§8'deki Seçenek A uygulaması, Vault Steward (Bayram Ali) tarafından override edildi.** Kullanıcı açık ve tekrarlı yönerge verdi: **`k14 media · k15 ai · k10 network` — disk klasör adları doğrudur**, içerik `node_id`'leri buna uydurulacak.

> **Karar sahibi notu:** §2/§8'de Seçenek A maliyet/şema gerekçeleriyle önerilmişti (A=~183 satır, B=~874 satır; B `K0100` 4-hane ihlalini yayar). Karar verme yetkisi Vault Steward'dadır; kullanıcı B'yi seçti ve bu ADR kayda geçer. Mühendislik itirazı §2/§8'de saklıdır — **uygulanan karar B'dir**.

### §9.1 Uygulanan nihai durum (B)

| Düğüm | Nihai node_id + klasör | Önceki (A) |
|---|---|---|
| network | **`K010-network`** | K014-network |
| identity | **`K0100-identitiy`** | K010-identitiy |
| media | **`K014-media`** | K015-media |
| AI | **`K015-AI`** | K016-AI |
| application / services / integraion | K011 / K012 / K013 — **değişmedi** | — |

**Uygulama:** 3 `git mv` (media · network · identity; AI zaten K015-AI'ydı) + içerik rewrite **285 dosya** (`K016-AI→K015-AI` · `K015-media→K014-media` · `K014-network→K010-network` · `K010-identitiy→K0100-identitiy`) + `git add -A .ai`. Bu ADR, içerik rewrite'tan **muaf tutuldu** (§1.2/§8.1 tarihsel tabloları eski durumu belgeler, kasıtlı).

### §9.2 Doğrulama (kanıt)

- **Kritik klasörler** `index.md` `node_id` ile uyumlu: `K010-network` ✓ · `K0100-identitiy` ✓ · `K014-media` ✓ · `K015-AI` ✓ · K011/K012/K013 ✓ (değişmedi).
- Eski string'ler (`K010-identitiy` · `K014-network` · `K015-media` · `K016-AI`) → **ADR-098 dışı 0 satır**.
- Yeni string'ler: `K010-network` 294 · `K0100-identitiy` 417 · `K014-media` 189 · `K015-AI` 329.

### §9.3 Bilinçli olarak kabul edilen sonuç (kayıt)

1. **`K0100-identitiy` 4-hane şema anomalisini** korur (`K010` artık network'te). Bu, §2/§8'de işaretlenen şema kusurudur; Vault Steward kararıyla **kabul edildi**.
2. Registry §5'teki `K010 kimlik · K014 network · K015 media · K016 AI` kesintisiz dizisi, `K010 network · K014 media · K015 AI · K0100 identity` kaymasına dönüşür; `K016` boştur. §5 satırları içerik rewrite ile güncellendi.
3. §8.4'teki 2 açık konu (**K002 "GAP"** · **K020-adr/comere disk'te yok**) bu override'dan **etkilenmez** ve hâlâ Vault Steward kararı bekler; ana işi (K014-media içerik üretimi) **bloklamaz**.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-17
**Mode:** Red Team · Human Mode · Truth Mode
