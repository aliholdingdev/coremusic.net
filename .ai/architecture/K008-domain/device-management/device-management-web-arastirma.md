---
node_id: "K008-device-management-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K008-domain"
children: []
depends_on:
  - "K008-domain/web-arastirma"
evidence:
  - "9 URL — ağ cihazı keşfi ve cihaz interop standartları (2026-10-15 tarama, hepsi HTTP 200)"
  - "k008-child-urls-ok.txt — havuz satır 24, 34, 58, 64, 69, 70, 71, 72, 78"
validation: "2026-10-15 — 9 benzersiz URL; K008 geneli çakışma 0 (C4)"
open_questions:
  - "DLNA/UPnP MediaRenderer keşfi diskte YOK — bu URL'ler hedef mimari referansıdır"
group: kanit
---

# device-management — Web Araştırması (K008)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-15
> 9 URL **yalnız bu dosyada** (C4). Doğrulama: **2026-10-15 (HTTP GET, hepsi 200)**.
> URL'ler havuzdan byte-exact kopyalanmıştır.

## §1 YENİ TARAMA (W-1…W-9 · 2026-10-15)

| # | URL | Ne için (bu dosyanın iddiası) | Durum |
|---|-----|-------------------------------|-------|
| W-1 | https://dns-sd.org | DNS-SD/Bonjour — ağ cihazı servis keşfi modeli | HTTP 200 |
| W-2 | https://en.wikipedia.org/wiki/digital_living_network_alliance | DLNA — ev içi cihaz interop çerçevesi | HTTP 200 |
| W-3 | https://en.wikipedia.org/wiki/universal_plug_and_play | UPnP — cihaz keşfi/kontrol temel protokolü | HTTP 200 |
| W-4 | https://learn.microsoft.com/en-us/openspecs/windows_protocols/ms-dlnhnd/d40517ad-2d0a-4397-a46b-db7ba281a837 | MS-DLNHND — Microsoft DLNA Handshake protokol spesifikasyonu | HTTP 200 |
| W-5 | https://openconnectivity.org/developer/specifications/upnp-resources/upnp/mediaserver4-and-mediarenderer3 | OCF — MediaServer4/MediaRenderer3 UPnP AV cihaz sınıfları | HTTP 200 |
| W-6 | https://upnp.org/specs/arch/upnp-arch-devicearchitecture-v2.0.pdf | UPnP Device Architecture v2.0 (PDF) — keşif/annonce/temsil modeli | HTTP 200 |
| W-7 | https://upnp.org/specs/av/upnp-av-avarchitecture-v2.pdf | UPnP AV Architecture v2 (PDF) — medya cihaz topolojisi | HTTP 200 |
| W-8 | https://upnp.org/specs/av/upnp-av-mediarenderer-v3-device.pdf | MediaRenderer v3 cihaz spesifikasyonu (PDF) — oynatma hedefi standardı | HTTP 200 |
| W-9 | https://www.cta.tech/standards/dlna-home-networked-device-interoperability-architecture-and-protocols | CTA — DLNA Home Networked Device Interoperability standard sayfası | HTTP 200 |

## §2 DAĞITIM (C4 KAYDI)

| Alıcı dosya | Adet | Havuz satırları |
|---|---|---|
| bu dosya (device-management) | 9 | 24, 34, 58, 64, 69, 70, 71, 72, 78 |

- K008 genelinde aynı URL iki dosyada **bulunmaz** (C4) — 2026-10-15 sayım: 0 çakışma.
- Kardeş `device-management-dogrulama` bu URL'lere **pointer ile** geçer (`§1 W-x`).

## §3 KAPSAM DIŞI

| Konu | Nereye gitti |
|---|---|
| Subdomain/host/port eşlemesi | [[context-mapping-web-arastirma]] |
| Çalma oturumu + kuyruk protokolleri | [[playback-web-arastirma]] |
| Medya transcode (DLNA uyumlu variant) | [[media-web-arastirma]] |
| UI cihaz matrisi/tier'lar | ui-design vault (K008 dışı) |

**Kardeş dosyalar:** [[device-management-kapsam]] · [[device-management-dogrulama]] · [[device-management-ornekler]] · [[device-management-bilesenler]] · [[index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15
