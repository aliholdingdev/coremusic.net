---
id: isletim-sistemi-api-cihaz-erisimi
title: Cihaz Erişimi — Alt Dal
node_type: subdomain
layer: L0
status: active
tags: [isletim-sistemi, cihaz-erisimi, alt-dal, mimari]
group: index
---
# Cihaz Erişimi — Alt Dal

## §0. ELI15

Bilgisayarın donanım parçalarına — hoparlör, ağ kartı, ekran kartı, klavye —
nasıl erişildiğini anlatan dal. Programlar bu cihazlara **dosya gibi** erişir.
Örneğin Linux'ta her cihaz `/dev/` altında bir dosya gibi görünür. Bu dal, o
dosyaların nasıl açıldığını, okunduğunu, yazıldığını ve kontrol edildiğini
anlatır.

## §1. Amaç

Bu alt dalın amacı, işletim sisteminin uygulamaya açtığı **cihaz erişim
arayüzünü** tanımlamaktır: cihaz dosyaları (`/dev` · `/proc`), dosya tanımlayıcı
(fd) yönetimi, `ioctl` kontrol arayüzü, hotplug olayları, cihaz yetkileri ve
bellek eşleme (`mmap`). HTTP/REST bu dalın **kapsamı dışındadır** — bu dalın
sınırları içinde yalnızca işletim sistemi seviyesindeki cihaz erişim arayüzü
yer alır.

## §2. Kademe ve Rol

| # | Değer | Kural |
|---|---|---|
| 1 | Kademe | Kademe-4 (Alt Dal) |
| 2 | Üst | `isletim-sistemi-api` (Kademe-3) |
| 3 | Rol | İşletim sistemi seviyesinde cihaz erişim arayüzü — HTTP/REST KAPSAM DIŞI |
| 4 | Bağımlılık yönü | Yalnız kendi çocuk modüllerine bağımlı |

## §3. Envanter

| # | Dosya | `node_type` | `group` | Rol |
|---|---|---|---|---|
| 1 | `index.md` | subdomain | index | Alt dal envanteri, §GRUPLAR, komşu sınırı |
| 2 | `cihaz-erisimi-anayasa.md` | module | anayasa | Anayasal çekirdek — `/dev` · fd · ioctl · hotplug · yetki · mmap · sıcak yüzey |
| 3 | `cihaz-erisimi-surucu.md` | module | surucu | Üretim sürümü — sürüm akışı · canlı ortam · olay · izleme |
| 4 | `cihaz-erisimi-ornekler.md` | module | ornekler | Empirik ölçüm · repo doğrulama · kod örnekleri · sınırlar |

## §4. §GRUPLAR

| # | Grup | Kademe | Sahiplik | Dosyalar |
|---|---|---|---|---|
| 1 | anayasa | Kademe-4 | çekirdek-kural | `cihaz-erisimi-anayasa.md` |
| 2 | surucu | Kademe-4 | uretim-kontrol | `cihaz-erisimi-surucu.md` |
| 3 | ornekler | Kademe-4 | kanit-uretim | `cihaz-erisimi-ornekler.md` |

## §5. Komşu Sınırı

| # | Komşu | Sınır |
|---|---|---|
| 1 | `sistem-cagrilari` | Sistem çağrıları — cihaz erişim arayüzünün **altında** |
| 2 | `platform-kutuphaneleri` | Platform kütüphaneleri — cihaz erişimini **sarmalayan** |
| 3 | K011/K012 | HTTP/REST — bu dalın **kapsamı dışındadır** |

## §6. `depends_on` — Bu Alt Dala Bağımlı Olanlar

| # | Alt Dal | Bağımlılık | Kural |
|---|---|---|---|
| 1 | `sistem-cagrilari` | Cihaz erişimi `ioctl` çağrısını kullanır | Yalnız kendi çocuk modüllerine bağımlı |

## §7. Açık Sorular

| # | Soru | Neden önemli | Sahip |
|---|---|---|---|
| 1 | `hotplug` olayları — Linux `udev` / macOS `IOKit` / Windows `WM_DEVICECHANGE` farkı | Üç platformda farklı olay modelleri | K000-isletim-sistemi |
| 2 | `mmap` — paylaşılan bellek (`MAP_SHARED`) çoklu süreçte | Çoklu süreç cihaz erişimi | K000-isletim-sistemi |
| 3 | Cihaz yetkileri — `CAP_SYS_ADMIN` / `SeLoadDriverPrivilege` | Yetki yükseltme saldırı yüzeyi | K000-isletim-sistemi |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
