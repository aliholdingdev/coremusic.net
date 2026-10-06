---
title: "subdomains.md — CoreMusic Subdomain Sistemi (ELI10 + Truth)"
type: dokuman
category: vault
created: 2026-10-06
updated: 2026-10-06
sources: [subdomains-v2.0.1, disk-envanteri]
tags: [subdomain, mimari, vhost, dns, katman]
status: active
authority: reference
---

# CoreMusic — Subdomain Sistemi Nasıl Çalışır? (10 Yaş Seviyesi)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[coremusic-platform]] · [[shared-infrastructure]]

> **Truth Mode notu (2026-10-06):** Bu sayfa `.ai/.subdomains` kök dokümanının
> (v2.0.1) vault wiki kopyasıdır. Tüm "Var" satırları disk kanıtıyla
> doğrulanmıştır; vhost/DNS değerleri depoda bulunamadığı için `⚠` işaretlidir.

---

## 1. Ana Fikir: Bir Kod Tabanı, Birden Fazla Adres

CoreMusic **tek bir kod deposudur**. Birden fazla domain adresi görünür
(`auth.coremusic.net`, `home.coremusic.net`…), ama hepsi **aynı depodaki farklı
klasörleri** açar. Buna **subdomain** denir — yani "aynı sitenin farklı kapısı".

```
coremusic.net/                <- depo kökü
├── api.coremusic.net/        <- API odası (kendi klasörü)
├── auth.coremusic.net/       <- giriş odası (kendi klasörü)
├── home.coremusic.net/       <- ana ekran (kendi klasörü)
├── media.coremusic.net/      <- medya/GUI odası (kendi klasörü)
├── assets.coremusic.net/     <- CSS/JS rafı (kendi klasörü)
└── shared/                   <- ORTAK KÜTÜPHANE (kapısız, herkes kullanır)
```

## 2. İstek Nasıl Bir Odaya Ulaşır?

1. Sen `auth.coremusic.net` yazarsın → tarayıcı **DNS**'e sorar (adresin IP'si ne?).
2. IP döner → web sunucusu (Apache) isteği alır.
3. Sunucu **virtual host** kuralına bakar: "Bu adres = şu klasör".
4. İlgili klasördeki giriş dosyası çalışır.
5. Yön belirirken **`shared/src/PageRouter/`** kullanılır — rota kararını o verir.

> Yani subdomain = sunucunun "şu adresi şu klasöre çevir" kuralıdır.
> Dosyalar gerçekten ayrı klasörlerde durur, kopyalanmaz.

**Sunucu/DNS/Vhost katmanı (Truth işaretli):**

| Adım | Kanıt | Durum |
|---|---|---|
| DNS A/CNAME kayıtları | `wiki/servers.md` (uzak sunucu) | ⚠ Uzak sunucuda — depoda dosya yok |
| Apache vhost blokları | `*vhost*` / `*httpd*` taraması: 0 isabet | ⚠ Depoda yok — deploy makinesinde |
| Sanal host → klasör eşlemesi | Bölüm 4 klasör listesi | ✅ Klasörler depoda mevcut |
| Rota kararı | `shared/src/PageRouter/` | ✅ Dizin diskte mevcut |

## 3. `shared/` Neden Ayrı ve Dokunulmaz?

`shared/` bir **ortak kütüphanedir** (composer PSR-4 ile otomatik yüklenir). İçinde
(`shared/src/` diskte doğrulandı — 20 dizin):

| Modül | Yol | Ne yapar? | Disk |
|---|---|---|---|
| PageRouter | `shared/src/PageRouter/` | Hangi adrese hangi sayfanın açılacağı | ✅ |
| Middleware | `shared/src/Middleware/` | İsteklerin güvenlikten geçmesi (PSR-15) | ✅ |
| Database | `shared/src/Database/` | PDO bağlantısı, prepared statement | ✅ |
| Session / Security | `shared/src/Session/`, `shared/src/Security/` | Oturum ve şifre kontrolü | ✅ |
| OAuth | `shared/src/OAuth/` | Sosyal giriş (Google vs.) | ✅ |
| Diğer katmanlar | Api, Bootstrap, Cache, Component, Config, Contracts, Events, Exception, Log, Repository, Theme, ViewMode, Device, AI | L0–L2 altyapı | ✅ |

**Kural:** Bir oda `shared/` dosyasını değiştirirse **tüm odalar** etkilenir.
Bu yüzden `shared/` değişikliği = HIGH öncelikli + ekstra test zorunlu.

## 4. Odalar — Diskte Gerçek Durum (Truth Mode · 2026-10-06)

| Klasör | Adres | Sorumluluk | Durum |
|---|---|---|---|
| `api.coremusic.net/` | api.coremusic.net | API uçları | ✅ Var |
| `auth.coremusic.net/` | auth.coremusic.net | Giriş, kayıt, şifre sıfırlama | ✅ Var |
| `home.coremusic.net/` | home.coremusic.net | Kitaplık, ana ekran, çalma listesi | ✅ Var |
| `media.coremusic.net/` | media.coremusic.net | Medya/GUI modülü | ✅ Var |
| `assets.coremusic.net/` | assets.coremusic.net | Statik varlıklar (ITCSS katmanları, JS modülleri) | ✅ Var |
| `shared/` | — | Ortak katman (L0–L2) | ✅ Var |

**Detay sayfaları:** [[api-coremusic-net]] · [[auth-coremusic-net]] ·
[[home-coremusic-net]] · [[media-coremusic-net]] · [[assets-coremusic-net]] ·
[[shared-infrastructure]]

## 5. Planlanan Odalar (klasör henüz AÇILMADI)

| Adres | Ne olacak? | Durum |
|---|---|---|
| `pro.coremusic.net` | Pro / abonelik odası | ⚠ Planlandı |
| `music.coremusic.net` | Keşif ve çalma | ⚠ Planlandı |
| `download.coremusic.net` | Çevrimdışı arşiv | ⚠ Planlandı |
| `car.coremusic.net` | Araç içi ekran (otomotiv) | ⚠ Planlandı |
| `studio.coremusic.net` | Stüdyo kontrol odası | ⚠ Planlandı |

**Doğrulama notu:** Bu tablo `ls` çıktısından türetildi. Bir satır "Var" olacaksa
önce gerçekten klasör açılmalı — yoksa satır yazılmaz.
**Kullanıcı kararı (2026-10-06):** liste kapanık değil — yeni subdomain fikirleri
geldikçe bu tabloya **eklenir** (silme yok, append-only).

## 6. Neden Ayırdık? (Mimari Gerekçe)

| Gerekçe | Açıklama |
|---|---|
| **İzolasyon** | Giriş çökerse müzik odası çalışmaya devam eder |
| **Ayrı deploy** | Bir oda güncellenirken diğerleri durmaz |
| **Katman disiplini** | Oda = L3 sunum; `shared/` = L0–L2; oda `shared/`'e yukarıdan bağımlıdır (DIP) |
| **Token/performans** | Assets ayrı domain → tarayıcı farklı origin'den paralel çeker, ana HTML bloklanmaz |

## 7. Protokol

- Boot sırası: [[CLAUDE.md]] §16
- Değişiklikler [[log.md]]'ye **yalnızca eklenir** (geçmişe dokunulmaz)
- Yeni oda: klasör aç → oda `CLAUDE.md`'si yaz → bu sayfaya "Var" satırı ekle →
  `index.md` güncelle → vault kaydı → deploy
- **Layer violation** (oda → `shared/` içine doğrudan yazmak) → derhal revert + log ERROR

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** `.ai/.subdomains` v2.0.1 (kullanıcı metni) + disk doğrulaması
