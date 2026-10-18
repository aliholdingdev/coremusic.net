---
node_id: donanım-bilesenler
node_type: subdomain
status: draft
parent_id: K001-donanım
children: []
depends_on: [K000-isletim-sistemi, K004-dsp, K005-firmware]
evidence:
  - ".ai/architecture/K001-donanım/donanım-anayasa.md §5 Katman ve Sorumluluk Matrisi (10 satır) + §6 Bileşen Sözleşmeleri (12 satır) · 2026-10-18"
  - "shared/src/Device/DeviceManager.php:24 (durum makinesi) + shared/src/Device/DeviceDetector.php (disk doğrulandı 2026-10-18) · 2026-10-18"
  - ".ai/architecture/K001-donanım/index.md Çocuk kataloğu (5 kademe-2) + Kademe-3 (10 alt bağlam) · 2026-10-18"
validation: "Her bileşen §5 matrisiyle birebir; disk kanıtı olmayan satır '⚠️ yok' etiketli; sözleşme→bileşen eşlemesi §6'daki 12 satırı eksiksiz karşılamalı."
open_questions:
  - "⚠️ donanım-kernel · donanım-core · donanım-api çocuklarının tek satır tanımları ad kökeninden türetildi (index.md notu) → içerik VERIFICATION REQUIRED."
  - "⚠️ donanım-kernel ↔ donanım-core sınırı v2'de tanımsız → ⚠️ VERIFICATION REQUIRED (anayasa §10 #2)."
  - "⚠️ Simüle cihaz katmanı hangi bileşende yaşayacak? (anayasa §10 #8) → UNKNOWN."
group: mimari
---

# K001 — Bileşenler (Katman Matrisi · Sözleşme Eşlemesi)

## §1 ELI15

K001'in iç parçaları: çekirdeğe en yakın soyutlama (`donanım-kernel`),
kayıt/durum merkezi (`donanım-core`), kapıyı açan arayüz
(`donanım-api`), tip ayıran dal (`donanaım-type`) ve cihazı bulan
tespitçi (`donanım-devices`). Her parçanın **sahip olduğu** ve
**sahip olmadığı** bellidir; belirsizse §5'teki matris kazanır.

## §2 Kademe-2 Bileşenler

| Bileşen | Rol | Sahip olduğu | Sahip olmadığı | Disk kanıtı | Kademe |
|---|---|---|---|---|---|
| donanım-kernel | donanıma en yakın soyutlama | interrupt/DMA sarmalayıcı rolü | çekirdek sürücü kodu (K000) | `donanım-kernel/{index,kapsam,ornekler,dogrulama}.md` | 2 |
| donanım-core | merkez bileşenler | cihaz kayıt, durum modeli, kaynak havuzu | API sözleşmesi | `donanım-core/{index,kapsam,ornekler,dogrulama}.md` | 2 |
| donanım-api | erişim arayüzü | keşif, komut, durum, olay | iş kuralı | `donanım-api/{index,kapsam,kod-seti,dogrulama}.md` | 2 |
| donanaım-type | tip sınıflandırma | audio · hdd · ssd (+7 tür) envanteri | firmware yükleme (K005) | `donanaım-type/index.md` + 10 alt dizin | 2 |
| donanım-devices | cihaz tespiti/envanter | cihaz tespiti ve kayıt | dosya sistemi | `donanım-devices/{index,kapsam,ornekler,dogrulama}.md` | 2 |

## §3 Kademe-3 Alt Bileşenler (donanaım-type)

| Alt bağlam | Sahip olduğu | Sahip olmadığı | Kanıt |
|---|---|---|---|
| hdd | disk/RAID envanteri | dosya sistemi | `donanaım-type/hdd/*.md` |
| ssd | SSD/NVMe envanteri | kalıcı depolama şeması | `donanaım-type/ssd/*.md` |
| audio | DAC/codec/amplifier envanteri | DSP zinciri (K003/K004) | `cipler/{pcm3168a-codec,ak4458-dac,tpa3255-amplifier}.md` |
| display | ekran/panel arayüzü sınıfı | görüntü işleme | ⚠️ anayasa §2 çelişkisi (C2) |
| power | güç kaynağı/koruma sınıfı — tasarım K002 | devre tasarımı | `power/*.md` · §0 kaydı |
| cables | kablo/konnektör sınıfı | PCB | `cables/*.md` |
| network | NIC/Wi-Fi sınıfı | ağ protokolü (K010) | `network/*.md` |
| memory | DDR/ECC sınıfı | bellek yönetimi (K000) | `memory/*.md` |
| cpu | İşlemci/SoC sınıfı | çekirdek kodu | `cpu/*.md` |
| gpu | graphics/compute sınıfı | sürücü kodu | `gpu/*.md` |

## §4 Repo Köprü Bileşenleri (kod kanıtı)

| Kod bileşeni | Dosya | Ne yapar | K001 ile ilişki |
|---|---|---|---|
| DeviceDetector | `shared/src/Device/DeviceDetector.php` | cihaz keşfi (§6 sözleşmesi 1) | donanım-api/keşif karşılığı |
| DeviceManager | `shared/src/Device/DeviceManager.php:24` | durum makinesi: onStart/onEnd/onDeviceAdded | donanım-core/durum modeli karşılığı |

> Repo kodu `.md` değildir ve bu görevde **değiştirilmez**; yalnız kanıt
> olarak okunur (klasör Uygulama Kuralı 2).

## §5 Sözleşme → Bileşen Eşlemesi (anayasa §6, 12/12)

| Sözleşme | Bileşen | Hata |
|---|---|---|
| cihaz keşfi | donanım-api | keşif-hatasi |
| cihaz aç | donanım-api | erisim-red |
| durum sorgu | donanım-core | bulunamadi |
| olay yayını | donanım-core | — |
| tip sınıflandırma | donanaım-type | belirsiz-tip |
| kaynak ayır | donanım-core | kaynak-yok |
| kaynak bırak | donanım-core | gecersiz-tutamak |
| soyutlama bağla | donanım-kernel | uyumsuz-arayuz |
| kapasite oku | donanaım-type | okuma-hatasi |
| sağlık kontrolü | donanım-core | — |
| envanter eşitle | donanım-core | kaynak-erişimi |
| yetki denetimi | donanım-api | yetkisiz |

## §6 Bağımlılık Yönü

```text
K000-isletim-sistemi  ← (K001 buraya bağımlı: sürücü/OS erişimi)
        ↑
   K001 root (anayasa · kapsam · bilesenler)
        ↑
donanım-kernel · donanım-core · donanım-api · donanım-devices
        ↑
   donanaım-type  →  10 alt bağlam (kademe-3)
        ↓
K005-firmware (yükleme) · K002-electroncis (tasarım/PCB) · K004-dsp (algoritma dışı)
```

Kural: **alt katman üst katmanı import etmez** (anayasa §3 İlke 1);
hata yayılımı Result/Either (İlke 6); realtime yolda tahsis yasak (İlke 3).

## §7 ⚠️ Bilinen Bozuk / Eksik Referanslar (salt rapor — DÜZELTME YASAK)

1. index.md Çocuk kataloğu satırları 1–3 (`donanım-kernel/core/api`) "Kanıt: ⚠️ yok" — içerik ad türetmesi.
2. `architecture/index.md` §6 ID GAP iddiası (C1) — K002 diskte VAR.
3. anayasa §5'te `donanım-bilesenler` kendisi listelenmiyor (yalnız `donanım-kapsam` var) — eksik satır raporlandı, anayasaya ekleme YAPILMADI.
4. index.md §GRUPLAR bu dosyayı listelemiyor — index.md bu oturumda yazılmadı (S2-B yetkisi).
