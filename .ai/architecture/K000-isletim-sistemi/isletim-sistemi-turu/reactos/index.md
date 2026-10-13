---
node_id: "K000-isletim-sistemi/isletim-sistemi-turu/reactos/index"
node_type: "architecture"
status: "verify-required"
parent_id: "K000–K5000 Architecture Registry"
children:
  - "K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-cekirdek-mimarisi"
  - "K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-win32-uyumlulugu"
  - "K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-ses-stacki"
  - "K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-surucu-modeli"
  - "K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-kod-ornekleri"
  - "K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-web-arastirma"
depends_on:
  - "isletim-sistemi-kernel"
  - "isletim-sistemi-audio"
  - "isletim-sistemi-api"
evidence:
  - "https://reactos.org/"
  - "https://github.com/reactos/reactos"
  - "https://jira.reactos.org/"
validation: "Dosya içi doğrulama tabloları (| İddia | URL | Tarih: 2026-10-12 | Durum |) — parent index §GRUPLAR satırı bekleniyor"
open_questions:
  - "reactos.org/wiki içeriği bot-duvarı (Anubis v1.19.1) arkasında — içerik iddiaları [⚠️] işaretli ve üretim edilemedi (bkz. reactos-cekirdek-mimarisi.md §11.5)"
  - "Sürüm 0.4.16+ iddiası resmi sürüm sayfasından 2026-10-12 doğrulanamadı (bot-duvarı) — UNKNOWN"
group: mimari
---

# ReactOS — Platform Derin-Dal Index

**Kapsam:** ReactOS derin-dalı (Kademe-3 deep-dal) — NT-typ kernel, Win32 API uyumluluk
hedefi, iç mimari NT 5.x referansı; hasarlı sürücü üzerinde korumalı mod erişimi,
ses cihazı ve sürücü modeli. `windows/`, `linux/`, `macos/` derin-dallarından AYRIDIR
(bkz. parent `isletim-sistemi-turu/index.md`, ilke 6).

**Yöntem (RED TEAM / TRUTH MODE):** Tek kaynak — yalnızca `reactos.org` resmi alan
adı + `github.com/reactos/reactos` (MIT) + `jira.reactos.org` + `build.reactos.org`.
Her iddia doğrudan HTTP 200 yanıtıyla desteklenir; desteklenmeyen her iddia
`⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` işaretlenir. Kod kanıtı repo'daki mevcut
tek format kullanılır: `**[ORİJİNAL ÖRNEK · derlenmedi]**` + resmi alıntı URL'li
satır (repo 0 `.cpp/.h` — `Dosya:/Satır:` uydurmak yasak, bkz. `windows-kod-ornekleri.md` §0).

**Kritik erişim notu (2026-10-12):** `https://reactos.org/wiki/*` HTTP 200 döner
ancak gövde Anubis v1.19.1 bot-duvarı sayfasıdır (4188 bayt) → wiki **içeriği**
okunamaz; yalnızca durum kodu doğrulanmıştır. Ayrıntı ve etkilenen iddiaların listesi:
`reactos-cekirdek-mimarisi.md` §11.5 (kanıt bütünlüğünün tek kaynağı).

## Dizin

| Dosya | node_id | Konu |
|---|---|---|
| `reactos-cekirdek-mimarisi.md` | `…/reactos/reactos-cekirdek-mimarisi` | NT-typ kernel, NT 5.x iç mimarisi, 13 URL |
| `reactos-win32-uyumlulugu.md` | `…/reactos/reactos-win32-uyumlulugu` | Win32 API hedefi, NTOSKRNL + kernel32/ntdll ayrımı, 10 URL |
| `reactos-ses-stacki.md` | `…/reactos/reactos-ses-stacki` | Kernel Mixer/WaveRT/WDM, WASAPI bağlantı durumu, 10 URL |
| `reactos-surucu-modeli.md` | `…/reactos/reactos-surucu-modeli` | WDM, sürücü yazma, INI tabanlı yükleme (jvc), 10 URL |
| `reactos-kod-ornekleri.md` | `…/reactos/reactos-kod-ornekleri` | 8 orijinal derlenmemiş kod bloğu, 10 URL |
| `reactos-web-arastirma.md` | `…/reactos/reactos-web-arastirma` | Ağ kaynak taraması + doğrulama matrisi, 8 URL |

Toplam: 6 içerik dosyası · 3.147 satır (minimum 500/dosya) · 61 tekil URL
(6 dosyada karşılıklı kesişim 0 — otomatik ölçüm), her dosyada 10 frontmatter alanı.

## §GRUPLAR

| Dosya | Grup | Satır | URL (tekil) | Durum |
|---|---|---:|---:|---|
| index | mimari | 40–100 | 3 | verify-required |
| reactos-cekirdek-mimarisi | mimari | 561 | 13 | verify-required |
| reactos-win32-uyumlulugu | mimari | 519 | 10 | verify-required |
| reactos-ses-stacki | mimari | 503 | 10 | verify-required |
| reactos-surucu-modeli | mimari | 508 | 10 | verify-required |
| reactos-kod-ornekleri | kod | 514 | 10 | verify-required |
| reactos-web-arastirma | kanit | 503 | 8 | verify-required |

**Authority: Bayram Ali / Vault Steward**

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode