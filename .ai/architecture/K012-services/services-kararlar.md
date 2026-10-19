---
node_id: K012-services/services-kararlar.md
node_type: subdomain
status: draft
parent_id: K012-services
children: []
depends_on:
  - K008-domain
  - K013-integraion
  - K014-media
evidence:
  - ".ai/.decisions/index.md §3–§5 (ADR envanteri — 80 accepted / 10 rejected / 3 draft) — okundu 2026-10-18"
  - "K012-services/services-master.md §4 · §7 · §10 · §23 — okundu 2026-10-18"
  - "K005-firmware/firmware-kararlar.md §2–§10 (kanonik karar defteri yapısı) — referans 2026-10-18"
validation:
  - "2026-10-18 — her kararın dayandığı ADR numarası decisions/index.md'de diskte mevcut (uydurma numara yok)"
  - "2026-10-18 — R-012 (Microservices, rejected) §4'te kayıtlı — K012 mimarisi bunu çiğnemiyor"
  - "2026-10-18 — ADR'siz kararlar 'NUMARA YOK' etiketiyle açık bırakıldı"
open_questions:
  - "hub-and-spoke + Protobuf seçenekleri için ADR açılacak mı (master §7/5) (⚠️ onay bekliyor)"
  - "background-jobs motoru kararı (master §7/6) (⚠️ UNKNOWN)"
  - "ai-service domein kararı K006 vs K015 (master §7/1) (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# services-kararlar — Mimari Karar Kaydı

> Dayanak: `.ai/.decisions/index.md` (gerçek ADR numaraları) ·
> `services-master.md` §4/§7/§10/§23. **Uydurma ADR numarası yasaktır:**
> bu dosyadaki her numara decisions index'inde disktedir; numarası olmayan
> karar `NUMARA YOK` etiketiyle taşınır ve üst onayı bekler.

## §1 ELI15

> **Karar defteri "neden böyle yapıyoruz" kitabıdır: her madde bir soruyu
> kesin cevaplar, alternatifini de neden reddettiğini yazar.**

Bu dosyada üç kaynak vardır: (1) **mevcut ADR'ler** — K012'yi bağlayan,
numarası diskte doğrulanmış kararlar; (2) **reddedilen ADR'ler** (R-xxx) —
K012'nin yapmaması gerektiğini gösteren kararlar; (3) **ADR'siz açık
kararlar** — master §7'de bekleyen, numara üretilmemiş maddeler.

## §2 Karar tablosu (dayanak ADR'li — 10 karar)

| # | Karar | Dayanak ADR (diskte) | Durum | Etiket |
|---|---|---|---|---|
| D1 | 7-service platform mimarisi — servisler tek deploy içinde | ADR-039 | accepted | [KARAR] |
| D2 | Modular Monolith: tek deploy + zorlanabilir modül sınırı | ADR-099 | accepted | [KARAR] |
| D3 | Modül sınırı = gizlenecek karar (Parnas 1972) | ADR-098 | accepted | [KARAR] |
| D4 | Event altyapısı PSR-14 (üretim wiring PLANNED) | ADR-086 | accepted | [KARAR] |
| D5 | Cross-DB yazmada outbox zorunlu; CQRS yalnız okumada | ADR-100 | accepted | [KARAR] |
| D6 | Uzak çağrı sınırları: timeout/retry/fallback kanonik sırada | ADR-101 | accepted | [KARAR] |
| D7 | API gateway/BFF yüzeyi (CQRS + OpenAPI PLANNED) | ADR-084 | accepted | [KARAR] |
| D8 | shared/ tek parça + PSR-4 namespace (paket bölünmez) | ADR-085 | accepted | [KARAR] |
| D9 | Oran kısıtı APCu ile (60/60s middleware ile uyumlu) | ADR-013 | frozen | [KARAR] |
| D10 | IPC/sözleşme sürümleme disiplini | ADR-032 | frozen | [KARAR] |

## §3 Karar blokları (Durum · Bağlam · Karar · Alternatifler · Sonuçlar)

### §3.1 D1 — 7-Service Platform Architecture (ADR-039)

| Alan | Metin |
|---|---|
| Durum | **accepted** (diskte: `.ai/.decisions/accepted/ADR-039-…`) |
| Bağlam | Servisler nerede koşar? Dağıtık mı, tek süreç mi? (master §10.2: repo'da tek süreçli defter) |
| Karar | Servis yüzeyi 7-service platform mimarisi içinde tanımlanır (ADR-039) |
| Alternatifler | Dağıtık mikroservis → **R-012 ile reddedildi** (bkz §4) |
| Sonuçlar | ✅ S1 tek süreç defter kanıtı ile tutarlı · ⚠️ ölçeklenme senaryosu UNKNOWN |

### §3.2 D2/D3 — Modular Monolith + gizlenen modül sınırı (ADR-099 · ADR-098)

| Alan | Metin |
|---|---|
| Durum | accepted (her ikisi diskte) |
| Bağlam | 10 çocuk servis birbirine nasıl sınırlanır? |
| Karar | Tek deploy; modül sınırı kodda **gizlenir** (Parnas) — dış yüzey kontrattır |
| Alternatifler | Servis başına ayrı süreç (R-012) · her şey tek sınıf (sınır yok) |
| Sonuçlar | service-contracts (B15) sınırın tek görünür yüzü olur → Q4 ⚠️ açık |

### §3.3 D5 — Outbox zorunluluğu (ADR-100)

| Alan | Metin |
|---|---|
| Durum | accepted (Database kategorisi) |
| Bağlam | K012 bir işi bitirince olay K013'e nasıl ulaşır? (atomik değil — master EK §9 S-1) |
| Karar | Cross-DB **yazmada** outbox zorunlu; CQRS yalnız okuma sorgusu düzeyinde |
| Alternatifler | Mesajı commit sonrası göndermek (2PC'siz kırılgan — [W1] tanımı) · 2PC (tercih edilmez) |
| Sonuçlar | K013 outbox deseni K012'nin yayını da kapsar (integraion EK §10) — sahiplik §6/1 ⚠️ |

### §3.4 D6 — Uzak çağrı sınırları (ADR-101)

| Alan | Metin |
|---|---|
| Durum | accepted (Security kategorisi) |
| Bağlam | integration-service K013 kontratını çağırırken süre/retry/fallback? |
| Karar | Güvenlik kanonik sırası + timeout/retry/fallback sınırları ADR-101'e bağlıdır |
| Alternatifler | Sınırsız bekleme · kör retry (idempotency'siz) |
| Sonuçlar | Repo'da retry/circuit-breaker kanıtı **YOK** → master §14 S5/⚠️ — uygulama gap |

### §3.5 D9 — Oran kısıtı (ADR-013 + S1)

| Alan | Metin |
|---|---|
| Durum | frozen (ADR-013 APCu) + ✅ S1 middleware kanıtı (60/60s) |
| Bağlam | İstek seli nasıl durdurulur? |
| Karar | Oran kısıtı APCu altyapısında, middleware katmanında (60 istek/60 sn) |
| Alternatifler | Yalnız sunucu-dışı rate limiter (ek altyapı — reddedilen Redis vb. ad yazılmaz, kanıt yok) |
| Sonuçlar | 429 + Retry-After üretimi S1 ile kanıtlı; APCu ↔ middleware bağlantısı ⚠️ (repo'da okunmadı) |

## §4 Red edilen alternatifler (neden YAPILMADI)

| # | Reddedilen | Red kararı | Neden | Etiket |
|---|---|---|---|---|
| R1 | **Microservices mimarisi** | R-012 (rejected — diskte) | "Erken optimizasyon" | ✅ decisions §5 |
| R2 | REST-Only API | R-005 (rejected) | WebSocket gerekli | ✅ decisions §5 |
| R3 | GraphQL API | R-011 (rejected) | Over-engineering | ✅ decisions §5 |
| R4 | Eloquent ORM / ORM geneli | R-006 · ADR-002 | ORM yasak, PDO zorunlu | ✅ frozen |
| R5 | 2PC/XA ile dağıtık atomiklik | [W1] [W4] + D5 | kilit + kırılgan koordinatör | ⚠️ TASARIM (EK §9.1) |

## §5 ADR'siz açık kararlar (NUMARA YOK — uydurma yasak)

| # | Bekleyen karar | Kaynak | Kapı |
|---|---|---|---|
| P1 | hub-and-spoke evrensel spec — K012 mi K013 mü? | master §4/1 · §7/5 | ADR bekliyor |
| P2 | Protobuf mı JSON mı (kanonik model biçimi) | master §4/2 · §10.1 | ADR bekliyor |
| P3 | background-jobs iş motoru seçimi | master §7/6 | ⚠️ UNKNOWN — önce keşif |
| P4 | ai-service domein sahipliği (K006/K015) | master §7/1 | üst karar |
| P5 | playback-service ↔ K014 sahipliği | master §7/2 | üst karar |

> Bu beş maddenin **ADR numarası YOKTUR**; `.ai/.decisions/index.md`'de
> karşılığı aranmamış/üretilmemiştir (Zero-Hallucination kural 4).

## §6 Karar kuralları

1. ADR numarası yalnız decisions index'inden alınır; tahmin edilmez.
2. `accepted` karar K012'yi bağlar; `draft` (ör. ADR-097/ADR-102/ADR-103)
   yalnız bilgi taşır, bağlayıcı değildir.
3. Reddedilen karar (R-xxx) "yapılmaz" anlamına gelir — K012'de tekrar açılmaz.
4. ADR'siz karar `NUMARA YOK` etiketiyle §5'te yaşar; kod'a bağlanamaz.

## §7 Doğrulama

| # | Kriter | Yöntem | Durum |
|---|---|---|---|
| C1 | §2'deki 10 numara decisions index §3/§4'te | çapraz okuma | ✅ 2026-10-18 |
| C2 | §4'teki 4 red numarası §5'te | çapraz okuma | ✅ 2026-10-18 |
| C3 | §5'te uydurma numara yok ("NUMARA YOK") | satır-başı | ✅ 2026-10-18 |
| C4 | D4 üretim wiring durumu (PLANNED) korundu | metin okuma | ✅ 2026-10-18 |
| C5 | P1–P5 kapıları kapatıldı | — | ❌ açık (§8) |

## §8 Açık sorular

1. P1–P5 hangi sırayla ADR'ye taşınacak? → ⚠️ onay bekliyor
2. ADR-101 retry/fallback'i repo'da uygulanmış mı (master §14 S5)? → ⚠️ VERIFICATION REQUIRED
3. ADR-084 gateway yüzeyi K012'yi mi K013'ü mü bağlar? → ⚠️ VERIFICATION REQUIRED
4. ADR-100 outbox'ın K012 ayağının fiziksel sahibi (K007/K013 sınırı)? → ⚠️ VERIFICATION REQUIRED

## §9 Karar → dosya izleme

| Karar | Etkilediği dosya | Ne kırar |
|---|---|---|
| D1/D2 (tek deploy) | `services-bilesenler.md` §5 | süreç topolojisi |
| D5 (outbox) | `services-kod-ornekleri.md` (yayın örnekleri) | yayım akışı |
| D6 (uzak çağrı) | `services-test-stratejisi.md` (timeout senaryosu) | test kapıları |
| D9 (rate limit) | `services-riskler.md` (varsa) · test 429 senaryosu | oran davranışı |

## §10 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `.ai/.decisions/index.md` | numara SSOT'u |
| `services-master.md` §4/§7/§10 | ADR'siz açık sorular |
| `integraion-kararlar.md` | D5/D6'nın K013 ayağı |
| `media-kararlar.md` | K014'ün kendi karar defteri |

## §11 Kapanış kontrol listesi

- [x] §1 ELI15
- [x] Karar blokları Durum/Bağlam/Karar/Alternatifler/Sonuçlar formatında
- [x] Tüm numaralar diskte (uydurma yok)
- [x] ADR'siz kararlar "NUMARA YOK" + açık kapı
- [x] open_questions ⚠️ işaretli
- [x] ≥120 satır

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-18
