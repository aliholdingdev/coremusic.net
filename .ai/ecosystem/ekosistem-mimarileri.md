---
title: "Ekosistem Mimarileri — Streaming Devi Pipeline Dersleri"
type: guide
category: ecosystem
version: 1.0.0
description: "Spotify microservice hesap/yükleme/arama/playlist kullanıcı servisleri ve transcode pipeline'ı, Apple Music ve YouTube Music mimarileri — CoreMusic K8/K9/K14/K15 katmanlarına server+client ikili rol ve session çıkarımlarıyla entegrasyon planı."
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24); Spotify Engineering (account/upload/search/playlist/user microservices + transcode pipeline), Apple Music mimarisi, YouTube Music mimarisi, Koel 17.175★ MIT, Ampache 3.809★ AGPL-3.0
status: active
authority: "Streaming devi mimarileri; CoreMusic sunucu/istemci katmanlarında K8/K9/K14/K15 referansıdır — AGPL kod asla kopyalanmaz."
updated: 2026-09-24
---

# CoreMusic — Ekosistem Mimarileri — Streaming Devi Pipeline Dersleri

> **Kapsam:** Bu belge, `.ai/ecosystem/` altındaki 6 ekosistem dokümanından 5.'sidir.
> Kapsadığı K katmanları: **K8 (sunucu servis mimarisi), K9 (streaming/servis mantığı), K14 (izleme/telemetri/servis sözleşmeleri), K15 (istemci/streaming UI)**.
> Doğrulama tarihi: **2026-09-24 (exa web doğrulaması)**.
> Yeni web araştırması yok; sağlanan doğrulanmış exa sonuçları `.ai/architecture/github-referanslari.md` ve `.ai/CLAUDE.md` ile çapraz bağlanır.

**⚠️ UYARI — Lisans Kuralı (CLAUDE.md §4, §20, §21):**
- **Ampache AGPL-3.0 — kodu CoreMusic'e asla kopyalanmaz** (sadece fikir/akış).
- **Koel MIT** — referans için uygundur, atıf korunur.
- **Spotify/Apple/YouTube mimarileri kapalı kaynaktır** — yalnızca yayınlanan **mimari dersleri** alınır, kod yoktur.

**Template hizası (Guardrail #16):** docs-md-template.md §1–§7 + §4.1 Şablon-Önce bloğu verbatim korunmuştur.

> 2026-09-24 — CoreMusic Ekosistem Serisi (5/6)

---

## Table of Contents

1. [§1 Amaç](#1-amaç)
2. [§2 Kapsam](#2-kapsam)
3. [§3 Mimari](#3-mimari)
4. [§4 Kurallar](#4-kurallar)
5. [§5 Workflow](#5-workflow)
6. [§6 Doğrulama](#6-doğrulama)
7. [§7 Referanslar](#7-referanslar)

---

## §1 Amaç

**Amaç:** Spotify, Apple Music ve YouTube Music gibi platformların yayınlanmış mimari derslerini (microservice servis ayrımı, upload→transcode→serve pipeline'ı, oturum/teslimat modeli) CoreMusic'in K8/K9/K14/K15 katmanlarına aktarmak; server+client ikili rolün oturum (session) etkilerini çıkarmak.

**Kapsam:**

| Kapsar | Kapsamaz |
|--------|----------|
| Spotify microservice servisleri: account, upload, search, playlist, user (K8) | Spotify iç kodu (kapalı kaynak) |
| Spotify transcode pipeline'ı dersi (K9) | Gerçek codec implementasyonu (bkz. muzik-streaming-sunuculari.md) |
| Apple Music mimari dersleri (K8, K15) | Apple API entegrasyonu (ayrı karar) |
| YouTube Music mimari dersleri (K8, K9) | YouTube veri kullanımı / API politikaları |
| Server+client ikili rol ve session çıkarımları (K8↔K15) | Kimlik doğrulama kodu (planlama) |
| Telemetri/izleme sözleşmeleri (K14) | Grok/AGPL kod incelemesi (ayrı belge) |
| Streaming sunucu deneyimi çapraz bağ (Koel MIT, Ampache AGPL) | Sunucu kurulumu (bkz. muzik-streaming-sunuculari.md) |

**Hedef Kullanıcı:**
- CoreMusic sunucu tarafı (K8) microservice sınırlarını çizen backend mimarı
- Streaming pipeline (K9) ve codec kararları sahibi mühendis
- Servis izleme/telemetri (K14) kuran SRE
- İstemci/streaming UI (K15) geliştiren frontend mühendisi

**Bağlantılar:**
- `.ai/architecture/index.md` → K0–K20 SSOT (K8, K9, K14, K15)
- `.ai/ecosystem/muzik-streaming-sunuculari.md` → Sunucu deneyimi (Koel MIT / Ampache AGPL / Mopidy)
- `.ai/ecosystem/service-integration.md` → Servis entegrasyon (mevcut, değişmedi)
- `.ai/CLAUDE.md` §4 / §20 / §21 → Lisans kuralları

---

## §2 Kapsam

### §2.1 CoreMusic İhtiyaçları

| İhtiyaç | K Katmanı | Streaming Devi Karşılığı |
|---------|-----------|--------------------------|
| Servis sınırları (account, user, playlist...) | **K8** | Spotify microservice ayrımı |
| Upload → transcode → serve akışı | **K9** | Spotify transcode pipeline |
| Arama/keşif servisi | **K8, K9** | Spotify search service |
| İzleme/ölçüm/sözleşme | **K14** | Pipeline gözlemlenebilirlik dersi |
| İstemci oturum + akış UI | **K15** | Apple/YouTube istemci dersleri |
| Server+client aynı üründe | **K8↔K15** | CoreMusic'in kendisi hem sunucu hem istemci |
| Lisans temiz sunucu referansı | **K8, K9** | Koel MIT (✅), Ampache AGPL (❌ kopya) |

### §2.2 Mevcut Durum (CoreMusic)

- **Mevcut sunucu deneyimi:** `.ai/ecosystem/service-integration.md` (mevcut, değişmedi) + Koel/Ampache/Mopidy analizi (dosya 2/6).
- **Eksik olan:** servis sınırlarının resmi K8'e oturtulması; transcode pipeline'ının K9'a şeması; K14 telemetri sözleşmesi; K15 oturum modeli.
- **Kısıt:** Ampache AGPL — kod yok, yalnız fikir (CLAUDE.md §4). Spotify/Apple/YouTube kapalı kaynak — yalnız mimari ders.

### §2.3 Boşluk Analizi

| Boşluk | Etkilenen K | Önem | Çözüm |
|---------|-------------|------|-------|
| Servis sınırları tanımsız | **K8** | **Yüksek** | §3.1 Spotify 5 servis dersi |
| Transcode pipeline şeması yok | **K9** | **Yüksek** | §3.2 pipeline dersi + §5.1 şema |
| Telemetri sözleşmesi yok | **K14** | Orta | §3.6 ders→K matrisi |
| Oturum modeli belirsiz (server+client) | **K15** | **Yüksek** | §3.7 server+client dersi |
| Apple/YouTube dersleri işlenmedi | K8, K15 | Orta | §3.3-§3.4 |
| AGPL sızıntısı riski | K8/K9 | **Kritik** | §3.6.3 kırmızı çizgi |

---

## §3 Mimari

### §3.1 Spotify Microservice Servisleri (K8)

**Doğrulanmış olgu (2026-09-24 exa):** Spotify mühendislik yayınları — product/infrastructure mikroservisleri arasında **account, upload, search, playlist ve user servisleri** ayrımı; her servis kendi veri deposu ve yaşam döngüsüyle (database-per-service deseni).

| Spotify Servisi | Sorumluluk | CoreMusic K8 Karşılığı |
|-----------------|------------|------------------------|
| **account** | Hesap, abonelik, kimlik | K8 — hesap servisi (CoreMusic üyelik) |
| **upload** | Katalog yükleme kabulü | K8 — upload gateway (K9'a devreder) |
| **search** | Arama indeksi/keşif | K8/K9 — arama servisi |
| **playlist** | Kullanıcı listeleri | K8 — playlist servisi (K15 UI besler) |
| **user** | Profil, tercih | K8 — user servisi |

**Yapı dersi (K8):** Spotify, tek monolit yerine **küçük, tek sorumlu servisler** kullanır; servisler arası sözleşme (API) ve veri izolasyonu (database-per-service) sayesinde bağımsız ölçeklenir ve bağımsız bozulur (failure isolation). CoreMusic K8'de aynı ayrım: account/user/playlist/search/upload beş servis iskeleti.

**Entegrasyon (§3.6.5):** K8 servis iskeletleri §5.2 kontrat taslağında; her servis K14 üzerinden izlenir (§3.6).

---

### §3.2 Spotify Transcode Pipeline (K9)

**Doğrulanmış olgu (2026-09-24 exa):** Spotify'ın **transcode pipeline**'ı: ham yükleme → per-format/bitrate dönüştürme → CDN/teslimat. Yükleme servisi (§3.1) işi transcode kuyruğuna devreder; transcode çıktıları teslimat katmanına yazılır.

~~~text
[K8 upload] ──> queue ──> [K9 transcode: ham → Ogg/MP3/AAC bitrates] ──> store ──> CDN/serve
                 │
                 └──> K14 telemetri (kuyruk derinliği, transcode süresi, hata oranı)
~~~

**Yapı dersi (K9):**

1. **Asenkron kuyruk:** transcode senkron istekte değil, kuyrukta çalışır — upload latency'si düşük kalır.
2. **Çoklu-bitrate çıktısı:** tek kaynaktan birçok bitrates varyant üretilir (adaptive streaming hazır).
3. **Idempotency:** aynı yükleme iki kez işlenirse aynı çıktı (hash-keyed job).
4. **Gözlemlenebilirlik:** kuyruk + iş süresi K14 metrikleri (§3.6).

**Entegrasyon:** CoreMusic K9 transcode şeması §5.1'de; codec seçimi `.ai/ecosystem/muzik-streaming-sunuculari.md` §5.4'e devredilir (burada yalnız akış dersi).

---

### §3.3 Apple Music Mimari Dersleri (K8, K15)

**Doğrulanmış olgu (2026-09-24 exa):** Apple Music mimarisi — cihaz tarafı istemci + iCloud kütüphane senkronu; DRM (FairPlay) teslimat; ekosistem içi oturum paylaşımı (Apple ID).

| Apple Dersi | CoreMusic K | Uygulama |
|-------------|-------------|----------|
| Apple ID tabanlı oturum | **K15** | Tek oturum, çok cihaz — session modeli §3.7 |
| Kütüphane senkronu | **K15, K8** | Yerel kütüphane ↔ sunucu senkronu sözleşmesi |
| DRM teslimat zinciri | **K9** | Lisanslı içerik akışı (CoreMusic'e gerekmezse atlanır) |
| Cihazlar arası geçiş (handoff) | **K15** | Akış cihaz değiştirme — oturum devri |

**Entegrasyon:** K15 oturum modeli §3.7'de; K8 kütüphane servisi ile birlikte. Apple kodu yok — yalnız published mimari davranış.

### §3.4 YouTube Music Mimari Dersleri (K8, K9)

**Doğrulanmış olgu (2026-09-24 exa):** YouTube Music — YouTube altyapısı üzerine kurulmuş servis: öneriler/recommendation pipeline, varyant adaptif teslimat (DASH benzeri), Google hesap oturumu.

| YouTube Dersi | CoreMusic K | Uygulama |
|---------------|-------------|----------|
| Altyapıyı yeniden kullanma (mevcut platform üzerine servis) | **K8** | CoreMusic mevcut K0-K7 üzerine K8 servisleri ekler |
| Recommendation pipeline | **K8, K9** | Keşif/öneri servisi (opsiyonel, arama servisiyle ilişkili) |
| Adaptif varyant teslimat | **K9, K15** | bitrate değişimi akışı (transcode çıktısı §3.2) |
| Google hesabı oturumu | **K15** | §3.7 oturum modeliyle çapraz ders |

**Entegrasyon:** K9 adaptif teslimat dersi §5.1 şemasında; öneri servisi düşük öncelikli (§3.6.4 S4).

---

### §3.5 Sunucu Deneyimi Çapraz Bağ (Koel MIT / Ampache AGPL / Mopidy)

| Sunucu | Lisans (Doğrulanmış) | Bu Belgedeki Rol |
|--------|----------------------|-------------------|
| Koel 17.175★ | **MIT** | ✅ K8 servis iskeleti referansı — kod incelenebilir, atıfla |
| Ampache 3.809★ | **AGPL-3.0** | ❌ Sadece fikir — kod asla kopyalanmaz (CLAUDE.md §4) |
| Mopidy | ⚠️ VERIFICATION REQUIRED | Bekleme — detay: muzik-streaming-sunuculari.md |

> Yıldız kaydı: exa 2026-09-24 → Koel 17.175★ / Ampache 3.809★; github-referanslari.md (2026-09-20) → 17.195★ / 3.793★ — iki kayıt da tarihli, çakışma değil (star drift).

### §3.6 Matrisler, Copy/Don't Copy ve Entegrasyon

### §3.6.1 Lisans / Kaynak Matrisi

| Kaynak | Lisans/Doğum | Kopyalanabilir mi? | Kullanım Şekli |
|--------|--------------|-------------------|----------------|
| Koel 17.175★ | MIT | ✅ Atıfla | K8 servis/kütüphane organizasyonu referansı |
| Ampache 3.809★ | AGPL-3.0 | ❌ **HİÇBİR ZAMAN kod** | Sadece fikir/akış (CLAUDE.md §4) |
| Mopidy | ⚠️ VERIFICATION REQUIRED | Bekleme | K9 kuyruk fikri (lisans doğrula) |
| Spotify mimarisi | Kapalı kaynak (yayın dersi) | Kod YOK — mimari ders OK | §3.1, §3.2 |
| Apple Music mimarisi | Kapalı kaynak | Kod YOK — davranış dersi OK | §3.3 |
| YouTube Music mimarisi | Kapalı kaynak | Kod YOK — mimari ders OK | §3.4 |

### §3.6.2 Ders → K Katmanı Eşlemesi

| # | Ders | Kaynak | K Katmanı |
|---|------|--------|-----------|
| 1 | Database-per-service mikroservis ayrımı (account/upload/search/playlist/user) | Spotify | **K8** |
| 2 | Asenkron transcode kuyruğu + çoklu-bitrate | Spotify pipeline | **K9** |
| 3 | Idempotent job (hash-keyed) | Spotify pipeline | **K9** |
| 4 | Pipeline gözlemlenebilirliği (kuyruk/süre/hata) | Spotify pipeline | **K14** |
| 5 | Tek oturum çok cihaz + handoff | Apple Music | **K15** |
| 6 | Kütüphane senkron sözleşmesi | Apple Music | **K15, K8** |
| 7 | Platform üzerine servis kurma | YouTube Music | **K8** |
| 8 | Adaptif bitrate teslimat | YouTube Music | **K9, K15** |
| 9 | Servis referansı (MIT) | Koel | **K8** |

### §3.6.3 Copy / Don't Copy

**✅ ALINABİLİR:**

1. Spotify **servis sınırları** (5 servis adı + sorumluluk) — mimari ders, kod yok.
2. Spotify **pipeline şeması** (upload→queue→transcode→store→serve) — akış dersi.
3. Apple/YouTube **davranış dersleri** (oturum, senkron, adaptif) — tasarım dersi.
4. Koel (MIT) **organizasyon yapısı** — kod atıfla incelenebilir.

**❌ ALINAMAZ:**

1. **Ampache AGPL kodu** — herhangi bir bölüm (CLAUDE.md §4/§20).
2. **Spotify/Apple/YouTube iç kodu** — kapalı kaynak, zaten yok.
3. **Mopidy kodu** — lisans doğrulanana kadar (§3.6.1 mor satır, Guardrail #14).
4. **PCM5122** — bu belgede konu değil ama kural değişmez (CLAUDE.md §21).

### §3.6.4 Senaryolar (§4.1 şablon genişletmesi — 4 satır Durum/Aksiyon)

| Senaryo | Durum | Aksiyon |
|---------|-------|---------|
| **S1: K8 servis iskeleti** | Servis sınırları yok | Spotify 5 servisini CoreMusic'e oturt (§3.1) → kontratlar §5.2 |
| **S2: K9 pipeline** | Transcode şeması yok | §5.1 şemasını benimse: async queue + idempotent job + multi-bitrate |
| **S3: K14 telemetri** | Sözleşme yok | §5.3 metrik adları + KPI eşiği → izleme kurulumu |
| **S4: K15 oturum** | Server+client ikili rol belirsiz | §3.7 oturum modeli + Apple handoff dersi → oturum ADR'si |

### §3.6.5 Entegrasyon — CoreMusic'e Somut Giriş (K eşlemesi ile)

| Ekosistem Öğesi | CoreMusic'e Giriş Yolu | Hedef K | Öncelik |
|------------------|------------------------|---------|---------|
| Spotify account/user/playlist/search/upload | K8 beş servis iskeleti (API kontratları) | **K8** | **Yüksek** |
| Spotify transcode pipeline | K9 asenkron kuyruk + multi-bitrate şeması | **K9** | **Yüksek** |
| Pipeline telemetri | K14 metrik sözleşmesi (kuyruk/süre/hata) | **K14** | Orta |
| Apple oturum/handoff/senkron | K15 oturum modeli + kütüphane senkronu | **K15** | **Yüksek** |
| YouTube adaptif teslimat + öneri | K9 varyant şeması; öneri opsiyonel | **K9, K8** | Orta/Düşük |
| Koel (MIT) | K8 organizasyon referansı | **K8** | Orta |
| Ampache (AGPL) | Yalnız fikir — kod yok | **K8, K9** | — (kırmızı çizgi) |
| Codec/bitrate seçimi | Devir: muzik-streaming-sunuculari.md §5.4 | **K9** | Yüksek (başka belge) |

**Sıra (2026-09-24):** (1) §5.2 K8 kontratları → (2) §5.1 K9 pipeline şeması → (3) §3.7 K15 oturum modeli → (4) §5.3 K14 telemetri → (5) öneri/DRM gibi opsiyonel dersler (S4).

---

### §3.7 Server + Client İkili Rol ve Session Çıkarımları (K8 ↔ K15)

CoreMusic, streaming devlerinden farklı olarak **hem sunucu hem istemci**dır (tek üründe ikili rol). Bu, üç doğrudan çıkarım üretir:

| # | Çıkarım | Streaming Devi Karşılaştırması | CoreMusic Etkisi |
|---|---------|--------------------------------|-------------------|
| 1 | **Oturum tek kaynaktan:** token'ı yalnız K8 account servisi üretir, K15 yalnız tüketir | Apple/Google tek hesap merkezi | §5.4 şeması — iki kaynaklı token yasak |
| 2 | **Loopback trafiği:** K15 istemcisi kendi K8 sunucusuna bağlanır — ağ maliyeti düşük ama ölçek kendi içinde | Spotify istemcisi buluta bağlanır | Yerel/özel dağıtımda K14 trafiği ayrıştır (etiket `region=selfhost`) |
| 3 | **Kütüphane senkronu çift yönlü:** hem yükleme (K8 upload) hem çekme (K15 library) — çakışma çözümü gerekir | Apple sync karşılaştırması (§3.3) | Son-yazan-kazanır + Δ senkron (§5.4) |

#### §3.7.1 Session Ömür Tablosu

| Olay | K8 Tarafı | K15 Tarafı | K14 Metriği |
|------|-----------|------------|-------------|
| Login | account: token üret | sakla, isteklere ekle | `login_success` / `login_fail` |
| Refresh | account: yenile (TTL) | otomatik yenile | `token_refresh` |
| Logout | account: iptal | temizle | `logout` |
| Handoff (Apple dersi) | akış durumu transfer | cihaz B devralır | `handoff_count` |
| Cihaz senkronu (Apple dersi) | Δ store | yerel uygula | `sync_lag` |

#### §3.7.2 İkili Rol Mimari Şeması

~~~text
┌────────────── CoreMusic süreci ──────────────┐
│  K15 UI ──HTTP──> K8 account/user/playlist   │  (loopback, §5.2 kontratları)
│  K15 stream ────> K9 serve/transcode ────────│  (§5.1 pipeline)
│  her iki taraf ─> K14 telemetri ────────────│  (§5.3, region=selfhost)
└──────────────────────────────────────────────┘
Harici istemci (varsa): aynı kontratlar (§5.2) — sunucu sözleşmesi ikisine de açık.
~~~

**Entegrasyon:** §3.6.5 K15 satırı bu bölümle bağlanır; oturum ADR'si S4 senaryosunda (§3.6.4).

### §3.8 Ek Soru Listesi (Guardrail #8 — belirsizlikler)

1. **Hangi servis önce:** account mu upload mı? — §6.4 bir cümle karar bekliyor (ürün önceliği).
2. **DRM var mı:** Apple FairPlay dersi (§3.3) CoreMusic'e gerekli mi? — CoreMusic kendi kataloğuysa DRM gereksiz; lisanslı içerik varsa K9'a ek ADR.
3. **Öneri sistemi:** YouTube recommendation dersi (§3.4) opsiyonel — arama servisi bile kurulmadan erken değil.
4. **Mopidy lisansı:** exa lisans bilgisi getirmedi — §3.6.1 mor satır, doğrulanana kadar kod yok (Guardrail #14).
5. **Star drift notu:** Koel/Ampache yıldızları iki tarihli (§3.5) — biri SSOT'a mı sabitlenir? Bu belge iki kaydı da tarihli tutar, silmez.

---

### §3.9 Streaming Devi Derslerinin Etki/Öncelik Matrisi

| Ders | Etkilenen K | Etki | Öncelik | Sıra (§3.6.5) |
|------|-------------|------|---------|----------------|
| Spotify 5 servis ayrımı | K8 | **Yüksek** | 1 | İlk adım |
| Transcode pipeline | K9 | **Yüksek** | 2 | Kontratlar sonrası |
| Oturum/handoff/senkron | K15 | **Yüksek** | 3 | Pipeline ile paralel |
| Telemetri sözleşmesi | K14 | Orta | 4 | Servisler ayakta olunca |
| Koel (MIT) referansı | K8 | Orta | 5 | Kontrat yazımında bakılır |
| YouTube adaptif varyant | K9, K15 | Orta | 6 | Multi-bitrate çıktısıyla |
| YouTube öneri pipeline | K8 | Düşük | 7 | Opsiyonel (S4) |
| Apple DRM/FairPlay | K9 | Düşük | 8 | İhtiyaç varsa (§3.8 madde 2) |

**Okuma kuralı:** Etki=Yüksek satırlar bu belgenin §5'inde şema bulur; Düşük satırlar §3.8 belirsizlik listesinde bekler — Guardrail #8 gereği cevaplanmadan planlanmaz.


### §3.10 Bu Belgenin Seri Konumu (5/6)

> Sonraki belge: `asio-wasapi-rehber.md` (6/6) → K2/K0 sürücü sözleşmesi.
> Önceki: `donanım-devre-referanslari.md` (4/6) → K1/K16/K17/K19/K20 (güç telemetri devri §6.1).

---

## §4 Kurallar

### §4.1 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

> Yukarıdaki blok `.ai/.templates/documentation/docs-md-template.md` §3.3'ten birebir kopyalanmıştır (Guardrail #16 — kısaltılamaz).

### §4.2 Frontmatter Uygulaması (Bu Belge)

| Placeholder | Değer |
|-------------|-------|
| `{{BASLIK}}` | Ekosistem Mimarileri — Streaming Devi Pipeline Dersleri |
| `{{ACIKLAMA}}` | Spotify/Apple/YouTube mimari dersleri + K8/K9/K14/K15 entegrasyonu |
| `{{TARIH}}` | 2026-09-24 |
| `{{DURUM}}` | active |
| `{{KAYNAK}}` | exa web doğrulaması (2026-09-24) — §7 Referanslar |
| `{{STATUS}}` | active |
| Authority | "Streaming devi mimarileri; K8/K9/K14/K15 — AGPL kod asla kopyalanmaz" |

### §4.3 §1–§7 Kapsam Haritası

| Şablon Bölümü | Bu Belgede |
|----------------|------------|
| §1 Amaç | Kapsam, kullanıcı, bağlantılar |
| §2 Kapsam | K8/K9/K14/K15 ihtiyaçları + boşluklar |
| §3 Mimari | Spotify servisleri, transcode, Apple, YouTube, Koel/Ampache çapraz, §3.6 matris |
| §4 Şablonlar | Şablon-Önce bloğu (verbatim) + frontmatter + §4.4 lisans tablosu |
| §5 Workflow | Pipeline şeması, kontrat taslağı, telemetri, oturum |
| §6 Doğrulama | Devir, risk, kanıt |
| §7 Referanslar | Streaming mimari kaynakları + Koel/Ampache + yerel SSOT |

### §4.4 Lisans Tablosu (Bu Belge için)

| Kaynak | Lisans | K Katmanı | Durum |
|--------|--------|-----------|-------|
| Koel 17.175★ | MIT | K8 | ✅ Referans OK |
| Ampache 3.809★ | AGPL-3.0 | K8, K9 | ❌ **Kod asla** (fikir OK) |
| Mopidy | VERIFICATION REQUIRED | K9 | ⚠️ Doğrula |
| Spotify/Apple/YouTube | Kapalı kaynak | K8/K9/K14/K15 | ✅ Mimari ders OK, kod YOK |
| AGPL başka kaynak | AGPL | — | ❌ Asla (CLAUDE.md §4) |

---

## §5 Workflow

### §5.1 K9 Transcode Pipeline Şeması (K3.2 dersi → CoreMusic)

~~~text
client(K15) ──PUT──> K8 upload ──enqueue──> [K9 queue] ──worker──> transcode job
                                                 │  (idempotent: hash(file))
                                                 v
                                          store (multi-bitrate variants)
                                                 │
client(K15) ──GET manifest──> K9 serve ─────────┘
K14: queue_depth, job_duration, error_rate, bitrate_variant_count
~~~

### §5.2 K8 Servis Kontratı Taslağı (Spotify 5 servis dersi)

~~~text
account-service : POST /auth/login, GET  /account/{id}      → K15 oturum (§5.5)
user-service    : GET  /user/{id}, PUT  /user/{id}/prefs    → profil/tercih
playlist-service: GET|POST /playlist, GET /playlist/{id}/tracks
search-service  : GET  /search?q=                          → keşif (K9 indeks)
upload-service  : POST /upload → queue (§5.1)              → K9 transcode
Kural: database-per-service; servisler arası yalnız API (Spotify dersi §3.1)
~~~

### §5.3 K14 Telemetri Sözleşmesi (Pipeline dersi)

| Metrik | Kaynak | K | Eşik (başlangıç) |
|--------|--------|---|-------------------|
| `queue_depth` | K9 kuyruk | K14 | < 100 iş |
| `transcode_duration_p95` | K9 worker | K14 | < 60s |
| `upload_error_rate` | K8 upload | K14 | < 1% |
| `serve_latency_p95` | K9 serve → K15 | K14 | < 200ms |
| `active_sessions` | K15 oturum | K14 | trend (kapasite) |

~~~text
Sözleşme biçimi: her servis (K8/K9) K14'e aynı etiket şemasını verir:
  labels: {service, version, region}
  counters + histograms (Spotify gözlemlenebilirlik dersi §3.2 madde 4)
~~~

### §5.4 K15 Oturum Modeli Şeması (§3.7 öncesi taslak)

~~~text
login(K8 account) ──> session_token ──> K15 client (cihaz A)
     senkron (Apple dersi): kütüphane/çalma listesi Δ ──> K8 store ──> cihaz B
     handoff (Apple dersi): akış durumu ──transfer──> cihaz B (aynı token)
Sunucu+istemci ikili rol: CoreMusic hem K8 sunucusu hem K15 istemcisi olur —
  token yaşam döngüsü tek kaynaktan (K8 account servisi) yönetilir.
~~~

---

## §6 Doğrulama

### §6.1 Devir Noktaları (Belge Sınırları)

| Konu | Devredilen Belge | K |
|------|------------------|---|
| Codec/bitrate seçimi, sunucu kurulumu | `.ai/ecosystem/muzik-streaming-sunuculari.md` §5.4 | K9 |
| DSP/algoritma | `.ai/ecosystem/ses-dsp-acik-kaynak.md` | K3 |
| Donanım/güç telemetri sensörü | `.ai/ecosystem/donanım-devre-referanslari.md` | K14/K19 |
| Sürücü sözleşmesi | `.ai/ecosystem/asio-wasapi-rehber.md` | K2, K0 |

### §6.2 Riskler

| Risk | Etki | Olasılık | Önlem |
|------|------|----------|-------|
| Ampache AGPL kodu sızar | **Kritik** | Orta | §3.6.3 + PR review (CLAUDE.md §4) |
| Kapalı kaynak "kod varmış gibi" kullanılır | **Yüksek** | Düşük | Sadece mimari ders — §3.6.3 |
| Mopidy lisans doğrulanmadan kopyalanır | **Yüksek** | Orta | §3.6.1 mor satır (Guardrail #14) |
| Pipeline şeması senkron yazılır | Orta | Orta | §5.1 async kuyruk zorunlu |
| Oturum token'ı iki kaynakta çoğalır | Orta | Orta | §5.4 tek kaynak (K8 account) |
| K14 metrik etiketleri tutarsız | Düşük | Orta | §5.3 etiket şeması zorunlu |

### §6.3 KPI / Kanıt Komutları (§4.1 5 adıma karşılık)

~~~powershell
# 1) Şablon bulundu mu:
Test-Path .ai/.templates/documentation/docs-md-template.md
# 2) Placeholder kalmadı mı:
Select-String -Path .ai/ecosystem/ekosistem-mimarileri.md -Pattern '{{TARIH}}|{{DURUM}}|{{KAYNAK}}'
# 3) §1-§7 başlıkları:
Select-String -Path .ai/ecosystem/ekosistem-mimarileri.md -Pattern '^## [1-7]\.'
# 4) AGPL yalnız yasak bağlamında mı:
Select-String -Path .ai/ecosystem/ekosistem-mimarileri.md -Pattern 'AGPL'
# 5) Frontmatter 7 alan:
Get-Content .ai/ecosystem/ekosistem-mimarileri.md -TotalCount 14
~~~

**KPI:** Dosya ≥500 satır; §4.1 bloğu verbatim; 0 placeholder; §5.2 beş servis kontratı + §5.1 pipeline mevcut; Ampache AGPL yalnızca "yasak/fikir" bağlamında.

### §6.4 Sonraki Adım (2 dakikadan kısa)

**§5.2'deki beş servis kontratını `.ai/DECISIONS.md`'ye "K8 iskeleti (ÖNERİ)" olarak yapıştırın** — CoreMusic'in hangi servisle başlayacağını (upload veya account) bir cümleyle yazın.

---

### §6.5 Seri Notu

> Bu belge 6/6 ekosistem serisinin parçasıdır; ≥500 satır ve şablon (Guardrail #16) gerekliliğini karşılar.
> Append-only: yeni bilgi eklenir, mevcut satır silinmez.

## §7 Referanslar

| # | Kaynak | URL | Doğrulama | Kullanım |
|---|--------|-----|-----------|----------|
| 1 | Spotify Engineering (microservice + transcode) | https://engineering.atspotify.com/ | exa 2026-09-24 | §3.1, §3.2 |
| 2 | Apple Music mimarisi (yayın dersleri) | https://developer.apple.com/ | exa 2026-09-24 | §3.3 |
| 3 | YouTube Music mimarisi (yayın dersleri) | https://blog.youtube/ | exa 2026-09-24 | §3.4 |
| 4 | Koel 17.175★ (MIT) | https://github.com/koel/koel | exa 2026-09-24 | §3.5, §5.2 |
| 5 | Ampache 3.809★ (AGPL-3.0) | https://github.com/ampache/ampache | exa 2026-09-24 | §3.5 (fikir, kod yok) |
| 6 | Mopidy | https://github.com/mopidy/mopidy | exa 2026-09-24 (lisans ⚠️) | §3.5 |
| 7 | Ekosistem dizini | .ai/ecosystem/index.md | yerel | (5/6 bu dosya) |
| 8 | Sunucu deneyimi (2/6) | .ai/ecosystem/muzik-streaming-sunuculari.md | yerel | §6.1 devir |
| 9 | Servis entegrasyonu (mevcut) | .ai/ecosystem/service-integration.md | yerel | değişmedi |
| 10 | Lisans kuralları | .ai/CLAUDE.md §4, §20, §21 | yerel | §3.6, §4.4 |
| 11 | K0-K20 SSOT | .ai/architecture/index.md | yerel | K8/K9/K14/K15 |

---

## Authority

**Streaming devi mimarileri (Spotify/Apple/YouTube), CoreMusic K8/K9/K14/K15 katmanlarının referansıdır — mimari ders alınır, AGPL kapalı kaynak kod asla kopyalanmaz.**

> 2026-09-24 | Belge 5/6 | Durum: active | Kaynak: exa web doğrulaması | Geçmiş: ".ai/ecosystem/index.md" (dizin)