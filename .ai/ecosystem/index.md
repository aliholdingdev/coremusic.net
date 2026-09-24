---
title: "CoreMusic — Ekosistem İndeksi"
type: guide
category: ecosystem
version: 1.0.0
status: active
authority: reference
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24)
updated: 2026-09-24
---

# CoreMusic — Ekosistem İndeksi

**Zorunlu Bağlantılar:** [[../CLAUDE.md]] · [[../AGENTS.md]] · [[../architecture/index]] · [[../architecture/github-referanslari]] · [[README]] · [[service-integration]]

**Şablon:** [[../.templates/documentation/docs-md-template]] (Guardrail #16) · **Şablon-Önce bloğu §4.1'dedir, silinemez.**

---

## §1 Amaç

Bu doküman, **.ai/ecosystem/** klasörünün ana indeksidir: CoreMusic'in dış ekosistemden (açık kaynak müzik sunucuları, ses/DSP kütüphaneleri, donanım referans devreleri, büyük müzik platformu mimarileri, ASIO/WASAPI sürücü ekosistemi) çıkardığı **dersleri**, her birini **K0-K20 katmanlarından hangisine** somut olarak bağladığını ve hangi kaynağın **lisans olarak CoreMusic'e girebileceğini/giremeyeceğini** tek yerde toplar.

| Alan | Değer |
|------|-------|
| Hedef kitle | Embedded Engineer, Audio HW Engineer, Backend Architect, Windows SW Engineer, DevOps Engineer, Vault Steward |
| Kapsadığı ekosistem | 6 ana konu alanı — streaming sunucuları, DSP, donanım, platform mimarileri, sürücü API'leri |
| Ana çıktı | Ders → K katmanı çapraz matrisi (§3.4, §3.5) + lisans tabloları (her dosyanın §4.4'ü) |
| Neden şimdi (2026-09-24) | exa web doğrulaması tazelendi; yeni referanslar (YUP, OpenStudio, wolfsound-dsp-utils, ddabidov) github-referanslari.md v2.0 ile vault'a girdi |
| Vault bağlantısı | Katman otoritesi [[../architecture/index]] §2 · referans havuzu [[../architecture/github-referanslari]] · anayasa [[../CLAUDE.md]] §5 |
| Kural | Bu dosya **reference** taşır; SSOT iddiası yoktur — çelişkide CLAUDE.md kazanır (§2.1 SSOT sırası) |

**Tek cümlelik tanım:** Ekosistem dosyaları "neyi kopyalamayız"ın da "neyi fikir olarak alırız"ın da tek kayıt defteridir.

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| .ai/ecosystem/ altındaki tüm .md dosyalarının envanteri ve amaçları | K0-K20 katman dokümanlarının kendisi (→ [[../architecture/index]]) |
| Dış kaynak derslerinin K katmanına eşlemesi (çapraz referans) | GitHub havuzu kayıt tablosu (→ [[../architecture/github-referanslari]]) |
| Lisans uyumluluğu özet kararları (MIT/AGPL/GPL/GPL-comercial) | ADR üretimi (→ [[../brain]] / .ai/.decisions; bu dosya karar üretmez) |
| exa doğrulamalı ekosistem verisi (tarih + kaynak etiketi) | Kod implementasyonu (Guardrail #1: Zero Code Before Plan) |
| NE kopyalanır / NE kopyalanmaz kararlarının özeti | Servis port/komut detayları (→ [[README]], [[service-integration]]) |

*Alt konular:* §3.1 harita → §3.2 envanter → §3.4/§3.5 K çapraz referans → §4 lisans kuralları → §5 okuma akışı → §6 doğrulama.
Kapsam dışı için: karar → ADR, katman içeriği → architecture/, servis özeti → [[README]].

### §2.2 Hedef Kitle ve Okuma Sırası

| Kitle | Önce Okuması Gereken | Neden |
|-------|---------------------|-------|
| Embedded Engineer (K2/K3) | ses-dsp-acik-kaynak.md + asio-wasapi-rehber.md | JUCE lisans kararı ve ASIO dağıtımı K3/K2 planını doğrudan bağlar |
| Audio HW Engineer (K1/K16-K20) | donanım-devre-referanslari.md | ddabidov/Headphone-DAC-AMP = XU316 referans tasarımı |
| Backend Architect (K8/K9) | muzik-streaming-sunuculari.md + ekosistem-mimarileri.md | Koel/Ampache desenleri ve Spotify pipeline ayrıştırması |
| Windows SW Engineer (K0/K2) | asio-wasapi-rehber.md | 32-bit ASIO driver gerçeği → platform stratejisi |
| DevOps / MO | index.md (bu dosya) → ihtiyaç'a göre diğer 5 dosya | routing, AGENTS.md §24.3: DevOps → .ai/ecosystem/*.md |

### §2.3 Bağımlılıklar (Bu Dosyadan Önce Okunanlar)

| Sıra | Dosya | Amaç |
|------|-------|------|
| 1 | [[../CLAUDE.md]] | 16 Hard Guardrail, §5 K0-K20, §21 Forbidden Patterns |
| 2 | [[../AGENTS.md]] §24.3 | DevOps/Ekosistem okuma listesi |
| 3 | [[../architecture/index]] §1-2 | Katman haritası ve bileşen sayıları |
| 4 | [[../architecture/github-referanslari]] | Mevcut referans havuzu + §6 katman eşleme |
| 5 | [[../.templates/index]] §7.1 | Şablon seçimi (Guardrail #16) |

---

## §3 Mimari

### §3.1 Ekosistem Haritası

~~~text
.ai/ecosystem/
├── index.md                        ← BU DOSYA (harita + envanter + K çapraz referans)
├── README.md                       ← mevcut (2026-09-20) — servis/panel/subdomain özeti · DOKUNULMADI
├── service-integration.md          ← mevcut (2026-09-20) — entegrasyon protokolleri + event types · DOKUNULMADI
├── muzik-streaming-sunuculari.md   ← Koel 17.175★ / Ampache 3.809★ / Mopidy  → K8 · K9 · K15
├── ses-dsp-acik-kaynak.md          ← JUCE 8.811★ / DaisySP / Dusk IPC / OpenStudio → K3 · K10 · K2
├── donanım-devre-referanslari.md   ← TPA3255 / XU316 / DAC / boost           → K1 · K16 · K17 · K19 · K20
├── ekosistem-mimarileri.md         ← Spotify / Apple Music / YouTube Music    → K8 · K9 · K14 · K15
└── asio-wasapi-rehber.md           ← ASIO SDK lisans + WASAPI exclusive       → K2 · K0
~~~

**İlişki şeması (içerik → katman):**

~~~text
DİŞ EKOSİSTEM (exa doğrulamalı, 2026-09-24)
  │  ① muzik-streaming-sunuculari  ② ses-dsp-acik-kaynak   ③ donanım-devre-referanslari
  │  ④ ekosistem-mimarileri        ⑤ asio-wasapi-rehber
  ▼
.ai/ecosystem/index.md  ← çapraz referans ve lisans kapısı (bu dosya §3.4-§3.5, §4.4)
  ▼
K0-K20 katman dokümanları (SSOT: [[../architecture/index]])
  ▼
ADR / kod planı (brain.md · .ai/.decisions)  →  Guardrail #1: plan onayı olmadan kod yok
~~~

### §3.2 Dosya Envanteri

| # | Dosya | Tür | Birincil K | İkincil K | Durum |
|---|-------|-----|-----------|-----------|-------|
| 0 | README.md | servis/panel özeti | K8 | K10 | mevcut — 2026-09-20, değiştirilmedi |
| 0 | service-integration.md | entegrasyon protokolleri | K9 | K8 | mevcut — 2026-09-20, değiştirilmedi |
| 1 | index.md | ekosistem indeksi (bu dosya) | — | tüm K | active · 2026-09-24 |
| 2 | muzik-streaming-sunuculari.md | dış sunucu dersleri | K8 | K9, K15 | active · 2026-09-24 |
| 3 | ses-dsp-acik-kaynak.md | DSP/kütüphane + lisans | K3 | K10, K2 | active · 2026-09-24 |
| 4 | donanım-devre-referanslari.md | devre/BOM referansları | K1 | K16, K17, K19, K20 | active · 2026-09-24 |
| 5 | ekosistem-mimarileri.md | platform pipeline dersleri | K8 | K9, K14, K15 | active · 2026-09-24 |
| 6 | asio-wasapi-rehber.md | sürücü/lisans stratejisi | K2 | K0 | active · 2026-09-24 |

**Envanter kuralı:** Yeni ekosistem dosyası açan kişi bu tabloya satır ekler; adı İngilizce, içerik Türkçe (docs-md-template §4.3).

### §3.3 Konu Kategorileri (6 Alan)

| # | Kategori | Kapsadığı | Ana çıktı | Dosya |
|---|----------|-----------|-----------|-------|
| 1 | Müzik streaming sunucuları | Koel, Ampache, Mopidy, karşılaştırma | Akıllı playlist, API ayrıştırması, lisans tablosu | muzik-streaming-sunuculari.md |
| 2 | Ses & DSP açık kaynak | JUCE, Awesome-Audio-DSP, DaisySP, YUP, wolfsound, Dusk, OpenStudio, OL_DSP | IPC crash izolasyonu, hybrid UI, GPL→ticari seçenek | ses-dsp-acik-kaynak.md |
| 3 | Donanım devre referansları | TPA3255, XU316, DAC, boost, OpAmp, RP2040 | Birebir eşleşenler tablosu, K16-K20 girdileri | donanım-devre-referanslari.md |
| 4 | Ekosistem mimarileri | Spotify, Apple Music, YouTube Music | upload→transcode→distribution akışı, servis ayrımı | ekosistem-mimarileri.md |
| 5 | ASIO / WASAPI | ASIO SDK açık kaynak lisansı, WASAPI exclusive, 32-bit gerçek | Dağıtım kuralları, K2/K0 stratejisi | asio-wasapi-rehber.md |
| 6 | İndeks & çapraz referans | Tümü | Ders → K matrisi, lisans kapısı | index.md (bu dosya) |

### §3.4 K Katmanı Çapraz Referans (K0-K20)

> Her K satırı, ekosistem dosyalarından hangi dersin o katmana girdiğini gösterir. Katman tanımları kanonik: [[../architecture/index]] §2.

| K | Katman | Hangi Ekosistem Dersi Girer | Hangi Dosya |
|---|--------|------------------------------|-------------|
| K0 | İşletim Sistemi | 32-bit WASAPI/COM gerçekliği, ReactOS seyrek ASIO, sürücü ABI | asio-wasapi-rehber.md |
| K1 | Donanım | XU316+ES9039 kulaklık DAC, PCM5102A DA15, spin-dac 24/192 | donanım-devre-referanslari.md |
| K2 | Sürücü | ASIO SDK açık kaynak lisans + dağıtım, WASAPI exclusive vs ASIO | asio-wasapi-rehber.md |
| K3 | Ses Motoru | JUCE 8/9 framework, DaisySP modüller, Awesome-Audio-DSP listesi, Dusk OOP host | ses-dsp-acik-kaynak.md |
| K4 | Yapay Zeka | Koel AI asistan + Last.fm/MusicBrainz öneri sinyalleri (fikir) | muzik-streaming-sunuculari.md |
| K5 | Veri Yönetimi | Koel smart playlist sorgu deseni, Ampache şema dersi (AGPL: sadece fikir) | muzik-streaming-sunuculari.md |
| K6 | Güvenlik | Lisans uyumu = hukuki güvenlik (§4.4), Subsonic API token dersi | muzik-streaming-sunuculari.md |
| K7 | Middleware | — (doğrudan ekosistem dersi yok; K9'a bak) | — |
| K8 | Servis | Koel servis ayrımı, Ampache Subsonic API, Spotify account/upload/search/playlist servisleri | muzik-streaming-sunuculari.md, ekosistem-mimarileri.md |
| K9 | API & Routing | Spotify API gateway dersi, Koel REST + smart query, BFF çıkarımı | ekosistem-mimarileri.md, muzik-streaming-sunuculari.md |
| K10 | Uygulama | OpenStudio React+WebView2 hybrid, Koel Vue arayüz liderliği, Dusk host IPC | ses-dsp-acik-kaynak.md |
| K11 | UX | Koel arayüz kalitesi dersi (fikir düzeyinde) | muzik-streaming-sunuculari.md |
| K12 | İzleme | Spotify pipeline gözlemlenebilirlik çıkarımı (transcode kuyruk metrikleri) | ekosistem-mimarileri.md |
| K13 | CI/CD | — (doğrudan ekosistem dersi yok) | — |
| K14 | Ağ | Spotify distribution akışı, upload→transcode→CDN, DLNA/UPnP karşılaştırma | ekosistem-mimarileri.md |
| K15 | Medya & Streaming | Ampache transcode zinciri, Koel stream uçları, HLS/DASH çıkarımı | muzik-streaming-sunuculari.md, ekosistem-mimarileri.md |
| K16 | Class AB | TPA3255 topolojisi karşılaştırma, modular-amplituner, OpAmp kulaklık | donanım-devre-referanslari.md |
| K17 | Güç Kaynağı | BoostCore MT3608, PBA MK1 boost/taşınabilir, OR-ing dersleri | donanım-devre-referanslari.md |
| K18 | Termal | Taşınabilir amplifikatör termal dersleri (dolaylı) | donanım-devre-referanslari.md |
| K19 | PCB | DA15, spin-dac, rp2040-dac-amp kart yerleşim referansları | donanım-devre-referanslari.md |
| K20 | BOM | ddabidov BOM yapısı, PBA MK1 maliyet, tedarik listesi dersi | donanım-devre-referanslari.md |

**Not:** K7 ve K13 için ekosistem dış kaynağı atanmamıştır; atama [[../architecture/github-referanslari]] §7.7/§7.13 havuzundan yapılır.

### §3.5 Ders → Katman Matrisi (Dosya Bazlı)

| Dosya | Öğrenilen Ders (kısa) | Hedef K | SOMUT Giriş (ne yapılır) |
|-------|----------------------|---------|--------------------------|
| muzik-streaming-sunuculari.md | Akıllı playlist sorgu motoru | K8, K5 | Playlist servis sözleşmesi OpenAPI'ye smart_query parametresi olarak yazılır |
| muzik-streaming-sunuculari.md | Subsonic API uyumlu harici uç | K9 | Cihaz istemcileri için /rest/*.json versiyonlu gateway kuralı |
| muzik-streaming-sunuculari.md | AGPL kodu asla kopyalanmaz | K6 | Lisans kapısı: yalnız fikir + beyaz oda (whitepaper) |
| ses-dsp-acik-kaynak.md | JUCE GPL vs ticari ayrımı | K3 | Audio Service kütüphane seçim ADR'si: GPL mi, JUCE commercial mi |
| ses-dsp-acik-kaynak.md | Dusk OOP plugin host IPC (crash izolasyonu) | K3, K0 | Audio Service → Neva Engine ayrı süreç; Windows'ta CreateFileMapping + WaitOnAddress |
| ses-dsp-acik-kaynak.md | OpenStudio hybrid (C++ motor + WebView2 UI) | K10, K3 | Studio paneli hybrid denemesi: Vanilla JS + native köprü (window.__JUCE__ benzeri) |
| donanım-devre-referanslari.md | XU316 kulaklık DAC referansı | K1 | XMOS XU316 USB audio + ES9039 vs PCM3168A/AK4458 fark analizi |
| donanım-devre-referanslari.md | TPA3255 Class D PBTL | K16 | Class AB 8 kanal BOM'unda alternatif topolojisi karşılaştırma satırı |
| donanım-devre-referanslari.md | MT3608 boost modülü | K17 | LM5122 ×2 tasarımında düşük güç yardımcı boost referansı |
| ekosistem-mimarileri.md | Spotify servis ayrımı | K8, K9 | Control/Media/AI/Download servis sınırı ve event bus event adları |
| ekosistem-mimarileri.md | upload→transcode→distribution | K15, K14 | Kuyruklu transcode pipeline + CDN dağıtımı planı |
| asio-wasapi-rehber.md | ASIO SDK açık kaynak lisans | K2 | ASIO SDK dağıtımı: LICENSE dosyası + atıf + lisans kontrol listesi |
| asio-wasapi-rehber.md | 64-bit evrensel ASIO var, 32-bit yok | K2, K0 | Tier-1 Windows stratejisi: 64-bit process + 32-bit fallback yok → WASAPI |

### §3.6 Doğrulanmış Kaynak Envanteri (exa · 2026-09-24)

> Tüm sayısal veriler bu tarihli exa web doğrulamasından gelir; yeni web araştırması yapılmamıştır. Yıldız sayıları zamanla değişir — kullanımda güncel değer kontrol edilir.

**Ses / DSP:**

| Kaynak | Değer | Not |
|--------|-------|-----|
| JUCE | 8.811★ | C++ framework, VST/AU/LV2 plugin üretimi |
| Awesome-Audio-DSP | 1.289★ | BillyDM kaynak listesi (derleme referansı) |
| DaisySP | — | embedded DSP modül kütüphanesi |
| YUP | 148★ | JUCE 7 fork'u, ISC lisans |
| wolfsound-dsp-utils | — | DSP yardımcıları |
| Dusk Studio | — | JUCE 8 DAW; OOP plugin host IPC (aşağıda) |
| OpenStudio | — | JUCE C++ motor + React UI + WebView2 köprüsü |
| OL_DSP | — | DSP kütüphanesi |

**Müzik sunucuları:**

| Kaynak | Stars | Lisans / Not |
|--------|-------|--------------|
| Koel | 17.175★ | Laravel+Vue, MIT, akıllı playlist, Last.fm/Spotify/MusicBrainz, AI asistan |
| Ampache | 3.809★ | PHP, AGPL-3.0, Subsonic API, 2001'den beri |
| Mopidy | — | Python, Mopidy-HTTP/MPD ekosistemi |
| Karşılaştırma | — | Koel = arayüz lideri · Ampache = özellik lideri |

**Donanım:** TPA3255 Class D PBTL devreleri · modular-amplituner · PBA MK1 (taşınabilir boost) · DA15 (USB-C DAC, PCM5102A) · ddabidov/Headphone-DAC-AMP (XMOS XU316 + ES9039 — K1'e çok yakın) · spin-dac · OpAmp-Headphone (16 paralel OpAmp) · BoostCore-Module (MT3608) · rp2040-dac-amp.

**Dusk Studio IPC (crash izolasyonu dersi):**

| Platform | Mekanizma |
|----------|-----------|
| Linux | shared memory + eventfd |
| macOS | shm_open |
| Windows | CreateFileMapping + WaitOnAddress |

**OpenStudio hybrid:** JUCE C++ motor + React arayüz + WebView2 köprüsü (window.__JUCE__) — "hybrid mimariye en yakın örnek".

**Ekosistem mimarileri dersleri:** Spotify = ayrık servisler (account, upload, search, playlist, user) + transcode pipeline · Apple Music / YouTube Music = upload→transcode→distribution zinciri · arama servisinin ayrı bir servis olması.

**ASIO/WASAPI:** Steinberg ASIO SDK açık kaynak lisans varyantı dağıtılabilir (steinberg.net/developers/asiosdk-open) · evrensel built-in ASIO driver 64-bit'te var, 32-bit'te YOK · WASAPI exclusive vs ASIO gecikme karşılaştırması · profesyonel ses için ASIO şart.

### §3.7 Entegrasyon — CoreMusic'e SOMUT Giriş

| # | Somut Giriş | Hedef K | Giriş Noktası | Sahip Agent |
|---|-------------|---------|---------------|-------------|
| 1 | Audio Service (9741/9742) süreç ayrımı — plugin/DSP crash'i ana oynatıcıyı öldürmez | K3, K0 | architecture/k3-ses-motoru/neva-engine-core.md + k0-isletim-sistemi/process-isolation.md | embedded |
| 2 | JUCE lisans kararı: GPL kaynak paylaşımı mı, JUCE commercial lisans mı (kapalı kaynak CoreMusic) | K3 | ADR (yeni; brain.md ADR aralığı) — Guardrail #14 onayı | embedded + Vault Steward |
| 3 | ASIO SDK lisans kontrol listesi + 64-bit-only dağıtım kararı | K2, K0 | architecture/k2-surucu/asio-drivers.md + asio-wasapi-rehber.md | win-sw |
| 4 | Playlist servisine smart query sözleşmesi (Koel dersi, MIT fikir) | K8, K9 | k8-servis/media-service.md + k9-api-routing/openapi-spec.md | backend |
| 5 | Transcode kuyruk pipeline'ı (Spotify/Ampache dersi) | K15, K14 | k15-medya-streaming/audio-transcoding.md + content-delivery.md | backend + devops |
| 6 | Servis sınırı ve event adları: account/upload/search/playlist ayrımı → Control/Media/AI/Download | K8 | k8-servis/* + service-integration.md event tablosu | backend |
| 7 | XU316 USB audio referans tasarımı ile mevcut XMOS/PCM3168A/AK4458 fark analizi | K1 | k1-donanim/xmos-xu316.md, usb-audio.md | audio-hw + dsp-fw |
| 8 | Class AB BOM'unda TPA3255 topolojisi karşılaştırma satırı (alım kararı Class AB'de kalır) | K16, K20 | k16-class-ab/index.md + k20-bom/ic-list.md | audio-hw |
| 9 | Boost yardımcı devre referansı (MT3608/PBA MK1) → LM5122 tasarımına kıyasla | K17 | k17-guc-kaynagi/lm5122-dual-boost.md | audio-hw |
| 10 | Studio paneli hybrid UI denemesi (WebView2 + native köprü) — ADR gerekir | K10 | k10-uygulama/studio-panel.md | ui + embedded |

**Entegrasyon kuralı:** Bu tablodaki her satır ya bir katman dosyasına işlenir ya da ADR'ye taşınır; ikisi de olmazsa satır "BLOKE" olarak işaretlenir ve CLAUDE.md §7 Contradiction Gate işletilir.

### §3.8 Riskler

| # | Risk | Etki | Önlem |
|---|------|------|-------|
| 1 | AGPL kaynaklı kodun CoreMusic'e (kapalı kaynak) sızması | Yüksek — hukuki | §4.4 lisans kapısı; yalnız fikir; kod incelemesinde lisans etiketi |
| 2 | JUCE GPL ile ticari dağıtım çatışması | Yüksek — ürün modeli | ADR ile commercial lisans bütçesi veya LGPL/MIT alternatif (YUP, sndfilter) kararı |
| 3 | Yıldız/sürüm verisinin eskimesi | Düşük — yanıltıcı | Kaynak tarihi her dosyada: exa 2026-09-24; 6 ayda tazele |
| 4 | Çift SSOT (github-referanslari vs ecosystem) | Orta — çelişki | github-referanslari = havuz kaydı; ecosystem = ders yorumu; çelişkide havuz kaydı ve CLAUDE.md kazanır |
| 5 | 32-bit ASIO varsayımıyla plan yapmak | Orta — israf | asio-wasapi-rehber.md §3 kararı: 64-bit-only ASIO, 32-bit için WASAPI exclusive |

### §3.9 Kardeş Dosyalar Özeti (Hızlı Yönlendirme)

> Bu özetler "hangi dosyada ne var"un kısa karşılığıdır; detay ve lisans tabloları ilgili dosyanın kendi §4.4'ündedir.

**[[muzik-streaming-sunuculari]] — Koel · Ampache · Mopidy**

| Başlık | İçerik |
|--------|--------|
| Ana ders | Koel'in akıllı playlist + Last.fm/Spotify/MusicBrainz entegrasyonu; Ampache'nin 2001'den beri süren Subsonic API uyumluluğu |
| Hedef K | K8 (servis), K9 (API sözleşmesi), K15 (transcode/stream uçları), K4/K5 (fikir düzeyinde) |
| Kopyalanmaz | Ampache AGPL-3.0 kodu — yalnız fikir |
| Karşılaştırma özeti | Koel = arayüz lideri · Ampache = özellik lideri · Mopidy = Python eklenti mimarisi |

**[[ses-dsp-acik-kaynak]] — JUCE · DaisySP · Dusk · OpenStudio**

| Başlık | İçerik |
|--------|--------|
| Ana ders | JUCE framework + Awesome-Audio-DSP derleme listesi; Dusk Studio'nun OOP plugin host IPC'si (crash izolasyonu); OpenStudio'nun C++/React/WebView2 hybrid köprüsü |
| Hedef K | K3 (motor), K10 (uygulama), K2 (sürücü köprüsü), K0 (süreç ayrımı) |
| Kopyalanmaz | GPL kod linklemesi lisanssız yapılamaz; fikir/algoritma serbest |
| Alternatif | YUP (148★, ISC) — JUCE 7 fork'u, lisans dostu referans |

**[[donanım-devre-referanslari]] — TPA3255 · XU316 · DAC · Boost**

| Başlık | İçerik |
|--------|--------|
| Ana ders | ddabidov/Headphone-DAC-AMP CoreMusic K1'iyle birebir örtüşür (XMOS XU316 + ES9039); TPA3255 PBTL topoloji kıyası; MT3608/PBA MK1 boost kıyası |
| Hedef K | K1, K16, K17, K19, K20 |
| Kopyalanmaz | Görsel/BOM kopyasında telif kontrolü; devre fikri (topoloji) serbest |
| Uyarı | PCM5122 CLAUDE.md §21'de yasak — DA15'in PCM5102A'sı da 8.1 kapsamına girmez |

**[[ekosistem-mimarileri]] — Spotify · Apple Music · YouTube Music**

| Başlık | İçerik |
|--------|--------|
| Ana ders | Ayrık servisler (account/upload/search/playlist/user) + upload→transcode→distribution pipeline'ı |
| Hedef K | K8, K9, K14 (distribution/CDN), K15 (transcode kuyruğu), K12 (kuyruk metrikleri) |
| Kopyalanmaz | Kapalı platform kodu zaten yok — yalnız mimari ders |
| Çıkarım | Server + client çift rolü olan CoreMusic için oturum/sync sınırları |

**[[asio-wasapi-rehber]] — ASIO SDK · WASAPI · 32-bit Gerçeği**

| Başlık | İçerik |
|--------|--------|
| Ana ders | ASIO SDK açık kaynak lisans varyantı dağıtılabilir; evrensel built-in ASIO 64-bit'te var, 32-bit'te yok |
| Hedef K | K2 (birincil), K0 (platform stratejisi), K3 (gecikme hedefi) |
| Kopyalanmaz | Lisans metni değiştirilemez; atıf korunur |
| Çıkarım | Tier-1 Windows: 64-bit ASIO + WASAPI exclusive fallback; 32-bit yalnız WASAPI |

**[[README]] ve [[service-integration]] (mevcut — değiştirilmedi)**

| Dosya | İçerik |
|-------|--------|
| README.md | 7 servis, 10 panel, 11 subdomain, 18 BCNF özet tabloları (2026-09-20) |
| service-integration.md | Entegrasyon protokolleri (HTTP/REST, WebSocket, Event Bus, gRPC, IPC) + 5 event tipi |

---

## §4 Kurallar

### §4.1 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*(Bu blok docs-md-template §3.3'ten birebir kopyalanmıştır — silinemez.)*

### §4.2 Ekosistem Dosyası Kuralları

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Frontmatter 7 zorunlu alan + durum/tarih/kaynak | title, type, category, version, status, authority, updated + durum: active, tarih, kaynak: exa web doğrulaması | Dosya geçersiz, revert |
| 2 | Her ana dosya ≥ 500 satır | Ham ölçüm (vault-utf8-writer verify → lines) | Eksik derinlik → tamamlanır |
| 3 | Yeni bilgi doğrulanır | Doğrulanmayan iddia: VERIFICATION REQUIRED | Etiketsiz iddia silinir (Guardrail #3) |
| 4 | Lisans yazımı zorunlu | Her ekosistem dosyasının §4.4'ünde lisans tablosu vardır | Lisanssız dosya → DUR |
| 5 | K çapraz referans zorunlu | Her ders §3.7 tablosuna (veya kendi entegrasyon bölümüne) K katmanı ile yazılır | K'sız ders → işlenemez sayılır |
| 6 | 0 dosya silme | Mevcut README.md ve service-integration.md değiştirilmez/silinmez | Silme tespit → git checkout + log ERROR |
| 7 | SSOT self-claim yasak | Bu dizin SSOT değildir; authority: reference | Çelişkide CLAUDE.md kazanır |
| 8 | Tek yazma arayüzü | vault-utf8-writer (UTF-8, BOM yok) | Bozuk dosya repair edilir |

### §4.3 Yasak / Doğru

| ✅ Yasak | ✅ Doğru |
|----------|----------|
| AGPL kodunu CoreMusic'e kopyalamak | Yalnız fikir/mimari dersi al; beyaz oda doküman |
| Lisanssız "referans" kod yapıştırmak | Kaynak + lisans + tarih ile referans ver |
| Ekosistemi ikinci SSOT ilan etmek | authority: reference; katman SSOT = architecture/index |
| Yeni web araştırması üretip eski doğrulamayı geçersiz kılmak | Kaynak tarihini koru; yeni veri → tarih + kaynak etiketiyle eklenir |
| Dosya adında Türkçe karakter | Dosya/klasör adı İngilizce (index.md, asio-wasapi-rehber.md) |
| Eski ekosistem dosyasını "yenisinin yerine" silmek | In-Place genişletme; silme yok (0 dosya silme) |

### §4.4 Lisans Kapısı (Özet — detay her dosyanın §4.4'ünde)

| Lisans | Örnek | CoreMusic (kapalı kaynak) Kararı |
|--------|-------|----------------------------------|
| MIT / ISC / BSD / Zlib / Public Domain | Koel (MIT), YUP (ISC), sndfilter (BSD-0), PortAudio (PD) | ✅ Fikir serbest; kod kopyalanırsa atıf + lisans dosyası korunur |
| GPL v2/v3 | JUCE (GPL), EasyEffects, HISE, FAUST | ⚠️ Kod linklenirse dağıtım GPL olur → ADR gerekir; fikir/algoritma serbest |
| AGPL-3.0 | Ampache | ❌ KOD ASLA kopyalanmaz — yalnız mimari fikir |
| LGPL | libsndfile, FFmpeg (varsayılan LGPL) | ⚠️ Dinamik link ile esnek; statik linkte kaynak paylaşım yükü |
| Steinberg ASIO SDK (açık kaynak varyant) | ASIO SDK | ✅ Dağıtılabilir; LICENSE şartları + atıf korunur (detay: asio-wasapi-rehber.md) |

---

## §5 Workflow

### §5.1 Ekosistem Dersi İşleme Adımları

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | İlgili ekosistem dosyasını oku (§2.2'ye göre) | Ders + kaynak + lisans | 5 dk |
| 2 | §3.4/§3.5'ten hedef K katmanını doğrula | K etiketi | 2 dk |
| 3 | Lisans kapısını uygula (§4.4) | ✅/⚠️/❌ kararı | 2 dk |
| 4 | SOMUT girişi belirle: katman dosyası mı, ADR mi | Giriş noktası | 5 dk |
| 5 | Guardrail #14: mimari karar ise kullanıcı onayı | Onay | değişken |
| 6 | Katman dosyasına işle veya ADR aç | Vault güncellemesi | 10 dk |
| 7 | log.md append + bu indeksin envanterini güncelle | Audit trail | 2 dk |

~~~text
EKOSİSTEM DOSYASI OKU → K KATMANI EŞLE → LİSANS KAPISI → SOMUT GİRİŞ (katman dosyası | ADR) → ONAY (gerekirse) → İŞLE → LOG
~~~

### §5.2 Dosya Bazlı Kullanım Senaryoları

| Senaryo | Okunan Dosya | Beklenen Çıktı |
|---------|--------------|----------------|
| "Koel'in playlist mantığını K8'e nasıl alırız?" | muzik-streaming-sunuculari.md | smart_query sözleşmesi + MIT lisans notu |
| "Audio Service'i JUCE ile kurabilir miyiz?" | ses-dsp-acik-kaynak.md | GPL kararı: ADR veya alternatif kütüphane |
| "XU316 referans tasarımını K1'e nasıl işleriz?" | donanım-devre-referanslari.md | fark analizi satırı (K1 xmos-xu316.md) |
| "Transcode pipeline nereden başlar?" | ekosistem-mimarileri.md | K15 audio-transcoding + K14 content-delivery |
| "ASIO SDK'yı dağıtabilir miyiz?" | asio-wasapi-rehber.md | ✅ evet + LICENSE kontrol listesi |

### §5.3 Güncelleme Protokolü (Yeni Ekosistem Verisi Geldiğinde)

| # | Kural |
|---|-------|
| 1 | Yeni veriye **kaynak + tarih** etiketi konur (ör. exa 2026-09-24). Etiketsiz veri girmez. |
| 2 | Yeni referans havuza önce [[../architecture/github-referanslari]] §1-§5'e, sonra bu dizindeki ilgili dosyanın ilgili tablosuna eklenir (§8.2 havuz bütünlüğü). |
| 3 | Yeni K katmanı eşlemesi varsa hem bu indeksin §3.4'ü hem github-referanslari §6'sı güncellenir (iki tablo birlikte taşınır). |
| 4 | Eski satır **silinmez**, üstü çizilmez — değişiklik geçmişi append-only'dir. |
| 5 | Lisans değişikliği (fork, relicensing) tespit edilirse dosyanın §4.4'ü + bu indeksin §4.4'ü birlikte güncellenir. |

### §5.4 Çapraz Okuma Akışları

| Rol | Akış |
|-----|------|
| Backend | index.md §3.5 → muzik-streaming → ekosistem-mimarileri → k8-servis/*, k9-api-routing/* |
| Embedded | index.md §3.5 → ses-dsp → asio-wasapi → k2-surucu/*, k3-ses-motoru/* |
| Audio HW | index.md §3.5 → donanım-devre → k1-donanim/*, k16-k20/* |
| Windows SW | asio-wasapi → k2-surucu/wasapi-exclusive.md, asio-drivers.md → k0-isletim-sistemi/windows-api.md |
| MO / Vault Steward | index.md tamamı → §3.8 riskler → §6 doğrulama |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] durum: active, tarih: 2026-09-24, kaynak alanları mevcut
- [ ] Tek H1 var, # ile başlıyor
- [ ] §4.1 Şablon-Önce bloğu birebir mevcut ve §4'ün ilk maddesi
- [ ] §1-§7 başlıkları eksiksiz (§3.7 Entegrasyon dahil)
- [ ] Kapsam / Kapsam Dışı tablosu ≥ 3 satır
- [ ] Zorunlu Bağlantılar satırı en az 1 wiki-link taşıyor
- [ ] §3.4 çapraz referans 21 K satırı içeriyor (K0-K20)
- [ ] §3.5 ders → K matrisi her satırda K katmanı ve SOMUT giriş barındırıyor
- [ ] §4.4 lisans kapısı 5 satır (MIT/GPL/AGPL/LGPL/ASIO SDK)
- [ ] §5.1 adım listesi ≥ 4 adım
- [ ] §6.1'de - [ ] maddeleri ≥ 8
- [ ] §7.2 Değişiklik Geçmişi append-only tablo var
- [ ] Authority footer (Authority + Last Updated + Mode) var
- [ ] Mojibake yok: node .ai/scripts/vault-utf8-writer.mjs verify → mojibake 0, BOM false
- [ ] 6 ana dosyanın her biri ≥ 500 satır (ölçüm: dosya listesi × satır sayısı raporu)
- [ ] 0 dosya silindi (git status delete = 0)
- [ ] Yeni web araştırması yapılmadı; tüm veri exa 2026-09-24 doğrulaması

### §6.2 Metrikler

| Metrik | Değer |
|--------|-------|
| Ekosistem dizini toplam dosya | 8 (2 mevcut + 6 yeni) |
| Yeni ana doküman | 6 (her biri ≥ 500 satır) |
| Kapsanan K katmanı | 19/21 (K7, K13 hariç — atama bekliyor) |
| Kapsanan lisans sınıfı | 5 (MIT ailesi, GPL, AGPL, LGPL, ASIO SDK) |
| Doğrulama kaynağı | exa web doğrulaması — 2026-09-24 |
| Versiyon | 1.0.0 |
| Silinen dosya | 0 |

### §6.3 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| K eşleşmesi eksik | §3.4/§3.5 satırı boş | github-referanslari §6/§7 ile senkronla |
| Lisans etiketi yok | Dosya §4.4'süz | Lisans tablosunu ekle, yoksa VERIFICATION REQUIRED |
| Satır < 500 | verify lines düşük | §3.5/§5 derinleştir; silme yapma |
| Çift SSOT iddiası | "ekosistem tek kaynaktır" | authority: reference'a çek |
| Mojibake | verify mojibake > 0 | vault-utf8-writer repair |

---

## §7 Referanslar

### §7.1 Wiki-link Referansları

| Hedef | İlişki |
|-------|--------|
| [[../CLAUDE.md]] | Vault anayasası — Guardrail #1/#3/#16 kaynağı |
| [[../AGENTS.md]] | Agent routing §6 + DevOps okuma listesi §24.3 |
| [[../architecture/index]] | K0-K20 katman SSOT'u |
| [[../architecture/github-referanslari]] | GitHub referans havuzu (bu dizinin veri kaynağı) |
| [[../brain]] | ADR defteri — JUCE/ASIO kararları buraya açılır |
| [[../.templates/documentation/docs-md-template]] | Bu dosyanın şablonu (Guardrail #16) |
| [[../.templates/index]] | Şablon registry'si |
| [[README]] | Ekosistem servis/panel özeti (mevcut) |
| [[service-integration]] | Entegrasyon protokolleri + event types (mevcut) |
| [[muzik-streaming-sunuculari]] | Kardeş dosya — Koel/Ampache/Mopidy |
| [[ses-dsp-acik-kaynak]] | Kardeş dosya — JUCE/Dusk/OpenStudio |
| [[donanım-devre-referanslari]] | Kardeş dosya — donanım |
| [[ekosistem-mimarileri]] | Kardeş dosya — Spotify/Apple/YT Music |
| [[asio-wasapi-rehber]] | Kardeş dosya — sürücü/lisans |
| [[../log.md]] | Audit trail (append-only) |

### §7.2 Kaynak Linkleri (Web — exa doğrulaması 2026-09-24)

- https://www.steinberg.net/developers/asiosdk-open/ — ASIO SDK açık kaynak lisans sayfası
- https://github.com/juce-framework/JUCE — JUCE framework
- https://github.com/koel/koel — Koel müzik sunucusu
- https://github.com/ampache/ampache — Ampache müzik sunucusu
- https://github.com/dusk-audio/dusk-studio — Dusk Studio (IPC dersi)
- https://github.com/ddabidov/Headphone-DAC-AMP — XU316 + ES9039 referansı
- https://github.com/EliMattingly22/TPA3255_ClassD_PBTL — TPA3255 Class D PBTL
- https://github.com/hamzadenizyilmaz/BoostCore-Module — MT3608 boost modülü

### §7.3 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-09-20 | 1.0.0 | Ekosistem dizini ilk kez açıldı (README.md, service-integration.md) | vault-updater |
| 2026-09-24 | 1.0.0 | index.md üretildi — 6 ekosistem dosyası, K çapraz referans, lisans kapısı (exa doğrulaması) | ekosistem-writer (subagent) |

*(Append-only: bu tabloya yeni satır EKLENİR; mevcut satır değiştirilmez/silinmez.)*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
