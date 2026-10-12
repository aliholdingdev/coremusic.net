---
title: "CoreMusic — Dependency Graph (Kenar Okuma Kuralı)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §4.5 (path-ID) · §4.3 (tek verilmiş kenar) · §4.2 (depends_on alanı) · §5.2 (kanıt→doğrulama)"
---

# Dependency Graph — Bağımlılık Okuma Kuralı

> Bu dosya **kenar (edge) verisini** taşır: hangi K hangisine `depends_on`.
> v2 spec'te bugün **yalnızca tek bir kayıtlı kenar çifti var** (§4.3) — kalanı iskelettir.

## §A Okuma Kuralı — Kademe ve Adres

**Kenar tanımı (v2 §4.2, zorunlu alan):** `depends_on` = "Hangilerine muhtaç
(bağımlı olduğu düğümler)" · `evidence` = "gerçek dosya yolu, link, test kaydı (tahmin YASAK)".

**Yol adresi (v2 §4.5 — Yöntem A, önerilen):**

```text
  YÖNTEM A — YOL ADRESİ (path ID) — ÖNERİLEN, çakışma imkânsız

  K003-audio                        ← kademe 1 · domain
      └── /routing                  ← kademe 2 · subdomain
             └── /transport         ← kademe 3 · bounded context
                    └── /udp        ← kademe 4 · module
                           └── /jitter-buffer    ← kademe 5 · component
                                  └── /enqueue   ← kademe 6 · service
                                         └── enqueue.js  ← kademe 8 · implementation

  YÖNTEM B — NOKTALI ADRES (alternatif)

  K003.02.01.05.003     =  K003 → routing → transport → udp → bileşen #3
```

**Okuma kuralı:** bir ok `A → B` okunduğunda anlam `A, B'ye muhtaçtır` olur;
ok yönü = bağımlılık yönüdür. Kademe-1 ID `K0xx-<türkçe-ad>`, kademe-2+ ID =
üst ID + `/` + alt ad (v2 §4.5 tablosu).

## §B Bilinen Kenarlar (v2 §4.3 — tek VERİLMİŞ veri)

```yaml
# v2 §4.3 · K003-audio boş şablonundan (kaynaktaki hâli — dolgu değil)
depends_on:
  - K001-donanım
  - K004-dsp
```

```text
┌────────────────────────────────────────────────────────────┐
│ KENAR DURUMU — v2 §4.3 itibarıyla                          │
├────────────────────────────────────────────────────────────┤
│                                                            │
│ K003-audio ──depends_on──→ K001-donanım   ✅           §4.3 │
│      │                                                     │
│      └──depends_on──→ K004-dsp   ✅                    §4.3 │
│                                                            │
│ K000 · K001 · K004 … K019 · 6× K020                        │
│ (kalan 24 domain) → kenar verisi v2'de YOK                 │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

## §C Kenar İskeleti — Boş Alanlar

| Durum | Domain | Kaynak |
|---|---|---|
| ✅ kenarlı | **K003-audio** → K001-donanım · → K004-dsp | v2 §4.3 |
| ⏳ iskelet (açık kenarı YOK) | K000-isletim-sistemi · K001-donanım · K004-dsp · K005-firmware · K006-yapay-zeka … K019-testing · 6× K020 | **toplam 24 domain** |

⚠️ **VERIFICATION REQUIRED: kenar verisi Band-1 üretiminden doldurulacak.**
Ayrıca v2 §4.3 açık sorusu: `K003 ile diskteki K003-ses-motoru aynı mı?
→ ID CONFLICT — VERIFY REQUIRED`. 24 domain için `depends_on` alanı v2'de
**henüz yazılmadı** → tahmin edilip grafiğe konmaz.

```text
   BAĞLANTI DURUMU — Band-1 (25 klasör · 19 tekil K + 6× K020)

   KENARLI (1 domain · 2 kenar)               KENARI OLMAYAN (24 domain)
   ────────────────────────────               ──────────────────────────
   K003-audio ──→ K001-donanım                K000-isletim-sistemi
   K003-audio ──→ K004-dsp                    K001-donanım
                                              K004-dsp · K005-firmware …
                                              K019-testing · 6× K020
                                              (band-1 üretiminde dolar)
```

## §D Kenar Doğrulama Zinciri (v2 §5.2)

```text
                     ┌──────────────────┐
                     │  ARCHITECTURE KG │
                     └────────┬─────────┘
                              │
       ┌──────────────────────┼──────────────────────┐
       ▼                      ▼                      ▼
   K-LAYERS                DOMAINS                  CODE
       │                      │                      │
       ▼                      ▼                      ▼
  COMPONENTS          BOUNDED CONTEXTS              FILES
       │                      │                      │
       └──────────────────────┼──────────────────────┘
                              ▼
                          EVIDENCE
                              │
                 ┌────────────┼────────────┐
                 ▼            ▼            ▼
              REPO          ADR          TEST
                 │            │            │
                 └────────────┼────────────┘
                              ▼
                          VALIDATION
```

## Lejant

| Sembol | Anlam |
|---|---|
| `A ──depends_on──→ B` | **kenar** — A, B'ye muhtaçtır (tek yön) |
| `──?── / ⏳` | **iskelet** — kenar henüz verilmemiş (uydurulmaz) |
| `✅ / ✓` | v2 spec'te açıkça yazılmış kenar |
| `← kademe n` | path-ID derinliği (v2 §4.5) |
| `EVIDENCE / VALIDATION` | kanıt ve doğrulama katmanı (v2 §5.2) |
| ⚠️ | `VERIFICATION REQUIRED` — doldurulmadan sayıma/kanıta katılmaz |

## Kaynak Atfı

- **v2 §4.5** "K5000+ İÇİN KİMLİK (ID) BİÇİMİ" → §A path-ID örneği (birebir).
- **v2 §4.3** "Örnek kayıt (boş şablon — K003-audio)" → §B tek verilmiş `depends_on` verisi.
- **v2 §4.2** "Her kayıt için ZORUNLU alanlar" → §A kenar tanımı (`depends_on` · `evidence`).
- **v2 §5.2** "Kanıt → Doğrulama Zinciri" → §D (birebir taşındı; enterprise setine taşınan bölüm).
- Eş kaynak: `.ai/architecture/index.md` §9 B fazı (Band-1 üretimi — kenarlar buradan gelecek).

## Durum Notu

- `status: draft` — **bu tamamlanmış bir graf değildir**; 2 kanıtlı kenar + iskelet taşır.
- **Kenarlar doldukça genişler:** her `index.md` `depends_on` alanı dolduğunda
  §C'deki ⏳ satırları bir kenar kazanır; **dolgu (uydurma kenar) yasaktır**.
