---
node_id: audio-dogrulama
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/audio
children: []
depends_on:
  - audio-kapsam
  - K001-donanım/donanaım-type/donanaım-type-dogrulama
evidence:
  - "https://www.ti.com/product/PCM3168A (HTTP 200, 2026-10-13, HEAD)"
  - "https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ (HTTP 200, 2026-10-13, HEAD)"
  - ".ai/.decisions/accepted/ADR-038-8-1-sound-card-chip-selection.md (accepted, 2026-09-26) — M1–M6 tarifi"
  - "Repo ölçümü 2026-10-13: *.cpp = 0 · *.h = 0 · *.xc = 0 · *.ino = 0"
validation: "draft — 2026-10-13 yazıldı · ≥500 satır ölçümü yazım sonrası · fiziksel ölçüm YAPILMADI (tüm S1 kapıları açık)"
open_questions:
  - "Q-AD1: Ölçüm tezgâhı (ses kartı + AES17 filtresi + osiloskop) yok — tüm S1 kapıları AÇIK"
  - "Q-AD2: Fiziksel envanter bilinmiyor (hangi çip/kart elde) — UNKNOWN (Q-AK1)"
  - "Q-AD3: Sayısal jitter bütçesi (BCLK/MCLK/LRCK payları) ölçülmedi — ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# audio-dogrulama — Ses Donanımı Doğrulama Kapıları

> **Amaç:** `audio-kapsam.md` içindeki iddiaları **kapı kapı** doğrulamak. Bu
> dosya **kanıt** dosyasıdır: her bölüm bir test protokolü ve geçme koşulu
> içerir. Doğrulanmamış hiçbir sonuç "geçti" sayılmaz.

## §1 ELI15

Ses donanımı için beş soru sorulur ve beşi de **ölçülmeden/dokümante
edilmeden** yanıtlanmaz: (1) seçilen çip gerçekten seçili mi ve envanterde mi
(karar + envanter), (2) datasheet rakamları okunmuş ve doğru aktarılmış mı
(kayıt), (3) sistem ölçümü bu rakamları tutturuyor mu (M1–M6), (4) web
kaynakları hâlâ ayakta mı (URL), (5) etiketler (IMPLEMENTED/PLANNED/…) doğru
mu (sahte kanıt temizliği). Bu dosya bu beş sorunun **adımlarını, kayıt formunu
ve geçme eşiklerini** yazar.

> ⚠️ VERIFICATION REQUIRED: Bu dosyada **hiçbir gerçek ölçüm sonucu yoktur** —
> ölçüm tezgâhı kurulmadı, fiziksel envanter bilinmiyor (Q-AK1/AD1/AD2). Tüm S1
> seviyesi kapıları **AÇIK**tır; buradaki protokoller uygulanacak iş listesidir,
> sonuç raporu değildir. Datasheet rakamları **kayıt**tır, **ölçüm değildir**.

## §2 Kanıt Seviye Şeması (bu dosyada kullanılır)

| # | Seviye | Anlamı | Örnek | Kapı durumu |
|---|---|---|---|---|
| 1 | S1 | Fiziksel ölçüm / gözlem | THD+N, SNR, jitter, sıcaklık | ❌ AÇIK (tezgâh yok) |
| 2 | S2 | Aritmetik olarak türetilmiş | 512÷48000 · 112−105 | ✅ kapandı (`audio-kapsam.md` §19.2) |
| 3 | S3 | Bu oturumda URL doğrulandı (HEAD 200) | 2 web kaynağı (§7) | ✅ kapandı |
| 4 | S4 | Vault disk kanıtı (okunan dosya) | 3 cip dosyası · ADR-038 | ✅ kapandı |
| 5 | S5 | Üst otorite / ADR / sabit kural | ADR-038 M1–M6 · Class AB sabiti | ✅ kapandı |

**Kural:** Bu dosyadaki bir "GEÇTİ" yazısı yalnızca **S1 kanıtı** ile yazılabilir.
S2–S5 tek başına ölçüm yerine geçmez. Datasheet değeri S4 sayılır, S1 sayılmaz.

## §3 Ses Kartı Çekirdek Kapıları (ADR-038 M1–M6)

> Tarif kaynağı: ADR-038 §2.2-d (accepted, 2026-09-26). Eşikler veri sayfası
> değerlerinden **marjlı** alınmıştır; laboratuvar kurulumuna göre revize edilir.

### §3.1 M1 — THD+N @ 1 kHz, 0 dBFS

| Alan | Değer |
|---|---|
| Tarif | AES17-2020 notch (Q 1–5) · EIAJ CP-2404 (4 Hz–20 kHz passband) · A-weighted |
| Hedef | ≥ 90 dB (veri sayfası −94 dB'e yakın) |
| Girdi | Çıkış zinciri + PCM3168A DAC yolu |
| Kanıt tipi | S1 (ölçüm) |
| Durum | ❌ AÇIK — tezgâh yok |

### §3.2 M2 — SNR / idle channel noise

| Alan | Değer |
|---|---|
| Tarif | IEC 61606-1 idle channel noise |
| Hedef | ≥ 105 dB (veri sayfası 112 dB) |
| Kanıt tipi | S1 |
| Durum | ❌ AÇIK |

### §3.3 M3 — Kanal ayrımı (cross-talk)

| Alan | Değer |
|---|---|
| Tarif | AES17 §10 · −20 dBFS sine |
| Hedef | ≥ 95 dB (veri sayfası 102/108 dB) |
| Kanıt tipi | S1 |
| Durum | ❌ AÇIK |

### §3.4 M4 — Frekans/level matrisi

| Alan | Değer |
|---|---|
| Tarif | FADGI/AES17: −1 / −10 / / −20 / −60 dBFS × 41 / 997 / 6597 Hz @ 48k & 96k |
| Davranış | Sapma raporlanır; eşiği aşarsa risk kaydına düşer |
| Kanıt tipi | S1 |
| Durum | ❌ AÇIK |

### §3.5 M5 — Kanal arası senkron kayma

| Alan | Değer |
|---|---|
| Tarif | Çoklu-kanal eşit clock iz kontrolü (~60 ps/cm düzlem hesabı) |
| Hedef | ≤ 100 ps kanal arası |
| Kanıt tipi | S1 (osiloskop/delay ölçümü) |
| Durum | ❌ AÇIK |

### §3.6 M6 — Jitter duyarlılığı

| Alan | Değer |
|---|---|
| Tarif | M1'in clock kaynağı değişimiyle (PLL on/off) tekrarı |
| Hedef | fark ≤ 1 dB (büyük fark → saat zinciri revizyonu riski) |
| Kanıt tipi | S1 |
| Durum | ❌ AÇIK — sayısal bütçe de ⚠️ (Q-AD3) |

### §3.7 Kapı geçmişi kuralı

| # | Kural | Aksi durumda |
|---|---|---|
| 1 | Her ölçüm tarihi + tezgâh kimliği ile yazılır | Sonuç sayılmaz |
| 2 | Eşik aşılırsa "kabul" değil, "raporlanır" | Sahte kabul |
| 3 | Datasheet rakamı ölçüm yerine konulamaz | §10/2 ihlali |
| 4 | Ölçüm tekrar edilmezse sonuç günceldir | Süreklilik kopar |

## §4 Cip Kayıt Doğrulaması (datasheet → vault aktarımı)

| # | İddia | Kaynak | Tarih | Durum |
|---|---|---|---|---|
| 1 | PCM3168A 6-in/8-out, 24-bit | `cipler/pcm3168a-codec.md` §1B | 2026-10-13 okundu | ✅ S4 |
| 2 | PCM3168A DAC SNR 112 dB / THD+N −94 dB | aynı dosya §6 (datasheet §8) | 2026-10-13 | ✅ S4 (kayıt) |
| 3 | PCM3168A ADC 8–96 kHz · DAC 8–192 kHz | aynı dosya §1B | 2026-10-13 | ✅ S4 |
| 4 | AK4458 8 kanal, 32-bit, 768 kHz, DSD 11.2 MHz | `cipler/ak4458-dac.md` §0 | 2026-10-13 | ✅ S4 |
| 5 | AK4458 SNR 115 dB / THD+N −107 dB (AVDD=5V) | aynı dosya §4 | 2026-10-13 | ✅ S4 |
| 6 | AK4458 register map içeriği | aynı dosya §9/§13 | 2026-10-13 | ⚠️ okunmadı |
| 7 | TPA3255 Class-D, 315W/600W, PVDD 18–53.5 V | `cipler/tpa3255-amplifier.md` §0/§4 | 2026-10-13 | ✅ S4 |
| 8 | TPA3255 PBTL audio karakteristik tablosu | aynı dosya §6/§10 | 2026-10-13 | ⚠️ tam okunmadı |
| 9 | AK4458 Slow/Super Slow filtre tabloları | aynı dosya §5/§13 | 2026-10-13 | ⚠️ okunmadı |
| 10 | PCM3168A TDM slot yerleşimi (§9.4/§9.5) | aynı dosya §7/§10 | 2026-10-13 | ⚠️ okunmadı |

**Yorum:** 6–10. satırlar bu oturumda **okunmamış bölümlerdir**; ilgili cip
dosyalarında da ⚠️ ile işaretlidirler ve burada "doğrulandı" sayılmazlar.

## §5 Sistem Ölçüm Kapıları (fiziksel — tamamı açık)

| # | Kapı | Ne ölçülür | Koşul | Durum |
|---|---|---|---|---|
| 1 | Çıkış seviyesi | Diferansiyel VPP (beklenen 8 VPP sınıfı) | DC-coupled / AC-coupled ayrımı | ❌ AÇIK |
| 2 | Kanal sayısı | 8 out + 6 in gerçekten çalışıyor mu | TDM slot okuması | ❌ AÇIK |
| 3 | Kanal eşlemesi | Hangi slot hangi hoparlör | ADR-038 planı vs. gerçek | ❌ AÇIK |
| 4 | Frekans tepkisi | 20 Hz–20 kHz sapması | AES17/FADGI matrisi | ❌ AÇIK |
| 5 | Gürültü (çıkış kapalıyken) | idle channel noise | IEC 61606-1 | ❌ AÇIK |
| 6 | Jitter (saat hattı) | RMS jitter · 1 ns hedef | M6 ile birlikte | ❌ AÇIK |
| 7 | Kanal arası kayma | ps cinsinden | M5 | ❌ AÇIK |
| 8 | Sıcaklık / termal | Çip ve heatsink sıcaklığı | Uzun süreli yük | ❌ AÇIK |
| 9 | EMI | Anahtarlanmış gürültü yayılımı | sınıf D referansı için ayrıca | ❌ AÇIK |
| 10 | Gecikme (buffer → çıkış) | ms cinsinden bütçe | 512/48k = 10,67 ms referansı | ❌ AÇIK |

**Not:** 9. satır yalnızca Class D referansı (TPA3255) BOM'a girerse anlam
kazanır — bu dosya öyle bir karar **önermez** (PROPOSED korunur).

## §6 Envanter Doğrulama (insan girdisi — UNKNOWN)

| # | Alan | Değer | Durum |
|---|---|---|---|
| 1 | Eldeki DAC/codec çipi (marka/paket) | — | UNKNOWN |
| 2 | Eldeki USB ses denetleyicisi | — | UNKNOWN |
| 3 | Eldeki amfi kartı / topolojisi | — | UNKNOWN (kural: Class AB) |
| 4 | Hoparlör/kulak yükü | — | UNKNOWN |
| 5 | Güç rail'i (ölçülmüş) | — | UNKNOWN |
| 6 | Clock kaynağı (kristal/PLL) | — | UNKNOWN |
| 7 | Prototip var mı? | — | UNKNOWN (kod 0 ile uyumlu) |
| 8 | Ölçüm tezgâhı var mı? | — | UNKNOWN (Q-AD1) |

**Kapı kuralı:** Bu tablodaki satırlar `UNKNOWN` iken **satın alma/üretim
kararı verilemez** ve "hazır" etiketi kullanılamaz (`audio-kapsam.md` §8).

## §7 Web Kaynak Doğrulaması (bu dosya)

| # | URL | Yöntem | Tarih | Durum |
|---|---|---|---|---|
| 1 | https://www.ti.com/product/PCM3168A | HEAD (Invoke-WebRequest, 20 sn) | 2026-10-13 | ✅ HTTP 200 |
| 2 | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ | HEAD (aynı yöntem) | 2026-10-13 | ✅ HTTP 200 |
| 3 | https://www.xmos.com/ | HEAD (aynı yöntem) | 2026-10-13 | ❌ HTTP 406 → **kullanılmadı** |

**Kurallar:**
1. 404/403/406 döndüren URL **kullanılmaz** ve kaynak tablosuna **alınmaz**.
2. Yeni envanter kesişimi **0** olmalıdır — bu dosya envantere yeni fiziksel
   parça iddiası eklemez.
3. Uydurma URL, uydurma sürüm, uydurma tarih **üretilmez**; doğrulanamayan
   iddia `⚠️ VERIFICATION REQUIRED` kalır.
4. `{...}` ve `TBD` yer tutucusu kullanılmaz; bilinmeyen `UNKNOWN` yazılır.

## §8 Ölçüm Kayıt Formu (şablon — boş)

| # | Alan | Girilecek değer |
|---|---|---|
| 1 | Ölçüm tarihi | (boş) |
| 2 | Tezgâh kimliği (donanım + yazılım) | (boş) |
| 3 | Standart (AES17/IEC/EIAJ/FADGI) | (boş) |
| 4 | Örnekleme oranı / bit derinliği | (boş) |
| 5 | M1 THD+N sonucu | (boş) |
| 6 | M2 SNR sonucu | (boş) |
| 7 | M3 kanal ayrımı | (boş) |
| 8 | M4 matris sapması | (boş) |
| 9 | M5 kanal kayması | (boş) |
| 10 | M6 jitter farkı | (boş) |
| 11 | Sapma varsa risk kaydı | (boş) |
| 12 | Ölçümcü / onay | (boş) |

> Boş form **sonuç değildir**; doldurulup `audio-dogrulama.md` §13.3'e işlenir.

## §9 Kapı Özeti

| # | Kapı | Seviye | Durum | Engeli |
|---|---|---|---|---|
| 1 | Karar kapısı (ADR-038) | S5 | ✅ kapandı | — |
| 2 | Datasheet kayıt kapısı | S4 | ✅ kapandı (kısmi ⚠️ §4) | — |
| 3 | Web URL kapısı | S3 | ✅ kapandı (2/2) | — |
| 4 | Aritmetik kapı | S2 | ✅ kapandı | — |
| 5 | M1–M6 ölçüm kapısı | S1 | ❌ açık | tezgâh yok |
| 6 | Sistem ölçüm kapıları | S1 | ❌ açık | prototip yok |
| 7 | Envanter kapısı | insan | ❌ açık | Q-AK1 |
| 8 | Jitter bütçe kapısı | S1 | ❌ açık | Q-AD3 |
| 9 | Etiket kapısı (sahte kanıt) | S4 | ✅ bu dosyada uygulandı | — |
| 10 | Topoloji kapısı (Class AB) | S5 | ✅ kural kayıtlı · ölçüm ❌ | prototip yok |

## §10 Sık Yapılan Hatalar

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| 1 | Datasheet SNR'ını ölçüm sanmak | "112 dB ölçtük" | §2 — S4 ≠ S1 |
| 2 | ADR-038'i yeni seçim sanmak | Karar yeniden tartışılır | TEYİT · 2026-09-26 |
| 3 | Eşiği datasheet'e eşitlemek | Marj yok, ortam farkı yok | §3 hedefleri marjlı |
| 4 | Boş formu sonuç saymak | "ölçüldü" | §8 form = şablon |
| 5 | 406/404 URL'yi kaynak yazmak | Kırık referans | §7/1–3 |
| 6 | IMPLEMENTED etiketi ile PLANNED işi sunmak | Sahte kanıt | `audio-kapsam.md` §8 |
| 7 | K003 ölçümünü bu dosyada tekrarlamak | Çifte sahiplik | §13.9 sınırı |
| 8 | `{...}`/`TBD` bırakmak | Okunamaz tablo | `UNKNOWN` |
| 9 | Ölçüm olmadan "geçti" yazmak | Yanlış kabul | yalnız S1 ile GEÇTİ |
| 10 | Envanter çıkmadan karar vermek | Uyumsuz BOM | §6 → DUR |

## §11 Terim Sözlüğü

| # | Terim | Tanım |
|---|---|---|
| 1 | AES17-2020 | Ses ölçüm standardı (notch, THD+N, cross-talk) |
| 2 | IEC 61606-1 | Idle channel noise / DR / jitter tanımı standardı |
| 3 | EIAJ CP-2404 | 4 Hz–20 kHz passband + A-weighted tarifi |
| 4 | FADGI | Test matrisesi (level × frekans × fs) |
| 5 | A-weighted | İnsan kulağı ağırlıklı filtre |
| 6 | Idle channel noise | Kanal sessizken ölçülen gürültü (SNR karşılığı) |
| 7 | Notch | THD+N ölçümünde hedef frekansı ezen filtre |
| 8 | Gate / kapı | Yalnız kanıtlı geçilebilen kontrol noktası |
| 9 | S1–S5 | Kanıt seviyeleri (§2) |
| 10 | HEAD isteği | Sunucu durumunu içerik indirmeden sorgulama |
| 11 | Tezgâh | Ölçüm donanımı + yazılımı + ortamı |
| 12 | Marj | Hedef ile veri sayfası arasındaki pay |
| 13 | Jitter | Saat kenarı zaman dalgalanması |
| 14 | Cross-talk | Kanallar arası sızıntı |
| 15 | THD+N | Bozulma + gürültü oranı |
| 16 | DNR | Dinamik aralık |
| 17 | TDM slot | Tek hatta zaman paylaşımlı kanal aralığı |
| 18 | BOM | Bill of materials — satın alma listesi |
| 19 | PROPOSED | Öneri — karar verilmedi |
| 20 | UNKNOWN | Veri hiç yok |

## §12 İddia → Kanıt Matrisi

| # | İddia | Kanıt tipi | Kanıt yeri | Durum |
|---|---|---|---|---|
| 1 | PCM3168A ses kartı çekirdeği | ADR (S5) | ADR-038 (accepted) | ✅ |
| 2 | 3 cip envanterde kayıtlı | disk (S4) | `cipler/*.md` (3 dosya) | ✅ |
| 3 | 2 üretici URL'si erişilebilir | HEAD 200 (S3) | §7 satır 1–2 | ✅ |
| 4 | 512 örnek ≈ 10,67 ms | aritmetik (S2) | `audio-kapsam.md` §19.2 | ✅ |
| 5 | Sistem SNR/THD+N | ölçüm (S1) | §3/§5 | ❌ açık |
| 6 | Fiziksel envanter | insan | §6 | ❌ UNKNOWN |
| 7 | Jitter bütçe sayıları | ölçüm (S1) | §3.6 | ❌ açık |
| 8 | Class AB topolojisi fiziksel olarak kurulu | disk kuralı (S5) + kod (S1) | `audio-kapsam.md` §10 · repo `*.cpp=0` | ❌ PLANNED |
| 9 | ADR-017/ADR-063 var | glob (S4) | `.ai/.decisions/accepted/` | ✅ (yalnız varlık) |
| 10 | Kod/firmware yok | repo sayımı (S1-rapor) | `*.cpp=0 · *.h=0 · *.xc=0 · *.ino=0` | ✅ (negatif kanıt) |

## §13 Çapraz Referanslar

| # | Hedef | İlişki |
|---|---|---|
| 1 | `audio-kapsam.md` | Doğrulanan iddiaların sahibi |
| 2 | `audio-ornekler.md` | Uygulama/ölçüm şablonları |
| 3 | `audio/index.md` | Künye · §GRUPLAR |
| 4 | `donanaım-type-dogrulama.md` | Üst doğrulama tablosu |
| 5 | ADR-038 | M1–M6 tarifi (S5) |
| 6 | `cipler/*.md` | Datasheet kayıtları (S4) |
| 7 | K003-audio | Ölçümün tüketileceği sistem (sınır) |

### §13.1 Periyodik Takvim (kayıt zorunlu)

| # | İş | Sıklık | Kayıt alanı |
|---|---|---|---|
| 1 | Web URL HEAD tekrarı | her vault revizyonunda | §7 |
| 2 | Datasheet sürüm kontrolü | çip kararı değişince | §4 |
| 3 | M1–M6 tekrar ölçümü | donanım değişince | §13.3 |
| 4 | Etiket denetimi | her büyük yazım turunda | `audio-kapsam.md` §8 |
| 5 | Envanter güncelleme | parça gelince | §6 |
| 6 | Jitter bütçe kapanışı | M6 sonrası | §3.6 |

### §13.2 Kapı Kapanış Akışı

| # | Adım | Şart | Sonuç |
|---|---|---|---|
| 1 | İddia listesi çıkar | `audio-kapsam.md` §19.4 | matris hazır |
| 2 | Kanıt tipini ata | S1–S5 | §2 |
| 3 | S1 gerekenler için tezgâh kur | Q-AD1 | ölçüm |
| 4 | Eşiği karşılaştır | §3 hedefleri | geç/kal |
| 5 | Sapmayı risk kaydına yaz | varsa | §13.6 |
| 6 | Kapıyı kapat ve tarihle | S1 kanıtı | §13.8 |

### §13.3 Ölçüm Kayıt Alanı (boş şablon)

| Tarih | Tezgâh | M1 | M2 | M3 | M4 | M5 | M6 | Ölçümcü |
|---|---|---|---|---|---|---|---|---|
| (boş) | (boş) | | | | | | | |

### §13.4 Datasheet Karşılaştırma Kayıt Alanı

| Çip | Parametre | Datasheet | Sistem ölçümü | Fark | Durum |
|---|---|---|---|---|---|
| PCM3168A | SNR | 112 dB | (boş) | (boş) | ❌ |
| PCM3168A | THD+N | −94 dB | (boş) | (boş) | ❌ |
| AK4458 | SNR | 115 dB | (boş) | (boş) | ❌ |
| AK4458 | THD+N | −107 dB | (boş) | (boş) | ❌ |
| TPA3255 | SNR (BTL) | 112 dB | (boş) | (boş) | ❌ (PROPOSED) |

### §13.5 Envanter Kayıt Alanı (boş şablon)

| # | Parça | Marka/Paket | Kaynak | Etiket | Tarih |
|---|---|---|---|---|---|
| 1 | (boş) | | | UNKNOWN | |

### §13.6 Bulgu → Kök Neden Tablosu

| # | Bulgu | Olası kök neden | Doğrulama yolu |
|---|---|---|---|
| 1 | M1 eşik tutmadı | Orta gürültü · clock · seviye | M6 + §5/5 |
| 2 | Kanal kayması yüksek | TDM slot/PCB iz farkı | §5/7 |
| 3 | Kanal eşlemesi ters | Slot ataması | §5/3 |
| 4 | URL 404 | Sayfa taşındı | §7 yeniden HEAD |
| 5 | Etiket tutarsız | Yazım hatası | `audio-kapsam.md` §8 |
| 6 | Ölçüm tekrarlanamıyor | Tezgâh tarifi eksik | §8 formu |

### §13.7 İlişkili Dosyalar

| # | Dosya | İlişki |
|---|---|---|
| 1 | `audio-kapsam.md` | İddia sahibi |
| 2 | `audio-ornekler.md` | Uygulama şablonu |
| 3 | `cipler/pcm3168a-codec.md` | Datasheet kaydı |
| 4 | `cipler/ak4458-dac.md` | Datasheet kaydı |
| 5 | `cipler/tpa3255-amplifier.md` | Datasheet kaydı (referans) |
| 6 | ADR-038 dosyası | Ölçüm tarifi |

### §13.8 Kapı Geçmişi (append-only kayıt tablosu)

| # | Tarih | Kapı | Aksiyon | Sonuç |
|---|---|---|---|---|
| 1 | 2026-10-13 | §7 web | 3 URL HEAD | 2 ✅ / 1 ❌ (406 — kullanılmadı) |
| 2 | 2026-10-13 | §4 kayıt | 3 cip dosyası okundu | ✅ (kısmi ⚠️ §4) |
| 3 | 2026-10-13 | §10-kod | repo sayımı | `*.cpp=0 · *.h=0 · *.xc=0 · *.ino=0` |
| 4 | 2026-10-13 | §5 S1 | — | ❌ açık (tezgâh yok) |
| 5 | 2026-10-13 | §6 envanter | — | ❌ açık (UNKNOWN) |

### §13.9 Komşu Kapılarla Sınır (K001 ↔ K003)

| # | Ölçüm/tarif | Sahip | Bu dosyanın tutumu |
|---|---|---|---|
| 1 | Ses donanımı M1–M6 (donanım seviyesi) | **K001 (bu dosya)** | uygular |
| 2 | Yazılım gecikme/underrun ölçümü | K003 (`realtime/`, `output/`) | yalnız atıf |
| 3 | ASIO/WASAPI sürücü davranışı | K003 (`backend/asio`) | iddia kurmaz |
| 4 | DSP zinciri doğrulaması | K003 (`engine/`, `mixing/`) | iddia kurmaz |
| 5 | Donanım tasarım standardı (ADR-063) | ADR-063 | yalnız varlık iddiası |
| 6 | DSP donanım modu (ADR-017) | ADR-017 | yalnız varlık iddiası |
| 7 | Depolama doğrulaması | `hdd-dogrulama.md` · `ssd-dogrulama.md` | komşu kapı |

### §13.10 S1 Ölçüm Kayıt Şablonu (BOŞ — doldurulmamış)

> Bu tablo **hiç doldurulmamıştır**. Satırlar yalnızca *neyin* kaydedileceğini
> gösterir; hiçbir hücre ölçüm sonucu taşımaz. `Durum` sütunu hepsinde
> `BEKLEMEDEDİR`.

| # | Ölçüm | ADR-038 eşiği | Alet (envanter) | Durum | Kayıt değeri | Tarih | Kanıt dosyası |
|---|---|---|---|---|---|---|---|
| M1 | SNR | ≥90 dB | UNKNOWN | BEKLEMEDE | — | — | — |
| M2 | DIN ADYN / dinamik aralık | ≥105 dB | UNKNOWN | BEKLEMEDE | — | — | — |
| M3 | THD+N | ≥95 dB (SNR eşdeğeri) | UNKNOWN | BEKLEMEDE | — | — | — |
| M4 | Kanal eşleniği / faz | FADGI matrisi | UNKNOWN | BEKLEMEDE | — | — | — |
| M5 | Jitter (RMS/PP) | ≤100 ps | UNKNOWN | BEKLEMEDE | — | — | — |
| M6 | Kanal arası kazanç farkı | ≤1 dB | UNKNOWN | BEKLEMEDE | — | — | |

**Kayıt kuralı:** Bir satıra değer yazabilmek için (a) alet envantere kayıtlı
olmalı, (b) kalibrasyon tarihi geçerli olmalı, (c) ham veri dosyası vault'a
konmuş olmalı. Üçünden biri eksikse hücre `—` kalır, `0` yazılmaz.

### §13.11 Alet & Tezgâh Envanteri (tamamı UNKNOWN)

| # | Öğe | Gereklilik | Envanter durumu | Kapıya etkisi |
|---|---|---|---|---|
| 1 | Ölçüm mikrofonu / kalibrasyon kaynağı | M1–M4 | UNKNOWN (envanterde yok) | S1 kapalı |
| 2 | Ses kartı / analyser (referans sınıfı) | M1–M3 | UNKNOWN (envanterde yok) | S1 kapalı |
| 3 | Osiloskop ≥1 GHz / jitter ölçüm paketi | M5 | UNKNOWN (envanterde yok) | S1 kapalı |
| 4 | Sinyal jeneratörü | M3, M6 | UNKNOWN (envanterde yok) | S1 kapalı |
| 5 | PCM3168A eval kartı / taşıma kartı | kodek doğrulaması | UNKNOWN (envanterde yok) | S1 kapalı |
| 6 | AK4458 eval kartı | DAC doğrulaması | UNKNOWN (envanterde yok) | S1 kapalı |
| 7 | XU316 geliştirme kartı (XMOS) | M5, firmware | UNKNOWN (envanterde yok) | S1 kapalı |
| 8 | Sabit yük / hoparlör empedans yükü | M6 | UNKNOWN (envanterde yok) | S1 kapalı |
| 9 | I2S/TDM laog analyser (donanım veya FPGA) | pin planı doğrulaması | UNKNOWN (envanterde yok) | S1 kapalı |
| 10 | Ölçüm odası / ortam gürültüsü kaydı | M1–M3 tekrarlanabilirlik | UNKNOWN (envanterde yok) | S1 kapalı |

**Sonuç:** 10/10 öğe `UNKNOWN` → fiziksel ölçüm kapısının açılması için önce
envanterin doldurulması gerekir. Bu tablo envanteri *tahmin etmez*; yokluğu
kaydeder.

### §13.12 Kayıt Bütünlüğü — Geçersiz Sayılan Girişler

Aşağıdaki girişler geçerli **sayılmaz** ve kapı kararına girmez:

| # | Geçersiz giriş | Neden geçersiz | Doğru karşılığı |
|---|---|---|---|
| 1 | Datasheet'teki tipik değer | Üretim ölçümü değildir | `DOC` etiketi + datasheet satırı |
| 2 | "Tasarım hedefi: 110 dB" | Hedef ≠ ölçüm | `PLANNED` + hedef ayrı sütun |
| 3 | ADR-038 içindeki eşik değeri | Eşik ≠ sonuç | Eşik sütununda kalır |
| 4 | Tahmini hesap / simülasyon | Fiziksel kanıt değil | `SIM` etiketi, S1'e girmez |
| 5 | Başka projeden kopya ölçüm | Bu donanıma ait değil | `UNKNOWN` |
| 6 | "Ölçüldü" yazısı, ham veri yok | Tekrarlanamaz | `BEKLEMEDE` + veri yolu |
| 7 | Alet seri numarası yok | Kalibrasyon izlenemez | `BEKLEMEDE` |
| 8 | Yalnız ekran görüntüsü, dosya yok | Denetlenemez | ham veri dosyası zorunlu |

### §13.13 Kapı Karar Matrisi (geçiş koşulu)

| Kapı | Koşul (hepsi aynı anda) | Karar | Kalan borç |
|---|---|---|---|
| K1 — Envanter | 10/10 öğe sahip + seri no + kalibrasyon tarihi | AÇILIR | 0 |
| K2 — Tezgâh | K1 açık · şema rev. · pin planı rev. · kodek init sırası rev. | AÇILIR | 0 |
| K3 — S1 ham veri | K2 açık · 6 ölçümün ham dosyaları vault'ta · alet kalibrasyonu geçerli | AÇILIR | 0 |
| K4 — S1 eşik | K3 açık · M1–M6 tablosu doldu · her satır ADR-038 eşiğiyle karşılaştırıldı | GEÇTİ / KALDI | varsa kalan: ölçülen−eşik |
| K5 — S2 envanter | 3 cip fiziksel olarak elde · batch/lot kaydı · istifleme kaydı | AÇILIR | 0 |
| K6 — S2 köprü | K5 açık · 3 cip ↔ envanter çapraz 0 kesişim · rev. uyumu | AÇILIR | 0 |
| K7 — S3 doküman | 3 cip datasheet'i vault'ta · rev. eşleşmesi · atıf satırı | GEÇTİ | 0 |

**Kural:** Bir kapı yalnız bir üst kapı kapalıysa **kapalı** kalır; kısmi
geçiş yoktur. `GEÇTİ` yazısı yalnız ilgili ham kanıt dosyası eklendikten sonra
yazılabilir.

### §13.14 Sık Hata Kaydı (bu revizyon boyunca gözlenenler)

| # | Hata | Gözlem | Önleme |
|---|---|---|---|
| 1 | 406 dönen URL'nin "kaynak" sayılması | `xmos.com` HEAD → 406 | Yalnız HTTP 200 kaynak alınır; 406 satırda "kullanılmadı" olarak durur |
| 2 | Envante kesişimini "boş" diye geçiştirme | kesişim 0 ise eşleşme yok | 0 kesişim = sorun değil; *id dia ile envanter uyumsuzluğu* = sorun |
| 3 | `parent` anahtarı kullanımı | şablon `parent_id` ister | Frontmatter'da `parent` YASAK |
| 4 | Yer tutucu `{...}` / `TBD` yazma | bilinmeyen gizlenir | `UNKNOWN` / `BEKLEMEDE` kullanılır |
| 5 | Sınıf kuralını otomatik onaylama | TPA3255 Class D | Kural durumu dokümanda OLDUĞU gibi yazılır; otomatik onay yok |
| 6 | ADR-038'i "yeni seçim" sunma | karar 2026-09-26'da kabul | `TEYİT` (mevcut kabul) olarak sunulur |
| 7 | ADR-017/063 içeriğini okumadan iddia kurma | yalnız dosya varlığı doğrulandı | Yalnız **varlık** iddiası |
| 8 | "Dokümanda var" ≠ "üretildi" | etiket karışıklığı | IMPLEMENTED/DOCUMENTED/PLANNED/PROPOSED/UNKNOWN ayrımı |

### §13.15 Bağımlılık Zinciri (kapı → kapı)

```text
[Envanter K1] → [Tezgâh K2] → [S1 ham veri K3] → [S1 eşik K4]
                         ↘
[S2 envanter K5] → [S2 köprü K6] → [S3 doküman K7]

K4 ve K7 kapalıysa → S4 kapısı (dokümante edilmiş durum) AÇILAMAZ.
S4 kapısı: bu dosyanın tamamı. Yani bu dosya, K4/K7 kapanana kadar
"durum raporu" olarak kalır; "geçiş raporu" olamaz.
```

**Zincir üzerinde tek risk:** K1 (envanter) — 10/10 öğe `UNKNOWN` olduğu için
zincirin başı kapalıdır ve tüm kapılar zincirleme kapalıdır. Bu, bir kusur
değil, **dürüst durumdur**.

### §13.16 Bu Dosyanın Kanıt Sayımı (özet)

| # | Kanıt türü | Adet | Kullanım |
|---|---|---|---|
| 1 | HTTP 200 web kaynağı | 2 | §7 — doğrulandı, atıf yapıldı |
| 2 | HTTP 406 web kaynağı | 1 | §7 — **kullanılmadı**, yalnız kayıt |
| 3 | Okunmuş cip dosyası (`cipler/*.md`) | 3 | §4 — durum + etiket çıkarımı |
| 4 | Kabul edilmiş ADR (ADR-038) | 1 | §3, §5, §12 — mevcut karar (TEYİT) |
| 5 | Yalnız varlık iddiası yapılan ADR | 2 (ADR-017, ADR-063) | §9 — içeriği okunmadı |
| 6 | Repo kod ölçümü | 4 (`*.cpp/ *.h/ *.xc/ *.ino` = 0) | §10 — negatif kanıt |
| 7 | K003-audio dizin sayımı | 12 dizin / 68 md | §9 — sınır iddiası |
| 8 | Yapılmış fiziksel ölçüm | **0** | §5, §13.10 — tüm kapılar kapalı |
| 9 | `{...}` / `TBD` yer tutucusu | **0** | — |
| 10 | `UNKNOWN` işareti | çoklu | bilinmeyenler işaretli |

**Toplam:** 8 kaynak sınıfı = 15 somut kanıt nesnesi; **fiziksel ölçüm: 0**.

---

## §14 Dürüstlük Bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez. Bu dosya 2 adet HEAD-200 web URL'si (7.
bölüm), 3 okunmuş cip dosyası ve 1 kabul edilmiş ADR (ADR-038) üzerine kuruludur;
406 döndüren URL kullanılmamıştır. **Hiçbir S1 ölçümü YAPILMAMIŞTIR** — tüm
fiziksel ölçüm kapıları açıktır ve "GEÇTİ" yazısı yalnız S1 kanıtı ile
yazılabilir. Datasheet rakamları ölçüm olarak sunulmamıştır. ADR-017 ve ADR-063
hakkında yalnız **varlık** iddiası kurulmuştur. `{...}` ve `TBD` yer tutucusu
kullanılmamış; bilinmeyenler `UNKNOWN` ile işaretlenmiştir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
