---
title: "NevaConnect — Dosya Envanteri & Bağlantı Haritası (dosya yolları)"
type: doc
category: connect-network
version: 1.0.0
status: active
authority: "2026-10-19 disk ölçümü (glob + grep + Get-ChildItem) — zero-hallucination: her satır disk kanıtlı"
updated: 2026-10-19
---

# §1 Amaç

Kullanıcı direktifi (2026-10-19): `.ai/projects/NevaConnect/` ile **ilgili tüm dosyaları tespit et
ve dosya yollarını md'ye kaydet**. Bu doküman salt envanterdir — içerik özeti değil, **yol listesi**.
Bulma yöntemi: (1) glob ile dizin dökümü, (2) repo geneli `grep NevaConnect|neva-connect|Neva Connect`
(`hidden: true`), (3) çapraz referans taraması.

## İlgili vault kayıtları

- `PROJECTS.md` §7.1.4 NevaConnect (Multi-Room Audio, C++20 + JS, WebSocket 9742)
- `architecture/K010-network/connectivity/neva-connect-wlan-dallanma-mimarisi.md` (birincil mimari node)

---

# §2 Çekirdek Dokümanlar — `.ai/projects/NevaConnect/` (11 md · disk ölçümü 2026-10-19)

| # | Tam dosya yolu | Boyut (byte) |
|---|----------------|--------------|
| 01 | `C:\www\coremusic.net\.ai\projects\NevaConnect\01-wlanapi-sürüm-matrisi-ve-windows-plani.md` | 10090 |
| 02 | `C:\www\coremusic.net\.ai\projects\NevaConnect\02-coklu-exe-windows-sdk-derleme-mimarisi.md` | 52268 |
| 03 | `C:\www\coremusic.net\.ai\projects\NevaConnect\03-wlan-api-ozellik-kisitlama-tablosu.md` | 42577 |
| 04 | `C:\www\coremusic.net\.ai\projects\NevaConnect\04-kapsam-yol-haritasi-wifi7-bluetooth-usb-depolama.md` | 68816 |
| 05 | `C:\www\coremusic.net\.ai\projects\NevaConnect\05-neva-wlan-cekirdek-modul-tasarimi.md` | 43952 |
| 06 | `C:\www\coremusic.net\.ai\projects\NevaConnect\06-test-ve-kalite-plani.md` | 54228 |
| 07 | `C:\www\coremusic.net\.ai\projects\NevaConnect\07-xp-wzc-stack-kaynak-arastirmasi.md` | 31278 |
| 08 | `C:\www\coremusic.net\.ai\projects\NevaConnect\08-xp-panel-davranis-sozlesmesi.md` | 36577 |
| 09 | `C:\www\coremusic.net\.ai\projects\NevaConnect\09-xp-ndisuio-felaket-yolu-tasarimi.md` | 40191 |
| 10 | `C:\www\coremusic.net\.ai\projects\NevaConnect\10-wzc-ndisuio-registry-degerleri.md` | 35542 |
| 11 | `C:\www\coremusic.net\.ai\projects\NevaConnect\11-windows-wlanapi-mimarisi.md` | 34520 |
| — | **Bu dosya (envanter)** `C:\www\coremusic.net\.ai\projects\NevaConnect\00-dosya-envanteri-ve-baglanti-haritasi.md` | — |

> Dizin diskte yalnız `.md` içerir (11 doc + bu envanter = 12). Yardımcı script/artefakt
> (`export_scan.py`, `pe_arch.py`, `exports.json`, indirilen `wlanapi.dll` kopyaları, nt5src clone)
> **bu dizinde DEĞİL ve repo genelinde de YOK** (glob `**/{export_scan,pe_arch}.{py,json}` = 0 isabet).

---

# §3 İlgili Vault Dosyaları (repo içinde referans veren / referans alan)

## §3.1 Mimari node'lar (architecture/)

| Tam dosya yolu | İlişki |
|----------------|--------|
| `C:\www\coremusic.net\.ai\architecture\K010-network\connectivity\neva-connect-wlan-dallanma-mimarisi.md` | **Birincil mimari node** (L0-L4 + çok-exe + WS 9742 bridge; §8'e 05-09 dokümanları bağlı) |
| `C:\www\coremusic.net\.ai\architecture\K010-network\connectivity\index.md` | Node'u children listesine kayıtlı (satır 7 + grup tablosu satır 40) |
| `C:\www\coremusic.net\.ai\architecture\K010-network\connectivity\connectivity-standart-kanit.md` | WLAN API kanıt satırı (learn.microsoft.com wlanapi — madde 7) |
| `C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-api\ag-api\native-wifi-api-temelleri.md` | Kavramsal Native Wifi API katmanı; NevaConnect 01/03/04 ile çapraz 30+ referans |
| `C:\www\coremusic.net\.ai\architecture\K010-network\comms\websocket\websocket-kod-ornekleri.md` | WS 9742 kültürü referansı (satır 24, 190) |
| `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\yapay-zeka-test-stratejisi.md` | `ctest -R NevaConnect::I2C` satır 205 (legacy NevaEngine test adı) |
| `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\testing\testing-ornekler.md` | `ctest -R NevaConnect::I2C` satır 506 (aynı legacy referans) |

## §3.2 Vault kök indeks/kayıt dosyaları

| Tam dosya yolu | Ne tür referans |
|----------------|-----------------|
| `C:\www\coremusic.net\.ai\index.md` | §projects satırı 187 (Neva Connect 11 doc envanteri) + satır 189 (delta 01→11) |
| `C:\www\coremusic.net\.ai\index\01-mimari.md` | satır 204 wiki-link `[[projects/NevaConnect/proj-neva-connect]]` (⚠️ hedef adı diskteki `NevaConnect/` ile uyuşmuyor — §5.1) |
| `C:\www\coremusic.net\.ai\keys.md` | satır 144-155 (12 keyword satırı: doc 01-11 + envanter) |
| `C:\www\coremusic.net\.ai\log.md` | satır 47, 48, 50, 54, 55, 56, 57, 73 (session audit kayıtları) |
| `C:\www\coremusic.net\.ai\MEMORY.md` | satır 409 (2026-10-15 son eylem notu) |
| `C:\www\coremusic.net\.ai\PROJECTS.md` | §7.1.4 "NevaConnect (Multi-Room Audio)" — proje tanımı |

## §3.3 Karar & çapraz proje dosyaları

| Tam dosya yolu | İlişki |
|----------------|--------|
| `C:\www\coremusic.net\.ai\.decisions\accepted\ADR-037-wirelessconnect-integration.md` | satır 232 — "kendi NevaConnect hedefi" karşılaştırma satırı |
| `C:\www\coremusic.net\.ai\projects\NevaEngine\legacy-2026-07\rules\cpp-global-CLAUDE.md` | 5 proje kuralı (Engine/Player/WirelessConnect/**NevaConnect**/shared) + `NevaConnect.exe --systemd` |
| `C:\www\coremusic.net\.ai\projects\NevaEngine\legacy-2026-07\rules\cpp-global-AGENTS.md` | proje tablosu: NevaConnect → `neva-connect/CLAUDE.md`, PCM5122 I2S |
| `C:\www\coremusic.net\.ai\projects\NevaEngine\09-open-source-virtual-audio-driver-karsilastirma.md` | satır 178 — cpp-global kural dosyası envanterinde 5 proje sayımı |

---

# §4 Dış Referanslar (repo DIŞI — bu makinede doğrulanamayan)

| Dış yol | Kaynak | Durum (2026-10-19 Test-Path) |
|---------|--------|------------------------------|
| `E:\Belgeler\coremusic.net.old\cpp-projects\neva-connect\CLAUDE.md` | NevaConnect 01 (satır 24) · K010 node (satır 38/505) | ❌ **YOK** — `E:\Belgeler\coremusic.net.old\cpp-projects\` bu makinede mevcut değil → ⚠️ VERIFICATION REQUIRED |
| `E:\Belgeler\coremusic.net.old\cpp-projects\wireless-connect\AGENTS.md` | NevaConnect 01 (satır 25) · native-wifi node (satır 557/594) | ❌ **YOK** — aynı durum |

> Bu iki dış yol, dokümanlarda "okundu/kanıt" olarak geçmektedir (2026-10-15 oturumunda erişim
> vardı). Bugün bu makinede **erişilemez**; içerikleri vault'a taşınmadığı süreuse bu dosyalara
> dayanan iddialar tek başına **doğrulanamaz**.

---

# §5 Bulunmayan / Var Olmayan Yüzeyler (negative inventory)

## §5.1 Bozuk wiki-link notu

- `.ai/index/01-mimari.md:204` → `[[projects/NevaConnect/proj-neva-connect]]`
  Diskteki dizin adı `projects/NevaConnect/` (altında `proj-neva-connect.md` **YOK**).
  `projects/WirelessConnect/proj-wireless-connect` da diskte **YOK** (WirelessConnect dizini yok).
  → bilinen eksik link; **onarım bu envanterin kapsamı dışındadır**, raporlanır.

## §5.2 Repo içinde NevaConnect KAYNAK KODU YOK

- `neva-connect/` dizini repo kökünde **YOK** (glob `**/neva-connect/**` = 0 isabet).
- Kökte `*connect*` eşleşmeleri yalnız alakasız web dosyaları:
  `shared/database/migrations/oauth_connections_migration.php`,
  `assets.coremusic.net/Image/res-pink/wifi/wifi-not-connection.png`,
  `assets.coremusic.net/Image/res-pink/bluethoot-connected.png.png`,
  `assets.coremusic.net/Image/res-pink/bluethoot-1-connected.png`.
- Sonuç: NevaConnect şu an **salt vault projesidir** (11 tasarım/araştırma dokümanı); kod yazımı
  kullanıcı onayı beklemektedir (log.md 2026-10-15 kayıtları: "KOD YOK").

---

**Authority:** disk ölçümü (glob + grep + Get-ChildItem, 2026-10-19)
**Last Updated:** 2026-10-19
**Mode:** Truth Mode · Zero-Hallucination
