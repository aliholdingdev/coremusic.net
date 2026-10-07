---
title: "Architecture — k0 İşletim Sistemi (Katman Girişi)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/k0-isletim-sistemi/index.md (katman girişi)"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: true
risk: medium
owner: "win-sw"
depends-on: [".ai/architecture/00-enterprise-index.md", ".ai/architecture/rules.md"]
---

# k0 — İşletim Sistemi (K0)

> **Durum:** ✍️ Faz 1 pilotu — iskelet + envanter yazıldı; içerik kanıtla (kod + web + K-matrix) dolduruluyor.
> **Sahip agent:** `win-sw` (Windows Software Engineer) · **K-matrix:** CLAUDE.md §5 K0 · **Kurallar:** [[architecture/rules]] · **Katalog:** [[architecture/00-enterprise-index]]

---

## §1 Amaç (arc42-1)

CoreMusic'in en düşük yazılım katmanıdır: tüm platformlarda (Windows/Linux/macOS/RPi5) **tek, tutarlı OS arayüzü** sağlamak. Ses işleme, çoklu ortam ve gerçek zamanlı gereksinimlere göre optimize edilmiş soyutlama sunar — donanım (K1) ile uygulama (K8-K11) arasındaki köprüdür.

**Gereksinim özeti:**
- R1: 4 platformda aynı davranış sözleşmesi (cross-platform API)
- R2: Gerçek zamanlı ses için tahmin edilebilir bellek/zamanlama (K3-K5'e hizmet)
- R3: Süreç izolasyonu ve güvenlik temeli (K6'ya zemin)
- R4: Container ile taşınabilir dağıtım (K13'e hizmet)

## §2 Kısıtlar (arc42-2)

| Kısıt | Kaynak |
|---|---|
| Ses motoru (K3) K2'ye sıkı bağımlı, sıfır gecikme | CLAUDE.md §5 K3 |
| K4 harici veriye erişemez | CLAUDE.md §5 K4 |
| Windows hedef: Win12 · Linux: Lin10 · macOS: Mac8 · RPi5 | CLAUDE.md §5 K0 |
| ReactOS = erişilebilirlik hedefi (PLANNED, kanıt yok) | K-matrix K0 |
| Kod kapsamı: repo'da C++/Docker/pcntl kanıtı **YOK** (2026-10-07 ölçüm) | disk taraması |

## §3 Bağlam ve Kapsam (arc42-3)

```text
[K1 Donanım / K2 Sürücü]  ←bu katman onlara bakar
        ↑
   [k0 İŞLETİM SİSTEMİ]   ← BAĞLAM: OS çekirdek servisleri
        ↑
[K8 Servis / K13 CI-CD / K3 Ses Motoru]
        ↑
   [Dış: Docker Hub, GitHub Actions, platform vendor API'leri]
```

**Kapsam:** OS çekirdek servisleri, process, IPC, thread modeli, bellek yönetimi, container runtime, cross-platform API.
**Kapsam Dışı:** sürücü detayları (K2) · DSP zamanlaması (K3) · DB connection (K5) · deployment pipeline (K13).

## §4 Çözüm Stratejisi (arc42-4)

- **Soyutlama:** platform-specific çekirdek (win/linux/macos/rpı5) + ortak cross-platform API katmanı.
- **Kalıp:** adaptör + façade — üst katmanlar platform API'sini doğrudan çağırmaz.
- **Teknoloji kararı (PLANNED, web-doğrulanmış 2026-10-07):** libuv (C, reactor+io_uring fs) · Boost.Asio (C++ proactor) · POSIX threads · GCD (macOS) · io_uring/epoll/kqueue (Linux/BSD backend'leri).
- **Kanıt durumu:** repo'da henüz implementasyon yok → tüm teknoloji seçimleri `PLANNED`.

## §5 Bina Blokları (arc42-5 · Sei layer catalog)

| Blok | Alt bölüm | Durum |
|---|---|---|
| Platform çekirdekleri | [[architecture/k0-isletim-sistemi/01-platformlar\|01-platformlar]] | PLANNED |
| Çekirdek mekanizmalar | [[architecture/k0-isletim-sistemi/02-cekirdek-mekanizmalar\|02-cekirdek-mekanizmalar]] | PLANNED |
| Güvenlik & izolasyon | [[architecture/k0-isletim-sistemi/03-guvenlik-izolasyon\|03-guvenlik-izolasyon]] | PLANNED |
| Taşınabilirlik | [[architecture/k0-isletim-sistemi/04-tasinabilirlik\|04-tasinabilirlik]] | PLANNED |
| Envanter (12 alan, Kx.yy.zzz) | [[architecture/k0-isletim-sistemi/inventory/k0-platformlar-part01\|inventory/k0-platformlar-part01]] + part02 | ✍️ |

**Katman arayüzü (public):** üst katmanlar yalnız `k0-cross-platform-api` üzerinden çağırır; platform-specific semboller (`epoll_*`, `CreateProcessW`, GCD queue'ları) **gizlidir**.

**İzinli kullanım ilişkisi (Sei):** K0 → yalnız K1 (donanım soyutlaması) · K8/K13/K3 → K0'a **üstten** çağırabilir · **K0 asla K8+ katmanlarına bağımlı olamaz**.

## §6 Çalışma Zamanı (arc42-6)

| Senaryo | Akış | Durum |
|---|---|---|
| Ses thread'i başlatma | Platform thread primitive → lock-free kuyruk (K3'e teslim) | PLANNED |
| Süreç izolasyonu | spawn sandbox → capability drop → exec (K6 politikası) | PLANNED |
| Container health | Docker healthcheck → K13 pipeline | PLANNED |

## §7 Dağıtım (arc42-7)

Mevcut kanıt: `.github/workflows/ci.yml` + `secret-scan.yml` (yalnız K13 kapsamında). Container/Dockerfile **repo'da YOK** (2026-10-07). Deployment topolojisi için [[architecture/k13-cicd]] SSOT'u.

## §8 Çapraz Kavramlar (arc42-8)

- **Bellek:** audio thread'inde heap allocation yasak (embedded kuralı) → K0 sabit boyutlu havuzlar sunmalı.
- **Zamanlama:** gerçek zamanlı öncelik (SCHED_FIFO / MMCSS) K3 gereksinimi.
- **İzolasyon:** process sandbox K6 auth zincirinin parçası.

> **ADR bağları (okunarak — 2026-10-07):** [[.decisions/accepted/ADR-017-dsp-hardware-mode]] (hard-RT kısıtları) · [[.decisions/accepted/ADR-019-per-os-neva-player]] (IAudioBackend adapter sınırı + ASIO→WASAPI→Null fallback, brain.md:862) · [[.decisions/accepted/ADR-032-ipc-contract-versioning]] (IPC şema/sürüm — spec PLANNED) · [[.decisions/accepted/ADR-006-performance-targets]] (<10ms ASIO / <20ms WASAPI, CLAUDE.md:429). Kararlar bu dizinde DEĞİL, .ai/.decisions/ (R1).## §9 Kararlar (arc42-9 → ADR pointer)

Bu katmanda **ADR üretilmez**; kararlar `.ai/.decisions/` (SSOT). İlgili mevcut ADR'ler: ADR-017 (DSP hardware mode), ADR-038 (ses kartı) — tam liste: `.ai/.decisions/index.md`.

## §10 Kalite Gereksinimleri (arc42-10)

| Kalite | Hedef | Ölçüm |
|---|---|---|
| Taşınabilirlik | Aynı API 4 platformda | derleme matrisi (PLANNED) |
| Gecikme | K3 sıfır gecikme sözleşmesi | benchmark (PLANNED) |
| Güvenlik | K6 bypass'sız izolasyon | security test (PLANNED) |

## §11 Riskler ve Teknik Borç (arc42-11)

1. **R-K0-1:** Repo'da K0 implementasyonu yok → tüm envanter PLANNED; gerçek kod gelene kadar `IMPLEMENTED` iddiası yazılmaz (Zero-Hallucination).
2. **R-K0-2:** K-matrix "ReactOS" hedefi kanıtsız → `UNKNOWN` işaretli.
3. **R-K0-3:** Container runtime kararı (Docker vs containerd) verilmemiş → Faz 1 kapı onayı bekliyor.

## §12 Sözlük (arc42-12)

| Terim | Tanım |
|---|---|
| Reactor | Hazırlık (readiness) tabanlı olay döngüsü — epoll/kqueue (libuv) |
| Proactor | Tamamlama (completion) tabanlı — io_uring/IOCP (Boost.Asio native) |
| Cross-platform API | Platform çekirdeklerinin üzerindeki ortak soyutlama |

---

## Vault Gövdesi (8-iskelet)

**Amaç/Kapsam/Bağlam:** §1-§3 · **Mimari:** §5 (layer catalog) · **Kurallar:** [[architecture/rules]] R1-R13 (bu dosyada kural tekrarı YOK) · **Workflow:** [[architecture/k0-isletim-sistemi/WORKFLOW]] · **Doğrulama:** validator 8 check + faz kapıları (R11) · **Referanslar:** [[architecture/00-enterprise-index]] · [[CONTEXT]] · .ai/.decisions/index.md · backup referans (salt-okunur): `C:\www\_backup\arch-2026-10-06_1057\architecture\k0-isletim-sistemi\`

**Sonraki adım:** envanter part'ları dolduruluyor → tam tarama → katman onayı (R10/R11).
