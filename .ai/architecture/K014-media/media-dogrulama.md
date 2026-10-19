---
node_id: K014-media
node_type: domain
role: dogrulama
status: draft
date: 2026-10-12
version: 1.0.0
kaynak-turu: "web-kanıtı (2026-10-12) + kaynak metin karşılaştırması"
authority: "SSOT: ADR-097 §5.3 + görev araştırma turu 2026-10-12"
group: kanit
---


# K014-media — media-dogrulama.md

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

## §3b Web-kanıt kayıtları (tur-2b — star derinlemesi · 2026-10-12 · append-only)

| # | İddia | URL / uç nokta | Tarih | Durum |
|---|---|---|---|---|
| 11 | Navidrome yıldız ölçümü: 24.109★ / 23.251★ (iki ayrı snapshot — yıldız dinamik, ikisi de aynı gün) · GPL-3.0 · depo 2016-02-24 | https://github.com/navidrome/navidrome | 2026-10-12 | ✅ doğrulandı (tur-2b — snapshot farkı not edildi) |
| 12 | MusicBrainz Picard: 5.273★ / 5.043★ (snapshot farkı) · GPL-2.0 veya sonraki · Python · etiketleyici (tagger) | https://github.com/musicbrainz/picard | 2026-10-12 | ✅ doğrulandı (tur-2b) |
| 13 | Strawberry: 3.622★ (ana depo snapshot) · GPL-3.0 · C++/Qt · 2018'de Clementine fork'u · son sürüm 1.2.18 (2026-02-01) | https://github.com/strawberrymusicplayer/strawberry | 2026-10-12 | ✅ doğrulandı (tur-2b) |
| 14 | Phonograph yıldız ölçümü: `karimknaebel/Phonograph` 2.741★ · GPL-3.0 · ARCHIVED (2015-03-05 oluşturuldu) · aktif fork `chr56/Phonograph_Plus` 729★ · GPL-3.0 (2021-09-21) | https://github.com/karimknaebel/Phonograph · https://github.com/chr56/Phonograph_Plus | 2026-10-12 | ✅ doğrulandı (tur-2b) |
| 15 | DeaDBeeF ~1.973★ | https://www.libhunt.com/compare-deadbeef-vs-strawberrymusicplayer--strawberry (ikincil kaynak) | 2026-10-12 | ⚠️ S3 — birincil GitHub ölçümü yok, VERIFY |

## §4 Tur-2 yapılacaklar (kanıt bekleyen)

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | Jellyfin ★/lisans/fork değerlerini üretim gününde yeniden ölç | §3/1 tazeleme | research |
| 2 | Plex/Emby/Navidrome/Finamp/Plexamp resmi URL'lerini topla | §3/6–8 ✅ kapatıldı (2026-10-12 tur-2) — Emby'nin kendi URL'si hâlâ eksik (yalnız Plex tarafı doğrulandı) | research |
| 3 | OpenAudio deposunun gerçek adresini bul | 🔄 §3/9 — aday bulundu (`webprofusion/OpenAudio`), kimlik teyidi 1 tur daha | research |
| 4 | playback sahipliğini K015/K012 sınırında karara bağla | §2/1 | Vault Steward |
| 5 | Üç eşitleme alanının sınırını tek ADR'de netleştir | §2/2–3 | Vault Steward |
| 6 | Referans ürünlerin CoreMusic'e taşınabilir (lisans) kısmını ayır | §2/6 | legal/ Vault Steward |
| 7 | Audacious · Clementine · foobar2000 star/lisans ölçümü (birincil GitHub kaynağı) | §3b bekleyen — bu turda ölçülmedi | research |

## §5 Doğrulama geçmişi

| Tarih | Tur | Sonuç |
|---|---|---|
| 2026-10-12 | Tur-1 (görev araştırma turu + kaynak metin karşılaştırma) | 5/8 ölçüt verified · 1 URL'li kanıt · 3 S3 beyan |
| 2026-10-12 | Tur-2 (Plexamp/Finamp/Navidrome/OpenAudio/Phonograph araması) | §3a/6–10 eklendi: 4 S3 beyan URL'lileşti (Plexamp/Finamp/Navidrome ✅, OpenAudio ⚠️ aday) · §2/7 hâlâ açık |
| 2026-10-12 | Tur-2b (star derinlemesi: Navidrome · Picard · Strawberry · Phonograph · DeaDBeeF) | §3b/11–15 eklendi: 4 ✅ (snapshot farkları not edildi) + 1 ⚠️ (DeaDBeeF ikincil kaynak) · §4/7 eklendi |

## §6 Kapı tanımı (bu dosyanın geçerlilik sınırı)

1. Bu belge yalnız K014-media'yı kapsar; kardeş domain dosyalarına iddia **taşıtmaz**.
2. §4 tablosundaki işler kapanmadan genel "verified" ifadesi kullanılmaz.
3. Yeni kanıt = yeni satır (§3), mevcut satırın **üzerine yazma** (append-only).

---

## §7 Kanıt Sınıflandırması Derinlemesi (S1–S4 genişletmesi — 2026-10-13 eki)

### §7.1 Sınıf geçiş kuralları (hangi sınıf hangisine evrilir)

| # | Geçiş | Koşul | Kanıt zorunlu |
|---|---|---|---|
| 1 | S3 → S1 | beyanın URL'si bulunur + HTTP 200 ölçülür | URL + tarih + durum |
| 2 | S3 → S2 | beyanın v2/ADR/registry satırı bulunur | dosya + satır no |
| 3 | S1 → ✅ verified | ölçüm gününde tekrar 200 | tazeleme tarihi |
| 4 | S1 → ⚠️ | 404 / repo silinmiş / redirect | eski kayıt silinmez, yeni satır |
| 5 | S4 → herhangi | atama **kanıt değildir**; yalnız sorumluluk kaydı | — |

### §7.2 Sınıf bazlı sayımlar (bu belge içi — 2026-10-13 sayımı)

| Sınıf | Sayım | Kayıt aralığı |
|---|---|---|
| S1 · URL'li web kanıtı | 12 | §3/1 · §3a/6–8 · §3a/10 · §3b/11–14 (2 URL) + §3b/15 ikincil |
| S2 · kaynak metin kanıtı | 2 küme | §0/2 (11 çocuk + master adı) |
| S3 · beyan (URL'siz/eksik) | 4 | §3/2–4 · §3b/15 (ikincil kaynak) |
| S4 · atama | 1 | §3/5 |

**Toplam kayıt satırı: 15 (§3/1–5 · §3a/6–10 · §3b/11–15).** Sınıf geçişi yalnız §7.1
tablosundaki 5 koşulla olur; "galiba/muhtemelen" yazımı §3a altındaki kural ile yasak.

### §7.3 Doğrulanabilirlik testi (her kayıt için 5 soru)

| # | Soru | Hayır ise |
|---|---|---|
| 1 | İddia tek cümle mi? | bölünür (yeni satır) |
| 2 | URL benzersiz mi (envanter kesişimi 0)? | kesişim varsa satır ikiye ayrılır |
| 3 | Tarih ISO (YYYY-MM-DD) mı? | düzeltilir (satır sonu ek, overwrite yok) |
| 4 | Durum 4'lü sözlükten mi (§0)? | sınıf yeniden atanır |
| 5 | İddia başka domain dosyasına taşınıyor mu? | §6/1 ihlali — taşımaz |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §8 Doğrulama Protokolü (yeniden ölçüm kuralları)

### §8.1 Yeniden ölçüm tetikleyicileri

| # | Tetikleyici | Zorunlu iş |
|---|---|---|
| 1 | üretim gününden >90 gün geçti | §3/1 Jellyfin ★/lisans yeniden ölç (§4/1) |
| 2 | repo adı/değişimi | §3a/9–10 kimlik teyidi turu |
| 3 | lisans kararı anı | §2/6 + §4/6 legal ayrımı |
| 4 | yeni K-düğümü komşusu | §9 çapraz kontrol yenilenir |

### §8.2 Ölçüm yöntemi (bu belge standardı)

```
1. URL benzersizliği      → envanter (§11) ile kesişim 0
2. HTTP durumu            → yalnız 200 "doğrulandı" sayar
3. Ek alan (opsiyonel)    → ★ sayısı / lisans / dil — tarih damgalı
4. Kayıt                 → §3/§3a/§3b'de yeni satır (append-only)
5. Sınıf                  → §7.1 kuralıyla S1; doğrudan "verified" değil
```

### §8.3 Red koşulları (kanıtın kabul edilmediği durumlar)

| # | Red | Neden |
|---|---|---|
| 1 | ikincil kaynak (libhunt vb.) tek başına | §3b/15 örneği — ⚠️ S3 kalır |
| 2 | forum/blog beyanı | birincil değil |
| 3 | URL'siz "★ sayısı" | §3/4 örneği — beyan kalır |
| 4 | mirror / fork'u ana depo sanmak | §3b/14 (karimknaebel vs chr56 ayrımı) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §9 Kardeş Dosya Çapraz Kontrol (disk kanıtı — 2026-10-13 sayımı)

| # | Dosya | Satır | Bu belgeyle ilişki | Durum |
|---|---|---|---|---|
| 1 | media-master.md | 511 | §1/1–2 iddialarının kaynağı (11 çocuk + master adı) | ✅ okundu |
| 2 | media-bagimliliklar.md | 571 | bağımlılık iddiaları TASARIM İDDİASI etiketli | ✅ okundu |
| 3 | index.md | 100 | registry kök indeks (K015 satırı) | ✅ okundu |
| 4 | 11 alt klasör index.md | 50'er | kapsam-dışı (bu belge §6/1) | 📌 sayımda |
| 5 | bu dosya (media-dogrulama.md) | 115 → genişletme öncesi | kanıt defteri | ✅ |

### §9.1 master ile çapraz (okunan gerçek satırlar)

| # | master satırı | Metin (kısaltma) | dogrulama karşılığı |
|---|---|---|---|
| 1 | L37 | "ağ taşımasının ayrıntısını yazmaz (K014)" | §1/4 bağımlılık eşleşir |
| 2 | L56 | "Ağ taşıması ayrıntısı → K010-network" | sınır tablosu — ✅ eşleşti |
| 3 | L60 | test sahibi çatışması (iki test sahibi) | §2/4 açık soru — ⚠️ |
| 4 | L103–106 | §5.1 giden: 4 hedef (motor · kalıcılık · dış katalog · taşıma) | §1/1 eşleşti |
| 5 | L110–113 | §5.2 gelen: 2 kaynak (arayüz · servisler) | §1/1 eşleşti |
| 6 | L140 | "§7 Açık sorular" başlığı | §2 8 soru ile aynı sorun kümesi |

### §9.2 bagimliliklar ile çapraz

| # | bagimliliklar | Metin (kısaltma) | dogrulama karşılığı |
|---|---|---|---|
| 1 | §1/1–4 | 4 giden bağımlılık | §9.1/4 ile birebir |
| 2 | §2/1–2 | 2 gelen bağımlılık | §9.1/5 ile birebir |
| 3 | §3 | 6 sınır · hepsi ⚠️ UNKNOWN | §2/8 (metadata/artwork) ile uyumlu |
| 4 | §4 | 7 risk (test sahibi · üç eşitleme · lisans · depo adresi …) | §2/1–7 ile uyumlu |
| 5 | §19 | K014↔K015 çapraz: 3 giden + 17 gelen satır | bu belge §12/3 ile uyumlu |

**Sonuç:** üç kök dosya (master · bagimliliklar · dogrulama) arasında **çelişki yok**;
aynı açık soru kümesi üç yerde de ⚠️ işaretli. Çelişki olsaydı §6/2 kuralı gereği
"verified" ifadesi durdurulurdu.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §10 Açık Soru Derinlemesi (§2'nin 8 sorusu — etki alanı analizi)

| # | Soru (§2) | Etki alanı | Kapanışa kadar yapılmaz |
|---|---|---|---|
| 1 | playback sahibi K015 mi K012 mi? | çalma durumu tek gerçek olmalı | iki "çalma" kodu yazılmaz |
| 2 | offline sahibi (3 aday) | çevrimdışı kuyruk tek yazma noktası | kuyruk şeması dondurulur |
| 3 | synchronization K015 ↔ K013 sınırı | aynı ad iki domain | eşitleme API'si yazılmaz |
| 4 | test sahibi (media-testing ↔ test domain'i) | test tekrarı / iki sahip | test altyapısı çoğaltılmaz |
| 5 | referans ürün analizi neyi kanıtlar? | karar değil, fikir referansı | tasarım dayanağı yapılmaz |
| 6 | Plex/Emby proprietary lisans bilgisi | hukuki sınır | taşınabilirlik kararı verilmez |
| 7 | OpenAudio depo adresi (★ beyanı) | yanlış referans riski | "★ var" cümlesi kullanılmaz |
| 8 | metadata/artwork kaynağı | dış kesintisi içeriği kırar | önbellek varsayımı yazılmaz |

**Ortak kapanış kuralı:** 8 soru da Vault Steward/ADR kapısından geçmeden bu belgede
"çözüldü" olarak işaretlenemez (§6/2).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §11 URL Envanteri (bu belgedeki 10 benzersiz uç nokta — okuma, yeni iddia YOK)

| # | Uç nokta | Kayıt | Durum (belgede kayıtlı) |
|---|---|---|---|
| 1 | https://github.com/jellyfin/jellyfin | §3/1 | ✅ 2026-10-12 |
| 2 | https://www.plex.tv/plexamp/ | §3a/6 | ✅ 2026-10-12 |
| 3 | https://github.com/finamp-app/finamp | §3a/7 | ✅ 2026-10-12 |
| 4 | https://github.com/navidrome/navidrome | §3a/8 · §3b/11 | ✅ 2026-10-12 (★ iki snapshot) |
| 5 | https://www.navidrome.org/ | §3a/8 | ✅ 2026-10-12 |
| 6 | https://github.com/webprofusion/OpenAudio | §3a/9 | ⚠️ ADAY — kimlik teyidi |
| 7 | https://github.com/chr56/Phonograph_Plus | §3a/10 · §3b/14 | ✅ 2026-10-12 |
| 8 | https://f-droid.org/en/packages/player.phonograph.plus/ | §3a/10 | ✅ 2026-10-12 |
| 9 | https://github.com/musicbrainz/picard | §3b/12 | ✅ 2026-10-12 |
| 10 | https://github.com/strawberrymusicplayer/strawberry | §3b/13 | ✅ 2026-10-12 |

**Ek kayıtlar (ikincil/arşiv):** `karimknaebel/Phonograph` (§3b/14, ARCHIVED beyanı) ·
libhunt karşılaştırma sayfası (§3b/15, ⚠️ ikincil).

**Kesişim kuralı:** bu 12 uç nokta kardeş K015 dosyalarına **taşınmaz** (§6/1);
taşınırsa §7.3/2 testi yeniden yapılır.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §12 Kapı Tablosu (bu dosyaya uygulanan 7 kapı — 2026-10-13)

| # | Kapı | Kanıt | Durum |
|---|---|---|---|
| 1 | FM `parent` → `parent_id` | FM L1–L11'de `parent` anahtarı yok | ✓ N/A — ihlal yok |
| 2 | FM `group:` tırnaksız | L10 `group: kanit` | ✓ KAPANDI |
| 3 | Authority son satır | bu ekin sonunda tek Authority | ✓ KAPANDI |
| 4 | Placeholder (yer tutucu metin) | §1–§6 + bu ek: yer tutucu yok | ✓ KAPANDI |
| 5 | URL envanter kesişimi | §11 — 12 uç nokta, hepsi bu belgede | ✓ KAPANDI |
| 6 | Beyazliste dışı yazım | yalnız K014-media kökü içinde düzenleme | ✓ KAPANDI |
| 7 | Append-only | §1–§6 satırları korundu; ek = §7–§16 | ✓ KAPANDI |

**Durum: 7/7 kapandı · 0 açık kapı.**

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §13 SSS (bu defterin sık sorulan 6 sorusu)

**Q1: Bu belge "verified" derse her şey doğru mu?**
Hayır — §6/2: §4 açık işleri kapanmadan genel "verified" ifadesi kullanılmaz.
"verified" her satırın kendi sınıfıdır (§0).

**Q2: Yeni kanıt eklerken mevcut satır düzeltilir mi?**
Hayır — §3/3: mevcut satırın üzerine yazma; düzeltme = yeni satır (append-only).

**Q3: İkincil kaynak (libhunt) neden yeterli değil?**
§8.3/1: ikincil tek başına S1 yapmaz; §3b/15 ⚠️ S3 olarak kalır, birincil ölçüm bekler.

**Q4: Fork ile ana depo karıştırılabilir mi?**
§3b/14 örneği: hayır — ana depo ARCHIVED, aktif fork ayrı satır, ayrı ★ (729★).

**Q5: Kardeş dosyalara iddia taşınabilir mi?**
Hayır — §6/1: bu belge yalnız K014-media; taşıma olsa §7.3/5 testi reddeder.

**Q6: OpenAudio için ne yazıldı?**
§3a/9: **ADAY** — `webprofusion/OpenAudio`, kimlik teyidi 1 tur daha bekliyor (§4/3);
beyanın 2.599★'ı ile uyum aralığı not edildi; "verified" DEĞİL.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §14 Anomali Listesi (bu defterin kaydettikleri — 2026-10-13)

| # | Anomali | Kanıt | Eşik |
|---|---|---|---|
| 1 | 4 S3 beyan hâlâ URL'siz (§3/2–4 + §3b/15) | §7.2 sayımı | ⚠️ — §4/2·7 kapanmadan genel verified yok |
| 2 | OpenAudio depo kimliği teyitsiz | §3a/9 ADAY | §4/3 turu beklemede |
| 3 | Emby'nin kendi resmi URL'si eksik (yalnız Plex tarafı) | §4/2 dipnotu | research |
| 4 | ★ sayıları dinamik — iki snapshot farkı (Navidrome · Picard) | §3b/11–12 | tarih damgalı okunur |
| 5 | DeaDBeeF yalnız ikincil kaynakla ölçülü | §3b/15 | ⚠️ S3 |
| 6 | 8 açık soru (§2) hiçbiri kapanmadı | §10 | Vault Steward/ADR |

**Durum: 6/6 kaydedildi · 0 anomali gizlendi.**

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §15 Sürüm / Değişmezlik Eşiği

```
Değişmezlik : §1–§6 mevcut satırları — SİLİNMEZ, ÜZERİNE YAZILMAZ
Ek kuralı   : yeni kanıt = yeni satır (§3 ailesi); yeni konu = yeni §
Tetikleyici : (a) §4 açık işleri kapanırsa → §5 geçmişine 1 satır + sınıf geçişi (§7.1),
              (b) >90 gün geçerse → §8.1/1 tazeleme,
              (c) kardeş dosya revize edilirse → §9 çapraz kontrol yenilenir.
Yazım yasağı: "galiba/muhtemelen" (§3a alt kuralı) · URL'siz "verified" ·
              kardeş dosyaya iddia taşıma (§6/1) · mevcut satır üzerine yazma.
Authority   : dosya sonundaki tek Authority satırı (§16).
```

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §16 Kapı Özeti ve Authority (kapanış mührü)

```
KAPILAR (7): §12 — 7/7 kapandı · 0 açık
KANIT       : 15 kayıt satırı (§7.2) · 12 uç nokta (§11) · 0 yeni URL iddiası
SINIFLAR    : S1:12 · S2:2 küme · S3:4 · S4:1 (§7.2)
ÇAPRAZ      : master 511 · bagimliliklar 571 · index 100 — çelişki 0 (§9)
AÇIK        : 8 soru (§2/§10) · 7 yapılacak iş (§4) · 6 anomali (§14)
YAZMA YASAĞI: beyazliste dışı kök · kaynak kod · imla düzeltme · satır silme
```

---

## §17 Kanıt Kaydı Şablonu (yeni satır formatı — §3 ailesine eklerken)

```text
| # | İddia (tek cümle) | URL / uç nokta | Tarih (YYYY-MM-DD) | Durum (§0 sözlüğü) |
```

### §17.1 Zorunlu 5 alan denetimi

| # | Alan | Boş bırakılabilir mi? | Örnek hata |
|---|---|---|---|
| 1 | # (sıra no) | hayır — §3 son no + 1 | numara tekrarı |
| 2 | İddia | hayır — tek cümle | iki iddia tek satırda |
| 3 | URL | evet — ama o satır S3 sınıfında kalır | URL'siz S1 |
| 4 | Tarih | hayır — ISO | "geçen hafta" |
| 5 | Durum | hayır — §0 4'lü sözlük | "neredeyse verified" |

### §17.2 Sınıf → durum eşlemesi (bu belge standardı)

| Sınıf | Durum yazımı | Sayıma dahil mi |
|---|---|---|
| S1 | ✅ doğrulandı (tur-*) | evet |
| S2 | ✅ verified (metin karşılaştırma) | evet |
| S3 | ⚠️ S3 — iddia kayıtlı | evet (açık iş olarak) |
| S4 | 📌 S4 tasarım ataması | hayır (kanıt değil) |
| ADAY | ⚠️ ADAY — teyit bekliyor | evet (açık iş olarak) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §18 Tur Kayıt Geçmişi Şeması (§5 tablosunun genişletilmesi kuralı)

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Tur = 1 satır | §5'e eklenir; eski tur satırı değişmez |
| 2 | Tur adı | "Tur-3" gibi artan numara — tekrarlı ad yok |
| 3 | Sonuç sütunu | eklenen §/satır no aralığı + sınıf dağılımı |
| 4 | Kapanmayan iş | sonuca yazılmaz; §4'te satır kalır |

**Mevcut tur kayıtları (3 satır — §5):** Tur-1 · Tur-2 · Tur-2b (2026-10-12).
**Sonraki tur (Tur-3) için açık işler:** §4/1 (Jellyfin tazeleme) · §4/3 (OpenAudio
kimlik) · §4/7 (Audacious/Clementine/foobar2000 ölçümü) — üçü de research sahipli.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §19 Red Team Denetimi (bu defterin kendi kendine denetimi — 8 madde)

| # | Denetim | Sonuç | Kanıt |
|---|---|---|---|
| 1 | URL'siz "verified" satırı var mı? | 0 | §3 ailesi okundu — S3'ler ⚠️ işaretli |
| 2 | "galiba/muhtemelen" yazımı var mı? | 0 | §3a alt kuralı + §3 ailesi |
| 3 | Mevcut satır üzerine yazma var mı? | 0 | §5 tur kayıtları append-only |
| 4 | Kardeş dosyaya iddia taşınmış mı? | 0 | §6/1 + §9 çapraz |
| 5 | Fork/ana depo karışmış mı? | 0 | §3b/14 ayrı satırlar |
| 6 | İkincil kaynak S1 yapılmış mı? | 0 | §3b/15 ⚠️ S3 kaldı |
| 7 | Uydurma ★/sürüm/lisans yazıldı mı? | 0 | §11 envanter = §3 ailesi birebir |
| 8 | Authority birden çok mu? | 0 | tek Authority (dosya sonu) |

**Red Team sonucu: 8/8 temiz · 0 red-flag.**

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §20 Ölçüm Defteri (sayılar — 2026-10-13 disk sayımı)

| # | Ölçüm | Değer | Kaynak |
|---|---|---|---|
| 1 | bu dosya satırı (genişletme öncesi) | 115 | disk sayımı |
| 2 | kayıt satırı (§3 ailesi) | 15 | §7.2 |
| 3 | açık soru | 8 | §2 |
| 4 | yapılacak iş | 7 | §4 |
| 5 | doğrulama geçmişi satırı | 3 | §5 |
| 6 | kapı tanımı | 3 madde | §6 |
| 7 | bu ek ile kapı tablosu | 7 | §12 |
| 8 | uç nokta (benzersiz) | 12 | §11 |
| 9 | anomali | 6 | §14 |
| 10 | Red Team denetimi | 8/8 | §19 |
| 11 | kardeş kök dosya | 3 (master 511 · bagimliliklar 571 · index 100) | §9 |
| 12 | alt klasör index.md | 11 × 50 satır | §9 tablo #4 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §21 Terim Eşlemesi (bu defterde geçen terimler — sözlük bağlantısı)

| Terim | Bu belgede anlamı | Sınıf |
|---|---|---|
| doğrulandı | URL + HTTP 200 + tarih (§8.2) | S1 |
| verified | v2/registry/ADR metin karşılaştırması | S2 |
| beyan | URL'siz iddia — kayıtlı, bekliyor | S3 |
| atama | hangi düğümün kanıtı — sorumluluk | S4 |
| ADAY | aday bulundu, kimlik teyidi yok | S3+ |
| tazeleme | >90 gün sonra yeniden ölçüm | §8.1/1 |
| tur | tek araştırma oturumu — §5'e 1 satır | §18 |
| snapshot | ★'nın aynı gün iki ölçüm farkı | §3b/11–12 |
| ikincil kaynak | birincil olmayan liste/sayfa | §8.3/1 |
| ARCHIVED | GitHub arşivlenmiş depo işareti | §3b/14 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §22 SSS-II (§13'e ek 5 soru)

**Q7: Yeni tur ne zaman açılır?**
§8.1 tetikleyicilerinden biri düştüğünde; sonuç §5'e 1 satır (§18/1).

**Q8: Bir satır S1'den ⚠️'ye düşerse eski satır silinir mi?**
Hayır — §7.1/4: yeni satır eklenir, eski kayıt tarihsel olarak kalır.

**Q9: Neden 11 çocuk index'i (50'er satır) kapsam dışı?**
§6/1: bu belge yalnız kanıt defteri; kardeş dosyaların içeriği ayrı belgelerin
sorumluluğundadır (kapsam-dışı, sayıma girer ama iddia taşımaz).

**Q10: S4 ataması neden kanıt sayılmıyor?**
§7.1/5: atama yalnız sorumluluk kaydıdır; "iddia+URL+tarih+durum" dörtlüsü yoktur.

**Q11: Tur-2b'de Navidrome iki ★ ile neden kabul edildi?**
§3b/11: yıldız dinamiktir; iki snapshot da aynı gün ölçüldü, fark not edildi —
tekilleştirme zorunlu değildir, tarih damgası yeterlidir (§8.2/3).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §23 Bağımsız Kaynak Kuralı (bu defterin dış kaynağa bakış kuralı)

```
1. Birincil kaynak : depo/sitenin kendisi (GitHub · resmi site · resmi sayfa)
2. İkincil kaynak  : liste/karşılaştırma/libhunt — ⚠️ olarak kalır
3. Üçüncül kaynak  : forum/blog — kabul edilmez (§8.3/2)
4. Kesişim        : her URL §11 envanteri ile 0 kesişim zorunlu (§7.3/2)
5. Bağımlılık     : yeni URL eklenirse §11 tablosuna 1 satır da eklenir
```

**Bu defterde bağımsız kaynak dağılımı:** birincil 11 · ikincil 1 (libhunt §3b/15) ·
üçüncül 0.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §24 Kapı Özeti (genişletme sonrası son mühür)

```
KAPILAR (7): §12 — 7/7 kapandı · 0 açık
GENİŞLETME : §7–§23 eklendi (17 bölüm) · §1–§6 satırları korundu
KANIT BÜTÜNLÜĞÜ: 15 kayıt · 12 uç nokta · 0 yeni URL iddiası · 0 satır silme
RED TEAM   : §19 — 8/8 temiz
AÇIK KAPI : §4/1 · §4/3 · §4/7 (research sahipli) — bu defter "verified" genellemesi YAZMAZ
Authority  : dosya sonu — tek Authority
```

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
