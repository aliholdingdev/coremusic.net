---
title: "CoreMusic — .ai/.subdomains Bağlam (ELI10)"
type: context
category: vault
date: 2026-09-21
updated: 2026-09-23
version: 2.0.0
status: active
authority: reference
---

# CoreMusic — Subdomain Sistemi Nasıl Çalışır? (10 Yaş Seviyesi)

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

## 1. Ana Fikir: Bir Kod Tabanı, Birden Fazla Adres

CoreMusic **tek bir kod deposudur**. Birden fazla domain adresi görünür
(`auth.coremusic.net`, `home.coremusic.net`…), ama hepsi **aynı depodaki farklı
klasörleri** açar. Buna **subdomain** denir — yani "aynı sitenin farklı kapısı".

```
coremusic.net/                <- depo kökü
├── auth.coremusic.net/       <- giriş odası (kendi klasörü)
├── home.coremusic.net/       <- ana ekran (kendi klasörü)
├── assets.coremusic.net/     <- CSS/JS rafı (kendi klasörü)
└── shared/                   <- ORTAK KÜTÜPHANE (kapısız, herkes kullanır)
```

## 2. İstek Nasıl Bir Odaya Ulaşır?

1. Sen `auth.coremusic.net` yazarsın → tarayıcı **DNS**'e sorar (adresin IP'si ne?).
2. IP döner → web sunucusu (Apache) isteği alır.
3. Sunucu **virtual host** kuralına bakar: "Bu adres = şu klasör".
4. İlgili klasördeki giriş dosyası çalışır.
5. Yön belirlerken **`shared/src/PageRouter/`** kullanılır — rota kararını o verir.

> Yani subdomain = sunucunun "şu adresi şu klasöre çevir" kuralıdır.
> Dosyalar gerçekten ayrı klasörlerde durur, kopyalanmaz.

## 3. `shared/` Neden Ayrı ve Dokunulmaz?

`shared/` bir **ortak kütüphanedir** (psr-4 ile otomatik yüklenir). İçinde:

| Modül | Yol | Ne yapar? |
|---|---|---|
| PageRouter | `shared/src/PageRouter/` | Hangi adrese hangi sayfanın açılacağı |
| Middleware | `shared/src/Middleware/` | İsteklerin güvenlikten geçmesi (PSR-15) |
| Database | `shared/src/Database/` | PDO bağlantısı, prepared statement |
| Session / Security | `shared/src/Session/`, `shared/src/Security/` | Oturum ve şifre kontrolü |
| OAuth | `shared/src/OAuth/` | Sosyal giriş (Google vs.) |

**Kural:** Bir oda `shared/` dosyasını değiştirirse **tüm odalar** etkilenir.
Bu yüzden `shared/` değişikliği = HIGH öncelikli + ekstra test zorunlu.

## 4. Odalar — Diskte Gerçek Durum (Truth Mode)

| Klasör | Adres | Sorumluluk | Durum |
|---|---|---|---|
| `auth.coremusic.net/` | auth.coremusic.net | Giriş, kayıt, şifre sıfırlama | ✅ Var |
| `home.coremusic.net/` | home.coremusic.net | Kitaplık, ana ekran, çalma listesi | ✅ Var |
| `assets.coremusic.net/` | assets.coremusic.net | Statik varlıklar (ITCSS katmanları, JS modülleri) | ✅ Var |
| `shared/` | — | Ortak katman (L0–L2) | ✅ Var |

## 5. Planlanan Odalar (klasör henüz AÇILMADI)

| Adres | Ne olacak? | Durum |
|---|---|---|
| `music.coremusic.net` | Keşif ve çalma | ⚠️ Planlandı |
| `download.coremusic.net` | Çevrimdışı arşiv | ⚠️ Planlandı |
| `car.coremusic.net` | Araç içi ekran (otomotiv) | ⚠️ Planlandı |
| `studio.coremusic.net` | Stüdyo kontrol odası | ⚠️ Planlandı |
| `media.coremusic.net` | Kurumsal medya | ⚠️ Planlandı |

**Doğrulama notu:** Bu tablo `ls` çıktısından türetildi. Bir satır "Var" olacaksa
önce gerçekten klasör açılmalı — yoksa satır yazılmaz.

## 6. Neden Ayırdık? (Mimari Gerekçe)

| Gerekçe | Açıklama |
|---|---|
| **İzolasyon** | Giriş çökerse müzik odası çalışmaya devam eder |
| **Ayrı deploy** | Bir oda güncellenirken diğerleri durmaz |
| **Katman disiplini** | Oda = L3 sunum; `shared/` = L0–L2; oda `shared/`'e yukarıdan bağımlıdır (DIP) |
| **Token/performans** | Assets ayrı domain → tarayıcı farklı origin'den paralel çeker, ana HTML bloklanmaz |

## 7. Protokol

- Boot sırası: [[../CLAUDE.md]] §16
- Değişiklikler [[../log.md]]'ye **yalnızca eklenir** (geçmişe dokunulmaz)
- Yeni oda: klasör aç → `CLAUDE.md` yaz → vault kaydı → deploy
- **Layer violation** (oda → `shared/` içine doğrudan yazmak) → derhal revert + log ERROR

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
