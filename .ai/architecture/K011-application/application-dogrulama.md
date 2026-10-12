---
node_id: K011-application
node_type: domain
role: dogrulama
status: draft
date: 2026-10-12
version: 1.0.0
kaynak-turu: "web-kanıtı (2026-10-12) + kaynak metin karşılaştırması"
authority: "SSOT: ADR-097 §5.3 + görev araştırma turu 2026-10-12"
---

# K011-application — application-dogrulama.md

> Bu belge **kanıt defteridir**: neyin doğrulandığı, neyin beklemede olduğu.
> Doğrulanmayan hiçbir iddia "verified" sayılmaz.

## §0 Kanıt sınıflandırması

| Sınıf | Ne demek | Bu belgede |
|---|---|---|
| S1 · URL'li web kanıtı | İddia + link + tarih + durum | Jellyfin, Spotify uç noktası |
| S2 · kaynak metin kanıtı | v2 / registry / ADR satırı | çocuk listesi, master adı |
| S3 · beyan (URL'siz) | Görev beyanında iddia var, link yok | Apple Music JWT |
| S4 · atama | Hangi düğümün kanıtı — tasarım kararı | Jellyfin → K011 eşlemesi |

## §1 Ölçütler ve sonuçlar

| # | Ölçüt | Yöntem / kaynak | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.11 ile birebir (10 çocuk) | metin karşılaştırma | ✅ verified |
| 2 | Master dosyası adı application-master.md (registry §5/11) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan (node_id…open_questions) | alan sayımı | ✅ verified |
| 4 | 5 dosyalık MD seti tam + 10 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 5 | §4 teknoloji satırları yalnız 2026-10-12 kanıtına dayanıyor | satır-başı kanıt kontrolü | ✅ verified |
| 6 | Teknoloji/stack kimliği (dil, framework, paket) | kanıt turu | ❌ doğrulanmadı → ⚠️ |
| 7 | Kademe-3 bounded-context kapsamı | — | ⏳ faz C (ADR-097 K4) |
| 8 | Uygulama çalışma zamanı barındırma yeri | — | ⏳ açık (§2/4) |

## §2 Açık sorular (open_questions)

| # | Soru | Etiket | Not |
|---|---|---|---|
| 1 | Uygulama çekirdeğinin dili/çatısı nedir? | ⚠️ VERIFICATION REQUIRED | Kanıt yok, ad yazılmaz |
| 2 | server-runtime nerede çalışır (sunucu türü)? | ⚠️ VERIFICATION REQUIRED | v2 tek satır |
| 3 | commands/queries = CQRS mi? | TASARIM İDDİASI | İsim benzerliği kanıt değil |
| 4 | application-services ↔ K012-services ayrımı | ⚠️ VERIFICATION REQUIRED | İki "servis" adı |
| 5 | application-testing ↔ K019-testing sahipliği | ⚠️ VERIFICATION REQUIRED | Test sahipliği |
| 6 | desktop ↔ electron sınırı | ⚠️ VERIFICATION REQUIRED | İki masaüstü yüzeyi |
| 7 | Jellyfin/PKCE/JWT kanıtları referans analizi mi, karar mı? | onay bekliyor | Vault Steward |

## §3 Web-kanıt kayıtları (tur-1 · 2026-10-12)

| # | İddia | URL / uç nokta | Tarih | Durum |
|---|---|---|---|---|
| 1 | Jellyfin: GitHub 55732★ · GPL-2.0 · .NET · Emby 3.5.2 fork (2018-12-09) | https://github.com/jellyfin/jellyfin | 2026-10-12 | ✅ doğrulandı (görev araştırma turu) |
| 2 | Spotify Authorization Code + PKCE; token uç noktası accounts.spotify.com/api/token | https://accounts.spotify.com/api/token | 2026-10-12 | ✅ doğrulandı (görev araştırma turu) |
| 3 | Apple Music API: ES256 + kid taşıyan JWT developer token | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ kısmi — iddia kayıtlı, URL tur-2 |
| 4 | Jellyfin → K011 (uygulama/çalışma zamanı referansı) eşlemesi | — | 2026-10-12 | 📌 S4 tasarım ataması (kanıt değil) |

> Bu belgeye yalnız **iddia · URL · tarih · durum** dörtlüsüyle kayıt girer;
> "galiba", "muhtemelen" gibi derecelendirme yazılmaz.

## §4 Tur-2 yapılacaklar (kanıt bekleyen)

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | Apple Music developer token dokümantasyonu için resmi URL topla | §3/3 | research |
| 2 | Jellyfin ★/lisans değerlerini üretim gününde yeniden ölç (star akışkandır) | §3/1 tazeleme | research |
| 3 | Uygulama çalışma zamanı teknolojisi için depo/dosya kanıtı topla | §2/1 · §1/6 | explore |
| 4 | PKCE akışının CoreMusic web/desktop'ta kullanımına ADR ile karar ver | §2/3 · §7/7 | Vault Steward |
| 5 | §2/4–§2/6 sorularını ilk sahipleriyle netleştir (K012 · K019) | §2/4–6 | Vault Steward |

## §5 Doğrulama geçmişi

| Tarih | Tur | Sonuç |
|---|---|---|
| 2026-10-12 | Tur-1 (görev araştırma turu + kaynak metin karşılaştırma) | 5/8 ölçüt verified · 3 açık ⚠️ |

## §6 Kapı tanımı (bu dosyanın geçerlilik sınırı)

1. Bu belge yalnız K011-application'ı kapsar; kardeş domain dosyalarına iddia **taşıtmaz**.
2. Sonraki tur: §4 tablosundaki işler kapanmadan "verified" genellemesi yapılmaz.
3. Yeni kanıt = yeni satır (§3), mevcut satırın **üzerine yazma** (append-only).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
