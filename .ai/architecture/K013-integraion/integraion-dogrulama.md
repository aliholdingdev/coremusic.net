---
node_id: K013-integraion
node_type: domain
role: dogrulama
status: draft
date: 2026-10-12
version: 1.0.0
kaynak-turu: "web-kanıtı (2026-10-12) + kaynak metin karşılaştırması"
authority: "SSOT: ADR-097 §5.3 + görev araştırma turu 2026-10-12"
group: kanit
---


# K013-integraion — integraion-dogrulama.md

> Bu belge **kanıt defteridir**: neyin doğrulandığı, neyin beklemede olduğu.
> Doğrulanmayan hiçbir iddia "verified" sayılmaz.

## §0 Kanıt sınıflandırması

| Sınıf | Ne demek | Bu belgede |
|---|---|---|
| S1 · URL'li web kanıtı | İddia + link + tarih + durum | Spotify token uç noktası |
| S2 · kaynak metin kanıtı | v2 / registry / ADR satırı | çocuk listesi, master adı |
| S3 · beyan (URL'siz) | Görev beyanında iddia var, link yok | Apple Music JWT · YouTube Music · hub-and-spoke |
| S4 · atama | Hangi düğümün kanıtı — tasarım kararı | iddiaların K013'e eşlenmesi |

## §1 Ölçütler ve sonuçlar

| # | Ölçüt | Yöntem / kaynak | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.13 ile birebir (8 çocuk) | metin karşılaştırma | ✅ verified |
| 2 | Master dosyası adı integration-master.md (registry §5/13) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan (node_id…open_questions) | alan sayımı | ✅ verified |
| 4 | 5 dosyalık MD seti tam + 8 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 5 | §4 satırları yalnız 2026-10-12 beyanına dayanıyor | satır-başı kanıt kontrolü | ✅ verified |
| 6 | YouTube Music resmi public API yok iddiası | kanıt turu | ❌ doğrulanmadı → ⚠️ VERIFY REQUIRED |
| 7 | synchronization sahipliği (K013 ↔ K015) | v2 metni okundu | ❌ iki domain aynı ad → ⚠️ |
| 8 | Kademe-3 bounded-context kapsamı | — | ⏳ faz C (ADR-097 K4) |

## §2 Açık sorular (open_questions)

| # | Soru | Etiket | Not |
|---|---|---|---|
| 1 | YouTube Music resmi public API yok mu, TOS riski nasıl? | ⚠️ VERIFY REQUIRED | Kritik — kapsam kararı |
| 2 | synchronization sahibi K013 mü K015 mi? | ⚠️ VERIFICATION REQUIRED | Aynı ad iki domain'de |
| 3 | Dış API'yi çağıran taraf K012 mi K013 mü? | ⚠️ VERIFICATION REQUIRED | Yön + döngü riski |
| 4 | PKCE akışı genel politika mı, Spotify'a özel mi? | TASARIM İDDİASI | ADR bekliyor |
| 5 | Apple Music JWT (ES256+kid) kim üretir? | ⚠️ VERIFICATION REQUIRED | Uygulama mı sunucu mu |
| 6 | event-contracts taşıma biçimi (webhook/akış)? | ⚠️ UNKNOWN | Kanıt yok |
| 7 | external-systems envanterinin güncel kapsamı? | ⚠️ VERIFICATION REQUIRED | Hangi sistemler dahil |

## §3 Web-kanıt kayıtları (tur-1 · 2026-10-12)

| # | İddia | URL / uç nokta | Tarih | Durum |
|---|---|---|---|---|
| 1 | Spotify Authorization Code + PKCE; token uç noktası accounts.spotify.com/api/token | https://accounts.spotify.com/api/token | 2026-10-12 | ✅ doğrulandı (görev araştırma turu) |
| 2 | Apple Music API: ES256 + kid taşıyan JWT developer token | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ S3 — iddia kayıtlı, URL tur-2 |
| 3 | YouTube Music resmi public API yok (TOS riski) | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ VERIFY REQUIRED — S3, kanıt yok |
| 4 | Evrensel Playlist/Track spec'i hub-and-spoke (open-playlist-engine yaklaşımı) | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ S3 — iddia kayıtlı, URL tur-2 |
| 5 | İddia 1 → external-systems / K013 eşlemesi | — | 2026-10-12 | 📌 S4 tasarım ataması |

> Bu belgeye yalnız **iddia · URL · tarih · durum** dörtlüsüyle kayıt girer;
> "galiba", "muhtemelen" gibi derecelendirme yazılmaz.

## §4 Tur-2 yapılacaklar (kanıt bekleyen)

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | YouTube Music API/TOS durumu için resmi kaynak topla | §3/3 · §2/1 | research |
| 2 | Apple Music developer token dokümantasyonu için resmi URL topla | §3/2 · §2/5 | research |
| 3 | open-playlist-engine kaynağını bul ve hub-and-spoke iddiasını doğrula | §3/4 | research |
| 4 | synchronization sahipliğini K013/K015 sınırında karara bağla | §2/2 | Vault Steward |
| 5 | Dış çağrı yönünü (K012 ↔ K013) kilitle | §2/3 | Vault Steward |
| 6 | HTTP istemci + OAuth kütüphanesi kanıtı topla | §1-§3 UNKNOWN satırları | explore |

## §5 Doğrulama geçmişi

| Tarih | Tur | Sonuç |
|---|---|---|
| 2026-10-12 | Tur-1 (görev araştırma turu + kaynak metin karşılaştırma) | 5/8 ölçüt verified · 2 açık ⚠️ · 1 ⚠️ VERIFY REQUIRED |
| 2026-10-16 | Tur-2 (kademe-3 genişletme — 7 subagent, openapi kalıbı) | 35 yeni MD · 113 URL fetch ile doğrulandı · 36 elenen (gerekçeli §9'lar) · 2026-10-12 beyanlarının 2'si kapatıldı (Apple ES256+kid ✅ · hub-and-spoke ✅) · YouTube Music kanıt sunuldu (karar üst otoritede ⚠️) · yeni açık: retry/breaker parametreleri, K013↔K019 sınır, canlı test gate adresi |

## §6 Kapı tanımı (bu dosyanın geçerlilik sınırı)

1. Bu belge yalnız K013-integraion'ı kapsar; kardeş domain dosyalarına iddia **taşıtmaz**.
2. §2/1 (YouTube Music) kapanmadan external-systems kapsamı "verified" sayılmaz.
3. Yeni kanıt = yeni satır (§3), mevcut satırın **üzerine yazma** (append-only).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
