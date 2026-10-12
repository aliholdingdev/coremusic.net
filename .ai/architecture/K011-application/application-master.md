---
title: "K011-application — Application Master"
type: architecture
category: k-space-v3
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K011-application
role: master
authority: "SSOT: v2 §2.11 + .ai/architecture/index.md §5/11"
related: ["application-bagimliliklar.md", "application-dogrulama.md", "index.md"]
---

# K011-application — application-master.md

> Bu dosya v2 §2.11'de adı geçen **master** dosyasıdır (ad kaynaktan birebir,
> ADR-097 K2/K3). Yeni rol dosyaları: application-bagimliliklar.md ·
> application-dogrulama.md.

## §1 Rol (ELI15)

v2 §2.11: *"Uygulamanın kendisi — web sayfası, masaüstü penceresi, Electron çatısı."*

K011, CoreMusic'in **kullanıcıya görünen yüzü**dür. Üç iş yapar:

1. **Göster** — sayfa/pencere bileşenlerini çizer (web · desktop · electron).
2. **Taşı** — kullanıcı talebini alır ve servislere iletir (commands · queries ·
   application-services · workflows).
3. **Ortak** — aynı çekirdeği farklı yüzeylerde çalıştırır
   (local-runtime · server-runtime).

Ne yapmaz: iş kuralı **üretmez** (K008-domain), veri saklamaz (K007-data),
dış API ile konuşmaz (K013-integraion), ağ paketi göndermez (K014-network),
ses decode etmez (K003-audio + K015-media), politika **kodlamaz** (K009-security).

## §2 Kapsam / Sınır

### §2.1 Kapsam içinde (bu odanın malı)

- Arayüz yüzeyleri: web · desktop · electron
- Uygulama çalışma zamanları: local-runtime · server-runtime
- Talep taşıma: commands · queries · workflows · application-services
- Katman testi: application-testing

### §2.2 Kapsam dışında (kimin?) — sınır tablosu

| Konu | Ait olduğu düğüm | Not |
|---|---|---|
| İş kuralı / domain modeli | K008-domain | Uygulama yalnız taşır |
| Arka plan servisleri ("çalan parça servisi") | K012-services | K011 yalnız çağrı yapar |
| Dış API kontratı / adaptör | K013-integraion | K011 yalnız kullanıcı akışını başlatır |
| Taşıma protokolü ayrıntısı | K014-network | K011 yalnız kullanır |
| Kitaplık / metadata / akış | K015-media | K011 yalnız gösterir |
| Oturum, rol, cihaz kimliği | K010-identitiy | K011 yalnız tüketir |
| Güvenlik politikası (CSRF, imza) | K009-security | Uygulama kodunda politika **yaşamaz** |
| Ortak test altyapısı | K019-testing | application-testing yalnız katmanına ait |
| Uygulama telemetrisi | K017-observability | K011 yalnız veri üretir |

## §3 Kademe-2 çocuk tablosu (v2 §2.11 birebir — 10 çocuk)

| # | Çocuk | ELI15 rol (TASARIM İDDİASI) | Ne içerir — kapsam notu | Durum |
|---|---|---|---|---|
| 1 | web | Tarayıcı arayüzü | Sayfa/bileşen/stil katmanı; tarayıcı API erişimi | draft |
| 2 | desktop | Yerel masaüstü penceresi | OS penceresi, menü, dosya erişimi (ayrıntı ⚠️) | draft |
| 3 | electron | Electron çatısı kabuğu | Masaüstü paketleme çatısı; desktop ile sınır ⚠️ | draft |
| 4 | local-runtime | Yerel çalışma zamanı | Kullanıcı makinesinde çalışan çekirdek (nerede ⚠️) | draft |
| 5 | server-runtime | Sunucu çalışma zamanı | Sunucu tarafı çalıştırma (barındırma yeri ⚠️) | draft |
| 6 | application-services | Uygulama orkestrasyonu | Kullanıcı akışını servislere bağlayan ara katman | draft |
| 7 | commands | Yazma talepleri | "Ekle/değiştir/çal" gibi eylem talepleri | draft |
| 8 | queries | Okuma talepleri | "Göster/listele" gibi sorgu talepleri | draft |
| 9 | workflows | Çok adımlı iş akışları | Adım sırası + durum makinesi (teknoloji ⚠️) | draft |
| 10 | application-testing | Katman testleri | Uygulama katmanı testleri; K019 ile sınır ⚠️ | draft |

> **Sahiplik notu:** commands/queries ayrımı CQRS'e benzer ama CQRS olduğu
> **iddia edilmez** — TASARIM İDDİASI etiketiyle durur (§7/3).

## §4 Teknoloji / ekosistem (YALNIZCA DOĞRULANMIŞ KANIT — 2026-10-12)

| # | İddia (kanıtlanmış) | Kanıt (URL / uç nokta) | Tarih | Bu domain'de kullanımı |
|---|---|---|---|---|
| 1 | Jellyfin açık kaynak medya sunucusu: GitHub 55732★ · GPL-2.0 · .NET · kökeni Emby 3.5.2 fork'u (2018-12-09) | https://github.com/jellyfin/jellyfin | 2026-10-12 | Referans ürün: sunucu+istemci yapısı (server-runtime ↔ web/desktop) — K011 + K015 ortak |
| 2 | Spotify Web servisleri Authorization Code + PKCE akışını kullanır; token uç noktası accounts.spotify.com/api/token | https://accounts.spotify.com/api/token | 2026-10-12 | web/desktop oturum açma akışının PKCE ile tasarlanması (public client) |
| 3 | Apple Music API, ES256 ile imzalanmış ve kid (key id) taşıyan JWT developer token ister | ⚠️ VERIFICATION REQUIRED (görev beyanında URL yok) | 2026-10-12 | Apple Music çağrıları için developer token akışı |

**§4'e girmeyen hiçbir dil, framework, paket veya sürüm BU BELGEDE YAZILMAZ.**

| Aranıp da kanıt bulunmayan (UNKNOWN — ad yazılmaz) | Durum |
|---|---|
| Uygulama çekirdeğinin dili / çatısı | ⚠️ UNKNOWN |
| Paket adları ve sürümleri | ⚠️ UNKNOWN |
| Barındırma/altyapı (server-runtime) | ⚠️ UNKNOWN |
| Build/paketleme zinciri | ⚠️ UNKNOWN |

## §5 Bağımlılık özeti (tam metin: application-bagimliliklar.md)

### §5.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| K009-security | Politika/İmza kuralları | TASARIM İDDİASI |
| K010-identitiy | Oturum, rol, cihaz kimliği | TASARIM İDDİASI |
| K012-services | Servis çağrıları | TASARIM İDDİASI |
| K013-integraion | Dış API kontratları | TASARIM İDDİASI |
| K014-network | Taşıma | TASARIM İDDİASI |
| K015-media | Medya verisi / oynatma durumu | TASARIM İDDİASI |

### §5.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| K017-observability | Telemetri/metrik toplama | TASARIM İDDİASI |
| K019-testing | Test hedefi | TASARIM İDDİASI |
| Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

### §5.3 Graf özeti (özet iddia — DAG denetimi 2026-10-12)

~~~text
K011-application
├─→ K009-security · K010-identitiy        (kimlik + politika)
├─→ K012-services  ─→ K015-media ─→ K013-integraion
├─→ K013-integraion → K014-network
└─→ K014-network · K015-media             (taşıma + medya)
K011'e geri dönüş oku: YOK (döngü yok)
~~~

## §6 Doğrulama özeti

| # | Ölçüt | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.11 ile birebir | metin karşılaştırma (10/10) | ✅ verified |
| 2 | Master dosyası adı registry §5/11 ile birebir | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan | sayım | ✅ verified |
| 4 | §4 satırları yalnız 2026-10-12 web kanıtı | satır-başı kanıt sütunu | ✅ verified (1 satır URL eksik ⚠️) |
| 5 | 5 dosyalık MD seti + 10 çocuk index'i | disk sayımı (2026-10-12) | ✅ verified |
| 6 | Kademe-3 (bounded-context) kapsamı | — | ⏳ faz C (ADR-097 K4) |
| 7 | Uygulama çalışma zamanı teknolojisi | kanıt toplanmadı | ⏳ tur-2 → §7/2 |

## §7 Açık sorular

1. web/desktop/electron/local-runtime/server-runtime — beşi arasındaki **kesin** sınırlar → ⚠️ VERIFICATION REQUIRED
2. Uygulama çekirdeğinin dili/çatısı (kanıt yok) → ⚠️ VERIFICATION REQUIRED
3. commands/queries = CQRS mi, sadece klasör adı mı? → TASARIM İDDİASI
4. application-services ↔ K012-services kim hangi servisi tutar? → ⚠️ VERIFICATION REQUIRED
5. application-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED
6. Electron masaüstü mü, desktop'ın paketleme yolu mu? → ⚠️ VERIFICATION REQUIRED
7. PKCE/Jellyfin kanıtları referans ürün analizi mi, CoreMusic kararı mı? → onay bekliyor

## §8 Kaynaklar

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 ağaç (§2.11 · §3 · §4.1–4.3) | G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md | Çocuk listesi + zorunlu alanlar |
| Registry | .ai/architecture/index.md §5/11 · §8 | Master adı + isimlendirme |
| Karar | .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md §5 | Rol sözlüğü, desen |
| Jellyfin | https://github.com/jellyfin/jellyfin | 2026-10-12 kanıtı |
| Spotify token uç noktası | https://accounts.spotify.com/api/token | 2026-10-12 kanıtı |
| Apple Music JWT (ES256 + kid) | ⚠️ URL yok | 2026-10-12 beyanı |

---

**Status:** draft · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-12
