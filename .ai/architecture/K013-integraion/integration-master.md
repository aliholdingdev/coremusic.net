---
title: "K013-integraion — Integration Master"
type: architecture
category: k-space-v3
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K013-integraion
role: master
authority: "SSOT: v2 §2.13 + .ai/architecture/index.md §5/13"
related: ["integraion-bagimliliklar.md", "integraion-dogrulama.md", "index.md"]
---

# K013-integraion — integration-master.md

> Bu dosya v2 §2.13'de adı geçen **master** dosyasıdır: adı kaynakta
> **integration-master.md** olarak geçer (birebir korunur), klasör adı ise
> **K013-integraion** (imla dokunulmaz — v2 §3 KURAL 1). Yeni rol dosyaları
> düğüm adından türetildi: integraion-bagimliliklar.md · integraion-dogrulama.md.

## §1 Rol (ELI15)

v2 §2.13: *"Dış dünya ile köprü — API konuşmaları, adaptörler, üçüncü taraf sistemler."*

K013, CoreMusic'in **dış dünyaya tek kapısı**dır. Dört iş yapar:

1. **Sözleş** — dış API'nin istek/yanıtını yazar (api-contracts · event-contracts).
2. **Çevir** — iç model ile dış sistem arasında adaptör kurar
   (adapters · protocol-adapters · external-systems).
3. **Eşitle** — dış sistemle veriyi senkronlar (synchronization).
4. **Sınırlandır** — hatayı, tekrar-denemeyi ve kota/rate sınırını yönetir
   (error-handling) · bunu ispatlar (integration-testing).

Ne yapmaz: iç servis işi yapmaz (K012), arayüz göstermez (K011), ağ paketinin
ayrıntısını uydurmaz (K014 yalnız taşır), kimlik bilgisini saklamaz (K007/K010),
politika/kripto üretmez (K009'un işidir).

## §2 Kapsam / Sınır

### §2.1 Kapsam içinde (bu odanın malı)

- Dış kontratlar: api-contracts · event-contracts
- Çeviriler: adapters · protocol-adapters
- Dış sistem envanteri: external-systems
- Eşitleme ve hata sınırı: synchronization · error-handling
- Entegrasyon testi: integration-testing

### §2.2 Kapsam dışında (kimin?) — sınır tablosu

| Konu | Ait olduğu düğüm | Not |
|---|---|---|
| Çağıran servis (dış çağrıyı başlatan) | K012-services (integration-service) | K013 yalnız kontrat/çeviri sunar |
| Kullanıcı akışı (dış servisi başlatan ekran) | K011-application | |
| Ağ taşıması, TLS, soket | K014-network | K013 yalnız HTTP vb. kullanır |
| Anahtar/secret saklama, imza | K009-security | Politika orada |
| OAuth/PKCE oturumu, kullanıcı kimliği | K010-identitiy | |
| Kalıcı veri şeması | K007-data | synchronization sonucu yazar, şema K007'de |
| Medya dosyası/akış | K015-media | Dış katalog senkronu sınırı ⚠️ |
| Servis dağıtım/hat sorumluluğu | K018-devops | |

## §3 Kademe-2 çocuk tablosu (v2 §2.13 birebir — 8 çocuk)

| # | Çocuk | ELI15 rol (TASARIM İDDİASI) | Ne içerir — kapsam notu | Durum |
|---|---|---|---|---|
| 1 | api-contracts | Dış API sözleşmesi | İstek/yanıt tipleri, sürüm (sürüm stratejisi ⚠️) | draft |
| 2 | adapters | Üçüncü taraf adaptörleri | Sistem başına bir adaptör (envanter §3'ün adayları) | draft |
| 3 | external-systems | Dış sistem envanteri | Hangi sistem, ne durumda — kayıtlar §4'te | draft |
| 4 | event-contracts | Dış olay sözleşmesi | Dış gelen olayların biçimi (webhook mu ⚠️ UNKNOWN) | draft |
| 5 | protocol-adapters | Protokol dönüşüm köprüleri | Farklı protokol/şema arası çeviriler | draft |
| 6 | synchronization | Dış veri senkronizasyonu | K015/synchronization ile sınır ⚠️ (§7/2) | draft |
| 7 | error-handling | Hata ve sınır yönetimi | Hata sınıflandırma, tekrar-dene, rate limit (biçim ⚠️) | draft |
| 8 | integration-testing | Entegrasyon testleri | Sahte/canlı çevre testi (ortak test altyapısı K019) | draft |

> **Sahiplik notu:** Dış API'yi **çağıran** servis K012'dedir (integration-service);
> bu domain kontrat ve adaptörü tutar. İki tarafın yönü §5.3'te.

## §4 Teknoloji / ekosistem (YALNIZCA DOĞRULANMIŞ KANIT — 2026-10-12)

| # | İddia (kanıtlanmış) | Kanıt (URL / uç nokta) | Tarih | Bu domain'de kullanımı |
|---|---|---|---|---|
| 1 | Spotify Web servisleri Authorization Code + PKCE akışı kullanır; token uç noktası accounts.spotify.com/api/token | https://accounts.spotify.com/api/token | 2026-10-12 | external-systems: Spotify entegrasyonu için token akışı (PKCE = public client) |
| 2 | Apple Music API, ES256 ile imzalanmış ve kid (key id) taşıyan JWT developer token ister | ⚠️ VERIFY REQUIRED (görev beyanında URL yok) | 2026-10-12 | external-systems: Apple Music için developer token akışı |
| 3 | YouTube Music için resmi public API yoktur; TOS riski taşır | ⚠️ VERIFY REQUIRED (görev beyanında URL yok) | 2026-10-12 | external-systems: kapsam dışı/beklemede kaydı |
| 4 | Evrensel Playlist/Track sözleşmesi hub-and-spoke modeliyle kurulabilir (open-playlist-engine yaklaşımı) | ⚠️ VERIFY REQUIRED (görev beyanında URL yok) | 2026-10-12 | api-contracts ile service-contracts (K012) sınırı |

**§4'e girmeyen hiçbir dil, framework, paket veya sürüm BU BELGEDE YAZILMAZ.**

| Aranıp da kanıt bulunmayan (UNKNOWN — ad yazılmaz) | Durum |
|---|---|
| HTTP istemci kütüphanesi / dil | ⚠️ UNKNOWN |
| OAuth kütüphanesi (PKCE uygulaması) | ⚠️ UNKNOWN |
| Belge/şema formatı (OpenAPI vb.) | ⚠️ UNKNOWN |
| Olay taşıma biçimi (webhook/akış — hangisi) | ⚠️ UNKNOWN |
| Rate limit / retry politika değerleri | ⚠️ UNKNOWN |

> İddia 2–4 **URL'siz beyan** sınıfındadır (S3): kaydedildi, kanıtlanmadı.
> İddia 3 kesin olumlu/olumsuz **karar değildir** — "resmi API yok" iddiasıdır.

## §5 Bağımlılık özeti (tam metin: integraion-bagimliliklar.md)

### §5.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| K007-data | Senkron sonucunun saklanması | TASARIM İDDİASI |
| K008-domain | Kontrat tipleri | TASARIM İDDİASI |
| K009-security | Anahtar/imza/güvenlik | TASARIM İDDİASI |
| K010-identitiy | OAuth/PKCE kimlik doğrulama | TASARIM İDDİASI |
| K014-network | Taşıma (HTTP vb.) | TASARIM İDDİASI |

### §5.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| K011-application | Kullanıcı akışının dış çağrısı | TASARIM İDDİASI |
| K012-services | integration-service kontrat tüketimi | TASARIM İDDİASI |
| K015-media | Dış katalog senkronu (sınır ⚠️) | TASARIM İDDİASI |
| Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

### §5.3 Graf özeti (özet iddia — DAG denetimi 2026-10-12)

~~~text
K011-application · K012-services · K015-media
        └─→ K013-integraion (dış kapıyı kullanır)
K013-integraion
├─→ K014-network   (taşıma)  ─→ dış dünya
├─→ K010-identitiy (OAuth/PKCE) · K009-security (anahtar/imza)
├─→ K008-domain    (kontrat tipleri)
└─→ K007-data      (senkron sonucu)
K013'ün K012'ye geri dönüşü: YOK (yalnız used_by oku) → döngü yok
~~~

## §6 Doğrulama özeti

| # | Ölçüt | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.13 ile birebir | metin karşılaştırma (8/8) | ✅ verified |
| 2 | Master dosyası adı registry §5/13 ile birebir (integration-master.md) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan | sayım | ✅ verified |
| 4 | §4 satırları yalnız 2026-10-12 beyanına dayanıyor | satır-başı kanıt sütunu | ✅ verified (3 satır URL eksik ⚠️) |
| 5 | 5 dosyalık MD seti + 8 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 6 | YouTube Music iddiası (resmi public API yok) | — | ⚠️ VERIFY REQUIRED (kanıt yok) |
| 7 | Kademe-3 (bounded-context) kapsamı | — | ⏳ faz C (ADR-097 K4) |

## §7 Açık sorular

1. YouTube Music resmi public API yok iddiası doğru mu, TOS riski nasıl yönetilir? → ⚠️ VERIFY REQUIRED
2. synchronization sahipliği: K013 mü, K015 mi? (aynı ad iki domain'de) → ⚠️ VERIFICATION REQUIRED
3. Dış API'yi **çağıran** taraf K012 mi (integration-service), yoksa K013 mü? → ⚠️ VERIFICATION REQUIRED
4. PKCE akışı Spotify'a özel mi, CoreMusic genel politikası mı? → TASARIM İDDİASI
5. Apple Music JWT (ES256+kid) kim üretir — uygulama mı, sunucu mu? → ⚠️ VERIFICATION REQUIRED
6. event-contracts taşıma biçimi (webhook/olay akışı) nedir? → ⚠️ UNKNOWN
7. hub-and-spoke evrensel Playlist/Track spec'i K012 mi K013 mü taşır? → TASARIM İDDİASI
8. external-systems envanterinin güncel kapsamı (hangi sistem dahil)? → ⚠️ VERIFICATION REQUIRED

## §8 Kaynaklar

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 ağaç (§2.13 · §3 · §4.1–4.3) | G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md | Çocuk listesi + zorunlu alanlar |
| Registry | .ai/architecture/index.md §5/13 · §8 | Master adı + isimlendirme |
| Karar | .ai/.decisions/draft/ADR-097-…-bant-rezervasyonu.md §5 | Rol sözlüğü, desen |
| Spotify token uç noktası | https://accounts.spotify.com/api/token | 2026-10-12 kanıtı |
| Apple Music JWT (ES256 + kid) | ⚠️ URL yok | 2026-10-12 beyanı |
| YouTube Music (resmi public API yok) | ⚠️ URL yok | 2026-10-12 beyanı |
| hub-and-spoke Playlist/Track (open-playlist-engine) | ⚠️ URL yok | 2026-10-12 beyanı |

---

**Status:** draft · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-12
