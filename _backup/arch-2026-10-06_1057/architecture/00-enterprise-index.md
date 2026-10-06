---
title: "CoreMusic — Enterprise Layered Architecture (Sıfırdan Master Index)"
type: architecture-index
category: architecture
date: 2026-10-06
updated: 2026-10-06
status: active
version: 1.0.0
authority: "SSOT — Enterprise katman modeli (yeni)"
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
supersedes: "index.md (v3.0.1 — K0-K20 envanteri, salt-okunur referans olarak kalır)"
---

# CoreMusic — Enterprise Layered Architecture (Sıfırdan)

> **Bu dosya sıfırdan yazılmış yeni mimari SSOT'udur.**
> Mevcut `index.md` (v3.0.1) ve `k0…k20` klasörleri **silinmemiştir** — envanter referansı olarak durur.
> Çelişkide bu dosya kazanır (kullanıcı kararı 2026-10-06: "sıfırdan yaz").

---

## §1 Amaç

CoreMusic ekosisteminin (backend · API/server/client · local+server yazılım · user session ·
DSP/ASIO/WASAPI · medya streaming · AI · güç/amplifikatör donanımı) **5000+ mantıksal katmanlı**
tek bir enterprise katman modelinde tanımlanması.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Katman modeli (fiziksel + mantıksal) | Kod implementasyonu |
| Bağımlılık matrisi ve yön kuralları | Veritabanı şeması |
| Araştırma kanıtları (web) | UI mockup (→ `.ai/ui-design/`) |
| Fiziksel dosya/klasör planı | BOM/PCB üretim dosyaları |

---

## §2 Sayım Birimi ve 5000+ Hedefi

**Sayım birimi = DÜĞÜM (mantıksal katman).** Fiziksel klasör/dosya sayısı ≠ katman sayısı.

```
Toplam mantıksal katman =
    16 ana katman (K0-K15, fiziksel iskelet)
  + 4 alt seviye × 16 katman           (doküman hiyerarşisi)
  + bileşen düğümleri (kanıt: 1.095 bileşen — index.md §2)
  + bağımlılık düğümleri
  + sınır/kontrat düğümleri
  ────────────────────────────────────
  Hedef: ≥ 5.000  → KABUL
```

> **Kanıt tabanlı sayı (disk):** 1.095 bileşen · 340 MD · 21 katman (eski envanter).
> **Yeni modelde hedef ≥5.000**; kesin sayı katman dosyaları (§7) yazıldığında
> `katman-sayim-rehberi.md` DÜĞÜM kuralıyla sayılacaktır — **şu an için kesin sayı `[VERIFY REQUIRED]`**.
>
> 🔴 **YASAK:** Kaynağı olmayan katman sayısı uydurmak. 5000+ iddiası yalnız
> DÜĞÜM sayımı bittiğinde yazılır.

**5000+ fiziksel klasör/dosya açmak YASAKTIR.** Mantıksal katmanlar belge içi tanımdır.

---

## §3 Katman Modeli — K0-K15 (Fiziksel İskelet)

Kullanıcı referans modeli (2026-10-06) ile vault mevcut katman adları **aynı kökene** oturur;
K16-K20 (class-ab, güç, termal, pcb, bom) **bağımsız bileşen alanıdır** — bkz. §4.

| # | Katman | Kod | Alan | Kapsam | Birincil Teknoloji |
|---|--------|-----|------|--------|--------------------|
| K0 | İşletim Sistemi | `k0-os` | A0 | Çalışma zamanı, boot, FS düzeni | Windows, MMCSS |
| K1 | Donanım | `k1-hw` | A0 | DAC/ADC, amplifikatör, güç arabirimi | PCM3168A, AK4458 |
| K2 | Sürücü | `k2-drv` | A0 | ASIO, WASAPI, cihaz sürücüsü | ASIO SDK, WASAPI |
| K3 | Ses Motoru | `k3-audio` | A0 | DSP zinciri, efekt, filtre, sentez | C++20, JUCE |
| K4 | Yapay Zeka | `k4-ai` | A0 | Ses analizi, öneri, model çıkarımı | Semantic ID, CF+CBF |
| K5 | Veri Yönetimi | `k5-data` | A0 | Şema, migration, BCNF | MySQL 9, PDO |
| K6 | Güvenlik | `k6-sec` | A1 | OWASP, CSRF/CSP, oturum, şifreleme | Argon2id, AES-256-GCM |
| K7 | Middleware | `k7-mw` | A1 | PSR-15 zinciri | PHP 8.4 PSR-15 |
| K8 | Servis | `k8-svc` | A2 | İş mantığı, servis nesneleri | PHP 8.4 |
| K9 | API / Routing | `k9-api` | A2 | Endpoint, controller, router | fast-route, PageRouter |
| K10 | Uygulama | `k10-app` | A3 | Ürün yüzeyi, masaüstü/web uygulama | Vanilla JS ES6+ |
| K11 | UX | `k11-ux` | A3 | Responsive mimari, cihaz matrisi | ITCSS, BEM, WCAG 2.2 |
| K12 | İzleme | `k12-obs` | A4 | Log, metrik, health check, audit | log.md, health states |
| K13 | CI/CD | `k13-cicd` | A4 | Pipeline, deploy, secret scan | GitHub Actions |
| K14 | Ağ | `k14-net` | A4 | DNS, CDN, subdomain, routing altyapısı | CDN, TLS |
| K15 | Medya Streaming | `k15-media` | A4 | Codec, transcode, akış protokolü | FFmpeg, HLS/DASH |

**Alan etiketleri (A0-A5)** — etikettir; kanonik bağımlılık matrisi §6'dadır.

| Alan | Katmanlar | Kapsam |
|------|-----------|--------|
| A0 | K0-K5 | Altyapı / Donanım-çekirdek |
| A1 | K6-K7 | Güvenlik |
| A2 | K8-K9 | Backend / Routing |
| A3 | K10-K11 | Presentation |
| A4 | K12-K15 | Veri / Entegrasyon |
| A5 | (bağımsız) | Bileşenler — §4 |

---

## §4 Bağımsız Bileşen Alanı (A5)

K16-K20, K0-K15 hiyerarşisine **girmez**; bağımsız bileşen katmanlarıdır
(vault kararı: "üretim/bileşen katmanlarını üst/alt hiyerarşiye sokmak YASAK").

| # | Bileşen | Kapsam | Not |
|---|---------|--------|-----|
| K16 | Class AB Amplifikatör | Sınıf-AB topolojisi, bias, çıkış kademesi | **Class D DEĞİL** |
| K17 | Güç Kaynağı | Boost converter, regülasyon, dağıtım | 12V nom / 24V maks |
| K18 | Termal | Sıcaklık dağıtımı, soğutma, koruma | bias termal izleme |
| K19 | PCB | Kart tasarımı, yerleşim, üretim dosyaları | — |
| K20 | BOM | Parça listesi, tedarik, maliyet | — |

---

## §5 Güç & Amplifikatör — Devre Seviyesi Tasarım (Doğrulanmış)

> **Zorunlu:** Class AB (Class D yasak) · boost converter · **12V normal / 24V maks** ·
> DC 12-24V aralık · pil/adapter uyumlu · temiz ses kalitesi.

### §5.1 Boost Converter (K17)

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Girdi aralığı | 6V-18V (örnek) → **12-24V hedef** | TI SNVA824 (LM5155) |
| Çıkış | **24V regüle** | TI SNVA824 |
| Örnek yük | 2A | TI SNVA824 |
| Anahtarlama frekansı | 440 kHz (AM bandı 530k-1.8M dışında) | TI SNVA824 |
| Tahmini verim | %90 | TI SNVA824 |
| İndüktör ripple oranı | %30-70 arası, hedef **%60** | TI SNVA824 |
| Örnek indüktör | 6.8 µH | TI SNVA824 |
| Akım sınırlama payı | %20 | TI SNVA824 |
| Sense direnci örneği | 8 mΩ | TI SNVA824 |
| Çıkış kap. ripple hedefi | 100 mV | TI SNVA824 |
| Geri besleme üst direnç | 47 kΩ (RFBT) | TI SNVA824 |
| Kompensasyon | Type II · sıfır ≈682 Hz | TI SNVA824 |

> `⚠️ VERIFICATION REQUIRED`: CoreMusic'e özel nihai L, C, R değerleri **henüz hesaplanmadı**;
> yukarıdaki değerler TI uygulama notu örneğidir, birebir kopya DEĞİLDİR.

### §5.2 Class AB Çıkış Kademesi (K16)

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Topoloji | Tamamlayıcı Darlington (sürücü + çıkış) | leungengineering/ngspice |
| Bias optimum kuralı | `Re` üzerinde ≈**26 mV** düşüm → `Ic = 26mV/Re` | leungengineering |
| Örnek: Re = 0.33 Ω | Ic ≈ **79 mA** quiescent | leungengineering |
| Sürücü emiter direnci örneği | 33 Ω → 21 mA (`0.7/33`) | leungengineering |
| Bias kaynağı | **Vbe multiplier** (Q11) + ayarlanabilir R7 | leungengineering |
| Termal izleme | Q11 **aynı soğutucuya** monte (output transistörleri ile) | leungengineering |
| Crossover bozulma | `Re = re(idle)` iken minimum | leungengineering |
| Çıkış empedansı (örnek) | ≈0.3 Ω (8Ω yük, 100mA bias) | leungengineering |
| `gm-doubling` | Class-A bölgesinde ikili iletim → artan bozulma | D. Self, *Audio Power Amplifier Design Handbook* |
| Güç/Vcc ilişkisi | `P ∝ Vrms²` → **Vcc iki kat → güç 4 kat** | QEL Kit 3048 ölçümü |
| QEL ölçüm (8Ω) | 9V→361mW · 12V→684mW · 15V→1.22W · 18V→1.68W | QEL 3048 |

> **Tasarım sonucu:** 12V normal modda ≈0.7W/kanal sınıfı, boost ile 24V'a çıkıldığında
> ≈4× güç potansiyeli — **12/24V mimarisi güç hedefini doğrular**.

---

## §6 Bağımlılık Matrisi (Yön Kuralları)

```
A5 (Bileşen) ──sınır──> A0 (Donanım/Çekirdek)
A0  →  A1  →  A2  →  A3          (yukarı akış: güvenlik → backend → presentation)
A4  →  A2                            (izleme/cicd → backend gözlemi)
A3  →  A2  →  A1  →  A0             (istek akışı — tersine çağrı yasak)
```

| Kural | İhlal |
|-------|-------|
| Alt katman, üst katmanı **import edemez** (A0 → A2 yasak) | Layer violation → revert + log ERROR |
| K15'in üretim/bileşen katmanına bağımlılığı **YOK** | — |
| K16-K20'yi K0-K15'e alt/üst yapmak **YASAK** | Bağımsız alan |
| Çapraz katman erişimi yalnız **kontrat (interface)** ile | Doğrudan bağımlılık yasak |

---

## §7 Araştırma Kanıtları (Web Research — 2026-10-06, 10 başlık)

| # | Başlık | Doğrulanmış Bulgu | Katman |
|---|--------|-------------------|--------|
| 1 | Streaming platform mimarisi | Spotify: katmanlı mikroservis · CDN · hibrit ML (CF+content+NLP) · **100B+ olay/ay** · metadata **500K-1M QPS** | K8, K14, K4 |
| 2 | C++ ASIO / düşük gecikme | ASIO SDK (Steinberg, özel lisans) · K2 birincil | K2 |
| 3 | WASAPI | `AUDCLNT_SHAREMODE_EXCLUSIVE` + `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` · `SetEventHandle` · MMCSS **"Pro Audio"** görevi · IAudioClient3 ile Win10 shared-düşük-period · WaveRT > WaveCyclic | K2, K0 |
| 4 | HLS/DASH streaming | LL-HLS (Parts) vs LL-DASH (chunked CMAF) · ortak anahtar **keyframe/GOP** · 1s GOP + 2s segment + 0.5s part · CBR + `zerolatency` + `bframes=0` · glass-to-glass ≈2.0s (DASH) / 3.0s (HLS) | K15 |
| 5 | Transcode pipeline | DAG (probe→split→encode→merge→package→post) · FFmpeg worker pool · VMAF kalite kapısı (eşik ≈80) · segment-paralel · priority router P0/P1/P2 | K15, K13 |
| 6 | AI müzik önerisi | **Semantic IDs** (RQ-VAE, n=4, k=64..16384) · parametre −%75…−%99 · Pandora 10M kullanıcı 30 gün A/B · TransE benzeri `u+q≈t` · cross-attention / MoE multimodal · 100K+ sorgu-öğe üçlüsü | K4 |
| 7 | Açık kaynak müzik server | Koel (17.195★, PHP/Vue) · Ampache (3.793★) · SPlayer (7.423★) | K8, K10 |
| 8 | DSP kütüphaneleri | JUCE (8.811★) · EasyEffects (9.855★) · sndfilter (486★) · DSP-Cpp-filters (187★) · Signalsmith (109★) · FAUST · SoLoud · PortAudio · libsndfile | K3, K15 |
| 9 | Class AB + boost | §5 tabloları | K16, K17 |
| 10 | Entegrasyon mimarisi | Stateless servis · job queue (SQS) · Redis state · CDN edge · token-auth segment teslimi | K8, K13, K14 |

> **Lisans riski:** JUCE/EasyEffects/FAUST/HISE = GPL · FFmpeg/libsndfile = LGPL → §8 notu.

---

## §8 Multi-MD Dosya Planı (Bu SSOT'un alt belgeleri)

| Dosya | İçerik | Durum |
|-------|--------|-------|
| `00-enterprise-index.md` | Bu dosya — master | ✅ yazıldı |
| `01-domain-boundaries.md` | A0-A5 + dosya tipi sorumlulukları | ⏳ |
| `02-layer-specs-k0-k05.md` | A0 katman detayları (OS→Veri) | ⏳ |
| `03-layer-specs-k06-k11.md` | A1-A3 (Güvenlik→UX) | ⏳ |
| `04-layer-specs-k12-k15.md` | A4 (İzleme→Medya) | ⏳ |
| `05-hardware-ab-power.md` | K16-K20 devre detayı (boost+bias hesap) | ⏳ |
| `06-dependency-matrix.md` | Tam düğüm matrisi + DÜĞÜM sayımı | ⏳ |
| `07-research-evidence.md` | 10 başlık tam kaynakça | ⏳ |
| `08-conflict-resolution.md` | Prompt↔disk çelişki kararları | ⏳ |

---

## §9 Çelişki Kararları (Kayıt)

| # | Çelişki | Karar | Kaynak |
|---|---------|-------|--------|
| 1 | `architecture.old.eksi/` diskte YOK | Birincil kaynak yok → mevcut vault SSOT | Görev 1 kanıtı |
| 2 | Prompt "K0-K15" ↔ disk "K0-K20" | **Yeni model K0-K15 iskelet + A5 bağımsız bileşen** (§3-§4) | kullanıcı 2026-10-06 |
| 3 | "5000+ katman" ↔ mevcut 340 MD | Sayım birimi **DÜĞÜM**, fiziksel dosya değil (§2) | ADR-026 |
| 4 | `.workflow/` ↔ `.workflows/` | Gerçek dizin **`.workflows/`** (12 dosya) | disk kanıtı |
| 5 | K1 (−17) / K13 (−30) sayım sapması | **`⚠️ VERIFICATION REQUIRED` açık** — kapatılmadı | index.md |
| 6 | Class D referansları (TPA3255 vb.) | **Yalnız topoloji karşılaştırması**; Class AB zorunlu | kullanıcı gereksinimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
