---
title: "K012-services — Services Master"
type: architecture
category: k-space-v3
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K012-services
role: master
authority: "SSOT: v2 §2.12 + .ai/architecture/index.md §5/12"
related: ["services-bagimliliklar.md", "services-dogrulama.md", "index.md"]
---

# K012-services — services-master.md

> Bu dosya v2 §2.12'de adı geçen **master** dosyasıdır (ad kaynaktan birebir,
> ADR-097 K2/K3). Yeni rol dosyaları: services-bagimliliklar.md ·
> services-dogrulama.md.

## §1 Rol (ELI15)

v2 §2.12: *"Arka planda iş yapan küçük uygulamalar — 'çalan parça servisi',
'kullanıcı servisi'."*

K012, sistemin **iş yapan sırtıdır**. Arayüz göstermez; istek geldiğinde işi
yapar, sonucu döner, gerekirse zamanlar. Üç iş yapar:

1. **Çalıştır** — çalma, katalog, kullanıcı, cihaz işlerini yürütür (ilk 8 çocuk).
2. **Zamanla** — arka plana atılmış işleri kuyruğa alıp koşar (background-jobs).
3. **Tanımla** — servislerin birbirine ne söylediğini yazar (service-contracts).

Ne yapmaz: arayüz çizmez (K011), kalıcı veri şemasına karar vermez (K007-data),
dış API'yi kendisi protokolle konuşmaz (K013-integraion — integration-service
yalnız oradan gelen kontratı kullanır), ses sinyali işlemez (K003-audio/K004-dsp).

## §2 Kapsam / Sınır

### §2.1 Kapsam içinde (bu odanın malı)

- Servis süreçleri: music · media · playback · audio · user · device · ai · integration
- Zamanlanmış iş: background-jobs
- Servisler arası sözleşme: service-contracts

### §2.2 Kapsam dışında (kimin?) — sınır tablosu

| Konu | Ait olduğu düğüm | Not |
|---|---|---|
| Uygulama akışı / arayüz | K011-application | K011 yalnız çağırır |
| Dış API protokolü, adaptör, hata | K013-integraion | integration-service K013'ün kontratını kullanır |
| Ağ taşıması ayrıntısı | K014-network | K012 yalnız kullanır |
| Kitaplık/metadata/akış dosyaları | K015-media | Servis veriyi okur, sahibi değildir |
| İş kuralı / domain modeli | K008-domain | Servis kuralı uygular, üretmez |
| Kalıcılık ve şema | K007-data | ORM/SQL kararı burada verilmez |
| ML modeli / ajan altyapısı | K006-yapay-zeka / K016-AI | ai-service yalnız sunar (hangisi ⚠️) |
| Servislerin dağıtımı/dağıtım hattı | K018-devops | |
| Servis test altyapısı | K019-testing | K012 yalnız testini yazar |

## §3 Kademe-2 çocuk tablosu (v2 §2.12 birebir — 10 çocuk)

| # | Çocuk | ELI15 rol (TASARIM İDDİASI) | Ne içerir — kapsam notu | Durum |
|---|---|---|---|---|
| 1 | music-service | Müzik işleri servisi | Katalog sorgusu, arama, sıralama (ayrıntı ⚠️) | draft |
| 2 | media-service | Medya varlığı servisi | Dosya/kaynak varlığına dair işler (ayrıntı ⚠️) | draft |
| 3 | playback-service | Çalma durumu servisi | Sıra, konum, oynatma komutu durumu | draft |
| 4 | audio-service | Ses işleri servisi | K003-audio ile sınır belirsiz ⚠️ | draft |
| 5 | user-service | Kullanıcı servisi | Profil, tercih, abonelik (ayrıntı ⚠️) | draft |
| 6 | device-service | Cihaz servisi | Cihaz kaydı, durum, yetki (K010 ile sınır ⚠️) | draft |
| 7 | ai-service | YZ servisi | K006-yapay-zeka mı K016-AI mı → ⚠️ | draft |
| 8 | integration-service | Dış bağlantı servisi | K013 kontratlarını çağıran servis | draft |
| 9 | background-jobs | Arka plan işleri | Zamanlanmış/kuyruk işleri (motor ⚠️) | draft |
| 10 | service-contracts | Servis sözleşmeleri | Mesaj/DTO biçimi; evrensel spec iddiası §4/2 | draft |

> **Sahiplik notu:** playback-service (K012) ile K015/playback, ai-service ile
> K006/K016 arasında **kapsam çakışması riski** var — §7/1–§7/2 açık sorudur.

## §4 Teknoloji / ekosistem (YALNIZCA DOĞRULANMIŞ KANIT — 2026-10-12)

| # | İddia (kanıtlanmış) | Kanıt (URL / uç nokta) | Tarih | Bu domain'de kullanımı |
|---|---|---|---|---|
| 1 | Servisler arası evrensel sözleşme için hub-and-spoke modeli öneriliyor: tek merkez (hub) evrensel Playlist/Track spec'i tanımlar, her yan (spoke) buna bağlanır (open-playlist-engine yaklaşımı) | ⚠️ VERIFICATION REQUIRED (görev beyanında URL yok) | 2026-10-12 | service-contracts çocuk yapısının tasarımı |
| 2 | Kanonik (canonical) model örneği Protobuf ile kurulabilir (portify benzeri yaklaşım) | ⚠️ VERIFICATION REQUIRED (görev beyanında URL yok) | 2026-10-12 | service-contracts mesaj biçimi seçeneği |

**§4'e girmeyen hiçbir dil, framework, paket veya sürüm BU BELGEDE YAZILMAZ.**

| Aranıp da kanıt bulunmayan (UNKNOWN — ad yazılmaz) | Durum |
|---|---|
| Servis çalışma zamanı (dil/çatı) | ⚠️ UNKNOWN |
| Mesaj kuyruğu / zamanlayıcı teknolojisi | ⚠️ UNKNOWN |
| Servisler arası taşıma biçimi (gRPC/REST/olay — hangisi) | ⚠️ UNKNOWN |
| Protobuf mü JSON mu kararı | ⚠️ UNKNOWN (yalnız seçenek var) |

> Her iki §4 iddiası da **seçenek/yaklaşım**tır; CoreMusic kararı **değildir**.
> Karar için ADR gerekir (§7/5).

## §5 Bağımlılık özeti (tam metin: services-bagimliliklar.md)

### §5.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| K006-yapay-zeka / K016-AI | ai-service'in arkası | TASARIM İDDİASI + ⚠️ (hangisi) |
| K007-data | Kalıcı veri erişimi | TASARIM İDDİASI |
| K008-domain | İş kuralı | TASARIM İDDİASI |
| K010-identitiy | Kimlik doğrulama | TASARIM İDDİASI |
| K013-integraion | Dış kontratlar | TASARIM İDDİASI |
| K014-network | Taşıma | TASARIM İDDİASI |
| K015-media | Medya verisi | TASARIM İDDİASI |

### §5.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| K011-application | Uygulama servis çağrıları | TASARIM İDDİASI |
| K019-testing | Servis testleri | TASARIM İDDİASI (sahiplik ⚠️) |
| Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

### §5.3 Graf özeti (özet iddia — DAG denetimi 2026-10-12)

~~~text
K011-application → K012-services
K012-services
├─→ K008-domain · K007-data          (kural + kalıcılık)
├─→ K010-identitiy · K006/K016-AI    (kimlik + YZ arkası)
├─→ K013-integraion → K014-network   (dış dünya + taşıma)
└─→ K015-media                       (medya verisi)
K012'ye geri dönüş: yalnız used_by okları (döngü yok)
~~~

## §6 Doğrulama özeti

| # | Ölçüt | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.12 ile birebir | metin karşılaştırma (10/10) | ✅ verified |
| 2 | Master dosyası adı registry §5/12 ile birebir | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan | sayım | ✅ verified |
| 4 | §4 satırları yalnız 2026-10-12 beyanına dayanıyor | satır-başı kanıt sütunu | ✅ verified (2 satır URL eksik ⚠️) |
| 5 | 5 dosyalık MD seti + 10 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 6 | ai-service'in bağlı olduğu düğüm | — | ❌ belirsiz → ⚠️ (§7/1) |
| 7 | Kademe-3 (bounded-context) kapsamı | — | ⏳ faz C (ADR-097 K4) |

## §7 Açık sorular

1. ai-service → K006-yapay-zeka mı, K016-AI mı? (registry §6 kapsam ayrımı) → ⚠️ VERIFICATION REQUIRED
2. playback-service (K012) ↔ K015/playback kimin? → ⚠️ VERIFICATION REQUIRED
3. audio-service ↔ K003-audio sınırı (servis mi, motor mu)? → ⚠️ VERIFICATION REQUIRED
4. service-contracts evrensel Playlist/Track spec'ini mi taşıyor? → TASARIM İDDİASI
5. hub-and-spoke + Protobuf seçenekleri için ADR var mı? → onay bekliyor
6. background-jobs motoru (kuyruk/zamanlayıcı) nedir? → ⚠️ UNKNOWN
7. device-service ↔ K010-identitiy/device-identity sahipliği → ⚠️ VERIFICATION REQUIRED
8. K012 ile K015 arasındaki veri yönlendirmesi tek yönlü mü? → ⚠️ VERIFICATION REQUIRED

## §8 Kaynaklar

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 ağaç (§2.12 · §3 · §4.1–4.3) | G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md | Çocuk listesi + zorunlu alanlar |
| Registry | .ai/architecture/index.md §5/12 · §6 · §8 | Master adı + K006/K016 kapsam ayrımı |
| Karar | .ai/.decisions/draft/ADR-097-…-bant-rezervasyonu.md §5 | Rol sözlüğü, desen |
| hub-and-spoke Playlist/Track (open-playlist-engine yaklaşımı) | ⚠️ URL yok | 2026-10-12 beyanı |
| Protobuf canonical model (portify benzeri) | ⚠️ URL yok | 2026-10-12 beyanı |

---

**Status:** draft · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-12
