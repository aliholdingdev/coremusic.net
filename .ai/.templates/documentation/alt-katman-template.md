---
title: "CoreMusic — Alt Katman md Şablonu (K{n}.a.b)"
type: template
category: template
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-24
---

# Alt Katman md Şablonu (K{n}.a.b)

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]] · [[../architecture/adlandirma-kurali]] · [[../architecture/katman-baglilik-matrisi]] · [[katman-readme-template]]

> **Kullanım:** `.ai/architecture/k{n}-<slug>/<konu>.md` dosyaları bu şablondan üretilir. Her alt katman md'si **3. seviye bir düğümdür** (`K{n}.a.b`); dosyanın kendisi kanıt türü (i)'dir ve sayım girdisidir. Dosya adı düğüm adıyla uyumlu olmalı (`<konu>` slug'ı README Dosya Haritası ile birebir).

---

## §1 Amaç

Tek bir alt bileşen grubunu (`K{n}.a.b`) belgelemek: neyi sahiplenir, hangi bileşenleri içerir (4. seviye kanıt), hangi arayüzle yukarıya/bağımlıya bağlanır, hangi kurallar geçerlidir. Her dosya tek düğüm = tek sorumluluk (dual-use: doküman + sayım kanıtı).

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Tek K{n}.a.b düğümünün içeriği, arayüzü, kuralları | Katman geneli ve bileşen haritası (→ README) |
| 4. seviye kanıt satırları (K{n}.a.b.c, PLANNED dahil) | Bağımlılık izinlerinin tanımı (→ matris) |
| Dosya içi yerel tasarım notları | Mimari karar (→ `.ai/architecture/adr/` veya `.ai/.decisions/`) |
| Wiki-link'lerle komşu düğümlere geçiş | ADR metni, sayım formülü |

## §2 Kapsam

- **Dosya yolu:** `.ai/architecture/k{n}-<slug>/<konu>.md` → düğüm `K{n}.a.b`
- **Derinlik sınırı:** dosya içinde referans edilebilecek en derin düğüm `K{n}.a.b.c` (4. seviye, yalnız kanıtla); `K{n}.a.b.c.d` (5. seviye) **yazım yasağı**.
- **Derinlik hedefi:** yeni/tümdüzeltilen alt katman md ≥500 satır (şablonlar hariç kuralı); bu şablonun kendisi 100-250 bandındadır.

## §3 Mimari (İskelet)

### §3.1 Frontmatter + Başlık

```markdown
---
title: "K{n}.a.b — <Alt Katman Adı>"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT — alt katman dokümanı (şablon: alt-katman-template)"
updated: {{DATE}}
---

# K{n}.a.b — <Alt Katman Adı>

**Düğüm:** K{n}.a.b · **Üst katman:** [[README]] (K{n}) · **Alan:** A{x}
**Kanıt:** (i) diskte bu dosya · **Durum:** implemented | planned
```

### §3.2 Bölüm Sırası (silinemez)

| # | Bölüm | İçerik |
|---|-------|--------|
| 1 | Amaç | Bu düğüm neyi sahiplenir (2-3 cümle) |
| 2 | Kapsam / Kapsam Dışı | Sınır tablosu |
| 3 | Arayüz | Yukarıya/aşağıya sunduğu/gittiği arayüz (fonksiyon/API/olay adları — kod referanslı) |
| 4 | İçerik / Bileşenler | 4. seviye `K{n}.a.b.c` satırları + kanıt + PLANNED etiketi |
| 5 | Kurallar | Yerel yasak/ızgara (OWASP, BCNF, zero-allocation…) |
| 6 | Bağımlılık Notu | README'den kopya **değil**; bu düğüme özel ok varsa "çağrı/gösterim" etiketiyle |
| 7 | İlgili Dosyalar | Wiki-link'ler `[[relative/path]]` (üst README, komşu düğümler, ADR) |

### §3.3 4. Seviye Satır Formatı (kanıt disiplini)

```markdown
| K{n}.a.b.c | Bileşen | Kanıt | Durum |
|------------|---------|-------|-------|
| K11.1.4.13 | c-player.css | (iii) frontend-restructuring-plan §2.2 | PLANNED |
| K6.1.1     | csrf_token üretimi | (i) bu dosya §5 | implemented |
```

**Kural:** kanıt yoksa satır yazılmaz (adlandirma §3.3); yalnız (iii) ise `PLANNED` zorunlu; kanıt kaybolursa satır bir sonraki turda düşer.

## §4 Kurallar

1. **Dosya = düğüm = tek sorumluluk.** İki bağımsız konu için ikinci dosya açılır (aynı batch içinde, ≤31 kuralı).
2. **Adlandırma regex'i** dosyanın her düğüm referansına uygulanır; `.0.` segmenti, 5. seviye, küçük-k, `K21+` yasak.
3. **L-önek yalnız alıntıdır:** `L{n}…` görüldüğünde (plan/CLAUDE §32 alıntıları) yanında kaynak yazılır; yeni yazımda kullanılmaz → `K{n}…`.
4. **Bağımlılık oku ekleme yetkisi yoktur:** yalnız README/matristen okunur; bu dosya "çağrı/gösterim" notu ekleyebilir (ADR-025 örneği: K8.2 → K15 çağrı).
5. **Klasör kuralları:** `firmware/` = K1.f (taşınmaz); `k-surucu`→`k2-surucu` taşıması yalnız ADR-024 ile (12 dosya, 0 silme).
6. **REDACTED:** `.env`, key, token, parola asla yazılmaz.
7. **Frontmatter 7 alan** + `type: architecture-sublayer`.

## §5 Workflow

```text
ŞABLONU SEÇ → DÜĞÜMÜ ONAYLA (README'de K{n}-NN ↔ K{n}.a.b satırı var mı?)
→ §3.2 bölümlerini doldur (kanıt (i) bu dosyanın kendisi)
→ 4. seviye satırlar: yalnız kanıtlı (i)/(ii)/(iii); yoksa VERIFICATION REQUIRED
→ Bağımlılık notu: matris/README'den, etiketli
→ DENETİM (§6) → UTF-8 verify → README Dosya Haritası'na 1 satır ekle → log append → senkron
```

**Sıra kuralı (architecture-write §5-4):** alt katman md, kendi katman README'sinden **sonra** yazılır (README önce, kanıt sonra kilitlenir).

## §6 Doğrulama (checklist)

- [ ] 7 alanlı frontmatter; H1 = `K{n}.a.b — <ad>`; `type: architecture-sublayer`
- [ ] §3.2'deki 7 bölüm eksiksiz
- [ ] Dosya adı slug'ı ≠ düğüm adı çelişmiyor; README Dosya Haritası'nda 1 satır eklendi
- [ ] İçerikteki tüm düğümler regex'e uyar; `K\d+(\.\d+){4,}` = 0 · `K7\.0` = 0
- [ ] 4. seviye satırlarının her birinde kanıt (i)/(ii)/(iii) + PLANNED/implemented etiketi
- [ ] Yeni bağımlılık oku yazılmamış (matriste olanlar referans)
- [ ] Wiki-link'ler `[[relative/path]]`, hedefler diskte var (kırık 0)
- [ ] ≥500 satır (yeni/tümdüzeltilen) · bu dosya hiçbir dosyayı silmedi
- [ ] `vault-utf8-writer verify` OK · log.md append · senkron adımları çalıştı

## §7 Referanslar — Dolu Örnek (gösterim)

```markdown
# K8.2 — Media Servis (Uç Nokta)

**Düğüm:** K8.2 · **Üst katman:** [[k8-servis/README]] (K8) · **Alan:** A2
**Kanıt:** (i) bu dosya · **Durum:** implemented

## Amaç
İş emri (job), akış URL'si ve kitaplık CRUD uçlarını sunar. Boru hattı
SAHİPLİĞİ bu düğümde DEĞİLDİR (ADR-025): FFmpeg/transcode/HLS-DASH K15'e aittir.

## Kapsam / Kapsam Dışı
| Kapsam | Kapsam Dışı |
|--------|-------------|
| job CRUD, akış URL üretimi, kitaplık CRUD | FFmpeg komut satırı, parametreleri, pipeline akışı (→ K15) |

## Arayüz
- Yukarı: K9 API router → bu düğüm (HTTP uçları)
- Aşağı: K15 arayüzünü **çağırır** (iş emri) — bağımlılık satırı EKLENMEZ

## Bileşenler
| K8.2.x | Bileşen | Kanıt | Durum |
|--------|---------|-------|-------|
| K8.2.1 | Job kuyruğu | (i) bu dosya §5 | implemented |
| K8.2.2 | Yayın URL imzalama | (ii) k8-servis README K8-07 | PLANNED |

## Bağımlılık Notu
| Ok | Tür | Kaynak |
|----|-----|--------|
| K8.2 → K15 | **çağrı** | ADR-025 (servis ucu → boru hattı sahibi) |

## İlgili Dosyalar
[[k8-servis/README]] · [[../k15-medya-streaming/README]] · [[../adr/ADR-025-k8-2-k15-siniri]]
```

*(Örnek kurgusal gösterimdir; gerçek içerik diskteki md'lerden türetilir.)*

---

**Registry notu:** Bu şablon `.ai/.templates/index.md`'ye kayıtlıdır; şablonsuz alt katman md'si üretilmez (Guardrail #16).

*CoreMusic Alt Katman Şablonu v1.0.0 — Authority: Bayram Ali / Vault Steward — 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
