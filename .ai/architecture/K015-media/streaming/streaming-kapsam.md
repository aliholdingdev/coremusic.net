---
node_id: K015-MEDIA-STREAMING-KAPSAM
node_type: mimari
status: active
parent_id: K015-MEDIA
children: []
depends_on: [K015-MEDIA-INDEX, K011-APPLICATION, K014-NETWORK, K013-INTEGRATION]
evidence: [K015-media/index.md, K015-media/media-master.md, K015-media/streaming/index.md]
validation: partial
open_questions: 3
group: mimari
---

# Streaming — Kapsam (K015-media/children/streaming)

> **Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
>
> Bu dosya `streaming` düğümünün **kapsam sözleşmesidir**: neyi kapsar, neyi
> kapsamaz, durum makinesi nedir, veri akışı nasıl akar, hangi hata
> durumları tanımlıdır ve hangi sorular açıktır. Referans formatı:
> `.ai/architecture/K009-security/threat-model/threat-model-kapsam.md`.
>
> **Her satır TASARIM İDDİASI'dır** (yalnızca düğüm adı — S2 — kesin ifadedir);
> doğrulanmamış içerik `VERIFICATION REQUIRED` ile işaretlenir.
> ADR ile çelişen madde ADR lehine düşer.

## §1 — Kapsam İçi

### §1a — Varlıklar (kapsam içi)

| Varlık | Kapsam içi tanım | Durum |
|---|---|---|
| Akış oturumu (stream session) | Bir kullanıcının başlattığı, sürdürülen ses akışı bağlantısı | TASARIM İDDİASI |
| Akış kaynağı (stream source) | Dış servisten gelen medya akışı (uzak sunucu) | TASARIM İDDİASI |
| Tampon (buffer) | İstemcide akış verisinin biriktirildiği alan | TASARIM İDDİASI |
| Yeniden tamponlama (rebuffering) | Tampon tükendiğinde oynatmanın duraklayıp doldurulması | TASARIM İDDİASI |
| Akış protokolü | İstemci ile sunucu arasındaki taşıma düzeni (türü §6/2 — UNKNOWN) | VERIFICATION REQUIRED |
| Codec seti | Akışta kullanılan ses codec'leri (§6/3 — media-master §12'de UNKNOWN) | VERIFICATION REQUIRED |

### §1b — Kullanıcı yüzeyi (kapsam içi)

| Yüzey | Kapsam içi davranış | Durum |
|---|---|---|
| Ak başlat / durdur | Kullanıcının akışı başlatması ve durdurması | TASARIM İDDİASI |
| Canlı konum (live position) | Akışın ilerleme/geride kalma göstergesi | TASARIM İDDİASI |
| Kalite/bit hızı seçimi | Kullanıcının veya sistemin akış kalitesini seçmesi (varsa) | TASARIM İDDİASI |
| Yeniden bağlanma | Kopma sonrası otomatik yeniden bağlanma davranışı | TASARIM İDDİASI |

### §1c — Zaman penceresi

| Pencere | Kapsam içi | Not |
|---|---|---|
| Bağlantı kurulumu | Evet | connecting durumu |
| Sürdürme (steady-state) | Evet | buffering / oynatma |
| Yeniden tamponlama | Evet | rebuffering — hata DEĞİL geçici durum |
| Bitiş / hata | Evet | ended / error |

## §2 — Kapsam Dışı

Bu düğüm aşağıdaki konuları **kapsamaz**; ilgili düğüme devredilir:

1. **Taşıma katmanı ayrıntıları (bağlantı/kurulum/uydurma/network taşınması)** —
   K014-network düğümünün sahipliğindedir; K015-media yalnız bu davranışları
   **gözlemleyip kullanır**, taşımayı kendisi **yürütmez**.
   *Geri dönüş koşulu:* taşıma davranışı K014 dışına çıkarsa ilgili ADR açılır.
2. **Dış kaynağın kendisi (uzak sunucu, içerik kütüphanesi, dış servis)** —
   K013-integration'ın sorumluluğundadır; K015-media bu kaynağı **bir uç
   olarak görür**, iç yapısına girmez.
   *Geri dönüş koşulu:* dış servis arayüzü değişirse K013 ile eşgüdüm gerekir.
3. **K011-application'dan gelen kullanıcı komutlarının yorumlanması** —
   uygulama katmanına aittir; streaming yalnız alınan **komutun kendisine
   ulaşmasıyla** ilgilenir.
4. **Yerel dosya oynatma ve dosya konumu çözümleme** — `playback` kardeş
   düğümünün kapsamıdır; streaming dosya tabanlı oynatma taşımaz.

> **Not (media-master §14/2):** `K011-application → K015-media/streaming →
> K014-network → K013-integration → dış servis` — bu zincirde K015-media
> **taşımaz, orkestre eder**.

## §3 — Durum Makinesi

**Durum kümesi:** `connecting · buffering · rebuffering · playing · ended · error`

> `playing` durumu §4 zincirinde akışın aktığı durumdur; §12/4'teki
> "buffering" tanımı bu tabloyla **çelişir** — bu bir açık sorudur (§6/4).

| Durum | Tanım | Tetikleyen geçiş | Durum |
|---|---|---|---|
| S-1 · connecting | Akış bağlantısı kuruluyor | `start` (kullanıcı ak başlatır) → S-1 | TASARIM İDDİASI |
| S-2 · buffering | İlk tampon dolduruluyor (bağlantı kuruldu, veri akıyor) | S-1'de ilk veri geldi → S-2 | TASARIM İDDİASI |
| S-3 · playing | Akış akıyor, oynatma sürüyor | S-2 yeterli doluluk → S-3 | TASARIM İDDİASI |
| S-4 · rebuffering | Tampon tükendi; oynatma duraklatıldı, dolduruluyor | S-3'te tampon tükendi → S-4 | TASARIM İDDİASI |
| S-5 · ended | Akış doğal sona erdi (yayın bitti / kaynak tükendi) | S-3'te kaynak bitti → S-5 | TASARIM İDDİASI |
| S-6 · error | Geri döndürülemez hata (bağlantı kurulamadı, kaynak yok, decode reddi) | herhangi bir durumdan → S-6 | TASARIM İDDİASI |

**Geçiş kuralı:** S-4 (rebuffering) **hata değildir** — doldurma başarılı
olursa S-3'e geri dönülür; üst üste tekrarlarsa kalite düşürme/kesme kararı
üst katmanın (K011/K012) sorumluluğundadır. S-6'ya yalnız **geri
döndürülemez** hatalarla girilir. Geçişlerin tamamı **tek yönlü
bağımlılıkla** izlenir (geri bildirim yukarı doğru akar).

## §4 — Veri Akışı Zinciri (media-master §14.2 — birebir)

```text
K011-application → K015-media/streaming → K014-network (taşıma) → K013-integraion (dış kaynak) → dış servis
```

> `K015-media/streaming` (dosya konumu) — zincir §14/2 ile **birebir aynıdır**;
> buradaki metin media-master.md'den kopyalanmıştır (diff temiz tutulur).

**Açıklama:** Kullanıcı komutu K011'den gelir; streaming düğümü komutu
**orkestre eder** ve taşımayı K014'e, dış kaynağı K013'e devreder; nihai
uç dış servistir. **K015-media bu zincirde ne taşır ne de dış kaynağa
doğrudan bağlanır — yalnız orkestre eder.**

## §5 — Hata Durumları

| # | Hata | Durum karşılığı | Kapsam içi davranış | Durum |
|---|---|---|---|---|
| H-1 | Bağlantı kurulamadı (dış servis erişilemiyor) | → S-6 error | Kullanıcıya hata yüzeyi; yeniden deneme üst katmanın | TASARIM İDDİASI |
| H-2 | Bağlantı koptu (akış ortasında) | S-3'ten → yeniden bağlanma (S-1) veya S-6 | Otomatik yeniden bağlanma; kaç kez — §6/5 | TASARIM İDDİASI |
| H-3 | Tampon tükendi (rebuffering) | → S-4 (hata DEĞİL) | Doldurmaya devam; kalite düşürme önerisi üst katmana | TASARIM İDDİASI |
| H-4 | Kaynak codec'i istemci tarafından reddedildi | → S-6 error | Decode reddi; codec seti §6/3 — UNKNOWN | VERIFICATION REQUIRED |
| H-5 | Dış servis hata yanıtı döndürdü (4xx/5xx) | → S-6 error | K013 üzerinden hata iletilir; yorum K013'ün | TASARIM İDDİASI |
| H-6 | Akış sona erdi (yayın bitti) | → S-5 ended (hata DEĞİL) | ended durumu; yeniden başlatma kullanıcıya | TASARIM İDDİASI |

## §6 — Açık Sorular

| # | Soru | Bağlantı | Öncelik | Durum |
|---|---|---|---|---|
| 1 | Akış protokolü türü ne olacak (HLS / DASH / ICY-Shoutcast / WebRTC / MSE-SourceBuffer)? | media-master §12/4 | Yüksek | **AÇIK** — media-master §12/4'te protokol UNKNOWN |
| 2 | Codec seti ne olacak (Örn. Opus / AAC / MP3)? | media-master §12/1-§12/3 | Yüksek | **AÇIK** — §12'de UNKNOWN, §14/2 zinciri codec taşımaz |
| 3 | `buffering` mü ayrı durum, `rebuffering` mı — §12/4 ile §14/2 çelişkisi nasıl çözülür? | media-master §12/4 | Orta | **AÇIK** — bu dosya §3'te rebuffering'i ayrı durum aldı (tasarım kararı) |
| 4 | K014-network'te protokol/taşıma seçimi mi, yalnız kanal mı? | K014-network | Orta | **AÇIK** — media-master §14/2 "taşıma" diyor; seçimin sahibi belirsiz |
| 5 | Kopma sonrası yeniden deneme sayısı ve geri çekilme (backoff) politikası? | K015-media/streaming | Düşük | **AÇIK** |

## §7 — Revizyon

| Tarih | Revizyon | Authority |
|---|---|---|
| 2026-10-16 | İlk kapsam — K009 formatı; durum makinesi 6 durum; §14.2 zinciri birebir; protokol/codec UNKNOWN olarak korundu | Bayram Ali / Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
