---
node_id: K012-services
node_type: domain
role: dogrulama
status: draft
date: 2026-10-12
version: 1.0.0
kaynak-turu: "web-kanıtı beyanları (2026-10-12) + kaynak metin karşılaştırması"
authority: "SSOT: ADR-097 §5.3 + görev araştırma turu 2026-10-12"
---

# K012-services — services-dogrulama.md

> Bu belge **kanıt defteridir**: neyin doğrulandığı, neyin beklemede olduğu.
> Doğrulanmayan hiçbir iddia "verified" sayılmaz.

## §0 Kanıt sınıflandırması

| Sınıf | Ne demek | Bu belgede |
|---|---|---|
| S1 · URL'li web kanıtı | İddia + link + tarih + durum | — (bu domain için URL'li kanıt yok) |
| S2 · kaynak metin kanıtı | v2 / registry / ADR satırı | çocuk listesi, master adı |
| S3 · beyan (URL'siz) | Görev beyanında iddia var, link yok | hub-and-spoke spec, Protobuf model |
| S4 · atama | Hangi düğümün kanıtı — tasarım kararı | iki iddianın K012'ye eşlenmesi |

## §1 Ölçütler ve sonuçlar

| # | Ölçüt | Yöntem / kaynak | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.12 ile birebir (10 çocuk) | metin karşılaştırma | ✅ verified |
| 2 | Master dosyası adı services-master.md (registry §5/12) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan (node_id…open_questions) | alan sayımı | ✅ verified |
| 4 | 5 dosyalık MD seti tam + 10 çocuk index'i | disk sayımı 2026-10-12 | ✅ verified |
| 5 | §4 satırları yalnız 2026-10-12 beyanına dayanıyor | satır-başı kanıt kontrolü | ✅ verified |
| 6 | servis çalışma zamanı / kuyruk teknolojisi | kanıt turu | ❌ doğrulanmadı → ⚠️ |
| 7 | ai-service'in bağlı olduğu düğüm (K006 vs K016) | registry §6 okundu | ❌ belirsiz → ⚠️ |
| 8 | Kademe-3 bounded-context kapsamı | — | ⏳ faz C (ADR-097 K4) |

## §2 Açık sorular (open_questions)

| # | Soru | Etiket | Not |
|---|---|---|---|
| 1 | ai-service → K006-yapay-zeka mı K016-AI mı? | ⚠️ VERIFICATION REQUIRED | registry §6 kapsam ayrımı |
| 2 | playback-service ↔ K015/playback sahipliği | ⚠️ VERIFICATION REQUIRED | İki "çalma" sahibi |
| 3 | audio-service ↔ K003-audio sınırı | ⚠️ VERIFICATION REQUIRED | Servis mi, motor mu |
| 4 | service-contracts evrensel Playlist/Track spec'i mi? | TASARIM İDDİASI | hub-and-spoke iddiası |
| 5 | hub-and-spoke + Protobuf için CoreMusic ADR'si var mı? | onay bekliyor | Vault Steward |
| 6 | background-jobs motoru (kuyruk/zamanlayıcı) nedir? | ⚠️ UNKNOWN | Kanıt yok |
| 7 | device-service ↔ K010-identitiy/device-identity sınırı | ⚠️ VERIFICATION REQUIRED | Kimlik verisi sahipliği |

## §3 Web-kanıt kayıtları (tur-1 · 2026-10-12)

| # | İddia | URL / uç nokta | Tarih | Durum |
|---|---|---|---|---|
| 1 | Evrensel Playlist/Track sözleşmesi hub-and-spoke modeliyle kurulabilir (open-playlist-engine yaklaşımı) | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ S3 — iddia kayıtlı, URL tur-2 |
| 2 | Kanonik model örneği Protobuf ile kurulabilir (portify benzeri) | ⚠️ URL görev beyanında yok | 2026-10-12 | ⚠️ S3 — iddia kayıtlı, URL tur-2 |
| 3 | İddia 1 → K012/service-contracts eşlemesi | — | 2026-10-12 | 📌 S4 tasarım ataması |
| 4 | İddia 2 → K012/service-contracts eşlemesi | — | 2026-10-12 | 📌 S4 tasarım ataması |

> Bu belgeye yalnız **iddia · URL · tarih · durum** dörtlüsüyle kayıt girer;
> "galiba", "muhtemelen" gibi derecelendirme yazılmaz.

## §4 Tur-2 yapılacaklar (kanıt bekleyen)

| # | İş | Kapatır | Sahip |
|---|---|---|---|
| 1 | open-playlist-engine (hub-and-spoke Playlist/Track) resmi kaynağını bul | §3/1 | research |
| 2 | portify (Protobuf kanonik model) kaynağını bul | §3/2 | research |
| 3 | ai-service sahipliğini K006/K016 üzerinden karara bağla | §2/1 | Vault Steward |
| 4 | playback sahipliğini K012/K015 sınırıyla netleştir | §2/2 | Vault Steward |
| 5 | Servis çalışma zamanı + kuyruk teknolojisi kanıtı topla | §1/6 · §2/6 | explore |

## §5 Doğrulama geçmişi

| Tarih | Tur | Sonuç |
|---|---|---|
| 2026-10-12 | Tur-1 (görev araştırma turu + kaynak metin karşılaştırma) | 5/8 ölçüt verified · 2 açık ⚠️ · 1 belirsiz ⚠️ |

## §6 Kapı tanımı (bu dosyanın geçerlilik sınırı)

1. Bu belge yalnız K012-services'i kapsar; kardeş domain dosyalarına iddia **taşıtmaz**.
2. Sonraki tur: §4 tablosundaki işler kapanmadan "verified" genellemesi yapılmaz.
3. Yeni kanıt = yeni satır (§3), mevcut satırın **üzerine yazma** (append-only).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
