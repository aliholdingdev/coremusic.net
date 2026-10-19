---
node_id: "isletim-sistemi-turu/windows"
node_type: "deep-dal"
status: "draft"
parent_id: "isletim-sistemi-turu"
children:
  - "windows-anayasa.md"
  - "windows-kernel.md"
  - "windows-kod-seti.md"
  - "windows-guvenlik.md"
depends_on:
  - "isletim-sistemi-turu"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads"
  - "https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal"
  - "disk: isletim-sistemi-turu/windows/ glob (2 dosya)"
validation: "web: processes-and-threads, event-tracing-portal yanıt kodları 2026-10-12 (HTTP 200). disk: alt dizin enumerasyonu (2 mevcut dosya — bu turda dokunulmadı)."
open_questions:
  - "W-D1: Windows alt-dalı hangi kapsamla yazıldı (bu turda içerik okunmadı)? ⚠️ VERIFICATION REQUIRED — dosyalar salt-okunur bırakıldı"
  - "W-D2: Windows üretim hedefi mi? (Q4/T-U3 ile ilişkili) UNKNOWN"
group: mimari
---

# windows — derin dal indeksi

> **Not:** Bu indeks bu turda **üretilen tek dosyadır**; altındaki mevcut dosyalar salt-okunur bırakılmıştır (kapsam içeriği kendilerindedir, yeniden yazılmaz).

## Dosya indeksi (disk enumerasyonu)

| Dosya | Rol |
|---|---|
| `windows-anayasa.md` | Windows dalı anayasası — **içerik bu turda okunmadı, dokunulmadı** |
| `windows-kernel.md` | Windows çekirdek katmanı — **içerik bu turda okunmadı, dokunulmadı** |
| `windows-kod-seti.md` | Portable dil seti + Win32- sınıf yüzey (C#/PowerShell/Node) — **2026-10-14 eklendi** |

## Bağlam

- Üst dal: [`../index.md`](../index.md) (`isletim-sistemi-turu` — OS tip/tür sınıflandırması).
- Windows türünün resmi referans kaynakları (erişimi doğrulanmış, 2026-10-12): processes-and-threads (https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads) · ETW portalı (https://learn.microsoft.com/en-us/windows/win32/etw/event-tracing-portal) — HTTP 200.
- Repo'daki Windows izi: `web.config` ×4 (Apache'e eklemiş çift hedef yapılandırma, bkz. `isletim-sistemi-runtime` §2).

## Açık sorular

W-D1 mevcut dosya kapsamı [⚠️] · W-D2 Windows üretim hedefi mi? [UNKNOWN]

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-3 |
| mimari | `windows-anayasa.md` | Anasözleşme ve bağlayıcı mimari kurallar | Kademe-3 |
| mimari | `windows-device-lifecycle.md` | Cihaz yaşam döngüsü ve durum geçişleri | Kademe-3 |
| mimari | `windows-dma-ve-interrupt.md` | DMA aktarımı ve kesme yönetimi | Kademe-3 |
| mimari | `windows-kernel.md` | Çekirdek veri yolu ve sistem çağrıları | Kademe-3 |
| mimari | `windows-kernel-modlari.md` | Çekirdek modülleri ve yüklenme sırası | Kademe-3 |
| mimari | `windows-ses-stacki.md` | Ses yığını ve katman sıralaması | Kademe-3 |
| kod | `windows-kod-ornekleri.md` | Gerçek repo kodu, dosya ve satır | Kademe-3 |
| kanit | `windows-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-3 |
| mimari | `windows-guvenlik.md` | OS güvenlik profili — K000-K009 sınırı | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode