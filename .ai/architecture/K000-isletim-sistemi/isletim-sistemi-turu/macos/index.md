---
node_id: "isletim-sistemi-turu/macos"
node_type: "deep-dal"
status: "draft"
parent_id: "isletim-sistemi-turu"
children:
  - "macos-anayasa.md"
  - "macos-kernel.md"
  - "macos-kod-seti.md"
  - "macos-dma-ve-interrupt.md"
  - "macos-guvenlik.md"
depends_on:
  - "isletim-sistemi-turu"
evidence:
  - "https://developer.apple.com/documentation/kernel"
  - "https://developer.apple.com/documentation/audiotoolbox"
  - "disk: isletim-sistemi-turu/macos/ glob (2 dosya)"
validation: "web: developer.apple.com/kernel, audiotoolbox yanıt kodları 2026-10-12 (HTTP 200). disk: alt dizin enumerasyonu (2 mevcut dosya — bu turda dokunulmadı)."
open_questions:
  - "M-D1: macOS alt-dalı hangi kapsamla yazıldı (bu turda içerik okunmadı)? ⚠️ VERIFICATION REQUIRED — dosyalar salt-okunur bırakıldı"
  - "M-D2: macOS hedef platform setinde mi? (Q4/T-U3) UNKNOWN — repo'da macOS hedefine dair disk kanıtı YOK"
group: mimari
---

# macos — derin dal indeksi

> **Not:** Bu indeks bu turda **üretilen tek dosyadır**; altındaki mevcut dosyalar salt-okunur bırakılmıştır (kapsam içeriği kendilerindedir, yeniden yazılmaz).

## Dosya indeksi (disk enumerasyonu)

| Dosya | Rol |
|---|---|
| `macos-anayasa.md` | macOS dalı anayasası — **içerik bu turda okunmadı, dokunulmadı** |
| `macos-kernel.md` | macOS çekirdek katmanı — **içerik bu turda okunmadı, dokunulmadı** |
| `macos-kod-seti.md` | Portable dil seti + platform-özel yüzey (launchd/Gatekeeper/posix_spawn) — **2026-10-14 eklendi** |
| `macos-dma-ve-interrupt.md` | IOKit DMA (IODMACommand/IOBufferMemoryDescriptor/prepare-complete) + kesme yüzeyi — **2026-10-14 eklendi** |

## Bağlam

- Üst dal: [`../index.md`](../index.md) (`isletim-sistemi-turu` — OS tip/tür sınıflandırması).
- macOS türünün resmi referans kaynakları (erişimi doğrulanmış, 2026-10-12): Apple Kernel dokümanı (https://developer.apple.com/documentation/kernel) · AudioToolbox (https://developer.apple.com/documentation/audiotoolbox) — HTTP 200.
- Repo'da macOS hedefine dair **doğrudan disk kanıtı yoktur**; dal varlığı v2 ağaç yapısından gelir (ad = disk + v2 birebir). Hedef platform seti [UNKNOWN] (Q4/T-U3).

## Açık sorular

M-D1 mevcut dosya kapsamı [⚠️] · M-D2 macOS hedefte mi? [UNKNOWN]

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `macos-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-3 |
| mimari | `macos-kernel.md` | Çekirdek veri yolu ve sistem çağrıları | Kademe-3 |
| mimari | `macos-dma-ve-interrupt.md` | IOKit DMA yaşam döngüsü, prepare/complete, kesme yüzeyi (2026-10-14) | Kademe-3 |
| mimari | `macos-guvenlik.md` | OS güvenlik profili — K000-K009 sınırı | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode