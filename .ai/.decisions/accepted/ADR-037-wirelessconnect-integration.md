---
id: ADR-037
title: WirelessConnect Integration — Cihaz Eşleştirme + Bağlantı Yönetimi + Keşif + Güvenlik (Bluetooth BLE/Classic · Ağ mDNS/UPnP-DLNA · çoklu-protokol, cihaz tipine göre otomatik seçim)
type: adr
category: audio
date: 2026-09-26
updated: 2026-09-26
version: 1.0.0
status: accepted
authority: ADR-037 Karar Metni (SSOT)
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
deciders: ["Vault Steward", "Master Orchestrator"]
consulted: ["Embedded Engineer", "DSP Firmware Engineer", "Security Engineer"]
informed: ["UI Designer", "Windows Software Engineer", "QA Engineer"]
supersedes: null
superseded-by: null
related:
  - "[[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]]"
  - "[[.ai/.decisions/accepted/ADR-019-per-os-neva-player.md]]"
  - "[[.ai/.decisions/accepted/ADR-011-session-management.md]]"
  - "[[.ai/.decisions/accepted/ADR-020-api-public-security.md]]"
  - "[[.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md]]"
  - "[[.ai/.sql/mysql/coremusic_wireless.sql]]"
---

# ADR-037: WirelessConnect Integration — Eşleştirme · Bağlantı Yönetimi · Keşif · Güvenlik (çoklu-protokol)

**Durum:** accepted (Draft → Review → Active → **Active**; frozen YOK)
**Tarih:** 2026-09-26
**Karar Veren:** Vault Steward (kullanıcı onaylı karar kapsamı: (a) eşleştirme + (b) bağlantı yönetimi + (c) keşif + (d) güvenlik · protokol = çoklu-protokol/otomatik seçim · sonuçlar/riskler/fallback) + Master Orchestrator (kanıt taraması)
**İlgili ADR'ler:** [[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]] (ses katmanı + hard-RT kısıtları — kablosuz akış RT yoluna giremez) · [[.ai/.decisions/accepted/ADR-019-per-os-neva-player.md]] (IAudioBackend arayüzü + fallback zinciri — kablosuz çıkışın bağlanacağı sınır) · [[.ai/.decisions/accepted/ADR-011-session-management.md]] (oturum yaşam döngüsü — cihaz hafızası/çoklu oturum hattı) · [[.ai/.decisions/accepted/ADR-020-api-public-security.md]] (yetki/scope reddi hattı — yetkisiz cihaz reddi ile hizalı) · [[.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md]] (coremusic_wireless 18. BCNF veritabanı)

---

## 1. Bağlam ve Kod Kanıtı

Bu ADR, CoreMusic'in **kablosuz cihaz entegrasyonunu** (WirelessConnect) dört maddelik tek karar altında bağlar: **(a) cihaz eşleştirme** (Bluetooth hoparlör/kulaklık + ağ cihazları), **(b) bağlantı yönetimi** (kesinti algılama, otomatik yeniden bağlanma, cihaz hafızası), **(c) keşif** (mDNS/DNS-SD ağ cihazları), **(d) güvenlik** (eşleme doğrulama, yetkisiz cihaz reddi). Protokol kararı **çoklu-protokol + cihaz tipine göre otomatik seçim**dir. Kanıt 2026-09-26'da kod + vault taramasıyla derlendi ve **IMPLEMENTED** (diskte/kodda var) / **PLANNED** (karar olarak kurulan, karşılığı henüz yok) olarak etiketlendi.

### 1.1 Mevcut Durum (kod + vault kanıtı)

**A) Kablosuz KOD — YOK (dürüst bulgu):**

Tarama kapsamı: repo geneli **399 kod dosyası** (260 `*.php` + `*.js` + `*.css` + `*.sql` betik; `*.cpp/*.h/*.hpp` = **0**; `*.ts` = **0**), kalıp: `bluetooth | \bble\b | mDNS | dns-sd | \bupnp\b | \bdlna\b | airplay | \bwireless\b | pairing | \bdiscover`.

| Bulgu | Sonuç |
|-------|-------|
| `**/*.{cpp,h,hpp}` | **0 dosya** → BLE/mDNS/UPnP/AirPlay implementasyonu repoda yok |
| `**/*.ts` | **0 dosya** → `DevicePairing.tsx` (`k10-uygulama/home-panel.md:69`) yalnızca spec, kod karşılığı yok |
| Dar tarama (11 eşleşme) | **Hiçbiri kablosuz ses işlevi değil** — hepsi arayüz işareti veya başka bir "discover" kavramı |

11 eşlemenin gerçek anlamı (dosya:satır):

| Dosya:satır | İçerik | Dürüst etiket |
|-------------|--------|---------------|
| `home.coremusic.net/footer.php:113-134` → **`:133`** | `<button ... data-action="openBluetoothPopup" title="Bluetooth">` | Buton **var**, JS handler **YOK** — repoda `openBluetoothPopup` **tek eşleşme = bu satır** → tıklama sahipsiz (sahte kumanda) |
| `home.coremusic.net/header.php:101` · `:107` | `<!-- C02 — WiFi + Bluetooth kapsülü -->` + Bluetooth `<img>` | Yalnız **görsel** (ikon), işlev yok |
| `shared/src/Api/Registry/ServiceRegistry.php:36,38` · `Contracts/Api/ServiceRegistryInterface.php:5,30,35` | `discover(string $name): array` | **In-process servis kaydı arama** (ad ile) — ağ keşfi / mDNS **DEĞİL** |
| `assets.coremusic.net/js/components/base/ComponentLoader.js:2` | `Auto-discover and mount data-cm-component` | Bileşen montajı — ağ keşfi değil |

→ **Sonuç: eşleştirme · keşif · bağlantı yönetimi kodu = 0 satır → tamamı PLANNED.**

**B) Vault spec — IMPLEMENTED (doküman), kod karşılığı yok:**

| Varlık | Kanıt (dosya:satır / boyut) |
|--------|-----------------------------|
| Bluetooth A2DP sürücü spec | [[.ai/architecture/k2-surucu/bluetooth-a2dp.md]] **8420 b** — A2DP akış diyagramı `:16-26`, `enum BluetoothCodec {…, CODEC_LC3}` `:49-53` (SBC/AAC/LDAC/aptX/LC3), jitter buffer `:130-155`, A2DP state machine `:157` |
| Cihaz keşif servisi spec | [[.ai/architecture/k8-servis/device-service.md]] `:20` (UPnP/DLNA discovery + mDNS/Bonjour + **Bluetooth scanning** + manual registration), `:48` `/api/v1/discovery/mdns`, `:49` `/api/v1/discovery/bluetooth`, `:295-305` `scanBluetooth()` → `BluetoothLEScanner`, `:553` bağımlılık `SSDP/mDNS | External` |
| Servis durum iddiası | Aynı dosya `:555-562` — "DLNA Discovery ✅ · mDNS Discovery ✅ · **Bluetooth Discovery 🔄** · Health ✅ · Remote 🔄" → **spec iddiasıdır; §1.1-A kod 0 ile çelişir** (→ §4.3 R5) |
| Ağ servisi spec | [[.ai/architecture/k8-servis/network-service.md]] `:253-254,:316` `mDNSResponder`, `:551` "mDNS | External | Apple Bonjour" |
| K14 keşif katmanı (asıl ev) | [[.ai/architecture/k14-ag/mdns-discovery.md]] **RFC 6762 (mDNS) / 6763 (DNS-SD)** `:16`, UDP **5353** + `224.0.0.251` / `ff02::fb`, probing state machine `:100`, Bonjour/Avahi uyumluluğu `:244` · [[.ai/architecture/k14-ag/dlna-upnp.md]] **DLNA 1.5 / UPnP 2.0** `:16`, SSDP port **1900** `:289`, MediaServer/MediaRenderer/ControlPoint rolleri `:25` · [[.ai/architecture/k14-ag/airplay-streaming.md]] `:32` "mDNS Discovery" + `:44` "pairing + key exchange" · [[.ai/architecture/k14-ag/README.md]] `:148` K14.4 mDNS = 6 alt alan / 14 yaprak |
| Ağ ses sürücüleri | [[.ai/architecture/k2-surucu/network-audio-drivers.md]] `:2` "Ağ Ses Sürücüleri" — **kablolu/ağ** odaklı, kablosuz eşleştirme kapsamı yok |
| UI/UX spec | [[.ai/architecture/k10-uygulama/home-panel.md]] `:69` `DevicePairing.tsx` (PLANNED — `*.ts`=0), `:177-178` QR + Bluetooth eşleştirme, `:204` "Room management, **Device pairing**" · [[.ai/ui-design/flow/settings/02-bluetooth-connect.md]] **10451 b** (Pairing Flow diyagramı) · `.ai/ui-design/screens/T08-embedded/bluetooth-modal.md` **8364 b** (T08, RPi5 7") · `.ai/ui-design/prompt/page/12-bluetooth.md` **3739 b** · PNG `.ai/.png/home-1024/Linux 1024 - Bluetooth Quick Page Base.png` **618422 b** |

**C) Veri katmanı — IMPLEMENTED (şema), okuyan kod YOK:**

[[.ai/.sql/mysql/coremusic_wireless.sql]] **10107 b / 5 tablo**: `wifi_networks` `:22` · **`bluetooth_peers` `:59`** · `sync_history` `:93` · **`bluetooth_audio_profiles` `:118`** · `network_profiles` `:144` → katalog: [[.ai/architecture/k5-veri-yonetimi/README.md]] `:45` ve `:287-291` (K5.1.11, "WiFi + Bluetooth networks"). Bu şema ADR-003'ün 18 veritabanından 11.'sidir ([[.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md]] `:120` — "Donanım-sınırı veri; cihaz tarafı senkronu"). → **şema hazır, eşleştirilen cihaz/bağlantı geçmişini yazan kod yok (PLANNED).**

**D) ADR-017 / ADR-019 bulguları — kablosuz cihaz hedefi YOK:**

| ADR | Kablosuz taraması (`wireless\|kablosuz\|Bluetooth\|mDNS\|AirPlay\|UPnP\|A2DP`) | Bulgu |
|-----|--------------------------------------------------------------------------------|-------|
| [[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]] | **0 eşleşme** | Katman 3 = "ASIO/WASAPI host … **cihaz keşfi**" (`:128`) = **OS cihaz enum'u, kablolu**; kurtarma `ASIO → WASAPI → Null Output` (`brain.md:862`) |
| [[.ai/.decisions/accepted/ADR-019-per-os-neva-player.md]] | **1 eşleşme** — o da yalnız dosya listesi (`:39` … `bluetooth-a2dp.md`) | `IAudioBackend` repoda **0 sonuç** (ADR-019 §1.1) → arayüz **kablolu/OS backend** sözleşmesidir; kablosuz çıkış bu zincirin **dışında** durur |

→ **Boşluk:** ADR-017/019 ses yolunu, ADR-011/020 oturum + API güvenliğini bağladı; **kablosuz cihaz katmanı (eşleştirme + keşif + yeniden bağlanma) hiçbir ADR'de yok** → bu ADR o halkayı bağlar. Kablosuz akış, ADR-017 hard-RT yasaklarına (`callback`'te bloklayıcı ağ I/O yasağı) tabidir: **kablosuz I/O yalnız non-RT iş parçacığında** (§2.2-f).

**E) Slot ve envanter kanıtı (numara-boşluğu-doldurma usulü):**

- [[.ai/.decisions/index.md]] `:74` → `ADR-037-wirelessconnect-integration | WirelessConnect Integration | **Audio**`
- Aynı slot: [[.ai/index.md]] `:654` (`… | WirelessConnect integration | **Integration**`) · [[.ai/keys.md]] `:272` (`ADR-037 | WirelessConnect, WiFi | Integration`) · [[.ai/brain.md]] `:992` (`ADR-037 | Kablosuz ağ entegrasyonu`) · `.ai/.templates/adr/adr-index.md` `:108`
- Proje envanteri: [[.ai/PROJECTS.md]] `:191-198` **WirelessConnect** — Dil C++20 · Protokoller **BLE 5.0, WiFi Direct, mDNS, DLNA/UPnP** · Hedef "Cihazlar arası keşif ve otomatik bağlantı" · **Durum: PLANLANMIŞ**
- Yol haritası: [[.ai/AGENTS.md]] `:445` `v23.0 — Cross-Project Memory (WirelessConnect)` ⚠️ PLANNED (`.ai/.agents/master-orchestrator.md:107` aynı etiketi taşır); `.ai/projects/` dizini **YOK** (motordan gelen `projects/WirelessConnect/...` wiki-link'i kırık — ayrı iş, §5.1 adım 12)
- **İki çelişki açıkça işaretli:** (1) [[.ai/.decisions/index.md]] `:28` aralığı "Frozen 37 (ADR-001 → ADR-037)" sayar — ADR-036 gibi bu dosya da `status: accepted` + **frozen YOK**; dondurma ayrımı index tarafında düzeltilir (§5.1 adım 11). (2) Kategori `:74` = Audio, `index.md`/`keys.md` = Integration → **kayıt SSOT `:74` (Audio)** alınır.

### 1.2 Sorun Tanımı

1. **Eşleştirme yok:** kullanıcı Bluetooth hoparlör/kulaklık veya ağ cihazı bağlamak isterse UI'da ikon var (`footer.php:133`), akış yok — pairing flow yalnız ui-design spec'inde (`02-bluetooth-connect.md`).
2. **Bağlantı yönetimi yok:** kesinti algılama, otomatik yeniden bağlanma, cihaz hafızası için şema hazır (`bluetooth_peers`, `sync_history`) ama **yazan/okuyan kod 0** → kopan bağlantı kullanıcıya "yeniden bağlan" diyerek döner.
3. **Keşif dağınık:** mDNS/DNS-SD + UPnP/DLNA + AirPlay spec'leri K14'te ayrı dosyalarda, device-service ile çakışan "✅ Tamamlandı" iddiaları var; tek bir **keşif servisi sözleşmesi** yazılmamış.
4. **Güvenlik boş:** eşleme doğrulama (MITM koruması), yetkisiz cihaz reddi, cihaz kimlik doğrulaması ADR-011/020 hattında **cihaz düzeyinde** tanımlanmamış — oturum güvenliği insan, cihaz güvenliği sahipsiz.
5. **Protokol parçalanması:** tek protokol tüm cihazları kapsamaz (LE Audio yeni, Classic yaygın; AirPlay Apple; DLNA ev cihazı; mDNS ortak keşif) → tek seçim ya cihaz uyumsuzluğu ya da keşif boşluğu üretir.
6. **Spec-kod uçurumu:** `device-service.md:557-562` "✅ Tamamlandı" derken repoda `*.cpp = 0` → durum iddiası doğrulanamaz (`⚠️ VERIFICATION REQUIRED`).

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) `wireless audio 2025 2026 Bluetooth LE Audio LC3 Auracast adoption speakers headphones` · (2) `mDNS DNS-SD device discovery security risks local network 2025 best practices` · (3) `Bluetooth pairing security Just Works numeric comparison LE Secure Connections MITM attack verification 2025` · (4) `UPnP DLNA vs mDNS AirPlay Chromecast local network audio casting protocol fragmentation interoperability 2025` · (5) `Bluetooth audio connection resilience reconnection dropout latency wireless speaker auto reconnect best practices 2025` |
| Web Search **Konusu** | (a) kablosuz ses 2025-26 olgunluğu (Bluetooth LE Audio / LC3 / Auracast yayılımı, Classic A2DP'nin kalıcılığı); (b) mDNS/DNS-SD yerel-ağ keşfinin gizlilik ve güvenlik gereksinimleri (RFC 8882, RFC 7558); (c) Bluetooth eşleştirme güvenliği (NIST SP 800-121, LE Secure Connections association modelleri, MITM koruması); (d) UPnP/DLNA vs mDNS/AirPlay/Google Cast protokol mimarisi farkları ve parçalanma; (e) bağlantı dayanıklılığı (dropout nedenleri, radyo/profil/geç-uyku, yeniden bağlanma, kablolu fallback) |
| Web Search **Bağlam** | Kablosuz kod **0** (399 dosya, 11 alakasız eşleşme — §1.1-A); spec **IMPLEMENTED** (k2 A2DP · k8 device-service · k14 mDNS/DLNA/AirPlay); şema **IMPLEMENTED** (coremusic_wireless 5 tablo); ADR-017/019'da kablosuz hedef **YOK** (§1.1-D) → çoklu-protokol + güvenlik kararı, spec'e gömülmeden 2025-26 literatürüyle hizalanmalı |
| Web Search **Kısa Açıklama** | 5 sorgu **2026-09-26**'da çalıştırıldı; **~48 adlandırılmış kaynak** derlendi: Bluetooth SIG resmi (9 — LE Audio, Auracast, 2026 trend blogu, Reliability PDF), NIST (4 — SP 800-121 r1/r2/legacy), IETF/RFC (8 — RFC 8882 ×4, RFC 7558, draft-04/05/02, draft-otis), NVD/CVE (2), üretici/destek (10 — Qualcomm, GN, Nordic ×2, TI, Silicon Labs ×2, Sony ×2, Microsoft ×2), protokol karşılaştırma (10 — IEEE DLNA, Cisco, Wikipedia, GitHub AirConnect, What Hi-Fi testi, zeecast/localcast/pigeoncast 2026, superuser, deepwiki), deste/SSS (4 — KEF ×2, Sennheiser, dn.org); tümü "çoklu-protokol + katmanlı eşleştirme güvenliği + keşif/yetki ayrımı + dayanıklılık katmanı" yönünde |
| Web Search **Uzun Açıklama** | **(a) LE Audio/Classic (kaynak 1-10):** LE Audio + LC3 + Auracast ana akıma giriyor (Bluetooth SIG "2026 trends" 2026-01-03; Nordic "about to hit the commercial mainstream"; Qualcomm/GN "3 milyar LE Audio cihaz"); LC3 aynı kaliteyi ~%50 daha düşük bit hızında veriyor (SIG LE Audio sayfası) → pil/kesinti avantajı; buna karşılık Classic A2DP (SBC/LDAC/aptX) kurulu taban — **tek protokol her iki dünyayı da karşılamaz → iki taraf da desteklenmeli**. **(b) Keşif gizliliği (kaynak 11-18):** RFC 8882 — DNS-SD/mDNS "public exposure" ister: service instance name, host name, service properties sızar; hostname güçlü cihaz/kişi tanımlayıcıdır, birleşik kayıt kümesi **device fingerprint** üretir; RFC 7558 — kapsam düzgün kısıtlanmazsa bilgi ağ dışına taşar; dn.org — mDNS/DNS-SD'de **gömülü kimlik doğrulama/şifreleme yok** (spoof/DoS); SentinelOne CVE-2025-68276 — Avahi DoS → **keşif yalnız listeleme yapar, yetkilendirme ayrı katmanda**. **(c) Eşleştirme güvenliği (kaynak 19-28):** NIST SP 800-121 r2 — LE **Security Mode 1 Level 4** en güçlüsü (authenticated Secure Connections, ECDH); **Just Works MITM koruması VERMEZ** (TK=0); Numeric Comparison / Passkey Entry / OOB MITM koruması verir; Bluetooth SIG Core 5.4 Security Manager — MITM seçeneği açıkça istenmeli, aksi halde IO capability'ye bakılmaksızın Just Works; NVD **CVE-2022-25836** — pairing method confusion ile passkey kaba-kuvveti (Core 4.0-5.3) → **tek association modeline sabitlenme yasak, bonding + kontrollü yeniden eşleştirme**. **(d) Protokol parçalanma (kaynak 29-38):** DLNA/UPnP = SSDP (UDP 239.255.255.250:1900) + SOAP + HTTP pull; AirPlay 2 = mDNS (5353) + RTSP/RTP (ALAC) + **HomeKit SRP pairing/key verify**; Google Cast = mDNS `_googlecast._tcp` + DIAL + TLS:8009 — üçü **bağımsız keşif/yayın modelleri**, tek protokol her cihazı kapsamaz (AirConnect tam da bu yüzden köprü kuruyor); Cisco — mDNS link-local (TTL=1), VLAN aşamaz; **What Hi-Fi 2025 ölçümü: AirPlay 2 sesi 44.1 kHz'e çevirir (bit-perfect değil), Cast Home modunda 48 kHz** → otomatik seçim yalnız **bağlantı** için değil **örnek hızı/kalite** için de gerekli. **(e) Dayanıklılık (kaynak 39-48):** Sony — dropout nedenleri mesafe/engel/parazit/pil + 2.4 GHz Wi-Fi çakışması (5 GHz'e geçiş önerisi); Sennheiser — duraklatınca radyo düşük güç durumundan uyanma + **profil geçişi** (A2DP↔HFP) kısa kopuş üretir; Microsoft — güç yönetimi/driver `IdleTimeout` kaynaklı oto-kopma; Bluetooth SIG — dayanıklılık **adaptive frequency hopping + küçük/hızlı paket** ile sağlanır ama yine de kayıp olur; KEF — kablolu "Cable Mode" son çare fallback'i. → **kesinti algılama + otomatik yeniden bağlanma + kablolu/yerel fallback zorunlu**. |
| Web Search **Paragraf Veri Uzun** | Araştırma dört karar zeminini besledi: (1) **çoklu-protokol zorunlu** — LE Audio + Classic A2DP + mDNS/UPnP-DLNA/AirPlay tek başına hiçbir zaman tüm cihaz setini kapsamıyor; "tek protokol seç" diyen ters kaynak yok. (2) **Otomatik seçim zorunlu** — cihaz tipi + bağlantı kalitesi + örnek hızı (AirPlay 44.1 kHz / Cast 48 kHz dönüşüm kanıtı) birlikte değerlendirilir; seçim yalnız bağlamak değil, doğru kaliteyle bağlamaktır. (3) **Eşleştirme güvenliği katmanlı ve varsayılan-güçlü** — NIST: Just Works MITM'siz → varsayılan LE Secure Connections + Numeric Comparison; yetkisiz cihaz reddi ADR-020 scope mantığıyla aynı anda kurulmalı; method-confusion CVE'si tek modele kilitlenmeyi yasaklıyor. (4) **Dayanıklılık katmanı zorunlu** — dropout nedenleri çok ve ortak (radyo uyku, profil geçişi, parazit, pil, sürücü); algılama + yeniden bağlanma + kablolu fallback tek sözleşmede toplanmazsa her protokol kendi yolunu çizerek parçalanır. Ters yön ("eşleştirmeyi atla", "keşfi güvenlik duvarına bırak", "tek protokol yeter", "kablolu fallback gereksiz") **hiçbir kaynakta yok**; tek açık çekince: mDNS keşfi kimlik sızdırır → keşif ile yetkilendirme ayrılmalı (RFC 8882) — bu, (d) maddesinin mitigasyonudur, reddi değil. |
| Web Search **Sonucu** | **9/9 alan tek yönlü**; ~48 kaynak / 5 sorgu (2026-09-26); çapraz doğrulama ≥2 kaynak: LE Audio yayılımı (SIG ×4 + Nordic ×2 + Qualcomm/GN), keşif gizliliği (RFC 8882 ×4 + RFC 7558 + dn.org + CVE), eşleştirme (NIST ×4 + SIG ×2 + NVD), protokol parçalanma (IEEE + Cisco + Wikipedia + 4 karşılaştırma makalesi + AirConnect), dayanıklılık (SIG ×3 + Sony ×2 + Microsoft ×2 + Sennheiser + KEF ×2). **Tek kaynakla kalan 2 çıkarım işaretli:** `⚠️ VERIFICATION REQUIRED` — AirPlay 44.1 kHz örnek-hazı dönüşümü tek test yazısından (What Hi-Fi 2025-04-14); "Avahi CVE yerel-DoS" tek güvenlik bülteninden (SentinelOne) → ikinci kaynak aranır (§5.1 adım 9). Ters/çelişkili iddia tespit edilmedi. |
| Web Search **Alınan Karar** | §2 (a)-(d): **(a) cihaz eşleştirme** — Bluetooth (kulaklık/hoparlör, BLE + Classic/A2DP) ve ağ cihazları tek pairing sözleşmesi altında; **(b) bağlantı yönetimi** — kesinti algılama + otomatik yeniden bağlanma + cihaz hafızası (bluetooth_peers/sync_history); **(c) keşif** — mDNS/DNS-SD birincil, UPnP/DLNA (SSDP) ikincil keşif yolu; **(d) güvenlik** — varsayılan LE Secure Connections + Numeric Comparison, Just Works yalnız I/O yetmezliğinde ve yalnız kayıtlı cihazda, yetkisiz cihaz reddi (ADR-011/020 hattı). **Protokol = çoklu-protokol, cihaz tipine göre otomatik seçim** (Bluetooth ↔ Ağ; seçim: cihaz tipi + bağlantı kalitesi + örnek hızı) + **fallback: kablolu/yerel** |
| Web Search **Sonuç** | Araştırma ile karar uyumu: **9/9 alan tek yönlü, ~48 kaynak / 5 sorgu (2026-09-26)**; §4.3 riskleri (eşleştirme açığı, keşif gizliliği, kesinti, protokol parçalanma) bu kaynakların da vurguladığı risklerle birebir örtüşüyor; 2 tek-kaynak çıkarım `⚠️ VERIFICATION REQUIRED` ile işaretli |

**Kaynaklar (~48):** (1) bluetooth.com — LE Audio Media Kit · (2) bluetooth.com — Auracast · (3) bluetooth.com — Bluetooth trends 2026 (2026-01-03) · (4) bluetooth.com — LE Audio (LC3 %50 bit-hızı) · (5) bluetooth.com — Auracast overview PDF (2025-05-23) · (6) blog.nordicsemi.com — LE Audio & Auracast mainstream · (7) blog.nordicsemi.com — LE Audio ready for developers · (8) qualcomm.com — Bluetooth LE Audio · (9) gnhearing.com — Auracast/LE Audio (3 milyar cihaz) · (10) platform.tracxn.com — Aurahear (Auracast ekosistemi) · (11) rfc-editor.org — RFC 8882 DNS-SD Privacy & Security · (12) ietf.org — draft-ietf-dnssd-prireq-04 · (13) ietf.org — draft-ietf-dnssd-prireq-05 · (14) ietf.org — draft-ietf-dnssd-prireq-02 · (15) datatracker.ietf.org — RFC 7558 scalable DNS-SD · (16) datatracker.ietf.org — draft-otis-dnssd-scalable-dns-sd-threats · (17) dn.org — mDNS/DNS-SD in Local Networks (auth/şifreleme yok) · (18) sentinelone.com — CVE-2025-68276 Avahi DoS ⚠️ tek-kaynak · (19) nvlpubs.nist.gov — SP 800-121r2-upd1 Guide to Bluetooth Security · (20) nvlpubs.nist.gov — SP 800-121r2 · (21) csrc.nist.gov — SP 800-121 Rev1 draft · (22) nvlpubs.nist.gov — SP 800-121 legacy · (23) bluetooth.com — LE secure connections numeric comparison (pairing part 4) · (24) bluetooth.com — Core 5.4 Security Manager Specification · (25) nvd.nist.gov — CVE-2022-25836 pairing method confusion · (26) dev.ti.com — BLE Security Fundamentals · (27) docs.silabs.com — Pairing Processes v3.1 · (28) docs.silabs.com — Pairing Processes v4.0 · (29) site.ieee.org — The DLNA Technology Solution (SSDP/UPnP AV) · (30) cisco.com — CUWN mDNS Gateway + Chromecast (TTL=1, link-local) · (31) en.wikipedia.org — AirPlay (RTSP/RAOP, Wi-Fi Direct) · (32) github.com/philippe44 — AirConnect (AirPlay↔UPnP/Cast köprüsü) · (33) zeecast.eslamx.com — AirPlay vs DLNA vs Chromecast (2026) · (34) localcast.app — Chromecast vs DLNA (2026) · (35) pigeoncast.com — AirPlay vs DLNA vs Cast vs Miracast (2026) · (36) superuser.com — DLNA vs UPnP farkı · (37) deepwiki.com — Chromecast discovery & casting · (38) whathifi.com — bit-perfect test: AirPlay 44.1 kHz / Cast 48 kHz (2025-04-14) ⚠️ tek-kaynak · (39) bluetooth.com — Understanding Reliability in Bluetooth (PDF) · (40) bluetooth.com — 2 ways Bluetooth makes connections reliable (2025-07-01) · (41) bluetooth.com — Bluetooth range & reliability: myth vs fact (2025-10-08) · (42) sony.co.uk — Bluetooth connection drops (mesafe/engel/parazit/pil) · (43) sony.com — Bluetooth sound skips (2.4/5 GHz, öncelik=stabilite) · (44) support.sennheiser-hearing.com — resume'da profil geçişi/gecikme · (45) support.microsoft.com — Bluetooth keeps disconnecting (güç yönetimi) · (46) learn.microsoft.com — BthA2dp IdleTimeout / oto-kopma · (47) kef.com — LS60 FAQ (dropout + Cable Mode fallback) · (48) kef.com — LSX II FAQ (yeniden bağlanma + kablo modu)

> Protokol: [[.claude/skills/prompt-maker/references/10-web-research-protocol.md]] — 5 sorgu bu protokolle çalıştırıldı (resmi/anahtar kaynak önce: bluetooth.com, nist.gov, ietf.org, rfc-editor.org; her ana iddia ≥2 çapraz kaynak; kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`).

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-017 hard-RT yasakları | Kablosuz I/O (BT socket, mDNS multicast, SSDP) **RT yolunda asla**: `callback`'te bloklayıcı ağ/disk I/O yasağı bağlayıcı → tüm kablosuz iş non-RT iş parçacığında, sonuç atomik/lock-free bayrakla iletilir |
| ADR-019 arayüz sınırı | Kablosuz çıkış, `IAudioBackend` çekirdeğini **bypass edemez**; OS/protokol-spesifik kod yalnız adapter altında (çekirdek asla protokol API'sini doğrudan çağırmaz) |
| ADR-011 / ADR-020 hattı | Cihaz yetkilendirmesi oturum/scope modelinden ayrı yazılır ama **aynı reddi mantığına** (deny-by-default, audit) bağlanır; `validateJwtToken` stub `null` döndükçe Bearer ile cihaz erişimi açılmaz (ADR-010 şart 3) |
| Vault SSOT + In-Place Refactoring | Karar `.ai/` altında yaşar; dosya adları onaysız değişmez (k2/k8/k14 spec dosyaları yerinde kalır, refactor = içerik katmanı) |
| REDACTED | Eşleme anahtarı, cihaz LTK/bonding verisi, Wi-Fi PSK, servis anahtarı **hiçbir koşulda** bu ADR'ye veya vault'a yazılmaz (NIST: passkey 20 bit → güvenli saklama şart) |
| Şablon + yazım usulü | Guardrail #16: `.ai/.templates/adr/adr-template.md` iskeleti (§1-§7); tüm vault yazımı yalnız `vault-utf8-writer.mjs`; `log.md` yalnız append |
| Dürüst etiket | Kod 0 olan her madde **PLANNED**; `device-service.md:557-562` "✅" iddiası doğrulanamadı → `⚠️ VERIFICATION REQUIRED` (uydurma durum yazılmaz) |

---

## 2. Karar (Decision)

CoreMusic'te kablosuz cihaz entegrasyonu **dört maddelik tek karar** ile standartlaştırılır (kullanıcı onaylı kapsam) ve **çoklu-protokol, cihaz tipine göre otomatik seçim** ilkesine bağlanır:

- **(a) Cihaz eşleştirme:** Bluetooth hoparlör/kulaklık (BLE + Classic/A2DP) **ve** ağ cihazları (DLNA/UPnP renderer, AirPlay hedefi) **tek bir pairing sözleşmesi** altında eşleştirilir; eşleştirme sonucu cihaz hafızasına yazılır.
- **(b) Bağlantı yönetimi:** **kesinti algılama** (heartbeat/sağlık), **otomatik yeniden bağlanma** (üstel backoff + jitter, maksimum deneme → kullanıcıya bildirim), **cihaz hafızası** (bond + profil + tercih edilen protokol) — üçü tek yaşam döngüsüdur.
- **(c) Keşif:** **mDNS/DNS-SD birincil** keşif yolu (K14.4 spec'i ile aynı: RFC 6762/6763, UDP 5353), **UPnP/DLNA (SSDP, port 1900) ikincil** yoldur; keşif **yalnızca listeler — yetkilendirmez** (RFC 8882 gerekçesi).
- **(d) Güvenlik:** **varsayılan LE Secure Connections + Numeric Comparison** (MITM korumalı); **Just Works yalnız I/O yetmezliğinde** (ekran/tuş yok) ve **yalnızca daha önce yetkilendirilmiş cihazda**; **yetkisiz cihaz reddi** deny-by-default + audit (ADR-011/020 hattı); pairing method-confusion'a karşı tek modele sabitlenme yasak (§1.3 kaynak 25).

**Protokol kararı — çoklu-protokol, otomatik seçim:**

| Protokol yolu | Kapsadığı cihaz | Otomatik seçim ölçütü |
|---------------|-----------------|------------------------|
| **Bluetooth** (BLE keşif + Classic/A2DP veya LE Audio/LC3 akış) | Kulaklık, taşınabilir hoparlör, giyilebilir | Cihaz tipi = kişisel ses (tek-hedef, kısa menzil, pil); RFC/latency profili |
| **Ağ** (mDNS/DNS-SD keşif + UPnP/DLNA veya CoreMusic kendi protokolü) | Ev hoparlörü, renderer, çok-odalı hedef | Cihaz tipi = ağ-üstü (sabit, çoklu-odak); örnek-hızı/bağlantı kalitesi |

**Otomatik seçim kararı üç sinyalle verilir:** **cihaz tipi** (kişisel vs ağ) → **bağlantı kalitesi** (sinyal/parazit/ölçülen paket kaybı — Bluetooth RSSI ve ağ-ölçüm birlikte) → **örnek hızı/hedef** (kaynaktan hedefe uyum; §1.3-kaynak 38: AirPlay 44.1 kHz, Cast 48 kHz dönüşümü → dönüşüm varsayılan değildir, açıkça hesaplanır). Kalite eşiği altındaysa yol değiştirilir; iki yol da yoksa **fallback: kablolu/yerel** (§4.4).

**Mimari:** keşif ≠ yetki ≠ akış — üçü ayrı katmandır. Keşif listeler, güvenlik doğrular/verir, `IAudioBackend` üretir (ADR-019 sınırı). **Tek protokol DEĞİL** (uyumsuz cihaz), **her cihaza tüm protokoller DEĞİL** (gereksiz keşif trafiği + gizlilik maliyeti) — ikisi de §3'te reddedildi.

### 2.1 Neden Bu Seçenek?

Bugün eksik olan kablosuz kütüphane değil **sözleşme**: 5 satırlık wireless şeması, 4 spec dosyası (k2/k8/k14), 4 ui-design dosyası ve 1 PNG var — ama hangisinin keşif, hangisinin yetki, hangisinin akış olduğu; kopan bağlantının ne yaptığı; hangi cihaz tipine hangi protokolün seçildiği **hiçbir yerde yazılı değil**. Web araştırması (§1.3, ~48 kaynak) dört şeyde oybirliği veriyor: her iki Bluetooth dünyası da desteklenmeli, protokol parçalanması gerçek ve tek protokol çözüm değil, eşleştirme varsayılanı güçlü olmalı (NIST), dayanıklılık ayrı bir katman (algılama + yeniden bağlanma + kablolu fallback). ADR-017/019 ses yolunu ve arayüz sınırını kurmuş, ADR-011/020 insan-güvenliğini kurmuştu — **cihaz-güvenliği ve keşif halkası eksikti**; bu ADR onu bağlar. Seçenek, mevcut dosyaları yeniden adlandırmadan, sıfır yeni altyapıyla kurulan bir **sözleşme katmanıdır**; kod PLANNED olarak açıkça etiketlidir.

### 2.2 Teknik Detaylar

**2.2-a Eşleştirme (§2a):**

| Öğe | Karar |
|-----|-------|
| Arayüz | `IWirelessPairing::begin(deviceClass, ioCapability) → PairingSession` (PLANNED — repo'da `*.cpp = 0`) |
| BLE yolu | Keşif → `Pairing Feature Exchange` → **LE Secure Connections** (P-256/ECDH) → association model seçimi → LTK bonding (Core 5.4 Security Manager §2.1-2.3, §1.3 kaynak 24) |
| Classic/A2DP yolu | SSP (Numeric Comparison varsayılan) → link key → A2DP/AVRCP bağlanışı (k2 spec `bluetooth-a2dp.md` state machine ile hizalı) |
| Association model sırası | Numeric Comparison → Passkey Entry → OOB; **Just Works = son çare**, yalnız `displayOnly/none` I/O'lu cihazda, yalnız cihaz hafızasında kayıtlıysa |
| Önyargı yasağı | Tek modele sabitlenmez; her eşlemede I/O capability yeniden değerlendirilir (CVE-2022-25836 — §1.3 kaynak 25) |
| Kalıcılık | bond = `bluetooth_peers` + `bluetooth_audio_profiles` (`coremusic_wireless.sql:59,:118`) + anahtar materyali **OS keyring/credential store**'da (vault'a YAZILMAZ — REDACTED) |
| Ağ cihazı eşleştirilmesi | SSDP/mDNS ile bulunan renderer için **CoreMusic servis token'ı ile doğrulama** (kendi protokolü) veya AirPlay SRP pairing/key-verify (§1.3 kaynak 31) |

**2.2-b Bağlantı yönetimi (§2b):**

| Yaşam olayı | Davranış |
|-------------|----------|
| Bağlandı | Protokol + cihaz tipi + son kalite ölçümü `sync_history`'ye yazılır |
| **Kesinti algılama** | Sağlık yoklaması (uygulama katmanı heartbeat; Bluetooth `connection supervision timeout`; ağ health endpoint) — eşik: ardışık N başarısızlık |
| **Otomatik yeniden bağlanma** | Üstel backoff + jitter (ör. 0.5s → 1s → 2s … maks 30s), maks 5 deneme; her denemede önce **kayıtlı tercihli yol**, o yoksa **ikinci protokol**, o da yoksa **fallback** |
| Yol değişimi | Yeniden bağlanırken §2 seçim sinyalleri yeniden hesaplanır (kalite düşüktüyse aynı yola kör bağlanılmaz) |
| Cihaz hafızası | Bond + profil + tercihli yol + son kalite; kullanıcı "unut" derse bond + kayıt silinir (ADR-011 hijyen ruhu) |
| Sonuç bildirimi | Maks deneme aşıldıysa sessiz değil **durum bildirimi** (ADR-017 "cihaz kaybı → kullanıcıya durum bildirimi UI'da" kuralı ile aynı ruh) |

**2.2-c Keşif (§2c):**

- **mDNS/DNS-SD birincil:** K14.4 spec (`mdns-discovery.md`) ile aynı parametreler — RFC 6762/6763, UDP 5353, `224.0.0.251`/`ff02::fb`, probing/anti-çakışma; servis kaydı `_coremusic._tcp` (PLANNED) + `_airplay._tcp`/`_googlecast._tcp` dinlemesi.
- **SSDP/UPnP ikincil:** port 1900, `M-SEARCH` + `NOTIFY` (k14 `dlna-upnp.md` rolleri: MediaServer/MediaRenderer/ControlPoint).
- **Sınır:** keşif çıktısı **ham aday listesidir**; cihaz kimliği doğrulanmadan bağlanılmaz, keşif kayıtları süreli/turlu tutulur (RFC 8882 gizliliği: hostname/instance-name sızıntısı → §4.3 R2).
- **Kapsam:** link-local dışında reklam/yayın yok (Cisco: TTL=1) — VLAN/genişletilmiş keşif **PLANNED ve kapsam dışı** (§3 alternatif 4 gerekçesi).

**2.2-d Güvenlik (§2d):**

| Kural | Kaynak/İlişki |
|-------|----------------|
| Varsayılan: LE Security Mode 1 Level 4 (authenticated Secure Connections, ECDH) | NIST SP 800-121 r2 — §1.3 kaynak 19-20 |
| MITM korumalı model tercihi: Numeric Comparison / Passkey / OOB; Just Works = son çare | NIST + SIG — kaynak 19, 23, 24 |
| **Yetkisiz cihaz reddi — deny-by-default:** eşleştirilmemiş cihaz akışa/alıcıya alınmaz; bilinen-ama-yetkisiz cihaz açık red + audit kaydı | ADR-020 (scope/audit) + ADR-011 (oturum hijyeni) hattı |
| Cihaz kimliği oturum kimliğinden ayrıdır: cihaz bond'u ≠ kullanıcı oturumu; oturum kapanınca cihaz yetkisi yeniden değerlendirilir | ADR-011 §4.4 / ADR-010 şart 3 ile hizalı |
| Eşleme/anahtar materyali **yalnız OS credential store'da**; vault/log'a yazılmaz (passkey 20 bit → tahmin edilebilir) | REDACTED + NIST — §1.4 kısıt |
| Keşif verisi yetki vermez; spoof'e karşı bağlanmadan önce cihaz kimliği doğrulanır | RFC 8882 + dn.org — kaynak 11, 17 |
| Audit: eşleştirme denemesi (başarı/başarısızlık), red, yol değişimi, oturum düşüşü → `log_security`/`audit_logs` (ADR-020 §G) | ADR-020 |

**2.2-e Protokol seçimi (otomatik):**

```
[Adaylar: mDNS/DNS-SD + SSDP]     [Adaylar: BLE scan + Classic/L2CAP]
        |                                      |
   [KEŞİF — yalnız listeler]            [KEŞİF — yalnız listeler]
        \                                    /
         \_____ [GÜVENLİK — doğrula / reddet] _____/
                          |
              [SEÇİM — cihaz tipi + kalite + örnek hızı]
                |                             |
     Bluetooth yolu (BLE/Classic)     Ağ yolu (mDNS + UPnP/DLNA veya kendi protokolü)
                \                             /
                 \___ [BAĞLANTI YÖNETİMİ — algıla / yeniden bağla / hafıza] ___/
                                   |
                        [IAudioBackend — ADR-019 sınırı]
                                   |
                     [FALLBACK — kablolu / yerel (Null Output)]
```

**2.2-f Katman ve RT kuralı:** keşif/güvenlik/bağlantı yönetimi **non-RT** iş parçacığında; ADR-019 çekirdeği yalnız `IAudioBackend` görür; kesinti bayrağı `std::atomic` ile iletilir (ADR-017 lock-free kuralı). **Sahiplik:** keşif + protokol + pairing = Embedded Engineer; güvenlik politikası (association model varsayılanı, red/audit) = Security Engineer; arayüz/UI = UI Designer; veri (`coremusic_wireless`) = Data Engineer (`.ai/AGENTS.md` §5).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Tek protokol** — yalnız Bluetooth (veya yalnız ağ/mDNS) | En az kod, tek entegrasyon, hızlı başlangıç | Ev renderer/AirPlay hedefleri kapsam dışı (DLNA cihazı Bluetooth'la ses vermez); LE Audio vs Classic kurulu tabanı bölünür; ağ çok-odalı hedefler (kendi NevaConnect hedefi) elenir | §1.3 kaynak 1-10 + 29-38: iki taraf da yaygın; "tek protokol yeter" diyen kaynak yok → cihaz uyumsuzluğu riski |
| 2 | **Her cihaza tüm protokoller** — tüm adayları her taramada mDNS + SSDP + BLE + AirPlay ile yokla | Maksimum uyumluluk, keşif boşluğu kalmaz | Keşif trafiği şişer; RFC 8882 gizlilik maliyeti (hostname/instance sızıntısı her turda tekrar); pilli cihazlarda BLE taraması enerji maliyeti; sahte cihaz yüzeyi büyür (spoof alanı) | §1.3 kaynak 11-18: keşif kimlik sızdırır → "daha fazla keşif" çözüm değil; gereksiz yüzey = R2 riski |
| 3 | **Hazır kütüphaneye tam teslimiyet + içindeki varsayılan eşleştirme** | Sıfır başlangıç maliyeti, hazır keşif/pairing | Varsayılan association modeli çoğu kütüphanede **Just Works**'e düşer (NIST: MITM'siz); yetkisiz cihaz reddi ve audit kütüphane içine gömülmez; protokol seçimini (örnek hızı dahil) kütüphane bilmez | §1.3 kaynak 19-25: varsayılan güçlü olmalı → kararın (d) maddesi kütüphane varsayılanına emanet edilemez; araç = adapter içi, sözleşme = bu ADR (ADR-019 ruhu) |
| 4 | **Çoklu-protokol + otomatik seçim + keşif/yetki ayrımı + kablolu fallback (SEÇİLEN)** | Her iki cihaz dünyası da kapsanır; güvenlik varsayılanı güçlü; kopma yönetilir; mevcut spec/şema yerinde kalır | Katman disiplini (keşif ≠ yetki ≠ akış); seçim mantığı + ölçüm maliyeti; PLANNED kod miktarı büyük | ✓ Seçildi — §2 (a)-(d) + protokol tablosu |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Kapsam tek kararda toplanır:** eşleştirme + bağlantı + keşif + güvenlik ayrı ADR'lere dağılmaz; "neden böyle?" tek dosyada cevaplanır (§1.2 altı sorunun karşılığı).
- **Cihaz uyumluluğu:** Bluetooth (BLE + Classic/A2DP) **ve** ağ (mDNS/UPnP-DLNA) yolları birlikte → kulaklık da, ev renderer'ı da bağlanır; otomatik seçim kullanıcıya protokol bilgisi yükünü bindirmez.
- **Kesinti dayanıklılığı:** algılama + backoff + yol değişimi + kablolu fallback ile kopma "yeniden bağlan"la kalmaz (§1.3 kaynak 39-48).
- **Güvenlik varsayılanı güçlü:** LE Secure Connections + Numeric Comparison varsayılan, Just Works sınırlı, yetkisiz cihaz red + audit → ADR-011/020 hattı cihaz düzeyine taşınır.
- **Mevcut varlıklar boşuna durmaz:** `coremusic_wireless` (5 tablo), k2/k8/k14 spec'leri ve 4 ui-design dosyası **sözleşmeye bağlanır** — hepsi yerinde kalır (In-Place Refactoring).

### 4.2 Olumsuz Sonuçlar

- **Kod sıfırdan:** `*.cpp/*.h = 0`, `*.ts = 0` → tüm §2 bileşenleri PLANNED; ilk implementasyon ciddi Embedded işi (aylar ölçeği).
- **Katman disiplini yükü:** her özellik "keşif mi, güvenlik mi, akış mı?" sorusu ister; etiketsiz eklenen kod RT yoluna veya keşif içine güvenlik gömer.
- **Ölçüm maliyeti:** otomatik seçim kalite/örnek-hızı ölçümü ister — ölçüm yapılmazsa seçim kör çalışır (yanlış yol = yanlış kalite).
- **Spec-kod çelişkisi geçici:** `device-service.md:557-562` "✅" derken kod 0 → kısa süre iki gerçeklik yan yana durur (R5).
- **İki bağımsız hedef:** AGENTS §19 `v23.0 Cross-Project Memory (WirelessConnect)` ayrı bir yol haritası işidir — bu ADR kapsamına girmez, bağ kuruştur.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon | Fallback |
|------|---------|------|------------|----------|
| R1 — **Eşleştirme açığı** (MITM / method confusion / zayıf model) | Orta (3) | Yüksek (4) | Varsayılan Secure Connections + Numeric Comparison; Just Works yalnız I/O-yoksuz + kayıtlı cihaz; tek modele sabitlenmez (CVE-2022-25836); red + audit | Şüpheli eşleştirme → bond sil + cihaz hafızasından çıkar, kullanıcı yeniden onaylar; yalnız kablolu/yerel modda devam |
| R2 — **Keşif gizliliği** (hostname/instance/fingerprint sızıntısı, spoof) | Orta (3) | Orta (3) | Keşif yalnız listeler (yetkilendirme ayrı); kayıt süreli/turlu; link-local kapsam (TTL=1); bağlanmadan önce kimlik doğrulama | Keşif kapatılıp **kayıtlı cihaz listesi** elle yenilenir (bilinen cihazlara doğrudan bağlanma) |
| R3 — **Kesinti / kararlılık** (parazit, pil, profil geçişi, sürücü, Wi-Fi çakışması) | Yüksek (4) | Orta (3) | N ardışık sağlık yoklaması → üstel backoff+jitter (maks 30s, 5 deneme) → kaliteye göre yol değişimi; kullanıcıya durum bildirimi | **Kablolu/yerel fallback** (ağ kablolu, USB/lokal çıktı veya Null Output — ADR-017 zinciri) |
| R4 — **Protokol parçalanma** (her cihaz farklı yol ister, seçim yanlış) | Orta (3) | Orta (3) | Çoklu-protokol + üç sinyalli otomatik seçim (cihaz tipi · kalite · örnek hızı); seçilen yol `sync_history`'de kayıtlı, elle override mümkün | Manuel yol seçimi UI (`DevicePairing` akışı); iki yol da yoksa kablolu/yerel |
| R5 — **Spec-kod uçurumu** (durum iddiası kanıtsız, "✅ Tamamlandı" yanlışı) | Yüksek (4) | Düşük (2) | Her PLANNED madde açık etiketli (§1.1-A/B); `device-service.md` durum satırı kod varlığıyla çapraz-kontrol edilir (§5.1 adım 10) | Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` ile işaretli kalır; ADR-005 standardı uygulanır |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Sözleşme başlıkları:** `IWirelessPairing`, `IDiscoveryService`, `IConnectionManager` + `enum PairingModel`/`enum WirelessPath`/`enum FallbackPolicy` (kod 0 → iskeleti bu ADR'den üret) | Embedded Engineer | 3 gün |
| 2 | **Eşleştirme akışı:** BLE Secure Connections + Classic SSP; association model sırası (Numeric Comparison → Passkey → OOB → Just Works-sınırı); bonding → OS credential store (vault'a yasak) | Embedded Engineer + Security Engineer | 5 gün |
| 3 | **Yetkisiz cihaz reddi + audit:** deny-by-default, red/seçim/eşleştirme olayları `log_security`/`audit_logs` (ADR-020 §G); `validateJwtToken` stub `null` süresince Bearer ile cihaz erişimi kapalı | Security Engineer | 3 gün |
| 4 | **Keşif servisi:** mDNS/DNS-SD (K14.4 parametreleri) + SSDP; çıktısı ham aday listesi; kayıt süreli; link-local kapsam | Embedded Engineer | 4 gün |
| 5 | **Bağlantı yönetimi:** sağlık yoklaması, üstel backoff+jitter (maks 30s/5 deneme), kaliteye göre yol değişimi, `sync_history`/`bluetooth_peers` yazımı (`coremusic_wireless.sql`), durum bildirimi | Embedded Engineer + Data Engineer | 5 gün |
| 6 | **Protokol seçimi:** üç sinyal (cihaz tipi · bağlantı kalitesi · örnek hızı) + manuel override; seçim karar logu | Embedded Engineer | 3 gün |
| 7 | **UI akışı:** `DevicePairing` + pairing modal + quick panel (`footer.php:133` butonuna gerçek handler) — ui-design spec'leri (Kalıp D / `02-bluetooth-connect.md`, `T08-embedded/bluetooth-modal.md`) okunarak yazılır (Guardrail #16 + Mockup Gate) | UI Designer | 4 gün |
| 8 | **Fallback zinciri:** kablolu/yerel → Null Output; kesinti bayrağı `std::atomic` (ADR-017), çekirdek yalnız `IAudioBackend` görür (ADR-019) | Embedded Engineer | 2 gün |
| 9 | **§1.3 tek-kaynak iddiaları kapatılır:** AirPlay 44.1 kHz dönüşümü (ikinci ölçüm) + Avahi CVE kapsamı (ikinci bülten) → `⚠️ VERIFICATION REQUIRED` kaldırılır veya madde yeniden yazılır | Research + Security Engineer | 1 gün |
| 10 | **Durum iddiası çapraz-kontrolü:** `device-service.md:555-562` satırları gerçek kod varlığıyla eşleştirilir (✅/🔄 → PLANNED olarak düzeltilir) | Master Orchestrator (vault-updater) | 0,5 gün |
| 11 | **Index mutabakatı:** `.decisions/index.md:28` "Frozen 001-037" satırı ile bu dosyanın `accepted + frozen YOK` durumu ve `:74`/`index.md:654` kategori farkı (Audio vs Integration) tek satırda uzlaştırılır | Vault Steward | 0,5 gün |
| 12 | **Kırık link onarımı:** `.ai/projects/WirelessConnect/...` (dizin YOK) ve `keys.md:377/:477` (`04-connectivity-layouts.md`, `F-quickpanel/` — diskte yok) işaretlenir | Master Orchestrator | 0,5 gün |
| 13 | **Debate Şart 1 — Sözleşme testi:** pairing model sırası + red durumu + fallback zinciri için sözleşme testi iskeleti; `IAudioBackend` katman denetimi (çekirdek `#ifdef`/protokol başlığı = 0) | QA Engineer + Embedded Engineer | 2 gün |
| 14 | **Debate Şart 2 — Kopma provası:** sahte kesinti enjeksiyonu → backoff + yol değişimi + kablolu fallback davranışı doğrulanır | QA Engineer | 2 gün |
| 15 | **Debate Şart 3 — Güvenlik provası:** yetkisiz cihaz red + spoof/keşif kaydı denetimi + Just Works'ün yalnız izinli senaryoda devreye girişi | Security Engineer | 2 gün |

### 5.2 Geri Dönüş Planı

1. **Adım 1-2 (sözleşme + pairing) işe yaramazsa:** `IWirelessPairing`/`IDiscoveryService` başlıkları arşive alınır, çekirdek `IAudioBackend` **dokunulmadığı** için geri dönüş tek commit (dosya adı değişmedi → In-Place Refactoring korunur); `log.md`'ye tek revert satırı.
2. **Adım 4 (keşif) riskliyse (gizlilik/spoof):** keşif kapatılır, sistem **yalnızca cihaz hafızasındaki kayıtlı cihazlara doğrudan bağlanır** (R2 fallback'i fiilen devreye girer); eşleştirme + güvenlik adımları aynen kalır.
3. **Adım 6 (otomatik seçim) güvenilmez ölçüm veriyorsa:** seçim elle override'a düşürülür (varsayılan = kullanıcı son seçtiği yol), otomatik mod `⚠️ VERIFICATION REQUIRED` ile işaretlenir → ADR-035/036 ruhuyla ölçüm düzelene kadar kapalı.
4. **Adım 9-11 (kanıt/iddia mutabakatı) yapılmazsa:** ADR `accepted` kalır ama **Frozen'a geçmez** (§4.5 kontrolü 9-11 maddelerini ister); eksikler tamamlanmadan `frozen` satırı yazılmaz.
5. **Tam geri alma:** bu ADR `status: rejected`/`superseded by ADR-NNN` ile kapatılır (metin silinmez); wireless şeması, spec ve ui-design dosyaları **değişmeden** durur (zaten bu karardan önce de vardı) → bilgi kaybı sıfır.

---

### 5.3 Debate Şartları (bağlayıcı — 2026-09-26 · §7.1)

| Şart | Bağlayıcı madde | Eşleşen adım | Durum |
|------|-----------------|--------------|-------|
| **1a** | [[.ai/architecture/k8-servis/device-service.md]] `:555-562` DLNA ✅ / mDNS ✅ / Bluetooth 🔄 iddiaları kod 0 ile çelişir → claim-code düzeltmesi: `⚠️ PLANNED (kod yok — ADR-037 şart 1a)` notu eklenir; ✅ işaretleri korunur, iddia silinmez | §5.1 adım 10 | ✅ UYGULANDI (2026-09-26) |
| **1b** | [[.ai/.decisions/index.md]] `:28` "Frozen 37 (ADR-001 → ADR-037)" ↔ bu dosyanın `frozen YOK` durumu → aralık `ADR-001 → ADR-036` + sayı **36** | §5.1 adım 11 | ✅ UYGULANDI (2026-09-26) |
| **2** | Kod 0 → **eşleştirme + reconnect + keşif test paketi** | §5.1 adım 13-15 | ⏳ PLANNED |
| **3** | §1.3 2 tek-kaynak iddia → `⚠️ VERIFICATION REQUIRED` korunur + **ikinci kaynak aranır** (AirPlay 44.1 kHz dönüşümü · Avahi CVE) | §5.1 adım 9 | ⏳ PLANNED |

> Numaralandırma notu: bağlayıcı şart numarası bu tablodur (1a · 1b · 2 · 3). §5.1 adım 13-15'teki "Debate Şart 1/2/3" etiketleri mevcut metin olarak korunmuştur ve şart 2'nin (test paketi) alt maddeleridir; mevcut metin değiştirilmemiştir. Şart 2 ve 3 kapanmadan bu ADR **frozen yapılmaz** (§7 3. satır zaten `⏳`).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md]] | Ses katmanı + **hard-RT yasakları** — kablosuz I/O non-RT kuralının kaynağı (§1.4, §2.2-f) |
| [[.ai/.decisions/accepted/ADR-019-per-os-neva-player.md]] | `IAudioBackend` arayüzü + fallback zinciri — kablosuz akışın bağlanacağı sınır (§1.1-D, §2.2-e) |
| [[.ai/.decisions/accepted/ADR-011-session-management.md]] | Oturum yaşam döngüsü — cihaz hafızası ve cihaz-≠-oturum kuralı (§2.2-d) |
| [[.ai/.decisions/accepted/ADR-020-api-public-security.md]] | Scope/audit/red hattı — yetkisiz cihaz reddi ile hizalı (§2.2-d, §5.1 adım 3) |
| [[.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md]] | `coremusic_wireless` 18. BCNF veritabanı (`:120` "Donanım-sınırı veri") |
| [[.ai/.decisions/accepted/ADR-005-ultrathink-protocol.md]] | `⚠️ VERIFICATION REQUIRED` standardı — kod 0 / tek-kaynak iddia disiplini (§1.1, §4.3 R5) |
| [[.ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md]] | Şart 3 kilidi — `validateJwtToken` stub'ı ile cihaz erişim kapısı (§1.4, §5.1 adım 3) |
| [[.ai/.decisions/accepted/ADR-036-multi-project-prompt-maker.md]] | Şablon/format referansı + §1.3 protokol hattı (bu dosyanın biçimsel kaynağı) |
| [[.ai/architecture/k2-surucu/bluetooth-a2dp.md]] | A2DP/LDAC/aptX/LC3 spec (8420 b) — §2a Bluetooth yolu |
| [[.ai/architecture/k2-surucu/network-audio-drivers.md]] | Ağ ses sürücüleri — kablolu/ağ sınırı (fallback hattı) |
| [[.ai/architecture/k8-servis/device-service.md]] | Keşif endpoint'leri + "✅/🔄" durum iddiası (`:48,:49,:555-562`) — §1.1-B, R5 |
| [[.ai/architecture/k8-servis/network-service.md]] | `mDNSResponder` kaydı (`:253-254,:316`) |
| [[.ai/architecture/k8-servis/README.md]] | K8.4 cihaz servisi kapsamı (`:40,:205,:254`) |
| [[.ai/architecture/k14-ag/mdns-discovery.md]] | **mDNS/DNS-SD birincil keşif** — RFC 6762/6763, 5353 (§2c) |
| [[.ai/architecture/k14-ag/dlna-upnp.md]] | **UPnP/DLNA ikincil keşif** — SSDP 1900, roller (§2c) |
| [[.ai/architecture/k14-ag/airplay-streaming.md]] | AirPlay yolu — mDNS + pairing/key exchange (`:32,:44`) |
| [[.ai/architecture/k14-ag/README.md]] | K14.4 alt-alan envanteri (`:148`) |
| [[.ai/architecture/k5-veri-yonetimi/README.md]] | K5.1.11 `coremusic_wireless` 5 tablo kataloğu (`:45,:287-291`) |
| [[.ai/architecture/k10-uygulama/home-panel.md]] | `DevicePairing.tsx` + QR/Bluetooth eşleştirme (`:69,:177-178,:204`) — PLANNED |
| [[.ai/architecture/k3-ses-motoru/neva-engine-core.md]] | Ses çekirdeği — kablosuz katmanın **dışında** (RT sınırı) |
| [[.ai/.sql/mysql/coremusic_wireless.sql]] | 10107 b / 5 tablo — cihaz hafızası şeması (IMPLEMENTED) |
| [[.ai/ui-design/flow/settings/02-bluetooth-connect.md]] | Pairing Flow akış spec'i (10451 b) |
| [[.ai/PROJECTS.md]] | WirelessConnect envanteri `:191-198` (C++20, BLE 5.0, WiFi Direct, mDNS, DLNA/UPnP — PLANLANMIŞ) |
| [[.ai/AGENTS.md]] | §19 `v23.0 Cross-Project Memory (WirelessConnect)` yol haritası (`:445`) |
| [[.ai/CLAUDE.md]] · [[.ai/WORKFLOW.md]] | Ana sözleşme + süreç (frozen ayrımı, guardrail'lar) |
| [[.ai/.decisions/index.md]] | ADR-037 slotu (`:74`) + "Frozen 001-037" mutabakatı (`:28` → §5.1 adım 11) |
| [[.ai/index.md]] · [[.ai/keys.md]] · [[.ai/brain.md]] | Katalog kayıtları (`:654` / `:272` / `:992`) |
| [[.ai/.templates/adr/adr-index.md]] | Şablon indeksi `:108` (durum işareti) |
| [[.ai/.templates/adr/adr-template.md]] | Bu dosyanın zorunlu iskeleti (Guardrail #16) |
| [[.claude/skills/prompt-maker/references/10-web-research-protocol.md]] | §1.3 araştırma protokolü (5 sorgu) |
| Debate Şart 1 — sözleşme testi (§5.1/13) | Pairing sırası + red + fallback + katman denetimi |
| Debate Şart 2 — kopma provası (§5.1/14) | Backoff + yol değişimi + kablolu fallback doğrulaması |
| Debate Şart 3 — güvenlik provası (§5.1/15) | Yetkisiz red + spoof/keşif denetimi + Just Works sınırı |
| Debate Şart 1a — spec claim-code düzeltmesi (§5.3 · §5.1 adım 10) | [[.ai/architecture/k8-servis/device-service.md]] `:555-562` → ✅ UYGULANDI (2026-09-26) |
| Debate Şart 1b — index frozen aralığı (§5.3 · §5.1 adım 11) | [[.ai/.decisions/index.md]] `:28` → ✅ UYGULANDI (2026-09-26) |
| Debate Şart 2 — test paketi (§5.3 · §5.1 adım 13-15) | Eşleştirme + reconnect + keşif test paketi (§6'daki üç "Debate Şart" satırı) → ⏳ PLANNED |
| Debate Şart 3 — V.R. ikinci kaynak (§5.3 · §5.1 adım 9) | AirPlay 44.1 kHz dönüşümü · Avahi CVE → ikinci kaynak aranır → ⏳ PLANNED |
| [[.ai/log.md]] | Audit trail — bu ADR'nin yazım + debate kayıtları |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-26 | ✅ (kullanıcı onaylı karar kapsamı — (a) cihaz eşleştirme · (b) bağlantı yönetimi · (c) keşif · (d) güvenlik + çoklu-protokol/otomatik seçim + sonuçlar/riskler/fallback) |
| Tech Lead | — | 2026-09-26 | ✅ (debate 3 tur / 20 persona → 18/2/0 KABUL — §7.1) |
| Arch Lead | — | — | ⏳ (Tech Lead sonrası) |

**Statü özeti:** debate `✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` · Tech Lead `✅` (2026-09-26) · Arch Lead `⏳` → **frozen YOK** (üç onay satırı da ✅ olmadan `frozen` yapılmaz; `.decisions/index.md:28` "Frozen" aralığı ve sayısı şart 1b ile `ADR-001 → ADR-036` + **36** olarak düzeltildi, 2026-09-26). `status: accepted` = karar metni SSOT olarak yayında.

### 7.1 Debate Kaydı

**Durum: ✅ TAMAMLANDI — 3 tur / 20 persona → 18 kabul / 2 çekimser / 0 red = KABUL (2026-09-26) · Tech Lead ✅ · Arch Lead ⏳ · frozen YOK**

| Tur | Tip | Kayıt |
|-----|-----|-------|
| 1 | 20 persona keşif turu (~48 adlandırılmış kaynak / 5 sorgu, 2026-09-26) | **Kod 0:** `*.cpp/*.h/*.hpp` = 0 dosya, `*.ts` = 0; `home.coremusic.net/footer.php:133` `openBluetoothPopup` = **ölü handler** (repoda tek eşleşme, JS handler yok → tıklama sahipsiz); `ServiceRegistry::discover()` = süreç-içi servis araması (mDNS/keşif DEĞİL). **Spec çelişkisi R5:** `k8-servis/device-service.md:555-562` DLNA ✅ / mDNS ✅ / Bluetooth 🔄 ↔ kod 0. **Şema IMPLEMENTED:** `coremusic_wireless.sql` 5 tablo (okuyan/yazan kod yok); k3/k0/k4'te kablosuz doküman YOK. **2 tek-kaynak iddia → `⚠️ VERIFICATION REQUIRED`** (AirPlay 44.1 kHz dönüşümü · Avahi CVE). Oy eğilimi: 15 kabul/neutral + 4 uyarı (DevOps: spec düzeltmesi şart · QA: test paketi · Critic: `index.md:28` "Frozen 37" + V.R. şart) |
| 2 | İtiraz → çözüm | (1) spec ✅ ↔ kod 0 → `device-service.md:555-562` claim-code düzeltmesi (**PLANNED** işareti, ✅ korunur) → **şart 1a**; (2) `index.md:28` "Frozen 37 (ADR-001 → ADR-037)" ↔ bu dosyanın `frozen YOK` durumu çelişkisi → aralık/sayı düzeltmesi → **şart 1b**; (3) kod 0 → eşleştirme + reconnect + keşif **test paketi** → **şart 2**; (4) 2 tek-kaynak iddia → V.R. korunur + **ikinci kaynak aranır** → **şart 3** |
| 3 | Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |

**Bağlayıcı 3 şart (detay §5.3):** **1a** spec `:555-562` PLANNED işareti → ✅ uygulandı (2026-09-26) · **1b** `index.md:28` frozen aralığı/sayısı → ✅ uygulandı (2026-09-26) · **2** test paketi (§5.1 adım 13-15) → ⏳ PLANNED · **3** V.R. ikinci kaynak (§5.1 adım 9) → ⏳ PLANNED.

**Onay etkisi:** Tech Lead `⏳ → ✅` (2026-09-26); Arch Lead `⏳` → §7 3. satır ✅ olmadan **frozen YOK**. Kayıt: `.ai/log.md` append (2026-09-26).

---

**1.0.0 | 2026-09-26 | Created**
*Authority: ADR-037 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
*ADR-037 debate | 2026-09-26 | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead ✅ · 3 şart (§5.3 — 1a/1b uygulandı, 2-3 PLANNED) · frozen YOK (Arch Lead ⏳)*
