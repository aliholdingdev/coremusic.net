---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/tizen
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu
children:
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-multimedya-altyapisi
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-ses-yoneticisi
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-audio-hal
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-uygulama-ortami
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-gercek-zamanli-tampon
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-web-arastirma
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-kod-seti
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-dma-ve-interrupt
  - K000-isletim-sistemi/isletim-sistemi-turu/tizen/tizen-guvenlik
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# Tizen — Platform Derin-Dal Index (Kademe-3)

**Kapsam:** Tizen derin-dalı (`K000-isletim-sistemi/isletim-sistemi-turu/tizen`) — Samsung
platformunun ses yüzeyi: GStreamer multimedya altyapısı, Sound Manager katmanı, ALSA/UCM tabanlı
audio HAL, TPK paketleme + imzalama ortamı, gerçek-zamanlı tampon disiplini ve resmi kaynak
taraması. `ios/`, `macos/`, `linux/`, `windows/`, `android/`, `reactos/` derin-dallarından AYRIDIR
(parent `isletim-sistemi-turu/index.md` üzerinden ayrışır).

**Yöntem (RED TEAM / TRUTH MODE):** Doğrulama turu 2026-10-13 — tüm URL'ler bu ortamdan
`Invoke-WebRequest -Method Head` ile ölçülmüştür (HTTP 200 zorunlu) ve envanterle
(ölçüm öncesi 1441 benzersiz URL) kesişimi 0'dır. **Kritik ölçüm bulgusu:** `docs.tizen.org`
altındaki derin yollar bu ortamdan **SPA iskeleti** döndürür (HTTP 200, aynı ~105 KB kabuk gövde,
API içeriği yok) — bu nedenle Tizen'e özgü CAPI imzaları bu dalda `⚠️ VERIFICATION REQUIRED`
ile işaretlenir; doğrulanabilen içerik `gstreamer.freedesktop.org` ve `alsa-project.org`
belgelerinden çekilmiştir. Bilinmeyen değer `UNKNOWN` yazılır.

**Kod kanıtı politikası:** Bu depoda `.cpp/.h` dosyası yoktur (L0 durumu: PLANNED).
`Dosya: … / Satır: …` referansları yalnız gerçek PHP/YAML dosyalarından üretilir ve
`Select-Object -Index (N-1)` ile doğrulanır; uydurma satır numarası yasaktır.

## Dizin

| Dosya | node_id sonu | Konu | group |
|---|---|---|---|
| `tizen-multimedya-altyapisi.md` | `…/tizen/tizen-multimedya-altyapisi` | GStreamer · pipeline · caps · pad · bus · state change | mimari |
| `tizen-ses-yoneticisi.md` | `…/tizen/tizen-ses-yoneticisi` | Sound Manager API · focus · route · session · ducking | mimari |
| `tizen-audio-hal.md` | `…/tizen/tizen-audio-hal` | ALSA UCM · device enumeration · `hw:` path · mixer control | mimari |
| `tizen-uygulama-ortami.md` | `…/tizen/tizen-uygulama-ortami` | TPK package · Tizen Studio · privilege · sandbox · signing | mimari |
| `tizen-gercek-zamanli-tampon.md` | `…/tizen/tizen-gercek-zamanli-tampon` | callback deadline · ring buffer · underrun · governor | kod |
| `tizen-web-arastirma.md` | `…/tizen/tizen-web-arastirma` | ≥8 resmi URL tarama kaydı (HTTP 200 · 2026-10-13) + repo kod kanıtı | kanit |
| `tizen-kod-seti.md` | `…/tizen/tizen-kod-seti` | Portable dil seti + TPK/manifest/GStreamer yüzeyi (CAPI imzaları ⚠️) · **2026-10-14 eklendi** | kod |
| `tizen-dma-ve-interrupt.md` | `…/tizen/tizen-dma-ve-interrupt` | DMA/kesme belge boşluğu · katman modeli · Raw Audio yüzeyi · ⚠️/UNKNOWN ağırlıklı · **2026-10-14 eklendi** | mimari |

Toplam: 8 içerik dosyası · hedef ≥500 satır/dosya (`tizen-kod-seti` 620 ile ≥500) · bu indeks 40–100 satır sınırındadır.

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | tizen-multimedya-altyapisi.md | GStreamer pipeline topolojisi, caps/pad eşleşmesi, bus mesajları, durum makinesi | 3 |
| mimari | tizen-ses-yoneticisi.md | Sound Manager / sound policy katmanı, odak (focus) devri, rota seçimi, oturum ve ducking | 3 |
| mimari | tizen-audio-hal.md | ALSA UCM profilleri, cihaz numerasyonu, `hw:` yol sözdizimi, mixer (kcontrol) erişimi | 3 |
| mimari | tizen-uygulama-ortami.md | TPK paketi, Tizen Studio/CLI, privilege listesi, sandbox sınırı, sertifika imzalama | 3 |
| kod | tizen-gercek-zamanli-tampon.md | Callback deadline hesabı, lock-free ring buffer, underrun tespiti, CPU governor etkisi | 3 |
| kanit | tizen-web-arastirma.md | Resmi URL tarama kaydı (HTTP 200 · 2026-10-13) + repo kod kanıtı | 3 |
| mimari | tizen-dma-ve-interrupt.md | DMA/kesme belge boşluğunun dürüst kaydı, katman modeli, Linux bağımlılığı (2026-10-14) | 3 |
| mimari | tizen-guvenlik.md | Tizen güvenlik profili: author+distributor imza, public/partner/platform privilege matrisi, `tizen-manifest` izinleri + CoreMusic repo izi (DOSYA:SATIR) · **2026-10-15 eklendi** | 3 |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode