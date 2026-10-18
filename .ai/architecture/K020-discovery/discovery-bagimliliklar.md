---
node_id: "K020-discovery/bagimliliklar"
node_type: "bagimliliklar"
status: "draft"
parent_id: "K020-discovery"
children: []
depends_on:
  - "K020-discovery/discovery-master.md"
  - ".ai/architecture/index.md (registry · §5 satır 87 · §9.1 ölçüm)"
  - "K007-data (S-1 arama sahipliği sınırı — K007-data/data-master.md:219)"
  - "K008-domain (C2 recommendation/discovery sahipliği — K008-domain/domain-master.md:188)"
  - "K021-recomenataion (depends_on: K020-discovery — recomenataion/index.md:12)"
evidence:
  - "2026-10-17 disk doğrulaması: v2 ağacı satır 571–575 · K007-data 40+ S-1 referansı · recomenataion depends_on"
validation: "partial"
open_questions:
  - "S-1 sahiplik: K007-data/search ↔ K020-discovery/search (K007 data-kapsam.md:148 — sahiplik ADR'si bekliyor)"
  - "K008 C2: recommendation/discovery sahipliği K008 mi K006-yapay-zeka/K020 mi (domain-master.md:188)"
  - "⚠️ medya-servisi auth gate ADR'si YOK — ADR-010 gerçekte CSRF Protection Strategy"
group: "mimari"
---

# K020-discovery — Bağımlılıklar (disk kanıtlı harita)

> **Rol:** K020-discovery'in **öncesi ve sonrası** bağımlılıklarını, bağımlılık
> yönünü (ok) ve kırılma durumunu toplar. **Yalnız disk kanıtlı** bağımlılıklar
> yazılır; doğrulanamayan her madde `⚠️ VERIFY REQUIRED` taşır (AGENTS.md §3).
>
> **Konum:** `.ai/architecture/K020-discovery/discovery-bagimliliklar.md`
> **Ölçüm:** canonical `[System.IO.File]::ReadAllLines($path).Length` (registry §9.12.1, satır 761–764).

---

## 1. Bağımlılık kuralı (yön semantiği)

| Ok | Anlam | Örnek |
|---|---|---|
| `X → Y` | X, Y'ye **bağımlıdır** (X önce Y'yi bekler) | `K021-recomenataion → K020-discovery` |

**Yön kuralı:** Ok **her zaman bağımlı → beklenen** yönündedir; ters çevrilemez.
Keşif disiplini **girdi** toplar (üst omurgadan) ve **çıktı** verir (K020 alt
node'larına); girdi veren node'un işini **yerine geçmez**.

---

## 2. Bağımlılık tablosu (yalnız disk kanıtlı — 2026-10-17 doğrulandı)

| # | Bağımlılık | Kanıt (dosya:satır) | Durum |
|---|---|---|---|
| **D-1** | `K021-recomenataion → K020-discovery` (öneri motoru, keşif/katalog verisi olmadan çalışmaz) | `K021-recomenataion/index.md:12` (depends_on) · `recommendation-master.md:16/34/43` | `✓ VAR` |
| **S-1** | `search` sahipliği: **K007-data/search ↔ K020-discovery/search** (iki düğüm de "arama" diyor) | `K007-data/data-master.md:219` · `data-kapsam.md:148` · `data-bagimliliklar.md:132/207/439` · `K007-data/search/index.md:21` (40+ referans) | `⚠️ SINIR AÇIK` — sahiplik ADR'si bekliyor |
| **D-3** | `K008-domain(discovery) ↔ K020-discovery` — sahiplik çakışması: `recommendation`/`discovery` çocukları K008 mi K006/K020 mi? | `K008-domain/domain-master.md:188` (C2) · `K008-domain/index.md:31/93` | `⚠️ VERIFY REQUIRED` — sahiplik sınırı ADR gerektirir |
| **D-4** | v2 mini-ağaç: K020-discovery = `index.md` + `search/` + `agent/` (3 öğe) | v2 ağacı satır 571–575 (`G:\Drive'ım\…\coremusic-katmanli-mimari-agaci-v2.md`) | `✓ VAR` (şekil sözleşmesi) |
| **D-5** | `6× K020 ID CONFLICT` (adr/comilance/comere/licence/discovery/recomenataion) | v2 satır 98/616 · ADR-097 §K6 (satır 78) · registry §5 satır 85–88 | `⚠️ CONFLICT` — onaysız düzeltilmez |

**Etiket notu (Truth Mode):** Düzeltme öncesi bu dosyada `S-2…S-7` bağımlılıkları
(K005→discovery, ADR-010 auth gate, ADR-101 sentinel vb.) **uydurma** olarak
taşındı ve **silindi** — vault'ta karşılığı yok. Yalnız **S-1** gerçek (K007-data
kaynaklı, 40+ referans). K005 gerçek adı `K005-firmware`'dir (component-library
değil) ve `K020-discovery`'e **0 referansı** vardır.

---

## 3. Öncesi (upstream) — ne GİRDİ verir

| # | Upstream | Girdi | Zorunluluk | Kırılırsa |
|---|---|---|---|---|
| 1 | v2 mini-ağaç (satır 571–575) | yapı: `index.md` + `search/` + `agent/` | **zorunlu** (şekil) | v2 şekliyle çelişen dosya düzeni |
| 2 | `K007-data` (S-1) | arama indeksi verisi + sahiplik sınırı | **sınır** (⚠️ açık) | çift indeks, tutarsız sonuç (K007 data-bagimliliklar.md:132) |
| 3 | `K008-domain` (C2) | `discovery` adının domain sahipliği | **önce** (domain → keşif) | sahiplik çakışması (K008 index:31) |
| 4 | Repo: `app-nodejs-projects/nova-search-engine/` (`packages/search/*.ts` · `packages/common/signals.ts` · `ADR-001-youtube-api-v3-yasak.md`) | gerçek arama/sinyal yüzeyi (dil + spam skoru) | zorunlu (kanıt) | tahmin → zero-hallucination ihlali |

---

## 4. Sonrası (downstream) — ne ÇIKTI verir

| # | Downstream | Çıktı | Bekleyen |
|---|---|---|---|
| 1 | `K021-recomenataion` (D-1) | keşif/katalog verisi (öneri girdisi) | keşif onayı |
| 2 | `K007-data` (S-1) | arama keşif **kuralları** (indeks verisi değil — K007 data-kapsam.md:127) | sahiplik ADR'si |
| 3 | `.ai/architecture/index.md` (registry) | kapanış kaydı (registry §5 satır 87 güncellenir) | **orchestrator yazımı** |

---

## 5. Kırılma senaryoları

| # | Senaryo | Sonuç | Eylem |
|---|---|---|---|
| 1 | S-1 sahipliği çözülmezse | K007 ve K020-discovery ikisi de "arama" yazar → çift indeks | sahiplik ADR'si (K007 data-master.md:219 bekliyor) |
| 2 | K008 C2 çözülmezse | `discovery` adı iki yerde (K008-domain/discovery ↔ K020-discovery) | üst karar (K008 domain-master.md:188) |
| 3 | D-1 yönü ters çevrilirse | keşif, önerinin girdisini beklerken öneri keşfi beklemez → sıra bozulur | ok yönü korunur: `recomenataion → discovery` |
| 4 | 6× ID CONFLICT çözülmezse | K020- öneki 6 klasörde paylaşılıyor | onaysız düzeltilmez (ADR-097 §K6) |

---

## 6. Bağımlılık denetim listesi (her keşif oturumunda)

- [ ] S-1 sınırı: K007 ile çakışan "arama" yazımı yapıldı mı? (kurallar K020'ye, indeks K007'ye — geçici karar K-SCOPE-4)
- [ ] K008 C2: `discovery` adı iki yerde mi? (karar üst mercide)
- [ ] D-1 yönü: `recomenataion → discovery` (tersi değil)
- [ ] ID CONFLICT: yeni `K020-*` dosya/klasör **açılmadı** (onay yok)
- [ ] Ölçüm: `ReadAllLines()` kullanıldı mı

---

## 7. Doğrulama ve İlgili dosyalar

### 7.1 Doğrulama

- [x] Frontmatter 9 alan + `group: mimari` + Authority footer.
- [x] **Her bağımlılık `dosya:satır` kanıtıyla** taşındı (2026-10-17 disk doğrulaması).
- [x] Uydurma `S-2…S-7` + sahte ADR adları + `log.md:xxx` referansları **silindi** (Truth Mode düzeltmesi).
- [x] `⚠️ VERIFY REQUIRED` alanları korundu (S-1 · K008 C2 · ID CONFLICT).
- [ ] `validation: partial` — S-1 sahiplik ADR'si + K008 C2 kararı bekleniyor.

### 7.2 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `.ai/architecture/index.md` §5 satır 87 | K020-discovery kaydı (registry) |
| `K007-data/data-master.md:219` · `data-kapsam.md:148` | S-1 sahiplik sınırı (gerçek kaynak) |
| `K008-domain/domain-master.md:188` | C2 sahiplik çakışması |
| `K021-recomenataion/index.md:12` | D-1 depends_on (gerçek) |
| `K020-discovery/discovery-master.md` | omurga sözleşmesi (bu dosyanın üstü) |

---

**Authority:** Bayram Ali / Vault Steward
