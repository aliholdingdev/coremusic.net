---
node_id: K003.04
node_type: subdomain
status: draft
parent_id: K003-audio
children:
  - K003.04.01 asio
depends_on:
  - K003.01 engine
  - K003.02 device
  - K003.03 driver
evidence:
  - ".ai/architecture/K003-audio/backend/asio/index.md (katalog, 2026-10-12)"
  - ".ai/architecture/K003-audio/backend/asio/asio-kapsam.md (kapsam sözleşmesi)"
  - ".ai/architecture/K003-audio/audio-master.md §4 A1–A6 · §5 T1–T4 (ASIO/WASAPI)"
validation:
  - "Çocuk: 1 (asio) — disk sayımı 2026-10-12: backend/asio/ = 8 dosya (index + kapsam + 6 deep-dive)"
  - "6 deep-dive dosyası salt-okunur (koruma kararı); backend/index.md bu oturumda yazıldı"
open_questions:
  - "K003-Q5: engine↔backend sınırı ve pull-model veri sahipliği?"
group: mimari
---

# K003.04 · backend — Backend / Platform API Katmanı

`node_id: K003.04` · `node_type: subdomain` · `parent: K003-audio`

## 1. Bu alan nedir?

**Backend**, ses motoru ile işletim sistemi ses API'si arasındaki **çeviri katmanıdır**:
motor "bu bloğu çal" der; backend bunu Core Audio `AudioUnit`'ine, ASIO `bufferSwitch`'ine,
WASAPI exclusive isteğine ya da ALSA `snd_pcm_writei`'ye çevirir. Çeviri = format/kanal/
çözünürlük uyarlama + latency ayarı + hata kodu tercümesi. K003.03 driver donanım/kernel
sınırını, backend ise **OS kullanıcı-zamanı API** sınırını taşır.

## 2. Kapsam

- **İçinde:** platform ses API bağlayıcıları (Core Audio / WASAPI / ASIO / ALSA / PipeWire),
  format dönüşümü (Float32↔Int16↔Int24), exclusive/shared seçim politikası, latency &
  buffer-size müzakeresi, backend hata kodlarının uygulama diline tercümesi.
- **Dışında:** DSP algoritması (K004) · çekirdek zamanlama/lock-free (K003.09) ·
  driver/kernel modülü (K003.03) · cihaz listeleme UI'ı (K003.02 device).
- **Komşu:** `K003.04.01 asio` — bu alt-alanın TEK çocuktur.

## 3. Çocuklar (diskten sayım — v2 §2.3 kaydıyla birebir)

| # | Çocuk | ID | Dosya | Yapısı |
|---|---|---|---|---|
| 1 | asio | K003.04.01 | 8 | `index.md` + `asio-kapsam.md` + 6 deep-dive |

**Diğer platform bağlayıcı dosyaları (Core Audio / WASAPI / ALSA / PipeWire) için ayrı
çocuk KAYITLI DEĞİLDIR** — bu bağlayıcıların omurgası `audio-master.md` §4/§5 seviyesindedir;
yeni çocuk ancak v2 §2.3'e eklenerek açılır (KURAL 1).

## 4. asio alt-alanı (katalog — 6 dosya salt-okunur)

`backend/asio/` bu oturumda **değiştirilmedi** (koruma kararı). İçerik 2026-10-12 tarihli,
kanıt-bağıtlı deep-dive setidir:

| Dosya | Ne belgeler | Durum |
|---|---|---|
| `index.md` | Katalog (7 satır: dosya → soru → kanıt yoğunluğu) | korunan katalog |
| `asio-kapsam.md` | Kapsam sözleşmesi (IN/OUT-SCOPE + açık sorular) | korunan |
| `asio-anayasa.md` | ASIO temel doğası ve host sözleşmesi | **salt-okunur** |
| `asio-arayuzler.md` | `IASIO*` arayüz ailesi (host↔driver API) | **salt-okunur** |
| `asio-surucu-modeli.md` | ASIO driver'ının Windows'ta çalışma modeli | **salt-okunur** |
| `asio-platform-uyumlulugu.md` | Windows/mimari kombinasyonları | **salt-okunur** |
| `asio-daw-uyumlulugu.md` | DAW uyumluluğu (multi-client) | **salt-okunur** |
| `asio-lisans-ve-dogrulama.md` | GPLv3 lisans + SDK 2.3.4 doğrulaması | **salt-okunur** |

Teknik omurga (A1–A6 pull/exclusive, T1–T4 ASIO/WASAPI) `audio-master.md` §4–§5'tedir;
lisans-riski K003-Q3 (GPLv3) `audio-dogrulama.md`'dedir.

## 5. Gezinti

`index.md` (bu dosya) · `asio/index.md` (alt-alan kataloğu) · `asio/asio-kapsam.md` ·
`asio/` 6 deep-dive. Bu seviyede kapsam/doğrulama belgesi yoktur: backend'in kapsamı
`audio-master.md` §2'de, doğrulaması `audio-dogrulama.md`'dedir (yalnız 1 çocuk kayıtlı).

## 6. Açık sorular

- **K003-Q5:** engine↔backend sınırı: pull-model'de render callback'i kimin sahipliği?
- ASIO Cihaz Paylaşım Protokolü ayrı node mu? (asio-kapsam §4.1 — korunan dosyada)
- Neva Engine ASIO-Guard uygulayacak mı? (asio-kapsam §4.2 — korunan dosyada)

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward

