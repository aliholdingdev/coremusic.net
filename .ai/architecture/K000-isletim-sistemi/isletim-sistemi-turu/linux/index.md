---
node_id: "isletim-sistemi-turu/linux"
node_type: "deep-dal"
status: "draft"
parent_id: "isletim-sistemi-turu"
children:
  - "linux-anayasa.md"
  - "linux-api.md"
  - "linux-kernel.md"
depends_on:
  - "isletim-sistemi-turu"
evidence:
  - "https://docs.kernel.org/"
  - "https://man7.org/linux/man-pages/man2/syscalls.2.html"
  - "https://developer.android.com/"
  - "disk: isletim-sistemi-turu/linux/ glob (3 dosya)"
validation: "web: docs.kernel.org, syscalls.2, developer.android.com yanıt kodları 2026-10-12 (HTTP 200). disk: alt dizin enumerasyonu (3 mevcut dosya — bu turda dokunulmadı)."
open_questions:
  - "L-D1: Linux alt-dalı hangi kapsamla yazıldı (bu turda içerik okunmadı)? ⚠️ VERIFICATION REQUIRED — dosyalar salt-okunur bırakıldı"
  - "L-D2: Linux üretim hedefi mi? — `home.coremusic.net/AGENTS.md` §1 "Raspberry Pi 5" notu güçlü bir ipucudur ama OS tür kararı DEĞİLDİR (Q4/T-U3) UNKNOWN"
group: mimari
---

# linux — derin dal indeksi

> **Not:** Bu indeks bu turda **üretilen tek dosyadır**; altındaki mevcut dosyalar salt-okunur bırakılmıştır (kapsam içeriği kendilerindedir, yeniden yazılmaz).

## Dosya indeksi (disk enumerasyonu)

| Dosya | Rol |
|---|---|
| `linux-anayasa.md` | Linux dalı anayasası — **içerik bu turda okunmadı, dokunulmadı** |
| `linux-api.md` | Linux API katmanı — **içerik bu turda okunmadı, dokunulmadı** |
| `linux-kernel.md` | Linux çekirdek katmanı — **içerik bu turda okunmadı, dokunulmadı** |

## Bağlam

- Üst dal: [`../index.md`](../index.md) (`isletim-sistemi-turu` — OS tip/tür sınıflandırması).
- Linux türünün resmi referans kaynakları (erişimi doğrulanmış, 2026-10-12): Kernel Documentation (https://docs.kernel.org/) · syscalls(2) (https://man7.org/linux/man-pages/man2/syscalls.2.html) — HTTP 200.
- Üretim hedefi ipucu: `home.coremusic.net/AGENTS.md` §1 "Raspberry Pi 5" (Linux/ARM sınıfı donanım) — **ipucu, tür kararı değil** (UNKNOWN).
- İlişkili alt tür: Android (mobil/Linux çekirdekli) — https://developer.android.com/ (HTTP 200, 2026-10-12).

## Açık sorular

L-D1 mevcut dosya kapsamı [⚠️] · L-D2 Linux üretim hedefi mi? [UNKNOWN]

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `linux-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-3 |
| mimari | `linux-api.md` | Linux sistem çağrıları ve API desenleri | Kademe-3 |
| mimari | `linux-kernel.md` | Çekirdek veri yolu ve sistem çağrıları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode