---
node_id: "K012-services/playback-service/playback-service-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K012-services/playback-service"
children: []
depends_on:
  - "services-dogrulama.md"
  - "index.md"
evidence:
  - "10 URL — HTTP 200 (2026-10-16)"
  - "kesişim testi: url-envanter.txt — çakışma 0"
  - "kesişim testi: url-inventory.txt — çakışma 0"
  - "K008 playback-web-arastirma (10 URL) ile byte-eşleşme 0 · K012 services-dogrulama §5 (15 URL) ile eşleşme 0 · kardeş background-jobs-web-arastirma ile eşleşme 0"
validation:
  - "2026-10-16 — 10 benzersiz URL; her biri HTTP HEAD ile 200 doğrulandı (2026-10-16)"
  - "Kesişim taraması iki envanter dosyasında da yapıldı; isabet 0 (elenen adaylar §3'te ayrıca gerekçelendirildi)"
open_questions:
  - "Q2 (services-dogrulama §4 / services-master §7/2) bu dosyada KAPANMAZ — yalnız seçenek sunulur; nihai karar ADR gerektirir"
  - "MDN sayfaları W3C spesifikasyonundan farklıdır; normatif değerlendirme K008 playback-web-arastirma §1 W-10 (w3.org/TR/mediasession) üzerindedir — tekrarlanmadı, pointer §4"
group: kanit
---

# playback-service — Web Araştırması (K012)

> **Authority:** Bayram Ali / Vault Steward · 2026-10-16
> 10 URL **yalnız bu dosyada** (K012 içi + kardeş dosyalar çakışması 0).
> Doğrulama: **2026-10-16 (HTTP HEAD, hepsi 200)** · Kesişim: url-envanter.txt (2701 satır) + url-inventory.txt (2761 satır) → **0**.
> Konu: **Q2 — "ne çalıyor / sıra / konum" durumunun sahipliği** (playback servisi K012 mi, medya kitaplığı domeni K015 mi).

## §1 KAYNAK TABLOSU

| W | URL | Ne için (bu dosyanın iddiası) | HTTP 200 |
|---|-----|-------------------------------|----------|
| W-1 | https://en.wikipedia.org/wiki/Client%E2%80%93server_model | İstemci/sunucu görev bölünümü — durumun kime ait olduğu sorusunun temel deseni | 200 |
| W-2 | https://en.wikipedia.org/wiki/Media_player_software | Oynatıcı yazılımı: oynatma durumu (kontrol/akış) ile kitaplık içeriği ayrımı | 200 |
| W-3 | https://en.wikipedia.org/wiki/Music_streaming_service | Akış servisi yapısı: oynatma oturumu ile hesap/geçmiş katmanının ayrımı | 200 |
| W-4 | https://en.wikipedia.org/wiki/Session_\(computer_networking\) | Session tabanlı now-playing durumu — sunucu tarafı oturum bağlamı | 200 |
| W-5 | https://en.wikipedia.org/wiki/Last.fm | Sunucu tarafı scrobble/geçmiş ayrımı — "ne çalıyor" (now playing) ile "ne çalındı" (history) ayrı yazılır | 200 |
| W-6 | https://en.wikipedia.org/wiki/Spotify | Cross-device oynatma (Spotify Connect) — cihazlar arası now-playing sahipliği endüstri örneği | 200 |
| W-7 | https://www.rfc-editor.org/rfc/rfc7233 | HTTP Range istekleri — kesintisiz/resumable oynatma (pozisyonun taşıyıcıda korunması) | 200 |
| W-8 | https://developer.mozilla.org/en-US/docs/Web/API/MediaSession | Sistem/lock ekranı now-playing metadata + playbackState yüzeyi (MDN) | 200 |
| W-9 | https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement/currentTime | İstemci pozisyon yüzeyi (currentTime) — arayüz sayfası değil özellik sayfası | 200 |
| W-10 | https://developer.mozilla.org/en-US/docs/Web/API/MediaCapabilities | Decode/encode yetenek yüzeyi — W3C TR media-capabilities K012 §5/14'te olduğu için MDN kullanıldı | 200 |

## §2 Q2 SEÇENEK MATRİSİ

services-dogrulama §4 Q2 · services-master §7/2 (playback-service ↔ K015/playback sınırı).

| Seçenek | Kaynak (W-x) | Artı | Eksi | Etkilenen dosya |
|---|---|---|---|---|
| A — "Ne çalıyor / sıra / konum" durumunu **playback-service (K012)** yazar; K015 medya kitaplığı salt içerik okur | W-1, W-2, W-8, W-9 | Tek yazıcı otorite → yarış/race durumu yok; Media Session ve pozisyon yüzeyi doğrudan servise bağlanır | Sunucu oturumu (W-4) ile istemci element (W-9) arasında iki katmanlı senkron gerekir; resume için ek taşıma (W-7) | playback-service/index · playback-service-anayasa · service-contracts/* · services-master §7/2 · services-dogrulama §4/Q2 |
| B — Sahiplik **K015 medya kitaplığı domeninde**; playback-service yalnızca görüntüleme/okuma yapar | W-2, W-3, W-5 | Dinleme geçmişi + scrobble + kitaplık sorguları tek yerde; tekrarlı depolama yok | "Ne çalıyor" akışı her okumada K015'e bağımlı; playback-service rolü daralır, Q2 yine kapanmaz | K015 playback kardeşi · playback-service/index · services-bagimliliklar |
| C — **Bölünmüş sahiplik:** canlı oturum durumu (now-playing/sıra/konum) playback-service'te (session), kalıcı geçmiş/favori/dinleme kaydı K015'te; sınır service-contracts ile sabitlenir | W-3, W-4, W-5, W-6 | Now-playing ile history ayrımı endüstri pratiğiyle (Last.fm) örtüşür; cross-device aktarımı (W-6) netleşir | İki yazıcı sınırı contract'te yoksa çakışma riski; K012-K015 veri yönü ayrıca doğrulanmalı (services-master §7/8) | service-contracts/* · playback-service/* · K015 playback · services-master §7/2, §7/8 |
| D — Durum **yalnız istemcide** yaşar (HTMLMediaElement + MediaSession), sunucu kalıcılığı yok | W-8, W-9 | Implementasyon en ucuz; kitaplık bağımsız kalır | Resumable/cross-device yok (W-6, W-7 ile çelişir); scrobble/history üretilemez (W-5); Q9 transport sorusu boş kalır | playback-service-ornekler · K015 library · services-dogrulama §4/Q9 |

**Öneri-seviyesi (tek yorum — KARAR DEĞİL):** Kanıt yoğunluğu sırası **C > A > B > D**; en çok bağımsız kaynak C seçeneğini (W-3, W-4, W-5, W-6) ve ardından A'yı (W-1, W-2, W-8, W-9) destekliyor. Bu bir karar değildir — **karar ADR'ye aittir** (services-master §7/2 → ⚠️ VERIFICATION REQUIRED korunur).

## §3 REDDEDİLEN ADAYLAR

| Aday | Neden (RED) | Tarih |
|---|---|---|
| https://html.spec.whatwg.org/multipage/media.html | **Envanter çakışması** — url-envanter.txt satır 1015 · url-inventory.txt satır 1023 | 2026-10-16 |
| https://www.w3.org/TR/mediasession | **K008 tekrarı** — playback-web-arastirma §1 W-10 (aynı spesifikasyon) | 2026-10-16 |
| https://www.w3.org/TR/media-capabilities/ | **K012 içi çakışma** — services-dogrulama §5/14 (15 URL yasağı) → W-10 (MDN) | 2026-10-16 |
| https://developer.mozilla.org/en-US/docs/Web/API/HTMLMediaElement (arayüz sayfası) | **K008 tekrarı** — playback-web-arastirma §1 W-2 ile aynı sayfa (mojibake yazım farkı) → W-9 (currentTime özellik sayfası) | 2026-10-16 |
| https://developer.mozilla.org/en-US/docs/Web/API/Media_Session_API | **K008 tekrarı** — playback-web-arastirma §1 W-3 → §4 pointer | 2026-10-16 |
| https://en.wikipedia.org/wiki/Media_control_keys | HTTP **404** | 2026-10-16 |
| https://en.wikipedia.org/wiki/Play_count | HTTP **404** | 2026-10-16 |
| https://en.wikipedia.org/wiki/Handoff | HTTP 200 ama içerik "#REDIRECT [[Handover]]" → hedef konu dışı (hücresel handover) | 2026-10-16 |
| https://en.wikipedia.org/wiki/Now_Playing | HTTP 200 ama **disambiguation** (albüm/dergi listeleri) — iddia taşımaz | 2026-10-16 |
| https://en.wikipedia.org/wiki/Media_player | HTTP 200 ama **disambiguation** → W-2 (Media_player_software) ile değiştirildi | 2026-10-16 |
| https://en.wikipedia.org/wiki/Scrobbling | HTTP 200 ama "#REDIRECT [[Last.fm]]" → W-5'e indirgendi (ayrı satır sayılmadı) | 2026-10-16 |
| https://en.wikipedia.org/wiki/State_management | HTTP 200 ama içerik UI kontrol state'i (form alanları) — Q2 kapsamı dışı | 2026-10-16 |
| https://en.wikipedia.org/wiki/Client-server_model | HTTP 200 ama "#REDIRECT [[Client–server model]]" → en-dash biçimiyle (W-1) yeniden yazıldı | 2026-10-16 |

## §4 KAPSAM DIŞI + KARDEŞ POINTERLAR

| Konu | Nereye gitti |
|---|---|
| Media Session Level 1 normatif spesifikasyon (w3.org/TR/mediasession) + Web Audio çekirdeği | [[../../K008-domain/playback/playback-web-arastirma]] §1 W-10, W-1 — **URL'ler tekrar yazılmadı** |
| HTMLMediaElement arayüz sayfası (çal/duraklat/pozisyon geneli) | [[../../K008-domain/playback/playback-web-arastirma]] §1 W-2 |
| Media Capabilities W3C TR | [[services-dogrulama]] §5/14 (15 URL envanteri) |
| HLS / akış protokolleri | K008 playlist-web-arastirma — URL'leri orada |
| Ses formatı kodlama | K008 media-web-arastirma — URL'leri orada |
| Dinleme geçmişi / favori (veri modeli) | K015 medya kitaplığı domeni — Q2'nin diğer tarafı |
| Arka plan/zamanlanmış iş motoru (Q5) | [[background-jobs/background-jobs-web-arastirma]] — URL'leri o dosyada |

**Kardeş dosyalar:** [[playback-service/index]] · [[playback-service/playback-service-anayasa]] · [[playback-service/playback-service-surucu]] · [[playback-service/playback-service-ornekler]]

---

## §5 DEĞİŞİKLİK KAYDI

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-16 | İlk yazım — 10 URL (HEAD 200), kesişim 0, K008/K012 çakışma 0 | Researcher subagent (git commit YASAK — commit orkestratöre aittir) |
| 2026-10-16 | 13 aday §3'te gerekçesiyle elendi (envanter 1 · 404 2 · K008/K012 tekrarı 3 · yönlendirme/disambig/kapsam dışı 7) | Researcher subagent |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode