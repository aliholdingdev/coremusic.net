---
node_id: K015-media
node_type: domain
role: dogrulama
status: draft
date: 2026-10-12
version: 1.0.0
kaynak-turu: "web-kanıtı (2026-10-12) + kaynak metin karşılaştırması"
authority: "SSOT: ADR-097 §5.3 + görev araştırma turu 2026-10-12"
---

# K015-media — media-dogrulama.md

> Bu belge **kanıt defteridir**: neyin doğrulandığı, neyin beklemede olduğu.
> Doğrulanmayan hiçbir iddia "verified" sayılmaz.

## §0 Kanıt sınıflandırması

| Sınıf | Ne demek | Bu belgede |
|---|---|---|
| S1 · URL'li web kanıtı | İddia + link + tarih + durum | Jellyfin (★ · lisans · .NET · fork) |
| S2 · kaynak metin kanıtı | v2 / registry / ADR satırı | 11 çocuk adı, master adı |
| S3 · beyan (URL'siz) | Görev beyanında iddia var, link yok | Plex/Emby · Plexamp/Finamp/Navidrome · OpenAudio |
| S4 · atama | Hangi düğümün kanıtı — tasarım kararı | iddiaların K015'e eşlenmesi |

## §1 Ölçütler ve sonuçlar

| # | Ölçüt | Yöntem / kaynak | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.15 ile birebir (11 çocuk) | metin karşılaştırma | ✅ verified |
| 2 | Master dosyası adı media-master.md (registry §5/15) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan (node_id…open_questions) | alan sayımı | ✅ verified |
| 4 | 5 dosyalık MD seti tam + 11 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 5 | §4/1 Jellyfin kanıtı URL'li | satır-başı kanıt kontrolü | ✅ verified |
| 6 | §4/2–§4/4 beyanları URL'siz (S3) | satır-başı kontrol | ⚠️ kayıtlı, kanıt bekliyor |
| 7 | playback / synchronization sahipliği | v2 metni okundu | ❌ belirsiz → ⚠️ |
| 8 | Kademe-3 bounded-context kapsamı | — | ⏳ faz C (ADR-097 K4) |

## §2 Açık sorular (open_questions)

| # | Soru | Etiket | Not |
|---|---|---|---|
| 1 | playback sahibi K015 mi K012/playback-service mi? | ⚠️ VERIFICATION REQUIRED | İki "çalma" |
| 2 | offline sahibi K015 mi K014/offline-sync mi K012 mi? | ⚠️ VERIFICATION REQUIRED | Üç aday |
| 3 | synchronization (K015) ↔ (K013) neyi ayırıyor? | ⚠️ VERIFICATION REQUIRED | Aynı ad iki domain |
| 4 | media-testing ↔ K019-testing sahipliği | ⚠️ VERIFICATION REQUIRED | |
| 5 | Referans ürün analizi (Plexamp · Finamp · Navidrome) neyi kanıtlar? | onay bekliyor | Karar değil |
| 6 | Plex/Emby proprietary bilgisi lisans kararı için yeterli mi? | ⚠️ VERIFICATION REQUIRED | |
| 7 | OpenAudio deposunun tam adresi | ⚠️ UNKNOWN | Yalnız ★ beyanı |
| 8 | metadata/artwork kaynağı (dış servis mi yerel mi)? | ⚠️ VERIFICATION REQUIRED | |

## §3 Web-kanıt kayıtları (tur-1 · 2026-10-12)

| # | İddia | URL / uç nokta | Tarih | Durum |
|---|---|---|---|---|
| 1 | Jellyfin: GitHub 55732★ · GPL-2.0 · .NET · kökeni Emby 3.5.2 fork'u (2018-12-09) | https://github.com/jellyfin/jellyfin | 2026-10-12 | ✅ doğrulandı (görev araştırma turu) |
| 2 | Plex ve Emby proprietary; Jellyfin bunların açık-kaynak olmayan alternatifi | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ S3 — iddia kayıtlı, URL tur-2 |
| 3 | Müzik alternatifleri: Plexamp (self-hosted) · Finamp (Jellyfin müzik mobile) · Navidrome | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ S3 — referans ürün analizi |
| 4 | OpenAudio — 2.599★ | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ S3 — depo adresi UNKNOWN |
| 5 | İddia 1 → K015 (+ K011) referans eşlemesi | — | 2026-10-12 | 📌 S4 tasarım ataması |

## §3a Web-kanıt kayıtları (tur-2 · 2026-10-12 · append-only)

| # | İddia | URL / uç nokta | Tarih | Durum |
|---|---|---|---|---|
| 6 | Plexamp = Plex'in resmi özel müzik oynatıcısı (Plex Labs) | https://www.plex.tv/plexamp/ | 2026-10-12 | ✅ doğrulandı (tur-2) |
| 7 | Finamp: açık kaynak Jellyfin müzik istemcisi (Android/iOS/Desktop); Navidrome ve Lyra ile de çalışır | https://github.com/finamp-app/finamp | 2026-10-12 | ✅ doğrulandı (tur-2) |
| 8 | Navidrome: açık kaynak web-tabanlı müzik koleksiyonu sunucusu + streamer; resmi site + GitHub deposu | https://github.com/navidrome/navidrome · https://www.navidrome.org/ | 2026-10-12 | ✅ doğrulandı (tur-2) |
| 9 | OpenAudio adayı: `webprofusion/OpenAudio` — açık kaynak ses yazılımı listesi, ~2,8k★ (beyan 2.599★ ile uyumlu büyüme aralığında) | https://github.com/webprofusion/OpenAudio | 2026-10-12 | ⚠️ ADAY — depo kimliği teyidi bekliyor (§2/7) |
| 10 | Phonograph (kabouzeid/Phonograph): 2020'den beri bakımsız, 2023'te arşivlendi; aktif fork = `chr56/Phonograph_Plus` (F-droid kayıtlı, 2021'den beri bakım) | https://github.com/chr56/Phonograph_Plus · https://f-droid.org/en/packages/player.phonograph.plus/ | 2026-10-12 | ✅ doğrulandı (tur-2) |

> Bu belgeye yalnız **iddia · URL · tarih · durum** dörtlüsüyle kayıt girer;
> "galiba", "muhtemelen" gibi derecelendirme yazılmaz.

## §4 Tur-2 yapılacaklar (kanıt bekleyen)

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | Jellyfin ★/lisans/fork değerlerini üretim gününde yeniden ölç | §3/1 tazeleme | research |
| 2 | Plex/Emby/Navidrome/Finamp/Plexamp resmi URL'lerini topla | §3/6–8 ✅ kapatıldı (2026-10-12 tur-2) — Emby'nin kendi URL'si hâlâ eksik (yalnız Plex tarafı doğrulandı) | research |
| 3 | OpenAudio deposunun gerçek adresini bul | 🔄 §3/9 — aday bulundu (`webprofusion/OpenAudio`), kimlik teyidi 1 tur daha | research |
| 4 | playback sahipliğini K015/K012 sınırında karara bağla | §2/1 | Vault Steward |
| 5 | Üç eşitleme alanının sınırını tek ADR'de netleştir | §2/2–3 | Vault Steward |
| 6 | Referans ürünlerin CoreMusic'e taşınabilir (lisans) kısmını ayır | §2/6 | legal/ Vault Steward |

## §5 Doğrulama geçmişi

| Tarih | Tur | Sonuç |
|---|---|---|
| 2026-10-12 | Tur-1 (görev araştırma turu + kaynak metin karşılaştırma) | 5/8 ölçüt verified · 1 URL'li kanıt · 3 S3 beyan |
| 2026-10-12 | Tur-2 (Plexamp/Finamp/Navidrome/OpenAudio/Phonograph araması) | §3a/6–10 eklendi: 4 S3 beyan URL'lileşti (Plexamp/Finamp/Navidrome ✅, OpenAudio ⚠️ aday) · §2/7 hâlâ açık |

## §6 Kapı tanımı (bu dosyanın geçerlilik sınırı)

1. Bu belge yalnız K015-media'yı kapsar; kardeş domain dosyalarına iddia **taşıtmaz**.
2. §4 tablosundaki işler kapanmadan genel "verified" ifadesi kullanılmaz.
3. Yeni kanıt = yeni satır (§3), mevcut satırın **üzerine yazma** (append-only).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
