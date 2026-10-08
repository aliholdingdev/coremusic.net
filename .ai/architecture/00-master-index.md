---
title: "00-master-index — CoreMusic K-Space V2 Master Index (K000–K5999)"
type: index
category: architecture
date: 2026-10-08
updated: 2026-10-08
version: 2.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — .ai/architecture/ ağacının tek giriş noktası (navigasyon); K-space içeriği: 00-kspace-anayasa.md"
tier: 3
domain: architecture-master
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: [".ai/CLAUDE.md", ".ai/architecture/00-kspace-anayasa.md"]
---

# 00-master-index — CoreMusic K-Space V2 Master Index

**Zorunlu Bağlantılar:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/context]] · [[decisions/accepted/ADR-096-kspace-5000-boundary-model]] · [[../CLAUDE.md]]

### CoreMusic K-Space V2 Master Index

**Kategori:** architecture (kontrol-plane)
**Durum:** Aktif (V2 rejimi — sıfırdan, ADR-096)
**Son Güncelleme:** 2026-10-08
**Yazar:** Vault Steward (plan v2.0 onayı · tek onay mercii)

---

#### §3.1 Genel Bakış

Bu dosya, `.ai/architecture/` ağacının **tek girişidir** ve K-Space V2 rejimini (K000→K5999 · 9 bant · boundary model · 5000+ hedef) kataloglar. Kurucu karar [[decisions/accepted/ADR-096-kspace-5000-boundary-model]] · K-space içeriğinin tek kaynağı [[architecture/00-kspace-anayasa]] (F1 EK A kopyası) · kurallar [[architecture/rules]] (v2.0.0). Eski rejim (K000–K499 / 10 domain / k0–k20 set) **terk edildi** — yalnız salt-okunur analiz girdisidir (`git show HEAD:…`).

---

#### §3.2 Detay

##### Bant Kataloğu (EK A §A — 9 bant)

| Bant | K-Aralığı | Kanonik Ad (EK A) | Türkçe Kapsam Özeti | Durum (2026-10-08) |
|------|-----------|-------------------|---------------------|---------------------|
| 1 | K000–K020 | FOUNDATION | İşletim sistemi, donanım, sürücü, ses motoru, AI, veri, güvenlik, middleware, servis, API, uygulama, UX, izleme, CI/CD, ağ, medya, amplifikatör, güç, termal, PCB, üretim (21 foundation kartı EK A'da) | **0/21 katman üretildi** — R3 band-1 girdisi |
| 2 | K021–K120 | ENTERPRISE EXPANSION | Kurumsal genişleme domainleri | Üretim bekliyor (R3) |
| 3 | K121–K500 | SPECIALIZED DOMAINS | Uzmanlaşmış domainler | Üretim bekliyor (R3) |
| 4 | K501–K1020 | EXTENDED / DEEP PLATFORM | Genişletilmiş / derin platform | Üretim bekliyor (R3) |
| 5 | K1021–K2020 | DEEP DOMAIN / RUNTIME / CROSS-CUT | Derin domain, runtime, çapraz-kesen | Üretim bekliyor (R3) |
| 6 | K2021–K3020 | HARDWARE / MEDIA / UI DEEP | Donanım / medya / UI derinleşmesi | Üretim bekliyor (R3) |
| 7 | K3021–K4020 | AGENT / SIM / RESEARCH / GOV | Ajan, simülasyon, araştırma, yönetişim | Üretim bekliyor (R3) |
| 8 | K4021–K5020 | COMMERCE / RIGHTS / AI / DATA | Ticaret, haklar, AI, veri | Üretim bekliyor (R3) |
| 9 | K5021–K5999 | RESILIENCE / FUTURE / RESERVED | Dayanıklılık, gelecek, ayrılmış alan | Üretim bekliyor (R3) — **dolum yalnız gerekçeli boundary ile** (ADR-096 §2.2) |

> **Hedef ≠ kanıt (H10):** hedef 5000+ katman · **kanıtla üretilen: 0** (2026-10-08 · bu satır R3 ilerledikçe güncellenir).

##### Durum Efsanesi (satır seviyesi)

| Durum | Anlamı | Kanıt standardı |
|-------|--------|-----------------|
| **PROPOSED** | K-ID + bant ayrıldı, içerik yok | K-ID kaydı (EK A/master-index) |
| **ACTIVE** | Özet satır (16 alan) dolu + kanıt + 👤 onay | `KANIT` alanı (R9 3'lü) |
| **DEPRECATED** | Kapatıldı, ID korunur | kapanış kanıtı + log |
| **IMPLEMENTED** | Repo kodu var | dosya yolu + satır (grep) |

> FM `status` enum'u (`active/draft/…`) ile **satır durumu** (bu efsade) farklı katmanlardır.

##### Yapı Ağacı (multi-MD · ADR-096 §2.5)

```text
.ai/architecture/
├── 00-master-index.md        ← bu dosya (giriş navigasyonu · version SSOT)
├── 00-kspace-anayasa.md      ← EK A kopyası — 9 bant + 21 foundation kartı (K-space SSOT)
├── 00-final-rapor.md         ← §10 28-başlık FINAL rapor (R4'te üretilecek, ≥5000 satır)
├── rules.md                  ← kural SSOT v2.0.0 (ADR-096'dan türetildi)
├── context.md                ← hot memory (derived, ≤660 bütçe)
├── K000-isletim-sistemi/     ← katman dizinleri {KID}-{türkçe-ad} (R3 üretir)
│   └── index.md              ← zorunlu çekirdek (özet satır 16 alan + derinleşme)
├── K001-donanim/ … K020-uretim/   ← (R3 band-1)
├── (bant 2-9 dizinleri)      ← R3, bant bant, kapı 5-9 gate'leri ile
└── inventory/                ← envanter partNN (R3+)
```

##### Okuma Sırası (P0 → P3)

| Katman | Dosya | Ne zaman |
|--------|-------|----------|
| P0 | `.ai/CLAUDE.md` + kök boot | her oturum |
| P1 | [[architecture/context]] (hot memory) → bu dosya | mimari görev |
| P2 | [[architecture/00-kspace-anayasa]] (bant/kart) → [[architecture/rules]] | K-space/iş kuralı |
| P3 | katman `KNNN-…/index.md` → `inventory/*` | yalnız ilgili katman |

##### Kapılar (özet — tam metin rules R11)

On Kapı [1]–[10] · §11 ≥85/100 · §9 ≥90 · her yazım sonrası `validate.mjs --check` exit 0 · KAPI 10 = insan onayı.

##### Kurulum Ölçümü (2026-10-08)

| Metrik | Değer | Kanıt |
|--------|-------|-------|
| validate gate | 35 dosya · 11 check · exit 0 | `node .ai/scripts/validate.mjs --check` |
| K-space anayasa | 5.857 satır (EK A) | `wc -l` |
| Kurucu ADR | ADR-096 (accepted) | `.ai/.decisions/accepted/` |
| Üretilen katman (KNNN-dizin) | **0** | glob: `K0*/` → boş (R3 bekliyor) |
| Arşiv spec | F1 7.576 satır · F2 9.928 satır | `.ai/prompts/2026-10-08-*` |

---

#### §3.3 Kullanım Notları

| Not | Açıklama |
|-----|----------|
| Bağlam | K-space üretimi/yönetimi yapan her görev bu dosyadan başlar |
| Kapsam Sınırı | Kural metni bu dosyada DEĞİL → [[architecture/rules]]; K-space içeriği → 00-kspace-anayasa |
| Güncellik | 2026-10-08 · sayım satırları faz ilerledikçe tazelenir (R3 gate'leri) |
| Belirsizlik | Henüz üretilmemiş katman içerikleri `PROPOSED` — uydurma içerik YOK |
| İlişki | ADR-096 + rules + kspace-anayasa + context zinciri |

---

### CoreMusic K-Space V2 Master Index — İlgili Sayfalar

| Sayfa | İlişki |
|-------|--------|
| [[architecture/00-kspace-anayasa]] | K-space içeriği SSOT'u (9 bant + foundation kartları) — bu dosya onu kataloglar, tekrarlamaz |
| [[architecture/rules]] | Kurallar SSOT'u v2.0.0 (ADR-096'dan türetildi) |
| [[architecture/context]] | Hot memory — otomatik yüklenen özet (≤660) |
| [[decisions/accepted/ADR-096-kspace-5000-boundary-model]] | Kurucu karar (K-space, format, kapılar, terk) |
| [[../CLAUDE.md]] | Anayasa (üst otorite) |

---

### CoreMusic K-Space V2 Master Index — Değişiklik Geçmişi

| Tarih | Değişiklik | Sorumlu |
|-------|-----------|---------|
| 2026-10-08 | İlk oluşturma — V2 rejimi sıfırdan (ADR-096 · F1+F2 · 12 karar); eski 110 satırlık master-index (v1.1.0) terk edildi | Vault Steward (plan v2.0)