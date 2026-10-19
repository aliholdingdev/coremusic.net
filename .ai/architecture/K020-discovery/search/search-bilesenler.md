---
node_id: "K020-discovery/search-bilesenler"
node_type: "bilesenler"
status: "draft"
parent_id: "K020-discovery/search"
children: []
depends_on:
  - "K020-discovery/search/search-kapsam.md"
  - "K020-discovery/discovery-master.md"
evidence:
  - "2026-10-17 repo doğrulaması: client/ · signal.js · history.js · manifest.json · seed_kit.json = 0 isabet (silindi) → gerçek arama yüzeyi: app-nodejs-projects/nova-search-engine/"
  - "2026-10-17 web taraması (12 servis rate/auth matrisi)"
validation: "partial"
open_questions:
  - "Federasyon bileşeni eşzamanlılık üst sınırı (servis başına 1 mi, servis paketi mi)"
group: "mimari"
---

# K020-discovery/search — Bileşenler (sorgu-keşif parçaları)

> **Rol:** Sorgu-keşif disiplinini oluşturan **parçaların** listesi, aralarındaki
> akış ve her parçanın **girdi/çıktı/kapı** sözleşmesi. Bunlar **sözleşme parçalarıdır**;
> uygulama kodu K020 alt katmanlarına aittir (master §6 kod sınırı).
>
> **Konum:** `.ai/architecture/K020-discovery/search/search-bilesenler.md`
> **Ölçüm:** canonical `[System.IO.File]::ReadAllLines().Length`.

---

## 1. Bileşen envanteri (7 parça)

| # | Bileşen | Görev | Girdi | Çıktı | Gate |
|---|---|---|---|---|---|
| B1 | **Gate denetçisi** | auth/YTM/lisans kapılarını kontrol | hedef servis listesi | `gate_state` | gate ADR'si YOK · ADR-097 §1 madde 6 |
| B2 | **Tohum kaynağı** | playlist seed / çekirdek liste (keşif taslağı) | seed kimliği | tohum sorguları | — |
| B3 | **Sorgu kuyruğu** | sorguları sıraya alır, kota-aware erteleme | sorgu listesi | sıralı iş | kota (429/402/503) |
| B4 | **Katalog istemcisi** | servis resmi docs'una göre çağrı düzeni | sorgu + servis | ham yanıt | vault-only token (ADR-095) |
| B5 | **Federatör** | çok servis sonucunu **tek dilekçe şemasına** indirger | ham yanıtlar | birleşik sonuç | — |
| B6 | **Skorlayıcı** | sonuçları önceliklendirir + kanıt URL'si | birleşik sonuç | skorlanmış küme | zero-hallucination |
| B7 | **Devir kapısı** | sonucu K020-search/K020-api'ye iletme kararı | skorlanmış küme | devir kaydı | K020 uygulama onayı |

**Akış:** `B1 → B2 → B3 → B4 ⇉ B5 → B6 → B7`
(`⇉` = servis başına paralel çağrı, `B5` tek noktada birleştirme).

---

## 2. Parça kartları (sözleşme detayı)

### B1 · Gate denetçisi

- **Karar ağacı:** `auth gate onayı yok mu? → blocked` · `servis == YTM? → closed`
  (kapı **değişmez**, ADR-097 §1 madde 6 (satır 33)) · `lisans gerekiyor mu (Genius)? → lisans gate`.
- **Çıktı:** her sorgu için `gate_state ∈ {open, blocked, closed}`.
- **Kural:** `closed`/`blocked` durumda sorgu **üretilmez bile** (B3'e girmez).

### B2 · Tohum kaynağı

- **Kaynak:** keşif taslağı — ödül sinyali olarak çekirdek/demo + arşiv **playlist seed**
  (ADR-097'de bu bölüm YOK; kendi taslağımız, `⚠️ ONAY BEKLİYOR`).
- **Çıktı:** `seed → sorgu` dönüşümü (ör. seed sanatçısı → katalog sorgusu).
- **Sinyal adları:** `playlist.follow` vb. sinyal adları vault'ta/repo'da **0 isabet**
  (`SignalType` enum'u YOK) — adlar `⚠️ TANIMSIZ` taşır; enum'a ekleme yetkisi **yok**.

### B3 · Sorgu kuyruğu

- **Ertelenme kuralı:** 429+`Retry-After` (Spotify 30 sn rolling) · 503 (MusicBrainz
  1/sn) · 402 (Musixmatch) → iş **ertelenir**, aynı turda **dövülmez**.
- **Sıra:** en ucuz servis önce (açık/ücretsiz: MusicBrainz · Deezer anonim) — kota
  tasarrufu.
- **Çıktı:** kota durumu (`retry_after`, `remaining`) yan kayıtta taşınır.

### B4 · Katalog istemcisi

- **Sözleşme:** her servis için **resmi docs** düzeni (tur-2 kanıtı, §web-arastirma):
  Spotify PKCE · Apple JWT (ES256+`kid`) · MB `User-Agent` zorunlu · Discogs OAuth 1.0a
  (imzalı) · Last.fm `api_sig` (md5) · Amazon `x-api-key` (LWA).
- **Secret:** anahtar/token **yalnız `.env`** (ADR-095 §1.2, satır 36) — bileşen **asla** token
  içermez.
- **Çıktı:** ham yanıt + HTTP durumu + rate başlıkları.

### B5 · Federatör

- **Girdi:** B4'ün farklı dillerde ham yanıtları.
- **Çıktı:** §3 sonuç dilbilgisine (uç model uyumlu) indirgeme.
- **Kural:** bilinmeyen alan **uydurulmaz** — `UNKNOWN` bırakılır (zero-hallucination).

### B6 · Skorlayıcı

- **Sinyal:** keşif taslağı — popülerlik/kanıt gücü ile önceliklendirme (ADR-097'de skorlama bölümü YOK).
- **Çıktı:** `score` + `evidence_url` (her skor **kanıt taşır**).
- **Yasak:** kanıtsız skor **yok**; "yüksek olabilir" tahmini skor sayılmaz.

### B7 · Devir kapısı

- **Karar:** `gate_state == open` **ve** skor eşiği (release 0.1: bileşik dinleme demeti —
  ad vault'ta 0 isabet, `⚠️ TANIMSIZ`) **ve** K020 onayı → devir.
- **Çıktı:** devir kaydı (kapanışta registry `index.md` §9.x'e **orchestrator** yazar).
- **Yasak:** bu bileşen **kod yazmaz** (entegrasyon K020-alt node).

---

## 3. Bileşen → servis eşlemesi (hangi parça hangi serviste)

| Parça | Spotify | Apple | YT v3 | Tidal | Deezer | MB | LB | Genius | Discogs | Amazon | Last.fm |
|---|---|---|---|---|---|---|---|---|---|---|---|
| B1 gate | auth | auth | auth | auth | auth | — | token | **lisans** | auth | **beta** | imza |
| B4 çağrı | PKCE | JWT | OAuth/anahtar | OAuth2.1+PKCE | OAuth2 | UA zorunlu | user token | OAuth2 | OAuth 1.0a | LWA+x-api-key | api_sig md5 |
| B3 erteleme | 429/30sn | 429 | 100/gün | — | — | 503/1sn | — | — | tier | — | 5/sn |
| KAPALI | — | — | YTM **KAPALI** | — | — | — | — | — | — | beta | — |

**Boş hücre** = o bileşen o serviste **işlevsiz değildir**, yalnız ek kanıt yok
(`⚠️ VERIFY REQUIRED` sayılmaz, **boş bırakılır** — uydurma yok).

---

## 4. Repo kanıtı ile eşleşme (2026-10-17 disk doğrulaması)

| Repo parçası | Kanıt (2026-10-17) | İlişki |
|---|---|---|
| `client/js/core/signal.js` · `history.js` · `manifest.json` · `seed_kit.json` · `signal_registry.json` | **0 isabet** (`client/` dizini diskte YOK) → uydurma iddialar silindi | — |
| `app-nodejs-projects/nova-search-engine/packages/search/{engine,ranking,search-index,recommend}.ts` | gerçek arama yüzeyi (255/58/127/35 satır) | B3/B5/B6 için gerçek referans yüzeyi |
| `packages/common/signals.ts` (60 satır) | dil + spam skoru (**domain sinyali DEĞİL**) | B6 skorlaması için mevcut skor dili |
| `packages/workers/{rate-limiter,queue}.ts` | kuyruk/limit işçileri | B3 kuyruk mantığı referansı |
| `.ai/projects/NovaSearchEngine/01-kurulum-mimari-ve-acik-konular.md:4` | proje kökü kanıtı | — |

**Kural:** Repo'da **var olan** bu parçalar **yeniden yazılmaz**; keşif yalnız
sözleşmeyi (girdi/çıktı) yazar (surgical changes ilkesi).

---

## 5. Bileşen bağımlılık grafiği

```
[B1 gate] ──kapalıysa DUR──┐
     │ open                │
[B2 tohum] → [B3 kuyruk] → [B4 istemci] ⇉ (servis başına paralel)
                                       ↓
                              [B5 federatör] → [B6 skorlayıcı] → [B7 devir]
                                                                        │
                                              K020-{search,api} (+ onay)
```

**Tek yönlülük:** geri besleme yalnız B7 → `open_questions` (üst merciye soru),
karar alttan yukarı **verilmez** (master zincir kuralı).

---

## 6. Riskler

| # | Risk | Etki | Aksiyon |
|---|---|---|---|
| 1 | Eşzamanlılık patlaması (10 servis × paralel) | rate-limit zinciri (429) | B3 kuyruk üst sınırı `⚠️ VERIFY` (açık soru) |
| 2 | Sonuç dilbilgisi K020-search ile çakışır | çift sözleşme | S-1 kararı beklenir |
| 3 | Token sızıntısı (log'lar) | ADR-095 ihlali | B4'te token log'lanmaz (yalnız `.env`) |
| 4 | YTM sorgusu sızarsa | kapı ihlali | B1'de hard-block (enum `closed`) |

---

## 7. Doğrulama ve İlgili dosyalar

### 7.1 Doğrulama

- [x] Frontmatter 9 alan + `group: mimari` + Authority footer.
- [x] 7 bileşen sözleşme kartı (girdi/çıktı/gate); **uygulama kodu yok** (master §6).
- [x] Repo iddiaları 2026-10-17'de doğrulandı: uydurma sinyal/istek katmanı (signal.js/history.js/manifest.json/seed_kit.json) **0 isabet → silindi**; gerçek repo yüzeyi `nova-search-engine` (§4).
- [x] KAPALI/beta/lisans kapıları korundu (YTM · Amazon · Genius).
- [ ] `validation: partial` — eşzamanlılık üst sınırı açık soru.

### 7.2 İlgili dosyalar

| Dosya | İlişki |
|---|---|
| `search/search-kapsam.md` | bu bileşenlerin kapsamı |
| `search/search-ornekler.md` | her bileşenin dilekçe örneği (13 dil) |
| `search/search-dogrulama.md` | bileşen kapı kanıtları |
| `K020-discovery/discovery-master.md` | omurga (§7 sentez) |

---

**Authority:** Bayram Ali / Vault Steward
