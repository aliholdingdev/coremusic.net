---
title: "CoreMusic .ai/architecture — Hot Memory Context"
type: context
category: architecture
version: "1.0.0"
status: active
authority: "SSOT: .ai/architecture/rules.md (kurallar) · bu dosya: derived projection (hot memory)"
updated: 2026-10-07
tier: 3
domain: architecture
ssot: false
risk: medium
owner: "MO"
depends-on: [".ai/architecture/rules.md", ".ai/architecture/00-enterprise-index.md"]
---

# .ai/architecture — Hot Memory Context (Otomatik Yüklenir)

> **Yükleme:** Bu dosya `opencode.json → instructions[]` (17. sıra) ve `.claude/settings.json → instructions[]` (5. sıra) içindedir → **her LLM oturumunda otomatik okunur**.
> **Bütçe:** ≤660 satır (Q35) — validator `budget-check` aşımı RED eder.
> **SSOT değildir:** Kuralların kaynağı [[architecture/rules]] · Katalog [[architecture/00-enterprise-index]] · Genel vault context [[CONTEXT]].

---

## 1. Bu Katman Nedir?

`.ai/architecture/` = CoreMusic **mimari katmanının (K0-K20) governed envanteri**.
Her bileşenin tek kanonik kaydı burada yaşar; kod ile `.ai` arasındaki fark drift olarak raporlanır.

| Soru | Cevap kaynağı |
|---|---|
| Mimari kural nedir? | [[architecture/rules]] (SSOT) |
| Hangi katman, ne içeriyor? | [[architecture/00-enterprise-index]] |
| Katman detayı? | `architecture/k0-…/index.md` (lazy) |
| Karar (ADR) neden alındı? | `.ai/.decisions/` (architecture altında ADR YOK) |
| Script/validator? | `.ai/scripts/` (architecture altında YOK) |

---

## 2. Katman Haritası (21 katman + firmware)

> **Authority (2026-10-07):** Ana giriş = `00-master-index.md` (500 katman K000–K499 + 10 domain). Aşağıdaki 21 katman = landing + envanter evi (derived); anlatı domain kataloglarında, kayıtlar katman `inventory/` part'larında.

| Katman | Dizin | Kapsam |
|---|---|---|
| K0 | [[architecture/k0-isletim-sistemi]] | İşletim sistemi, platform, process, IPC, thread, bellek, container |
| K1 | [[architecture/k1-donanim]] | DAC/ADC zinciri, AK4458/XMOS, analog giriş, Class AB çıkış, PCB |
| K2 | [[architecture/k2-surucu]] | ASIO, WASAPI, ALSA, sürücü yığını |
| K3 | [[architecture/k3-ses-motoru]] | Neva Engine, DSP chain (EQ/reverb), ses işleme |
| K4 | [[architecture/k4-yapay-zeka]] | Music analysis, recommendation, voice, edge AI |
| K5 | [[architecture/k5-veri-yonetimi]] | MySQL 9 (18 BCNF), Redis, APCu, SQLite |
| K6 | [[architecture/k6-guvenlik]] | JWT, RBAC, CSRF, CSP, rate limit, vault, audit |
| K7 | [[architecture/k7-middleware]] | PSR-15 pipeline (OriginCheck→…→Validation) |
| K8 | [[architecture/k8-servis]] | Control/Media/Audio/Device servisleri, event bus |
| K9 | [[architecture/k9-api-routing]] | Gateway, BFF, CQRS, SPA router, OpenAPI |
| K10 | [[architecture/k10-uygulama]] | 10 panel (uygulama katmanı) |
| K11 | [[architecture/k11-ux]] | ITCSS 9-layer, BEM, PWA, WCAG 2.2 AA |
| K12 | [[architecture/k12-izleme]] | Prometheus, Grafana, Sentry, audit log |
| K13 | [[architecture/k13-cicd]] | GitHub Actions, Docker, deploy |
| K14 | [[architecture/k14-ag]] | HTTP/3, WebRTC, DLNA, mDNS |
| K15 | [[architecture/k15-medya-streaming]] | FFmpeg, FLAC, HLS, DASH |
| K16 | [[architecture/k16-class-ab]] | MJL21194/93, 50W/kanal, 8 kanal |
| K17 | [[architecture/k17-guc-kaynagi]] | LM5122, ±35V, 6S LiPo, UVP/OVP/OCP/OTP |
| K18 | [[architecture/k18-termal]] | Fischer SK53, PWM fan, thermal cutoff |
| K19 | [[architecture/k19-pcb]] | 6-layer stackup, ENIG, star ground |
| K20 | [[architecture/k20-bom]] | 1.775 BOM satırı |
| FW | [[architecture/firmware]] | XMOS/xTIMEcomposer firmware (cross-cutting) |

**Kanonik isimlendirme:** dizinler Türkçe, `k0-isletim-sistemi` formatı (Q31) — bu adlar wiki-link hedefidir, **DEĞİŞTİRİLMEZ**.

---

## 3. Bileşen ID Şeması (Q22)

```text
Kx.yy.zzz   →   K5.02.017
│ │   │       K5 = katman · 02 = bölüm (alt katman) · 017 = bileşen sırası
```

- Her envanter kaydının **sabit ID'si** vardır; link `[[architecture/k5-veri-yonetimi/inventory/k5-part01#K5.02.017]]` ile hedeflenir.
- **Tekillik validator zorunlu:** `id-check` aynı ID'yi iki kez görürse ihlal.
- ID asla yeniden kullanılmaz; bileşen silinirse kayıt `status: deprecated` olur, ID boşa düşmez.

---

## 4. Envanter Kaydı — 12 Alan (Q26)

Her bileşen satırı şu alanları taşır:

| # | Alan | Açıklama |
|---|---|---|
| 1 | `id` | Kx.yy.zzz |
| 2 | `ad` | Bileşen adı |
| 3 | `tip` | servis / sınıf / tablo / endpoint / DSP / PCB / BOM… |
| 4 | `kod-yolu` | Disk kanıtı (repo köküne göreli path) |
| 5 | `durum` | IMPLEMENTED / PLANNED / UNKNOWN |
| 6 | `A-alanı` | A0-A5 etiketi |
| 7 | `bagimlilik` | bağımlı olduğu ID'ler |
| 8 | `sahip-agent` | sorumlu agent (bkz. `.ai/AGENTS.md` §6) |
| 9 | `risk` | low / medium / high |
| 10 | `guvenlik` | security sensitivity (yes/no + not) |
| 11 | `kanit-tarihi` | son doğrulama tarihi (stale taraması girdisi) |
| 12 | `not` |serbest kısa not / `⚠️ VERIFICATION REQUIRED` |

**Durum etiketi kuralı:** Doğrulanmayan satır `UNKNOWN` — uydurma `IMPLEMENTED` yazmak Zero-Hallucination ihlalidir.

---

## 5. Dosya İskeleti ve Parçalama (Q10/Q17/Q21)

```text
architecture/
├── 00-enterprise-index.md        # master katalog (navigation)
├── rules.md                      # mimari kurallar SSOT
├── context.md                    # bu dosya (hot memory, derived)
├── kX-<ad>/
│   ├── index.md                  # katman girişi (arc42-öz + vault 8-iskelet HİBRİT, Q25)
│   ├── README.md · AGENTS.md · CLAUDE.md · WORKFLOW.md   # tam set (Q10)
│   ├── 01-…/02-…/03-…/04-…      # derinleşme (yalnız gerektiğinde, Q6/Q19)
│   └── inventory/
│       └── kX-<tür>-partNN.md    # tür-bazlı bölünmüş envanter
└── firmware/
```

- **Tek MD yasak (Q17):** hiçbir içerik dosyası tek parça değildir — linkleyerek çoklu-md.
- **Parçalama:** tür-bazlı (sınıflar, tablolar, endpointler ayrı) + **min 500 satır/md (Q21)**; 500 satıra yaklaşıldığında `partNN` ile bölünür.
- **Kademeli derinlik (Q19):** her katman varsayılan olarak `index.md` + envanter tablosu; yalnız kritik katmanlar 01-04 alt bölümlerine iner.

---

## 6. Okuma / Loading Sırası (P0→P3)

```text
P0 (her görev):  hot memory → bu dosya (zaten yüklü) + kök AGENTS.md/CLAUDE.md
P1 (mimari görev): @architecture/rules.md → 00-enterprise-index.md
P2 (katman görevi): @architecture/kX-…/index.md (yalnız ilgili katman)
P3 (detay):       inventory/kX-…-partNN.md (yalnız gerekli parça)
```

**Kural:** İlgisiz katman **yüklenmez** (context pollution, Q57). Bir görevde P2'den fazlası okunmuyorsa P3 açılmaz.
**Kural:** Token optimizasyonu uğruna kural/ID/kanıt satırı **atlanmaz** (§27).

---

## 7. Kurallar Özeti → SSOT: [[architecture/rules]]

1. FM: her dosyada **13 alan zorunlu** (Control Plane v2) — validator `fm-check`.
2. `ssot: true` yalnız **konunun canonical owner'ında**; katalog/context `ssot: false`.
3. `depends-on` graf kenarıdır → `dep-check` circular'ı ve ölü hedefi yakalar.
4. ADR ≠ architecture: kararlar `.ai/.decisions/`, uygulama `.ai/architecture/` (§49).
5. Yüksek riskli değişim (rules, SSOT, ID şeması, kök boot) → **insan onayı** (Q32: her şey onaylı).
6. Kök boot dosyalarında yalnızca architecture linkleri + tespit çelişkiler revize edilir (Q7).

---

## 8. Validator ve Faz Kapıları

```bash
node .ai/scripts/validate.mjs          # tam rapor (8 check)
node .ai/scripts/validate.mjs --check  # tek satır (hook modu)
```

**8 check:** `fm` · `link` · `ssot` · `dep` (circular) · `tier` · `orphan` · `id` (tekillik) · `budget` (≤660).
**Faz kapıları (Q24/Q29):** Faz 0 iskelet → Faz 1 k0 pilotu → **tam tarama + onay** → kalan katmanlar.
**Stale (Q27→Q29):** tam tarama faz kapılarında yapılır; `kanit-tarihi` eski kayıtlar `STALE` işaretlenir.

---

## 9. Durum (Faz 0 — 2026-10-07)

| Faz | Kapsam | Durum |
|---|---|---|
| A | Authority reconciliation (500-katman tek esas — rules R1a/R7, enterprise→derived) | ✅ 2026-10-07 |
| 0 | İskelet: 22 dizin + index + rules + context + validator | ✅ tamamlandı |
| 1 | k0 pilotu (tam set + envanter + tam tarama) | ⏳ onay bekliyor |
| 2+ | k1-k20 katman başına onaylı genişleme | ⏳ |
| R | Kök boot link revizyonu (AGENTS/index/keys + §24.3 çelişkiler) | ⏳ high-risk onayı |

**Kaynaklar (salt-okunur):** `_backup/arch-2026-10-06_1057/architecture/` · `.ai.copy2/architecture/` — bunlardan **kopyalama/taşıma YOK**; içerik sıfırdan, kod+web kanıtıyla yazılır (Q8/Q11).
