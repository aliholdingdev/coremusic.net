---
node_id: K014-media/media-riskler.md
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on:
  - K003-audio
  - K007-data
  - K010-network
  - K013-integraion
evidence:
  - ".ai/architecture/K014-media/media-master.md §7 (8 açık soru), §12/§16 (14 bilinmeyen), §18 (komşu etki), §22.6 (kalan UNKNOWN) — okundu 2026-10-18"
  - ".ai/architecture/K014-media/media-kararlar.md §1-§8 (ADR'ler + TASARIM ÖNERİLERİ + açık konular) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integraion-web-arastirma.md §3.8 (MusicBrainz rate limit — doğrulandı) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 10 risk; her biri Probability × Impact × Mitigation × Owner; risk ID'leri bu dosyada üretilir (uydurma ADR yok)"
open_questions:
  - "⚠️ Risk-01/02/03/04 (sahiplik belirsizlikleri) — Vault Steward kararı gerekli; tek owner ATANAMADI."
  - "⚠️ Risk-07 (codec seti onayı) — TASARIM ÖNERİSİ §22.1 Vault Steward onayı ister."
  - "⚠️ Owner'lar K012 master §20 rollerinden türetildi — medya rolleri fictionel."
group: kanit
---

# K014 MEDIA — Riskler

> Format: Probability (düşük/orta/yüksek) × Impact (düşük/orta/yüksek) × Mitigation × Owner.
> Kaynak: `media-master.md` §7 (8 açık soru) · §12/§16 (14 bilinmeyen) · §18 (komşu etki — HIGH/MEDIUM/LOW) · `media-kararlar.md` §7-§8.

## §1 Risk Kayıt Defteri (10 risk)

| # | Risk | Probability | Impact | Mitigation | Owner |
|---|---|---|---|---|---|
| R-MED-01 | **playback sahipliği belirsiz** (§7/1: K014 vs K012/playback-service) — sahipsiz özellik / çift implementasyon | Yüksek | Yüksek | §7/1: Vault Steward kararı gerekli; karar öncesi uygulama YAPILMAZ; §14.1 zinciri (K012 orkestra → K014 dosya → K003 motor) öneri olarak durur | `architect` (karar) + `product-manager` |
| R-MED-02 | **offline sahipliği belirsiz** (§7/2: K014/offline-sync vs K012/offline kuyruğu) | Orta | Yüksek | §7/2: sınır ADR gerektirir; ADR-027 (Dual-Mode Storage) mevcut ama sahiplik ayrı konu | `architect` |
| R-MED-03 | **synchronization adı iki domain'de** (§7/3: K013 + K014) — kafa karışıklığı / yanlış uygulama | Yüksek | Orta | §7/3: "ne ayırıyor?" — ad ayırma kararı (K013 = dış sistem senkronu, K014 = cihaz-içi medya verisi?) doğrulanmalı; K013 §22.4 de çelişkiyi taşıyor | `architect` + `backend-developer` |
| R-MED-04 | **media-testing sahipliği belirsiz** (§7/4: K014 vs K18-testing — master'da K019-testing) — test çifti / boşluk | Orta | Orta | §7/4: K18-testing incelemesi (§12/10 araç seti UNKNOWN); test stratejisi media-test-stratejisi.md'de taslak | `qa-engineer` (K18) + `architect` |
| R-MED-05 | **Dış servis rate limit ihlali** (MusicBrainz 1 sn · AcoustID ≤3/sn — §22.3) — ban/kesinti | Orta | Yüksek | ADR-028 (Anti-Ban System — accepted): rate limit + backoff + User-Agent; limitler doğrulandı (web-arastirma §3.8) | `backend-developer` + `research-analyst` |
| R-MED-06 | **Patent/lisans riski** (§22.1: AAC Via LA patent — encoder/decoder satışına uygulanır; WMA proprietary) | Orta | Yüksek | §22.1 TASARIM ÖNERİSİ: temel set MP3 + FLAC + WAV (tescil riski taşımaz); AAC/WMA kaçınılır; Vault Steward onayı ister | `architect` + `legal-advisor` |
| R-MED-07 | **Codec seti / eşikler onaysız** (§22.1-§22.5 TASARIM ÖNERİLERİ + §16/3 boyut/TTL) — uygulama belirsizlikle başlar | Yüksek | Orta | §22.6: 5 UNKNOWN kapandı ama öneriler Vault Steward onayı ister; §16/3 eşik kararı açık | `architect` (Vault Steward kararı) |
| R-MED-08 | **metadata/artwork kaynağı belirsiz** (§7/8: yerel etiket vs dış servis) — çift yol / tutarsızlık | Yüksek | Orta | §7/8: kaynak kararı; §22.3 önerisi: MusicBrainz birincil kimlik + CAA kapak + AcoustID fuzzy; fallback yerel etiket (§9.2) | `backend-developer` + `data-engineer` |
| R-MED-09 | **OpenAudio kimlik belirsiz** (§7/7: ~2800★ ama tam adres UNKNOWN) — yanlış referans | Orta | Düşük | §16/5: research turu; aday referans olarak kullanılmaz (zero-hallucination) | `research-analyst` |
| R-MED-10 | **Proprietary referans kopyalama riski** (Plex/Emby/Plexamp — §11.1: yalnız fikir, kopya yasak) | Düşük | Yüksek | §11.1 sınıflandırma: açık kaynak (Jellyfin/Navidrome/Finamp/Phonograph_Plus/Picard/Strawberry) kod/fikir incelemesi; proprietary yalnız fikir; lisans kararı §7/6 | `legal-advisor` + `architect` |

## §2 Risk Dağılımı

| Olasılık × Etki | Sayı | Riskler |
|---|---|---|
| Yüksek × Yüksek | 1 | R-MED-01 |
| Yüksek × Orta | 3 | R-MED-03, R-MED-07, R-MED-08 |
| Orta × Yüksek | 3 | R-MED-02, R-MED-05, R-MED-06 |
| Orta × Orta | 2 | R-MED-04, R-MED-09 |
| Düşük × Yüksek | 1 | R-MED-10 |

**En kritik 3**: R-MED-01 (playback sahipliği) · R-MED-05 (rate limit/anti-ban) · R-MED-06 (patent/lisans).

## §3 Azaltma Öncelik Sırası

1. **Sahiplik kararları** (R-MED-01/02/03/04): Vault Steward — §7/1-§7/4 + K013 §22.4; karar öncesi uygulama yapılmaz.
2. **Codec/lisans kararı** (R-MED-06/07): §22.1 önerisi onaylanmalı (MP3+FLAC+WAV); AAC/WMA kaçınılır.
3. **Anti-ban uygulaması** (R-MED-05): ADR-028 — dış servis istekleri rate limit + backoff + User-Agent ile sarılır.
4. **Kaynak kararı** (R-MED-08): §22.3 önerisi (MusicBrainz + CAA + AcoustID) + yerel etiket fallback.

## §4 Risk İzleme Tetikleyicileri

| Risk | İzlenecek sinyal | Eşik / Aksiyon |
|---|---|---|
| R-MED-05 | Dış servis 429/503 yanıtı | 1 → backoff + User-Agent denetimi (ADR-028) |
| R-MED-06 | Yeni codec/format talebi | AAC/WMA talebi → legal-advisor veto |
| R-MED-07 | Vault Steward onay tarihi | Onay yoksa uygulama başlamaz |
| R-MED-08 | Metadata eşleşmeme oranı | %10 üstü → AcoustID fuzzy (§22.3) |
| R-MED-10 | Proprietary kod incelemesi | Kopya izi → derhal durdur (§11.1) |

## §5 Karar Gerektiren Konular (Vault Steward)

| # | Konu | Kaynak | Risk |
|---|---|---|---|
| 1 | playback sahipliği | §7/1 | R-MED-01 |
| 2 | offline sahipliği | §7/2 | R-MED-02 |
| 3 | synchronization ayırma (K013 vs K014) | §7/3 | R-MED-03 |
| 4 | media-testing sahipliği | §7/4 | R-MED-04 |
| 5 | codec seti onayı (MP3+FLAC+WAV) | §22.1 | R-MED-06 |
| 6 | cache eşikleri (boyut/TTL) | §16/3 | R-MED-07 |
| 7 | metadata kaynağı (dış vs yerel) | §7/8 | R-MED-08 |

## §6 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K014-media/media-kararlar.md` | ADR'ler + TASARIM ÖNERİLERİ + açık konular | media-kararlar |
| `.ai/architecture/K014-media/media-web-arastirma.md` | Doğrulanmış RFC/ISO kaynakları | media-web-arastirma |
| `.ai/architecture/K013-integraion/integraion-riskler.md` | K013 riskleri (sınır — sync çelişkisi) | integraion-riskler |

## §7 Risk Sahipliği ve Gözden Geçirme Döngüsü

### §7.1 Sahiplik Matrisi

| Risk grubu | Sahip (rol) | Çözüm yetkisi | Escalation |
|---|---|---|---|
| R-MED-01 … R-MED-04 (sahiplik — §7/1-§7/4) | Vault Steward (karar) · business-analyst (taslak) | Domain sahipliği ataması | AGENTS §6 — Expert+Senior+Junior |
| R-MED-05 (rate limit/anti-ban) | data-engineer (limit) + researcher (kaynak) | ADR-028 uygulama kapsamı | architect |
| R-MED-06 (codec/lisans) | compliance-auditor (lisans) + researcher (pazar) | §22.1 set onayı | Vault Steward + legal-advisor |
| R-MED-07 (görsel/lisans portal) | researcher (kanıt) + compliance-auditor | Lisans türü → §20/3 | Vault Steward |
| R-MED-08 (metadata kaynağı) | data-engineer (eşleme) + researcher (URL) | §22.3 kaynak seti | architect |
| R-MED-09, R-MED-10 (donanım/proprietary) | embedded-engineer (bant) · security-engineer (inceleme) | Kapsam dışı kod yasağı | Vault Steward |

### §7.2 Gözden Geçirme Tetikleyicileri

| Tetikleyici | Aksiyon | Sıklık / Koşul |
|---|---|---|
| Vault Steward §7/1-§7/4 kararı | İlgili risk kapanır → Durum sütunu "kapatıldı" | Karar günü |
| §22.x standards değişikliği (kod-ornekleri §13) | R-MED-06/07/08 tabloları güncellenir | Değişiklik başına |
| Dış servis 429/503 olayı (§4 eşik) | 24 saat içinde backoff/limit gözden geçirmesi | Olay başına |
| Kapsam değişikliği (K013 synchronization sınırı) | integraion-riskler çapraz okunur | Sınır kararında |

**Kural**: risk durumu yalnız kanıtla değişir; çözülmemiş konu açıkça "⚠️ Vault Steward kararı
bekliyor" olarak kalır — tahmini kapanış tarihi yazılmaz (zero-hallucination).

## §8 Kaynak ve Kapsam Notu

Ana kanıt: `media-master.md` §7 (8 açık soru) · §12/§16 (14 bilinmeyen) · §22 (standartlar);
karar karşılıkları `media-kararlar.md` §1-§8. Risk ID'leri (R-MED-01…10) bu dosyada üretilmiştir,
ADR numaraları yalnız `.ai/.decisions/index.md` kayıtlıdır.

Kapsam sınırı: yalnız K014-media iç riskleri. K013 synchronization riski
(`integraion-riskler.md` R-INT-06) bu dosyaya kopyalanmaz — yalnız §7.2'de çapraz izlenir;
K18-testing (master'da: K019-testing) ve K17-devops riskleri dışarıdadır.
