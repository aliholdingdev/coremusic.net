---
title: "CoreMusic — Yeni MD Üretim Listesi & Band-2 Slot Önerileri (K000–K5000)"
type: proposal
category: architecture
date: 2026-10-19
updated: 2026-10-19
status: draft
version: 1.0.0
authority: "Taslak — onay: kullanıcı · SSOT: .ai/architecture/index.md (ADR-097 / ADR-098)"
source_of_truth: ".ai/architecture/index.md §4 §5 §6 §8 · disk ölçümü 2026-10-19"
related: [".ai/architecture/k-space-slot-listesi.md", ".ai/architecture/index.md", ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md"]
group: mimari
---

# Yeni MD Üretim Listesi & Slot Önerileri (K000 – K5000)

## §1 Amaç & Kapsam

K000–K5000 aralığındaki (**5.001 slot**) yeni `.md` üretimini ikiye ayırır:

| Katman | Ne | Slot ADR'si | Durum |
|---|---|---|---|
| **A** | Mevcut 22 K'nın **içinde** eksik MD'ler | **GEREKMEZ** | ✅ onayla ve başla |
| **B** | Yeni K slot adayları (Band-2) | **GEREKİR** (ADR-098 kapalı) | 🟡 taslak — registry'ye/`k-space-slot-listesi.md`'ye **YAZILMAZ** |

**Değiştirilemez:** `k-space-slot-listesi.md` içindeki `REZERVE` satırları bu dosya
yüzünden **değişmez** (ADR-097 K5). Bu dosya `status: draft`'tır.

## §2 — GÜVENLİ ÜRETİM LİSTESİ (slot ADR'si gerekmez)

### §2.1 A1 · Kök `*-anayasa.md` — 21 K'da eksik (P0)

| # | Slot | Klasör (disk) | Önek (master dosyasından) | Üretilecek dosya |
|---|---|---|---|---|
| 1 | K000 | `K000-isletim-sistemi` | `os-` | `os-anayasa.md` |
| 2 | K002 | `K002-electroncis` | `electroncis-` | `electroncis-anayasa.md` |
| 3 | K003 | `K003-audio` | `audio-` | `audio-anayasa.md` |
| 4 | K004 | `K004-dsp` | `dsp-` | `dsp-anayasa.md` |
| 5 | K005 | `K005-firmware` | `firmware-` | `firmware-anayasa.md` |
| 6 | K006 | `K006-yapay-zeka` | `ai-` | `ai-anayasa.md` |
| 7 | K007 | `K007-data` | `data-` | `data-anayasa.md` |
| 8 | K008 | `K008-domain` | `domain-` | `domain-anayasa.md` |
| 9 | K009 | `K009-security` | `security-` | `security-anayasa.md` |
| 10 | K010 | `K010-network` | `network-` | `network-anayasa.md` |
| 11 | K011 | `K011-application` | `application-` | `application-anayasa.md` |
| 12 | K012 | `K012-services` | `services-` | `services-anayasa.md` |
| 13 | K013 | `K013-integraion` | `integration-` | `integration-anayasa.md` |
| 14 | K014 | `K014-media` | `media-` | `media-anayasa.md` |
| 15 | K015 | `K015-AI` | `ai-` ⚠️ | `ai-anayasa.md` **ÇAKIŞMA → üst karar** (öneri: `agent-anayasa.md`) |
| 16 | K016 | `K016-observability` | `observability-` | `observability-anayasa.md` |
| 17 | K017 | `K17-devops` | `devops-` | `devops-anayasa.md` |
| 18 | K018 | `K18-testing` | `testing-` | `testing-anayasa.md` |
| 19 | K019 | `K019-comilance` | `compliance-` | `compliance-anayasa.md` |
| 20 | K020 | `K020-discovery` | `discovery-` | `discovery-anayasa.md` |
| 21 | K021 | `K021-recomenataion` | `recommendation-` | `recommendation-anayasa.md` |

> `K001-donanım` hariç — onda `-anayasa.md` zaten master rolünde (13 kök MD · eksik `master` → §2.4).

### §2.2 A2 + A3 · Eksik kök rolleri tamamla (P0)

| Slot | Kök MD | Eksik roller | Üretilecek (3'er dosya) |
|---|---|---|---|
| `K019-comilance` | 10 / 13 | anayasa · bagimliliklar · dogrulama | `compliance-{anayasa,bagimliliklar,dogrulama}.md` |
| `K021-recomenataion` | 11 / 13 | anayasa · bagimliliklar · dogrulama | `recommendation-{anayasa,bagimliliklar,dogrulama}.md` |

### §2.3 A4 · K001 master boşluğu (P1)

`K001-donanım` → `donanım-master.md` yok (tek K). **İki seçenek:** (a) `-master.md` üret,
(b) "`anayasa = master`" kaydı ekle → **üst karar**.

### §2.4 A5 · Derin-dal üretimi — 3 K'da ≥500 satırlık dosya = 0 (P1)

| Slot | Dosya | Satır | ≥500 | Önerilen kademe-3 dosya adları (rol son-eki kuralı) |
|---|---|---|---|---|
| K004 | 76 | 25.141 | **0** | `biquad-kod-ornekleri` · `fft-surucu-modeli` · `latency-arayuzler` · `reverb-kod-ornekleri` · `dsp-test-stratejisi` · `efekt-zinciri-bilesenler` |
| K005 | 66 | 23.480 | **0** | `rtos-anayasa` · `boot-dogrulama` · `watchdog-kod-ornekleri` · `isletim-katmani-surucu` · `firmware-test-stratejisi` |
| K016 | 34 | 8.303 | **0** | `logging-konfigurasyon` · `metrics-toplama` · `tracing-koruma` · `alerting-riskler` · `observability-kod-ornekleri` |

> Hedef (Band-1): **≥150 dosya ≥500** → bugün **128** → kalan ~22; bu 3 K kalanın çekirdeği.

### §2.5 A6 · İskelet klasör derin-dalları (P2)

| Yer | Durum | İş |
|---|---|---|
| `K000-isletim-sistemi` (10 kademe-2 klasör = 40 dosya iskelet) | derin-dal yok | her klasöre ≥500 teknoloji dosyası |
| `K003-audio` (11 klasör × 4 dosya) | iskelet var, derin-dal yok | `device`·`engine`·`mixing`·`realtime`·`routing` derin-dalı |

## §3 — B: YENİ K SLOT ÖNERİLERİ (Band-2 · DRAFT · **yazılmaz**)

| Sıra (atama DEĞİL) | Önerilen ad | İngilizce terim | Gerekçe | Kapatığı ölü ref |
|---|---|---|---|---|
| 1 | `kimlik-ve-erisim` | identity & access | auth/RBAC domaini | ✅ `K0100-identitiy` |
| 2 | `lisans-ve-telif` | licensing & rights | telif/spotify modeli | ✅ `K020-licence` |
| 3 | `odemeler` | payments | gelir akışı | — |
| 4 | `gercek-zamanli` | realtime (WebSocket) | canlı dinleme + chat | — |
| 5 | `mobil-istemci` | mobile client | R17 mobile DSP | — |
| 6 | `bulut-altyapi` | cloud infrastructure | VISION §129 | — |
| 7 | `sosyal-ag` | social graph | `coremusic_social` | — |
| 8 | `analitik` | analytics | K021 beslemesi | — |

### §3.1 K121 – K5000 (Band-3..7 · 4.800 slot) — neden ÖNERİ YOK

1. **ADR-097 K5:** gerçek bir domain ADR'si yokken isim uydurmak yasak.
2. **Talep yok:** şu an için 22 domain + Band-2 adayları işi karşılıyor; isim üretmek
   yalnızca registry şişirir (boş klasör = kural ihlali).
3. **Format açık:** `K1000+` 4-hane biçimi + `K021+` A0–A5 alan etiketi **ADR-098**'de —
   bu iki karar verilmeden Band-3..7 aday adı **üretilmez** (1.880 slot Band-7'de dahil).

## §4 Kapılar (başlamadan)

- [ ] **Kapı 1 (A için):** üretim onayı — A1 (21 dosya) + A2/A3 (6 dosya) → **27 dosya, P0**
- [ ] **Kapı 2:** A4 (K001 master) kararı (a)/(b) · K015 `ai-` önek çakışması kararı
- [ ] **Kapı 3 (B için):** ADR-098 3 açık madde (eski adların tasdiki · 4-hane format ·
      A0–A5 etiketi) → kapandıktan **sonra** B adayları ADR'ye taşınır
- [ ] **Yasak:** B adayları `k-space-slot-listesi.md` / `index.md` §5'ye **kopyalanmaz**

## §5 Üretim Sırası & Agent Dağıtımı

| Sıra | İş | Dosya | Agent | Öncelik |
|---|---|---|---|---|
| 1 | A1 kök anayasa (21) | 21 | `architect` + `developer` (paralel, beyazliste) | P0 |
| 2 | A2/A3 eksik roller (6) | 6 | `architect` | P0 |
| 3 | A5 derin-dal ≥500 (3 K) | ~22 | `embedded-engineer` · `devops-engineer` | P1 |
| 4 | A4 / K015 önek kararı | — | **kullanıcı** | P1 |
| 5 | A6 iskelet derin-dal | ~21 | paralel ajanlar | P2 |
| 6 | ADR-098 + B listesi | 1 | `architect-reviewer` → kullanıcı onayı | P2 |

## §6 Doğrulama & Kaynak

| Alan | Değer |
|---|---|
| Ölçüm | 2026-10-19 · kök rol taraması (13 rol × 22 K) · recursive `*.md` + ≥500 sayımı |
| Kanıt | 21/22 K'da `-anayasa` YOK · K001'de `master` YOK · K019 10/13 · K021 11/13 · K004/K005/K016 `≥500: 0` |
| Aritmetik | 5.001 = 21 + 100 + 200 + 400 + 800 + 1.600 + 1.880 (index.md §4) |
| Bilinçli ayrım | B katmanı **öneridir**, atama **değildir**; atama yetkisi tek başına ADR-097 K5 + kullanıcı |
