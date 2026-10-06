---
title: "CoreMusic — Bağımlılık Matrisi & DÜĞÜM Sayımı"
type: architecture-rule
category: architecture
date: 2026-10-06
updated: 2026-10-06
status: active
version: 1.0.0
authority: "SSOT — bağımlılık yönü + DÜĞÜM sayım kuralı (bu dosya)"
governance: Red Team · Human Mode · Truth Mode
---

# Bağımlılık Matrisi & DÜĞÜM Sayımı

> İlgili: [[00-enterprise-index]] · [[katman-sayim-rehberi]] (DÜĞÜM tanımı) · [[katman-baglilik-matrisi]]

---

## §1 Bağımlılık Yön Kuralları

### §1.1 Akış yönü

```
[Client/UI]  A3 (K10-K11)
     │  istek
     ▼
[API/Route]  A2 (K8-K9)
     │  middleware
     ▼
[Security]   A1 (K6-K7)
     │  doğrulanmış çağrı
     ▼
[Core]       A0 (K0-K5)
     ▲
     │  gözlem (sadece okuma)
[Ops]        A4 (K12-K15)

[A5]  K16-K20 ──sınır──> A0  (bağımsız; hiyerarşi yok)
```

### §1.2 İzin / Yasak tablosu

| Kaynak → Hedef | İzin? | Not |
|-----------------|-------|-----|
| A3 → A2 | ✅ | UI API'yi çağırır |
| A2 → A1 | ✅ | Routing → security middleware |
| A1 → A0 | ✅ | Doğrulama sonrası çekirdek |
| A2 → A0 | ⚠️ | Yalnız A1 üzerinden (doğrudan atlama yasak) |
| A0 → A3 | ❌ | Alt katman üst katmanı import edemez |
| A0 → A2 | ❌ | Çekirdek backend'i tanımaz |
| A4 → A2 | ✅ (okuma) | İzleme yalnız **okur**, yazar değil |
| A4 → A0 | ✅ (okuma) | Health check |
| A5 → A0 | ✅ (sınır) | Donanım arabirimi — tek temas noktası |
| A0 → A5 | ⚠️ | Yalnız **kontrat (interface)** üzerinden |
| K15 → üretim/bileşen | ❌ | K16-K20'ye bağımlılığı YOKTUR |
| K16 ↔ K17 ↔ K18… | ⚠️ | Eşdüzey referans; hiyerarşi **yasak** |

**İhlal durumunda:** derhal revert + `log.md`'ye ERROR girişi (AGENTS.md §5 / §18).

---

## §2 DÜĞÜM Sayım Kuralları

### §2.1 Sayım birimi

| Sayılır | Sayılmaz |
|---------|----------|
| Bir katman dosyasındaki **mantıksal katman satırı** | Fiziksel klasör |
| Kontrat/endpoint düğümü | Aynı dosyanın tekrarı |
| Bağımlılık kenarı (edge) | Görsel/ASCII çizgi |
| Bileşen düğümü (envanter kanıtlı) | Yorum satırı |

### §2.2 Sayım formülü

```
DÜĞÜM = F (fiziksel katman iskeleti)
      + S (seviye katmanları: her K için foundation/core/service/interface)
      + B (bileşen düğümleri)
      + E (bağımlılık kenarları)
      + C (kontrat/sınır düğümleri)

Hedef: DÜĞÜM ≥ 5.000  → KABUL
```

### §2.3 Mevcut kanıt vs hedef

| Kalem | Değer | Kaynak | Durum |
|-------|-------|--------|-------|
| Fiziksel katman (yeni iskelet) | 16 (K0-K15) | `00-enterprise-index` §3 | ✅ |
| Bağımsız bileşen katmanı | 5 (K16-K20) | `00-enterprise-index` §4 | ✅ |
| Envanter bileşeni | **1.095** | `index.md` §2 (eski envanter) | ✅ disk kanıtı |
| Envanter MD | 340 | `index.md` (2026-09-24 sayımı) | ✅ disk kanıtı |
| **Toplam DÜĞÜM (yeni model)** | `[VERIFY REQUIRED]` | Sayım betiği çalıştırılınca | ⏳ |

> 🔴 **KURAL:** Toplam DÜĞÜM değeri **betik çıktısı gelmeden yazılmaz**.
> "5000+" iddiası yalnız betik `≥5000` verdiğinde onaylanır.
> Sayım betiği: `.ai/architecture/scripts/` altındaki DÜĞÜM betiği
> (mevcut `katman-sayim-rehberi.md` § PowerShell taslağı yeniden kullanılacaktır).

---

## §3 Katman Bazlı Bağımlılık Satırları (K0-K15)

| Katman | Bağımlı olduğu (çağırdığı) | Çağrıldığı | Kenar sayısı |
|--------|---------------------------|------------|--------------|
| K0 OS | — (en alt) | K1, K2, K5 | `[VERIFY REQUIRED]` |
| K1 Donanım | K0 | K2, K16, K17 | `[VERIFY REQUIRED]` |
| K2 Sürücü | K0, K1 | K3 | `[VERIFY REQUIRED]` |
| K3 Ses Motoru | K2 | K10, K15 | `[VERIFY REQUIRED]` |
| K4 AI | K5, K3 | K8 | `[VERIFY REQUIRED]` |
| K5 Veri | K0 | K6, K8 | `[VERIFY REQUIRED]` |
| K6 Güvenlik | K5 | K7 | `[VERIFY REQUIRED]` |
| K7 Middleware | K6 | K8 | `[VERIFY REQUIRED]` |
| K8 Servis | K7, K5 | K9, K12 | `[VERIFY REQUIRED]` |
| K9 API/Routing | K8 | K10 | `[VERIFY REQUIRED]` |
| K10 Uygulama | K9, K3 | K11 | `[VERIFY REQUIRED]` |
| K11 UX | K10 | — | `[VERIFY REQUIRED]` |
| K12 İzleme | K8 (okuma) | K13 | `[VERIFY REQUIRED]` |
| K13 CI/CD | K12 | K14 | `[VERIFY REQUIRED]` |
| K14 Ağ | K13 | K15 | `[VERIFY REQUIRED]` |
| K15 Medya | K14 | K3 (oynatma) | `[VERIFY REQUIRED]` |

> Bu tablo **yön kuralıdır**; sayısal kenarlar ADR/dosya analizinden sonra doldurulacaktır.
> Boş hücre uydurulmaz.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
