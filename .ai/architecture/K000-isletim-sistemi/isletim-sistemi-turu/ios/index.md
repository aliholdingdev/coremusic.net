---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/ios
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu
children:
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-audio-unit-arayuzu
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-avfoundation-semasi
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-core-audio-ayrintisi
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-gercek-zamanli-tampon
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-cihaz-ve-rota-yonetimi
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-web-arastirma
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-kod-seti
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-dma-ve-interrupt
  - K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-guvenlik
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# iOS — Platform Derin-Dal Index (Kademe-2)

**Kapsam:** iOS derin-dalı (K000-isletim-sistemi/isletim-sistemi-turu/ios) — Apple mobil
platformunun ses yüzeyi: AudioUnit arayüzü, AVFoundation şeması, Core Audio detayları,
gerçek-zamanlı tampon disiplini, cihaz/rota yönetimi ve resmi kaynak taraması.
`macos/`, `windows/`, `linux/`, `android/`, `reactos/` derin-dallarından AYRIDIR
(parent `isletim-sistemi-turu/index.md` üzerinden ayrışır).

**Yöntem (RED TEAM / TRUTH MODE):** Doğrulama turu 2026-10-12 — tüm URL'ler bu ortamdan
HTTP isteğiyle ölçülmüştür (durum kodu + gövde). `developer.apple.com` alt sayfaları bu
turda **yalnızca var olan yollarda** 200 döndürür (olmayan yol 404 → ölçümle elenmiştir).
`docs.tizen.org` gibi SPA iskeleti döndüren kaynaklar için bu dalda benzer bir durum
**yoktur**; Apple doküman sayfaları bu turda başlık düzeyinde okunabilmiştir.
Desteklenmeyen iddia `⚠️ VERIFICATION REQUIRED`, bilinmeyen değer `UNKNOWN` yazılır.

**Kod kanıtı politikası:** Bu depoda `.cpp/.h` dosyası yoktur (L0 durumu: PLANNED).
Bu nedenle `Dosya: … / Satır: …` referansları yalnızca **gerçek PHP/YAML dosyalarından**
üretilir ve `Get-Content` ile satır numarası doğrulanır; uydurma satır numarası yasaktır.

## Dizin

| Dosya | node_id sonu | Konu | group |
|---|---|---|---|
| `ios-audio-unit-arayuzu.md` | `…/ios/ios-audio-unit-arayuzu` | AudioUnit · AudioBufferList · render callback · AUHAL sınırı | mimari |
| `ios-avfoundation-semasi.md` | `…/ios/ios-avfoundation-semasi` | AVAudioEngine · AVAudioPlayerNode · AVAudioMixerNode | mimari |
| `ios-core-audio-ayrintisi.md` | `…/ios/ios-core-audio-ayrintisi` | AudioComponent · AudioUnitProperty · AudioStreamBasicDescription | mimari |
| `ios-gercek-zamanli-tampon.md` | `…/ios/ios-gercek-zamanli-tampon` | ring buffer · render deadline · input→output zinciri | kod |
| `ios-cihaz-ve-rota-yonetimi.md` | `…/ios/ios-cihaz-ve-rota-yonetimi` | AVAudioSession · category · route change · interruption | mimari |
| `ios-web-arastirma.md` | `…/ios/ios-web-arastirma` | ≥8 resmi URL tarama kaydı + repo kod kanıtı | kanit |
| `ios-kod-seti.md` | `…/ios/ios-kod-seti` | Portable dil seti + platform-özel yüzey (Swift/ObjC/C · AVAudioSession) · **2026-10-14 eklendi** | kod |
| `ios-dma-ve-interrupt.md` | `…/ios/ios-dma-ve-interrupt` | Darwin/IOKit DMA sınırı · prepare/complete · CoreAudio soyutlaması · DMA belge boşluğu ⚠️ | mimari |

Toplam: 8 içerik dosyası · hedef ≥500 satır/dosya (`ios-kod-seti` 409 ile <500, bilinçli gevşetildi) · bu indeks 40–100 satır sınırındadır.

## §GRUPLAR

> ADR-097 §5.5 · registry §8.1 · `group` = frontmatter 9. alan (``group`` çift-backtick).

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | ios-audio-unit-arayuzu.md | AudioUnit bileşen modeli, render callback sözleşmesi, AUHAL'ın iOS'ta bulunmayışı | 3 |
| mimari | ios-avfoundation-semasi.md | AVAudioEngine düğüm grafiği, player/mixer düğümleri, şema topolojisi | 3 |
| mimari | ios-core-audio-ayrintisi.md | AudioComponent kayıt/çözümleme, AudioUnitProperty kapsamları, ASBD alanları | 3 |
| kod | ios-gercek-zamanli-tampon.md | Ring buffer tasarımı, render deadline hesabı, input→output zinciri | 3 |
| mimari | ios-cihaz-ve-rota-yonetimi.md | AVAudioSession kategorileri, rota değişikliği, kesinti (interruption) yönetimi | 3 |
| kanit | ios-web-arastirma.md | Resmi URL tarama kaydı (HTTP 200 · 2026-10-12) + repo kod kanıtı | 3 |
| mimari | ios-dma-ve-interrupt.md | Darwin IOKit DMA yaşam döngüsü, iOS katman sınırı, kesme/doğrulama boşlukları (2026-10-14) | 3 |
| mimari | ios-guvenlik.md | OS güvenlik profili — K000-K009 sınırı | 3 |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode