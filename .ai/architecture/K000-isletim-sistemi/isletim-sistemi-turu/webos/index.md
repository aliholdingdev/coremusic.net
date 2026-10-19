---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/webos
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu
children:
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-multimedya-cati
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-ses-ve-rota
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-media-servisi
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-uygulama-gelistirme
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-gercek-zamanli-tampon
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-web-arastirma
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-kod-seti
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-dma-ve-interrupt
  - K000-isletim-sistemi/isletim-sistemi-turu/webos/webos-guvenlik
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# webOS — Platform Derin-Dal Index (Kademe-3)

**Kapsam:** webOS derin-dalı (`K000-isletim-sistemi/isletim-sistemi-turu/webos`) — LG
platformunun ses yüzeyi: multimedya çatısı (GStreamer/PulseAudio katmanları), ses ve rota
yönetimi, Luna Service tabanlı media servisi, webOS TV uygulama geliştirme ortamı (web app ·
appinfo.json · imzalama · developer mode), gerçek-zamanlı tampon disiplini ve resmi kaynak
taraması. `ios/`, `macos/`, `linux/`, `windows/`, `android/`, `reactos/`, `tizen/`
derin-dallarından AYRIDIR (parent `isletim-sistemi-turu/index.md` üzerinden ayrışır).

**Yöntem (RED TEAM / TRUTH MODE):** Doğrulama turu 2026-10-13 — tüm URL'ler bu ortamdan
`Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` ile ölçülmüştür
(HTTP 200 zorunlu) ve envanterle (ölçüm öncesi 1441 benzersiz URL) kesişimi 0'dır.

**İki kritik ölçüm bulgusu (bu dalın truth-mode kaydıdır):**

1. Görev tanımındaki `webostv.developer.lg.com` alanı bu ortamdan **çözümlenememiştir**
   (DNSNXDOMAIN — Google DoH Status 3). Çalışan resmi alan **`webostv.developer.lge.com`**
  'dur (HTTP 200). İddialar `lge.com` üzerinden kurulur; `lg.com` yazımı hatalı tespit edilir.
2. `pulseaudio.org` alanı HEAD ve GET sorgusunda **HTTP 418** (bot duvarı) döndürmüştür — 200
   kanıtı üretilemez. Bu nedenle PulseAudio'ya ilişkin iddialar `webosose.org` belgelerinden
   ve `gstreamer.freedesktop.org` sayfalarından dayandırılır; `pulseaudio.org` doğrulama
   kaynağı olarak kullanılmaz.

**Kod kanıtı politikası:** Bu depoda `.cpp/.h` dosyası yoktur (L0 durumu: PLANNED).
`Dosya: … / Satır: …` referansları yalnız gerçek PHP/YAML dosyalarından üretilir ve
`Select-Object -Index (N-1)` ile doğrulanır; uydurma satır numarası yasaktır.

## Dizin

| Dosya | node_id sonu | Konu | group |
|---|---|---|---|
| `webos-multimedya-cati.md` | `…/webos/webos-multimedya-cati` | GStreamer · PulseAudio katmanı · speech enhancement · architecture overview | mimari |
| `webos-ses-ve-rota.md` | `…/webos/webos-ses-ve-rota` | audio API · output route · media database · DRM · device information | mimari |
| `webos-media-servisi.md` | `…/webos/webos-media-servisi` | Luna Service (LS2) · system service · settings · activity manager · luna-send | mimari |
| `webos-uygulama-gelistirme.md` | `…/webos/webos-uygulama-gelistirme` | web app types · appinfo.json · developer mode · app lifecycle · CLI/IDE | mimari |
| `webos-gercek-zamanli-tampon.md` | `…/webos/webos-gercek-zamanli-tampon` | callback deadline · ring buffer · underrun · LS2 asenkron mesajlaşma | kod |
| `webos-web-arastirma.md` | `…/webos/webos-web-arastirma` | ≥8 resmi URL tarama kaydı (HTTP 200 · 2026-10-13) + NXDOMAIN/418 bulguları | kanit |
| `webos-kod-seti.md` | `…/webos/webos-kod-seti` | Portable dil seti + IPK/appinfo.json/Luna yüzeyi (LS2 imzaları YOK) · **2026-10-14 eklendi** | kod |
| `webos-dma-ve-interrupt.md` | `…/webos/webos-dma-ve-interrupt` | LS2 media/audio servis katmanı · DMA belge boşluğu ⚠️ · Linux bağımlılığı · **2026-10-14 eklendi** | mimari |

Toplam: 8 içerik dosyası · hedef ≥500 satır/dosya (`webos-kod-seti` 561 ile ≥500) · bu indeks 40–100 satır sınırındadır.

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | webos-multimedya-cati.md | Multimedya yığını topolojisi, GStreamer ve PulseAudio katman ilişkisi, speech enhancement akışı | 3 |
| mimari | webos-ses-ve-rota.md | Ses API yüzeyi, çıktı rota seçimi, medya veritabanı, DRM ve cihaz bilgisi servisleri | 3 |
| mimari | webos-media-servisi.md | Luna Service (LS2) mimarisi, sistem ve ayar servisleri, activity manager, `luna-send` konsolu | 3 |
| mimari | webos-uygulama-gelistirme.md | webOS TV web uygulama tipleri, `appinfo.json`, developer mode, uygulama yaşam döngüsü, CLI/IDE | 3 |
| kod | webos-gercek-zamanli-tampon.md | Callback deadline hesabı, lock-free ring buffer, underrun tespiti, LS2 asenkron mesaj disiplini | 3 |
| kanit | webos-web-arastirma.md | Resmi URL tarama kaydı (HTTP 200 · 2026-10-13) + `webostv.developer.lg.com` NXDOMAIN ve `pulseaudio.org` 418 bulguları | 3 |
| mimari | webos-dma-ve-interrupt.md | LS2 servis katmanının belgelenmiş ses yüzeyi, DMA/kesme belge boşluğunun dürüst kaydı (2026-10-14) | 3 |
| mimari | webos-guvenlik.md | webOS güvenlik profili: Luna Service Bus (LS2), Access Control Groups, Trust Level (dev/part/oem), `appinfo.json` izinleri + CoreMusic repo izi (DOSYA:SATIR) · **2026-10-15 eklendi** | 3 |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode