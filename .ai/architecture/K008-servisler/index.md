---
title: "K008 SERVICES «KULE» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K008-servisler/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: services
ssot: true
risk: medium
owner: backend
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K008 SERVICES «KULE» — Katman Index

> **Authority:** Bu dosya K008 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md §A.1 K008 kartı` > `.ai/CLAUDE.md §5/§10` > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md §2`. **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10).
> Bu dosya staging'dir; vault'a yazılmadı.

## Künye

| Alan | Değer |
|---|---|
| K-ID | K008 |
| Kanonik Ad | SERVICES |
| Teatral Epitet | «KULE» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2) |
| Dizin deseni | `.ai/architecture/K008-servisler/index.md` (R2.2 — henüz üretilmedi, bu dosya staging taslağı) |
| Tier / Domain | 3 / services |
| Owner (`.ai/AGENTS.md` §4 registry) | backend |
| Risk | medium — güvenlik yüzeyi K006'ya bağımlı (security=ORTA, EK A §A.1); K006 dışı high yok |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-007 · ADR-019 · ADR-026 · ADR-030 · ADR-037 · ADR-039 · ADR-050 · ADR-058 · ADR-062 · ADR-085 · ADR-086 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K007-K013) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K008 SERVICES «KULE», CoreMusic'in yedi backend servisini ve olay sınırını (Event Boundary) barındırır:
Control · Media · Audio · Device · Network · AI · Download — servisler arası doğrudan çağrı YASAKTIR,
iletişim yalnız Event Bus üzerinden yapılır (`.ai/CLAUDE.md §5 K8` hard guardrail · ADR-086).
Anayasa §10 yedi servisi hedef mimari olarak tanımlar; repo'da 2026-10-08 ls ile fiziksel bulunanlar:
`media.coremusic.net/`, `auth.coremusic.net/` (Control/ kimlik ayağı) + `shared/src/{Events,Device,AI,...}`
altyapı kütüphaneleri — diğer servis dizinleri PLANNED (H1).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K008 | SERVICES | «KULE» | services | PHP 8.4 (Control/Media/AI) · C++20 JUCE (Audio 9741/9742) · C++20 (Device/Network) · Node.js LTS (Download 3001) | 7 backend servis (Control, Media, Audio, Device, Network, AI, Download) + Infra (11) + Event Boundary — 55 bileşen (anayasa §5 K8) | K009 API'den gelen command/query · K007'den devredilmiş doğrulanmış istek · Event Bus abonelikleri | servis yanıtı (K009'a) · domain/integration event'leri · kalıcı veri yazımı (K005 üzerinden repository) | K000-K007 (alt katmanlar) + port/adapter · K005 DATA (repository/port) | K007+ üst katmanlara doğrudan erişim (H20) · servisler arası senkron doğrudan çağrı (§5 K8) · katmanlar arası doğrudan veri paylaşımı (H19) | Her servis kendi veri sahipliğini taşır (18 BCNF bölünmesi — §18); servisler arası veri YOK, yalnız event | ORTA (EK A §A.1); servis içi authz K006 RBAC'e tabi; iç servis-üstü-servis kimlik kanıtı K006/JWT alanı | fail-over (EK A) · servis yanıt vermezse degrade mod + Event Bus kuyruğu · zaman aşımı → hata sözleşmesi (K009) | servis logları + health-check + event bus kuyruk/yaş metrikleri (merkezi altyapı K012 — PLANNED) | PHPUnit 11 (`shared/tests/` — Api · Events · Repository · Security · Unit · …) · hedef ≥80% (§17); C++/Node testleri katmana özel ⚠️ | repo: `media.coremusic.net/` + `auth.coremusic.net/` + `shared/src/Events/` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K8/§10/§18` + `00-kspace-anayasa.md §A.1 K008` · ADR: ADR-039/086/026/058 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B) |

**Alan okuma notu:** İZİNLİ ∩ YASAK = ∅ ✓. Hedef ≠ kanıt ayrı (H10): "55 bileşen" anayasa envanter hedefidir;
"2 servis dizini + 1 olay kütüphanesi" repo kanıtıdır — aynı cümlede birleştirilmez.

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K008 |
| 2 | KANONİK_AD | SERVICES |
| 3 | TEATRAL_EPİTET | «KULE» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | services |
| 5 | SUBDOMAIN | control-service · media-service · audio-service · device-service · network-audio-service · ai-service · download-service · event-boundary |
| 6 | BOUNDED_CONTEXT | Backend Servis Uçları — her servis kendi BC'sini yönetir; ortak dil (contract) K009 OpenAPI + K008 Contracts |
| 7 | RUNTIME | PHP 8.4 (Control 81 · Media 5000/6000 · AI internal) · C++20 JUCE (Audio REST 9741 / WS 9742) · C++20 (Device BLE/WiFi/USB · Network WebRTC/P2P) · Node.js LTS (Download 3001 HTTP/WS) |
| 8 | SORUMLULUK | Control (auth/session/RBAC uçları) · Media (library/metadata/streaming) · Audio (player/DSP/mixer/EQ) · Device (BT/WiFi/USB) · Network (streaming/multi-room) · AI (recommendation) · Download (Deezer/YouTube) · Event Boundary (PSR-14 Event Bus) · Infra (11) |
| 9 | GIRDI | K009'dan gelen command/query (doğrulanmış DTO) · Event Bus event'leri · K005 repository sorguları · K000 runtime · yapılandırma (env/config) |
| 10 | CIKTI | K009'a servis yanıtı (JSON/DTO) · yayınlanmış domain/integration event'leri · K005'e repository yazımı · K012'ye log/metrik akışı |
| 11 | IZINLI_BAGIMLILIK | K000-K007 (alt katmanlar; K007'den yalnız devredilmiş istek alır), K005 üzerinden repository/port, port/adapter (EK A aralık), PSR-14 Event Bus (dış standard) |
| 12 | YASAK_BAGIMLILIK | K007'ye geri çağrı (H20) · K009/K010/K011'e doğrudan erişim · servisler arası senkron doğrudan çağrı (anayasa §5 K8) · H19 doğrudan veri paylaşımı · K005'e repository'siz doğrudan SQL/`SELECT *` |
| 13 | DATA_BOUNDARY | 18 BCNF veritabanının servis-bazlı sahipliği (§18); servisler arası veri paylaşımı yalnız event payload'ı ile ve minimal; dosya medya erişimi K015/K019 ile sınırlı |
| 14 | SECURITY_BOUNDARY | security=ORTA (EK A §A.1); RBAC kararları K006'dan; servis içi yetkisiz erişim → 401/403 (K007'den gelen kimlikle); Download servisi anti-ban/credential yüzeyi (ADR-028/026) K006'ya devredilir |
| 15 | FAILURE_MODE | fail-over (EK A §A.1) · servis DOWN → degrade servis yanıtı + event kuyrukta bekler · zaman aşımı → K009 hata sözleşmesi · circuit-breaker davranışı tanımsız → ⚠️ |
| 16 | OBSERVABILITY | servis seviye logları (structured — `shared/src/Log/LoggerFactory.php` altyapısı) · health-check uçları (PLANNED ⚠️) · event bus kuyruk/yaş metriği (K012'ye devredilir) |
| 17 | TEST | PHPUnit 11 — `shared/tests/` altında Api · Events · Repository · Security · Unit · Component · OAuth · Middleware · Fixture dizinleri (ls 2026-10-08); hedef ≥80% (anayasa §17); Download=Vitest, Audio=Google Test hedefleri (§17) |
| 18 | KANIT | repo: `media.coremusic.net/` · `auth.coremusic.net/` · `shared/src/Events/Domain/*Event.php` (7 dosya ls) · `shared/src/Events/{Domain,Integration}` · `shared/src/Contracts/Events/*` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K8 · §10 · §11 · §18` + `00-kspace-anayasa.md §A.1 K008` · ADR: ADR-039/086/026/058/007/085 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED |
| 19 | KANIT_TARIHI | 2026-10-08 (repo ls + vault read) |
| 20 | EPİTET_KALİTE_NOTU | «KULE» — yüksekte duran, birbirine bağlı ama ayrı ayrı ayakta duran yapı metaforu; EK A §A.1 anahtar satırı: `K008 · SERVICES · «KULE» · SOFTWARE` |

**R4.4 kart kapıları:** (a) 20 alan dolu ✓ · (b) İZİNLİ ∩ YASAK = ∅ ✓ · (c) KANIT 3'lü format
(repo | vault+ADR | web ⚠️) ✓ · (d) veri sınırı: her servis kendi BC/DB sahipliğinde, paylaşımlı veri yok ✓.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K008 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: Control · Media · Audio · Device · Network ·
AI · Download · Event Boundary".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| Control | Auth, session, RBAC uçlarını yürütür (port 81) | IMPLEMENTED (repo — kimlik ayağı) | `auth.coremusic.net/` (ls 2026-10-08; index.php · routes · handler · tests) · `.ai/CLAUDE.md §10 #1` · ADR-058 (accepted/ ls) |
| Media | Library, metadata, streaming (port 5000/6000 · PHP + FFmpeg) | IMPLEMENTED (repo dizini) | `media.coremusic.net/` (ls: src · config · bin · composer.json) · `.ai/CLAUDE.md §10 #2` · §11 port 5000/6000 |
| Audio | Player, DSP, mixer, EQ (REST 9741 · WS 9742 · C++20 JUCE) | PLANNED — C++ servis dizini root'ta gözlenmedi → ⚠️ | `.ai/CLAUDE.md §10 #3 · §11 9741/9742` · `00-kspace-anayasa.md §A.1 K008` · ⚠️ (grep kapsamı: kök dizin) |
| Device | Bluetooth, WiFi, USB (BLE/WiFi/USB · C++20) | PARTIAL — kütüphane ayağı var, servis ayağı PLANNED | `shared/src/Device/` (ls) · `.ai/CLAUDE.md §10 #4` · ⚠️ (servis süreci grep edilmedi) |
| Network Audio | Streaming, multi-room (WebRTC/P2P · C++20) | PLANNED → ⚠️ | `.ai/CLAUDE.md §10 #5` · anayasa §A.1 K008 · ⚠️ |
| AI | Recommendations (internal · PHP + Python) | PARTIAL — kütüphane ayağı var | `shared/src/AI/` (ls) · `.ai/CLAUDE.md §10 #6` · ADR-030 (accepted/ ls) |
| Download | Deezer/YouTube indirme (port 3001 · Node.js + TS) | PLANNED — `download.coremusic.net/` root'ta YOK (ls 2026-10-08) | `.ai/CLAUDE.md §10 #4/§11 3001` · ADR-026 (accepted/ ls) · repo: dizin YOK |
| Event Boundary | Servisler arası tek iletişim: PSR-14 Event Bus; senkron doğrudan çağrı yasak | IMPLEMENTED (altyapı — kütüphane düzeyi) | `shared/src/Events/` + `shared/src/Contracts/Events/{DomainEventInterface,IntegrationEventInterface}.php` (ls) · ADR-086 (accepted/ ls) · `.ai/CLAUDE.md §6A.4` |

### §4.2 Anayasa §5 K-Matrix Satırı (K8) — 55 bileşenin açılımı

Kaynak: `.ai/CLAUDE.md §5` — **K8 Servis | Control, Media, Audio, Device, Network, AI, Download +
Infra (11) | 55 | Servisler arası doğrudan çağrı yasaktır, Event Bus kullanılır.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| Control | Auth/session/RBAC servis ayağı | IMPLEMENTED | `auth.coremusic.net/` (ls) · §10 #1 |
| Media | Kütüphane/metadata/streaming | IMPLEMENTED | `media.coremusic.net/` (ls) · §10 #2 |
| Audio | Player/DSP/mixer/EQ | PLANNED ⚠️ | §10 #3 · ⚠️ |
| Device | BT/WiFi/USB | PARTIAL | `shared/src/Device/` (ls) · §10 #4 |
| Network | WebRTC/multi-room | PLANNED ⚠️ | §10 #5 · ⚠️ |
| AI | Öneri motoru | PARTIAL | `shared/src/AI/` (ls) · §10 #6 |
| Download | 3001 HTTP/WS indirme | PLANNED ⚠️ | §10 #7 · repo dizin YOK |
| Infra (11) | Altyapı bileşen grubu (toplam 55'in parçası) | HEDEF — 11'lik döküm anayasa §5'te verilmedi → ⚠️ | `.ai/CLAUDE.md §5 K8 satırı` (11 sayısı) · bileşen listesi: ⚠️ VERIFICATION REQUIRED (F1/EK A detayı araştırılmalı) |
| "55 bileşen" | K8 envanter toplamı | HEDEF (H10: hedef ≠ kanıt) | `.ai/CLAUDE.md §5 K8` · repo kanıtı ayrı satırda |

### §4.3 Servis Detay Blokları

#### §4.3.1 Control Service (port 81 · HTTP · PHP 8.4)

| Boyut | İçerik |
|---|---|
| Sorumluluk | Auth, session, RBAC — kimlik yaşam döngüsünün servis ayağı (anayasa §10 #1) |
| Girdi/Çıktı | K007'den kimlikli istek → oturum/RBAC kararı → K009 yanıtı + event (login/logout) |
| Event'ler | `UserLoggedInEvent` · `UserLoggedOutEvent` · `PasswordResetRequestedEvent` (repo `shared/src/Events/Domain/`, ls) |
| Kanıt | `auth.coremusic.net/` (ls 2026-10-08: index.php, routes/, handler/, tests/, phpunit.xml) · ADR-058 centralized-auth-service · ADR-052 hybrid-auth (accepted/ ls) |
| Sınır | RBAC kararı K006'dan; K008 yalnız taşıyıcı |

#### §4.3.2 Media Service (port 5000/6000 · HTTP · PHP + FFmpeg)

| Boyut | İçerik |
|---|---|
| Sorumluluk | Library, metadata, streaming (anayasa §10 #2) |
| Girdi/Çıktı | medya sorgusu/komutu → metadata + streaming URL'leri · event'ler (MediaAccessed) |
| Event'ler | `MediaAccessedEvent` · `MusicAddedEvent` (repo `shared/src/Events/Domain/`, ls) |
| Kanıt | `media.coremusic.net/` (ls: src · config · bin · composer.json · docs) · §10 #2 · §11 5000/6000 |
| Sınır | Dosya/FFmpeg erişimi bu servisin içindedir; K010 asla FFmpeg'e dokunmaz (§6A.5) |

#### §4.3.3 Audio Service (9741 REST / 9742 WS · C++20 JUCE)

| Boyut | İçerik |
|---|---|
| Sorumluluk | Player, DSP, mixer, EQ (anayasa §10 #3) |
| Çalışma zamanı | C++20 + JUCE 9 · ASIO SDK 2.3.4 (§12) · sıfır tahsis/noexcept guardrail'leri (§19) |
| Durum | PLANNED — repoda C++ servis dizini root seviyesinde gözlenmedi (ls 2026-10-08) → ⚠️ |
| Kanıt (hedef) | `.ai/CLAUDE.md §10 #3 · §11 9741/9742 · §12 · §19` · `00-kspace-anayasa.md §A.1 K008` |
| Test hedefi | Google Test ≥80% (§17) — altyapı durumu ⚠️ |

#### §4.3.4 Device Service (BLE/WiFi/USB · C++20)

| Boyut | İçerik |
|---|---|
| Sorumluluk | Bluetooth, WiFi, USB cihaz yönetimi (anayasa §10 #4) |
| Repo kanıtı (partial) | `shared/src/Device/` kütüphanesi (ls 2026-10-08) — cihaz modeli/soyutlama katmanı |
| Durum | PARTIAL: kütüphane var · bağımsız servis süreci PLANNED → ⚠️ |
| Sınır | Donanım erişimi K002 DRIVERS üzerinden (port/adapter); K008 doğrudan sürücü çağırmaz |
| Kanıt | `shared/src/Device/` (ls) · `.ai/CLAUDE.md §10 #4` · anayasa §A.1 |

#### §4.3.5 Network Audio Service (WebRTC/P2P · C++20)

| Boyut | İçerik |
|---|---|
| Sorumluluk | Streaming, multi-room (anayasa §10 #5) |
| Durum | PLANNED → ⚠️ (root'ta dizin/altyapı gözlenmedi) |
| İlişki | Ağ taşıması K014 NETWORK; bu servis K014'ü port/adapter ile kullanır (H20 ihlali yok) |
| Kanıt (hedef) | `.ai/CLAUDE.md §10 #5` · anayasa §A.1 K008 · ⚠️ |

#### §4.3.6 AI Service (internal · PHP + Python)

| Boyut | İçerik |
|---|---|
| Sorumluluk | Recommendation (anayasa §10 #6) |
| Repo kanıtı (partial) | `shared/src/AI/` + `shared/src/Contracts/AI/` (ls 2026-10-08) |
| Durum | PARTIAL: sözleşme/kütüphane var · servis süreci (PHP+Python iç yüzey) PLANNED ⚠️ |
| Sınır | AI katmanı K005 dışına veri erişemez (anayasa §5 K4 satırı — K004); K008 AI servisi K004 ile sözleşmeli |
| Kanıt | `shared/src/AI/` (ls) · `.ai/CLAUDE.md §10 #6` · ADR-030 ai-strategy-core (accepted/ ls) |

#### §4.3.7 Download Service (port 3001 · HTTP/WS · Node.js + TS)

| Boyut | İçerik |
|---|---|
| Sorumluluk | Deezer/YouTube indirme (anayasa §10 #7) |
| Durum | PLANNED — `download.coremusic.net/` dizini repo kökünde YOK (ls 2026-10-08) → ⚠️ |
| Güvenlik yüzeyi | Kaynak API/anti-ban/credential — K006'ya devredilir; secret gövdeye gömülmez (R17 U1) |
| Test hedefi | Vitest ≥80% (§17) |
| Kanıt (hedef) | `.ai/CLAUDE.md §10 #7 · §11 3001` · ADR-026 download-service-architecture (accepted/ ls) · repo: dizin YOK |

#### §4.3.8 Event Boundary (PSR-14 Event Bus)

| Boyut | İçerik |
|---|---|
| Kural | "Servisler birbirini doğrudan çağırmaz, event yayınlar: Service A → Event Bus (PSR-14) → Service B, C, D" (anayasa §6A.4 · ADR-086) |
| Repo kanıtı | `shared/src/Events/` (Domain/ + Integration/ alt dizinleri) · `shared/src/Contracts/Events/DomainEventInterface.php` · `IntegrationEventInterface.php` (ls 2026-10-08) |
| Gözlenen event'ler | GenderSetEvent · MediaAccessedEvent · MusicAddedEvent · MusicPlayedEvent · PasswordResetRequestedEvent · PlaylistCreatedEvent · UserLoggedInEvent · UserLoggedOutEvent (Domain/, ls — en az 8) |
| Durum | IMPLEMENTED (kütüphane/sözleşme düzeyi) · yayın-koşucu (dispatcher) entegrasyonu grep edilmedi → ⚠️ |
| Sınır | Event payload minimal (H19 ile uyum); olay yukarı serbest, senkron geri çağırır yasak (R6.2) |

### §4.4 Infra (11) — Açılım Notu

`.ai/CLAUDE.md §5 K8` satırındaki "+ Infra (11)" bileşen grubunun 11'lik dökümü anayasa §5'te
verilmemiştir. Sayı kaynağı (HEDEF): `.ai/CLAUDE.md §5 K8 satırı`. İddia-türü iddialar için
bileşen listesi **⚠️ VERIFICATION REQUIRED** (F1/EK A detay araştırması — R14). Repo'da gözlenen
altyapı kütüphaneleri (ls 2026-10-08): `shared/src/{Cache,Config,Database,Repository,Bootstrap,
Contracts,Exception,Log,OAuth,PageRouter,Security,Session,Theme,ViewMode,Component}` — bunlar
K008'in kullandığı **altyapı kütüphaneleridir**, "Infra (11)" sayımına birebir karşılığı kanıtlanmaz.

### §4.4.1 Altyapı Kütüphane Envanteri (repo ls — `shared/src/`, 2026-10-08)

| # | Kütüphane | Rol (K008 ile ilişkisi) | Durum |
|---|---|---|---|
| 1 | `shared/src/Events` (Domain/ + Integration/) | Event Boundary — domain/integration event'leri | IMPLEMENTED (8 event dosyası ls) |
| 2 | `shared/src/Contracts/Events` | DomainEventInterface · IntegrationEventInterface | IMPLEMENTED (ls) |
| 3 | `shared/src/Device` | Device servisi model/soyutlama katmanı | IMPLEMENTED (kütüphane) · servis PLANNED |
| 4 | `shared/src/AI` (+ `Contracts/AI`) | AI servisi sözleşme/yardımcıları | IMPLEMENTED (kütüphane) · servis PLANNED |
| 5 | `shared/src/Database` (+ `Config`) | BCNF repository altyapısı (raw PDO — ADR-002) | IMPLEMENTED |
| 6 | `shared/src/Repository` (+ `tests/Repository`) | Repository deseni katmanı | IMPLEMENTED (ls + test) |
| 7 | `shared/src/Cache` | APCu/CacheManager zinciri (ADR-007) | IMPLEMENTED |
| 8 | `shared/src/Security` (+ `tests/Security`) | güvenlik yardımcıları (K006 ile ortak) | IMPLEMENTED |
| 9 | `shared/src/Session` · `shared/src/OAuth` | oturum/OAuth taşımaları (Control girdisi) | IMPLEMENTED |
| 10 | `shared/src/Log` | LoggerFactory — yapısal log altyapısı (K012 girdisi) | IMPLEMENTED |
| 11 | `shared/src/Bootstrap` · `Config` · `Exception` | başlatma / yapılandırma / istisna sözleşmesi | IMPLEMENTED |
| 12 | `shared/src/PageRouter` · `Component` · `Theme` · `ViewMode` | UI/Route yardımcıları (K010/K011'e hizmet) | IMPLEMENTED |

> Not: Bu envanter repo ölçümüdür; "Infra (11)" hedefinin karşılığı olduğu **iddia edilmez** (H10).

### §4.5 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K8 hedefi | 55 bileşen (HEDEF) |
| Repo kanıtı (ls 2026-10-08) | 2 servis dizini (media, auth) + Events kütüphanesi + Device/AI kütüphaneleri |
| Servis kapsamı | 2 IMPLEMENTED (dizin) · 2 PARTIAL (Device, AI) · 3 PLANNED (Audio, Network, Download) |
| Event boundary | IMPLEMENTED (sözleşme + 8 event dosyası) · dispatcher entegrasyonu ⚠️ |
| Infra (11) dökümü | ⚠️ VERIFICATION REQUIRED |

### §4.6 K008 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti | K008 etkisi |
|---|---|---|
| ADR-039-7-service-platform-architecture | 7 servis platform mimarisi | §10 yedi servis listesinin kurucu kararı |
| ADR-086-event-driven-architecture | Event Bus (PSR-14) ile iletişim | Event Boundary — servisler arası tek meşru kanal |
| ADR-026-download-service-architecture | Download servisi mimarisi | §4.3.7 — 3001/Node.js ayağı |
| ADR-058-centralized-auth-service | Merkezi auth servisi | §4.3.1 Control kimlik ayağı |
| ADR-007-cache-namespace | Cache namespace stratejisi | Infra kütüphane: `shared/src/Cache/` |
| ADR-050-multi-db-sync-strategy | Çoklu DB senkronu | Data boundary (18 BCNF sahiplik) |
| ADR-085-modular-composer-packages | PSR-4 modüler paketler | Servis/paket ayrımı (`coremusic/shared`) |
| ADR-030-ai-strategy-core | AI stratejisi | §4.3.6 AI servisi |
| ADR-019-per-os-neva-player | OS-bazlı Neva player | Audio servisi tasarım girdisi (K003 sınırı) |
| ADR-062-dsp-pipeline-architecture | DSP pipeline | Audio servisi DSP zinciri |
| ADR-037-wirelessconnect-integration | WirelessConnect entegrasyonu | Device/Network servis entegrasyonu |
| ADR-096-kspace-5000-boundary-model | K-space V2 rejimi | Bu dosyanın format/bağımlılık kaynağı |

### §4.7 K008 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K008'in verdiği | K008'in beklendiği | Kanıt |
|---|---|---|---|---|
| K007 MIDDLEWARE | devredilmiş istek | doğrulanmış/validate edilmiş Request/DTO üzerine iş | pipeline'dan gelmeyen istek kabul edilmez | `.ai/CLAUDE.md §6` (Controller devri) |
| K009 API | OpenAPI sözleşmesi | servis yanıtları + event yayını | command/query yönlendirmesi + hata sözleşmesi | `.ai/CLAUDE.md §6A.1/§6A.3` · §5 K9 |
| K005 DATA | repository/BCNF | repository yazımı/okuması | raw PDO + prepared statement; ORM yok | ADR-002/040 (accepted/ ls) · §18 |
| K006 SECURITY | kimlik/yetki kararları | RBAC'e uygun davranış + audit olayları | JWT/claim doğrulaması | §5 K6 · ADR-052/095 |
| K012 OBSERVABILITY | log/metrik akışı | yapısal log + event kuyruk sinyali | merkezi toplama (PLANNED) | `shared/src/Log/LoggerFactory.php` (ls) · §5 K12 |
| K014 NETWORK · K015 MEDIA | port/adapter | ağ/medya taşıma soyutlaması | servis dışında doğrudan ağ/medya erişimi yok | §5 K14/K15 satırları |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K000-K005 (OS → DATA) | aşağı | anayasa §A.1 K008 "izinli=K000-K007"; veriye erişim yalnız K005 repository/port | `00-kspace-anayasa.md §A.1 K008` |
| K006 SECURITY | aşağı | RBAC/JWT kararları; servisler kimlik kararını K006'dan alır | `.ai/CLAUDE.md §5 K6/K8` |
| K007 MIDDLEWARE | aşağı | servis yalnız K007'den devredilmiş doğrulanmış istek alır; K007'ye geri dönmez | `.ai/CLAUDE.md §6` (pipeline sonu Controller devri) |
| port/adapter | yan | EK A istisnası (R6.3 sıçraması) — ör. Device → K002 sürücü portu | `00-kspace-anayasa.md §A.1` |
| PSR-14 Event Bus | dış/yukarı | olay yayını yukarı serbest (R6.2) | `rules.md R6.2` · ADR-086 |
| K005 repository | aşağı | 18 BCNF'ye yalnız repository ile erişim; raw PDO (ADR-002) | ADR-002/040 (accepted/ ls) · `.ai/CLAUDE.md §18` |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| Servisler arası senkron doğrudan çağrı | "Servisler arası doğrudan çağrı yasaktır, Event Bus kullanılır" (hard guardrail) | `.ai/CLAUDE.md §5 K8 satırı` · §6A.4 |
| K008 → K009/K010/K011 üst katman erişimi / geri çağrı (H20) | klasik yön + anayasa §5.1 | `rules.md R6.1` · `ADR-096 §2` |
| Katmanlar arası doğrudan veri paylaşımı (H19) | servisler arası DB/servis içi veri köprüsü yasak; yalnız event payload | `rules.md R6.1` · ADR-086 |
| Repository'siz doğrudan SQL / `SELECT *` / ORM | Guardrail #9 + ADR-002 + §18 kuralları | `.ai/CLAUDE.md §18 · §21` · ADR-002 (ls) |
| K002 sürücüsüne doğrudan erişim (Device servisi) | donanım-yazılım köprüsü yalnız port/adapter | anayasa §5 K2 satırı · EK A "yalnız port/adapter" |

### §5.3 Boundary Matrisi

| Boundary | K008 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | Servis-bazlı BC/DB sahipliği (18 BCNF — §18); olay payload'ı minimal | K005 (depolama) · K015/K019 (medya dosya) |
| SECURITY_BOUNDARY | security=ORTA (EK A); kimlik/yetki kararı K006'da; servis yalnız uygular | K006 · K007 (istek hattı) |
| FAILURE_MODE | fail-over (EK A) · servis DOWN → degrade + kuyruk; zaman aşımı → hata sözleşmesi | K009 (hata sözleşmesi) · K012 (alarm) |
| RUNTIME boundary | dili/nginx süreçleri: PHP 8.4 (FPM), C++20 süreç (JUCE), Node.js LTS (3001) | K000 · K013 (dağıtım) |
| CONTRACT boundary | OpenAPI/K009 sözleşmesi dışında dışa açılan endpoint YASAK | K009 (sahip) |
| Event boundary | tek iletişim kanalı PSR-14; payload minimal (H19 ile uyum) | K009 (Event Bus sahipliğiyle ortak — §5 K9 satırı) |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay Akışı — Event Bus Yükselişi (R6.2)

```text
K012 OBSERVABILITY  ← (log/metrik, yukarı serbest)  ←  K008 servisleri
                          ↑        ↑        ↑
                    [Event Bus — PSR-14, tek kanal; senkron servis↔servis YASAK]
                          │        │        │
                    Control   Media   Download …   (aynı katman, yatay)
                          └────────┼────────┘
                       K009 API (çağıran) / K005 (repository)   ← aşağı izinli
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| Servis → Event Bus → servis | yatay | tek meşru iletişim; doğrudan çağrı yasak | `.ai/CLAUDE.md §5 K8 · §6A.4` |
| Servis → K012 log/olay | yukarı | olay yayını yukarı serbest | `rules.md R6.2` |
| Servis → K005 repository | aşağı | BC sahipliği + BCNF | `.ai/CLAUDE.md §18` · ADR-040 |
| K009 → servis (command) | aşağı/çağıran | API katmanı servisi çağırır (doğru yön) | `.ai/CLAUDE.md §6A.3` |
| Servis → K009 geri senkron | yukarı | hata sözleşmesi dışında senkron geri çağrı yok (H20) | `rules.md R6.1` |

### §5.5 Event Kataloğu (repo gözlemi — `shared/src/Events/Domain/`, ls 2026-10-08)

| # | Event dosyası | Muhtemel üretici (§10) | Muhtemel abone | Durum |
|---|---|---|---|---|
| 1 | `UserLoggedInEvent.php` | Control | AI (tercih), K012 (audit) | dosya VAR · üretici/abone eşlemesi ⚠️ |
| 2 | `UserLoggedOutEvent.php` | Control | K012 (audit) | dosya VAR · eşleme ⚠️ |
| 3 | `PasswordResetRequestedEvent.php` | Control | K006/K012 (audit) | dosya VAR · eşleme ⚠️ |
| 4 | `MusicAddedEvent.php` | Media | AI (öneri), Download (cache) | dosya VAR · eşleme ⚠️ |
| 5 | `MusicPlayedEvent.php` | Audio/Media | AI (öneri), K012 (dinleme) | dosya VAR · eşleme ⚠️ |
| 6 | `MediaAccessedEvent.php` | Media | K012 (access log) | dosya VAR · eşleme ⚠️ |
| 7 | `PlaylistCreatedEvent.php` | Control/Media | AI | dosya VAR · eşleme ⚠️ |
| 8 | `GenderSetEvent.php` | Control (profil) | tema/K011 | dosya VAR · eşleme ⚠️ |

> Üretici/abone eşlemeleri **türetmedir** (§10 servis sorumluluklarından) — kod grep'i yapılmadı → ⚠️.
> `Integration/` alt dizini gözlemlendi ancak dosya dökümü bu üretimde alınmadı → ⚠️.

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası (servis başına)

| Servis | Girdi | Çıktı | Event çıkışı (repo gözlemi) |
|---|---|---|---|
| Control | kimlikli istek, kimlik bilgileri | oturum/RBAC sonucu | UserLoggedInEvent · UserLoggedOutEvent · PasswordResetRequestedEvent |
| Media | medya komutu/sorgusu | metadata, streaming URL | MediaAccessedEvent · MusicAddedEvent |
| Audio | transport/DSP komutları (REST/WS) | oynatma durumu, spectrum | ⚠️ (event listesi grep edilmedi) |
| Device | cihaz olayı/komutu | cihaz durumu | ⚠️ |
| Network | multi-room/stream komutu | akış durumu | ⚠️ |
| AI | tercih/dinleme özelliği | öneri listesi | ⚠️ |
| Download | indirme işi (queue) | indirme durumu + dosya | ⚠️ (download DB: §18 #15) |
| Event Boundary | event nesnesi (Domain/Integration) | abone servislere dağıtım | Contracts/Events interface'leri (ls) |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Control | PHP 8.4 · HTTP · port 81 | `.ai/CLAUDE.md §10 #1 · §11` |
| Media | PHP + FFmpeg · HTTP · port 5000/6000 | `.ai/CLAUDE.md §10 #2 · §11` |
| Audio | C++20 JUCE · REST 9741 / WS 9742 | `.ai/CLAUDE.md §10 #3 · §11 · §12` |
| Device | BLE/WiFi/USB · C++20 | `.ai/CLAUDE.md §10 #4` |
| Network | WebRTC/P2P · C++20 | `.ai/CLAUDE.md §10 #5` |
| AI | internal · PHP + Python | `.ai/CLAUDE.md §10 #6` |
| Download | HTTP/WS · Node.js LTS · port 3001 | `.ai/CLAUDE.md §10 #7 · §11 · §24` |
| Mesajlaşma | PSR-14 Event Bus | `.ai/CLAUDE.md §6A.4` · ADR-086 |
| Cache | APCu (CacheManager zinciri) · Redis PLANNED | `.ai/CLAUDE.md §12` (satır: IMPLEMENTED APCu / PLANNED Redis) |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Yapısal log altyapısı | `shared/src/Log/LoggerFactory.php` · `shared/src/PageRouter/StructuredLogger.php` (ls 2026-10-08) | IMPLEMENTED (altyapı) |
| Servis health-check uçları | anayasa §A.1 K012 "Health Checks" bağlantılı | PLANNED ⚠️ (grep edilmedi) |
| Event bus kuyruk/yaş metrikleri | K012'ye devredilir | PLANNED (§5 K12 satırı: yalnız K8'den okuma) |
| Audit olayları | servis olayları → K006/K012 (§18 #7 coremusic_logs) | K012/K006 alanı |

### §6.4 Test

| Katman | Framework | Kanıt | Hedef |
|---|---|---|---|
| PHP servis/sözleşme | PHPUnit 11 | `shared/tests/{Api,Events,Repository,Security,Unit,Component,OAuth}` (ls 2026-10-08) · `shared/phpunit.xml` | ≥80% / ≥90% (§17) |
| Auth servisi | PHPUnit | `auth.coremusic.net/phpunit.xml` + `tests/` (ls) | ≥80% |
| Media servisi | PHPUnit (varsayım — composer.json var, phpunit.xml teyidi ⚠️) | `media.coremusic.net/composer.json` (ls) · phpunit.xml ⚠️ | ≥80% |
| Event sözleşmeleri | PHPUnit | `shared/tests/Events/` (ls) | ≥80% |
| Download servisi | Vitest | hedef (§17) · servis dizini YOK → altyapı PLANNED | ≥80% |
| Audio servisi | Google Test | hedef (§17) · C++ dizini yok → PLANNED ⚠️ | ≥80% |

### §6.5 Failure Mode Senaryoları (failure=fail-over)

| # | Senaryo | K008 davranışı | Kanıt |
|---|---|---|---|
| 1 | Bir servis DOWN | degrade servis yanıtı; event kuyrukta kalır → sonra işlenir (tasarım) | anayasa §A.1 K008 failure=fail-over · kuyruk davranışı ⚠️ |
| 2 | Senkron doğrudan çağrı denemesi | mimari ihlal → revert + CRITICAL log | `.ai/CLAUDE.md §5 K8` · anayasa §5.1 |
| 3 | Event handler istisnası | hatayı yutan abone → veri kaybı riski; retry/dead-letter politikası tanımsız | ⚠️ VERIFICATION REQUIRED |
| 4 | DB erişilemez (K005) | repository hatası → 5xx hata sözleşmesi (K009) | `.ai/CLAUDE.md §6A` · ADR-014 |
| 5 | Zaman aşımı (uzun ffmpeg/iş) | timeout soft constraint 30s (60s batch) | `.ai/CLAUDE.md §8 #2` |
| 6 | Download servisi limit/ban | anti-ban stratejisi devrede (tasarım) | ADR-028 anti-ban (accepted/ ls) · ADR-026 |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K008 durumu |
|---|---|
| KAPI 1 vault oku | Tam (anayasa §A.1 + §5/§10/§18 + rules + ADR-096 okundu) |
| KAPI 9 hallucination | ⚠️ listesi §7.1'de |
| KAPI 10 onay | BEKLİYOR — `status: draft` (R10) |
| Guardrail #9 (ORM yasak) | Repository deseni + ADR-002; tam grep ⚠️ |
| H19/H20 | §5.2 yasak listesi ile kilitli; ihlal → revert + CRITICAL |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | ✓ (§2) |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | ✓ (§3) |
| K3 | 7 servis + Event Boundary kapsamı | her kalem için Durum + Kanıt satırı | ✓ (§4.1/§4.3) · 3 PLANNED işaretli |
| K4 | Event Bus kuralı kilitli | "doğrudan çağrı yasak" hard guardrail belgelenmiş | ✓ (§4.3.8 · §5.2) |
| K5 | Veri sahipliği | servis ↔ BCNF bölünmesi belgeli | ✓ (§5.3 · §18) |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | ✗ → ⚠️ (§7.1 G6) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | ✗ bekliyor (Kapı 10) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K008 - SERVICES «KULE»` (+ §A.0) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5 K8` (55 · Event Bus) · `§6A.3/§6A.4` · `§10` (7 servis) · `§11` (port) · `§12/§24` (stack) · `§17` (test) · `§18` (18 BCNF) | dosya yolu (vault read) | K-matrix + servis tablosu açılımı |
| 3 | `media.coremusic.net/` · `auth.coremusic.net/` · `shared/src/Events/` · `shared/src/Device/` · `shared/src/AI/` · `shared/tests/` (ls 2026-10-08) | repo ls | IMPLEMENTED/PARTIAL/PLANNED durumları |
| 4 | `.ai/.decisions/accepted/` ls: ADR-007/019/026/030/037/039/050/058/062/085/086/096 | ADR (ls teyitli) | karar atıfları |
| 5 | `rules.md R2/R3/R4/R6/R9` · `ADR-096 §2` | dosya yolu | format + yön |
| 6 | F1 EK B (37 URL · `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md`) | URL (vault arşivi) | web research üssü |
| 7 | Aşağıdaki boşluklar | ⚠️ VERIFICATION REQUIRED | R14 research kapısı |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | Infra (11) bileşen dökümü | §4.4 / EK C SORUMLULUK | F1/EK A detay araştırması (R14) |
| G2 | Audio/Network/Download servis süreçleri repo'da yok | §4.3.3/.5/.7 | kod üretim planı veya `git show HEAD:` taraması |
| G3 | Event dispatcher entegrasyonu (yayın-koşucu) grep edilmedi | §4.3.8 | `shared/src/Events/` kod okuması |
| G4 | health-check uçları | EK C OBSERVABILITY | servis kodu grep |
| G5 | Retry/dead-letter politikası tanımsız | EK C FAILURE_MODE | tasarım kararı + test |
| G6 | Web kanıtı (URL+tarih) | KANIT web ayağı | F1 EK B research kapısı (R14) |

**Kural:** Boşluklar dosyayı geçersiz kılmaz; `ACTIVE` için R4.4c/R16.2 kapanışı gerekir.

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR». Üst/yasak yön (H20): K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» · K016 AMPLIFIER «ZAR» · K017 POWER «KANTAR» · K018 THERMAL «MEZİT» ·
K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL». Kardeşler arası ilişki yalnız `refers-to` (R6.4).

**Yatay komşu notu:** K014 NETWORK ve K015 MEDIA ile sınırı servis/olay sözleşmesiyle tanımlıdır
(K014 üzerinden ağ, K15 üzerinden medya — `.ai/CLAUDE.md §5 K14/K15` satırları).

**İlişki türleri (R6.4 — kardeşle yalnız `refers-to`):**

| Komşu | İlişki | Geçerli mi? |
|---|---|---|
| Kardeş KNNN (K007, K009-K020) → K008 | `refers-to` (doküman) | Evet |
| Kardeş KNNN → K008 | `depends-on` | Hayır (yatay bağımlılık döngü riski — R6.6) |
| Alt K000-K007 → K008 | `depends-on` (aşağı) | Evet (K008'i besler) |
| K008 → üst K009-K020 | `depends-on` | Hayır (H20) |
| Olay K008 → K012/K009 | yayın (yukarı) | Evet (R6.2) |

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):**

| Dosya | Katman | Durum |
|---|---|---|
| b1-K007-middleware.md | K007 MIDDLEWARE «SUR» | band-1 setinin parçası |
| b1-K008-servisler.md | K008 SERVICES «KULE» | bu dosya (draft) |
| b1-K009-api.md | K009 API «HÜCRE» | band-1 setinin parçası |
| b1-K010-uygulama.md | K010 APPLICATION «DÜĞÜM» | band-1 setinin parçası |
| b1-K011-ux.md | K011 UX «OMURGA» | band-1 setinin parçası |
| b1-K012-izleme.md | K012 OBSERVABILITY «AYNA» | band-1 setinin parçası |
| b1-K013-cicd.md | K013 CI/CD «PUSULA» | band-1 setinin parçası |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.