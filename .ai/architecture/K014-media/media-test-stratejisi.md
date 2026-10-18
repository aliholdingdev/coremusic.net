---
node_id: K014-media/media-test-stratejisi.md
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on:
  - K003-audio
  - K007-data
  - K013-integraion
  - K18-testing
evidence:
  - ".ai/architecture/K014-media/media-master.md §13 (8 test senaryosu), §12/§16/10 (araç seti UNKNOWN), §7/4 (K019 sahiplik), §23.1 (media-testing ornekler.md) — okundu 2026-10-18"
  - ".ai/architecture/K014-media/media-kod-ornekleri.md §11 (PCM SHA-256 golden), §12 (EBU R128), §7 (Range 206) — okundu 2026-10-18"
validation:
  - "2026-10-18 — Piramit + §13 8 senaryo + kabul kriterleri; araç seti UNKNOWN korundu; sahiplik §7/4 belirsizliği her senaryoda"
open_questions:
  - "⚠️ media-testing sahipliği (§7/4: K014 vs K18-testing — master'da K019-testing) — KARAR YOK; testler karar öncesi K014 taslağı olarak durur."
  - "⚠️ Test araç seti (§12/10) UNKNOWN — K019 incelemesi bekliyor; bloklarda araç ADI YOK (ffmpeg yalnız söylem, §11.1)."
  - "⚠️ Tüm senaryolar TASARIM İDDİASI — repo'da medya kodu YOK (§23); uygulama geldiğinde çalıştırılır."
group: kod
---

# K014 MEDIA — Test Stratejisi

> Kaynak: `media-master.md` §13 (8 test senaryosu — K019 sınırı) · §12/§16/10 (araç seti UNKNOWN) · §7/4 (sahiplik belirsiz) · §23.1 (media-testing ornekler.md: decode PCM SHA-256 golden fixture · EBU R128 −23 LUFS · Range 206 sözleşmesi · etiket round-trip).
> **Önemli**: K014 design node — repo'da medya kodu YOK (§23). Senaryolar uygulama geldiğinde çalıştırılır; araç seti UNKNOWN (§12/10 — K019 incelemesi bekliyor).

## §1 Test Piramidi (K014)

```text
                    ╱ E2E ╲                     K011 → K012 → K014 → K003 zinciri
                  ╱─────────╲                   — KOD YOK (design node §23)
              ╱ Integration (dosya/cihaz) ╲     Range/indirme/eşitleme — KOD YOK
            ╱───────────────────────────── ╲
          ╱ Unit (etiket/hash/durum makinesi) ╲  media-kod-ornekleri söylemleri — ÇALIŞTIRILABİLİR
        ╱─────────────────────────────────────╲
```

- **Unit**: ID3 TYER→TDRC (§3.1) · content-hash dedup (§2.1) · durum makinesi (§6.1) · LWW merge (§10.1) · R128 kazanç (§12.1) — bağımsız fonksiyon söylemleri test edilebilir.
- **Integration**: Range 206 (§7.1) · resume (§8.1) · CAA fetch (§4.1) — uygulama geldiğinde.
- **E2E**: çalma/zincir (§14.1-§14.3) — K011/K012/K003 entegrasyonu gerektirir.

## §2 Test Senaryoları (§13 — 8 senaryo)

### §2.1 Decode/Çalma Doğruluğu (§13/1 — sahiplik §7/4 belirsiz)

- **Senaryo**: Decode PCM SHA-256 golden fixture ile byte-birebir (media-kod-ornekleri §11.1).
- **Kabul kriteri**: ✅ PCM hash == golden hash · ✅ MP3/FLAC/WAV üçü de (§22.1 temel set) · ❌ AAC/WMA test edilmez (§22.1: kaçınılır).

### §2.2 Akış Kalitesi (§13/2 — bitrate/kesinti)

- **Senaryo**: HTTP Range 206 + `Content-Range` bütünlüğü (RFC 9110 — §22.2; doğrulandı 2026-10-18).
- **Kabul kriteri**: ✅ `bytes=0-` → 200 tam gövde · ✅ `bytes=100-199` → 206 + `Content-Range: bytes 100-199/<size>` · ✅ geçersiz aralık → 416 · ✅ `Accept-Ranges: bytes` başlığı var.

### §2.3 Çevrimdışı Erişim (§13/3 — K014/offline)

- **Senaryo**: ADR-027 (Dual-Mode) — `is_offline_ready` READY/PENDING/MISSING durum geçişleri.
- **Kabul kriteri**: ✅ indirme tamam → READY · ✅ yarım → PENDING · ✅ dosya silindi → MISSING · ✅ ağ yokken READY parçalar çalar (offline fallback — §9.6).

### §2.4 İndirme Resume (§13/4 — K014/downloads · ADR-026)

- **Senaryo**: Yarıda kesilen indirme `Range: bytes=N-` ile devam eder; `.part` + atomik rename.
- **Kabul kriteri**: ✅ resume 206 ile devam · ✅ SHA-256 checksum eşleşir · ✅ `.part` → atomik rename (yarıda kesilirse `.part` kalır, hedef dosya YOK) · ✅ exponential backoff (§23.1).

### §2.5 Önbellek TTL (§13/5 — K014/media-cache)

- **Senaryo**: §22.5 — ARC (scan-resistant) + ETag revalidation (304); TTL sweep.
- **Kabul kriteri**: ✅ ETag eşleşirse 304 (yeniden indirme yok) · ✅ scan (tarama) T2'yi kirletmez (ARC claim) · ✅ TTL aşımı → temizlik · ⚠️ eşikler §16/3 UNKNOWN (VAULT ONAYI İSTER).

### §2.6 Metadata Doğruluğu (§13/6 — K014/metadata)

- **Senaryo**: ID3v2.3 TYER→v2.4 TDRC dönüşümü (§22.3) + etiket round-trip (§23.1).
- **Kabul kriteri**: ✅ TYER "2019" → TDRC "2019" (kayıpsız) · ✅ TCON "(17)Rock" → "Rock" · ✅ round-trip: yaz→ok→aynı · ✅ FLAC Vorbis Comment / MP4 `ilst` / WAV LIST-INFO okunur (§22.3).

### §2.7 Artwork Yükleme (§23/7 — K014/artwork)

- **Senaryo**: CAA kademeleri (§22.4: 250/500/1200 px) + `immutable` + ETag.
- **Kabul kriteri**: ✅ üç kademe üretilir (thumbnail ~64-128 · medium ~250-500 · large ~640-1200) · ✅ `Cache-Control: public, max-age=31536000, immutable` · ✅ kapak yoksa placeholder (§9.3) · ✅ gömülü kapak (APIC/covr) çıkarılır (media-kod-ornekleri §14.1).

### §2.8 Eşitleme Tutarlılığı (§23/8 — K014/synchronization · yön §12/9 UNKNOWN)

- **Senaryo**: LWW merge (media-kod-ornekleri §10.1) — iki cihaz çakışan kayıt.
- **Kabul kriteri**: ✅ daha yeni updatedAt kazanır · ✅ eşitlikte deterministik (remote) · ⚠️ yön (çift/tek) UNKNOWN (§12/9) — tek yönlü varsayım test edilmez · ⚠️ vector clock senaryosu yön kararı sonrası.

## §3 EBU R128 / ReplayGain Ölçümü (§22.3 — §13/1 tamamlayıcı)

- **Senaryo**: Entegre loudness ölçümü → R128 hedefe uzaklık (media-kod-ornekleri §12.1).
- **Kabul kriteri**: ✅ ölçüm −23 LUFS hedefine normalize (R128 yayın) · ✅ ReplayGain −18 LUFS (RG2) etiketi yazılır · ⚠️ tolerans eşiği UNKNOWN (Vault Steward).

## §4 Kapsam Dışı (Bu Stratejide Yok)

| Alan | Sahip | Not |
|---|---|---|
| Genel test stratejisi / coverage | `K18-testing` | §7/4: sahiplik belirsiz — bu dosya K014 taslağı |
| Servis altyapısı testleri | `K012-services` | services-test-stratejisi.md |
| Entegrasyon adaptör testleri | `K013-integraion` | integraion-test-stratejisi.md |
| DSP/sinyal testleri (measure, spectrum) | `K003-audio` / `K004-dsp` | §2.2: sinyal işleme kapsam dışı |
| UI testleri | `K011-application` | §2.2 |

## §5 Test Edilebilirlik Engelleri

1. **Kod YOK** (§23): tüm senaryolar uygulama geldiğinde; şu an yalnız bağımsız fonksiyon söylemleri (media-kod-ornekleri) test edilebilir.
2. **Araç seti UNKNOWN** (§12/10): K019 incelemesi bekliyor; bloklarda araç adı YOK (§11.1 ffmpeg yalnız söylem).
3. **Sahiplik belirsiz** (§7/4): K014 vs K18-testing — karar öncesi strateji taslak.
4. **Eşikler UNKNOWN** (§16/3): TTL/boyut eşikleri Vault Steward onayı ister — sabit değer YAZILMAZ (§23.4).

## §6.1 Test → Sahiplik Matrisi

| Senaryo | Sahiplik (belirsiz) | Karar |
|---|---|---|
| §2.1 decode doğruluğu | K014/media-testing vs K18-testing | §7/4 AÇIK |
| §2.2-§2.5 akış/indirme/önbellek | K014 (indirme/offline/cache çocukları) | §7/2 sınır notuyla |
| §2.8 eşitleme | K014/synchronization | §7/3 + §12/9 yön AÇIK |

## §7 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K014-media/media-kod-ornekleri.md` | Test edilecek söylemler (14 blok) | media-kod-ornekleri |
| `.ai/architecture/K014-media/media-web-arastirma.md` | Doğrulanmış RFC/ISO kaynakları | media-web-arastirma |
| `.ai/architecture/K18-testing/testing-master.md` | Genel test stratejisi (K18) | testing-master |
