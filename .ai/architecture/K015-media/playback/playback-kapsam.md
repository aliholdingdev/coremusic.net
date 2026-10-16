---
node_id: "K015-media/playback/playback-kapsam"
node_type: "module"
status: "draft"
parent_id: "K015-media/playback"
children: []
depends_on: ["K015-media/media-master", "K003-audio", "K012-services", "K011-application"]
evidence:
  - "media-master.md §9.5 (playback — 5 satır TASARIM İDDİASI) · §14.1 (çalma zinciri — §4'e birebir)"
  - "playback/index.md §GRUPLAR — bu dosya Kademe-2 gruba eklendi (2026-10-16)"
validation: "Draft — §4 zinciri media-master §14.1 ile birebir karşılaştırıldı (2026-10-16); sahiplik açık sorusu §7/1'de korundu, kesinleştirilmedi"
open_questions:
  - "Sahiplik: playback K015'te mi K012/playback-service'te mi → ⚠️ VERIFICATION REQUIRED (media-master §7/1 — bu dosyada KESİNLEŞTİRİLMEZ)"
  - "Desteklenen codec/format seti → ⚠️ UNKNOWN (media-master §12/1)"
group: mimari
---

# K015-media · playback · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Modelleme Dışı, Ne Hariç
>
> **Etiket kuralı:** bu dosyanın **tüm satırları TASARIM İDDİASI**'dır; yalnız `playback`
> çocuk adı S2 kanıtlıdır (v2 §2.15 — media-master §9.5 ilk satırı).

---

## §1 KAPSAM İÇİ (modellenen) — TASARIM İDDİASI

### §1a Varlıklar (media-master §9.5 ile hizalı) — TASARIM İDDİASI

1. **Dosya konumu** — çalınacak kaynağın çözümlenmesi (yol/URI → okunabilir kaynak) — TASARIM İDDİASI (S2: çocuk adı).
2. **Oynatma durumu** — oynat / duraklat / ileri / geri komutları ve bunların durum karşılıkları — TASARIM İDDİASI (media-master §9.5 "Durum" satırı).
3. **Konum (pozisyon)** — saniye cinsinden takip; atlama (seek) girişimi — TASARIM İDDİASI (media-master §9.5 "Konum" satırı).
4. **Ses motoru çağrısı arayüzü** — K003-audio'ya giden çağrı sınırı (motorun kendisi değil, **sadece sınır**) — TASARIM İDDİASI (media-master §9.5 "Motor" satırı: "K003-audio motoru çağrısı").
5. **Sahiplik bağı** — playback ile K012/playback-service arasındaki sınır → **belirsiz**, §6/1 — TASARIM İDDİASI (etiketsiz iddia YOK).

### §1b Kullanıcı Yüzeyi (arayüz envanteri) — TASARIM İDDİASI

| Yüzey | Ne taşır | Kanıt durumu |
|---|---|---|
| Çal/duraklat düğmesi | durum geçişi (§3) | TASARIM İDDİASI |
| İleri/geri (seek) | konum değişimi (§1a-3) | TASARIM İDDİASI |
| Sıradaki/önceki | kuyruk navigasyonu (K012 sınırı ⚠️ §6/1) | TASARIM İDDİASI |
| Konum çubuğu | pozisyon yayını (saniye) | TASARIM İDDİASI |
| Hata göstergesi | §5 durumları | TASARIM İDDİASI |

### §1c Zaman penceresi — TASARIM İDDİASI

- **Birincil:** 2026-10-16 vault anlık görüntüsü (media-master.md §9.5/§14, playback/index.md).
- **Kanıt sınırı:** yalnız çocuk adı S2; kalan satırlar tasarım iddiasıdır (frontmatter `validation`).

---

## §2 KAPSAM DIŞI (bilinçli hariç — gerekçeli) — TASARIM İDDİASI

1. **K012/playback-service sahipliği** — "kullanıcı çal der → servis orkestrasyonu" katmanı **bu dosyanın sahipliği DEĞİLDIR**; sınır media-master §7/1'de açık soru olarak kayıtlıdır ve bu dosyada **kesinleştirilmez**. Geri dönüş koşulu: §7/1 kapanırsa yalnız sınır satırı revize edilir — sessiz devir yasak. — TASARIM İDDİASI
2. **K003-audio ses motoru (DSP/decode/çıkış)** — motorun içi (decode zinciri, örnekleme, mixer) hariç; bu dosya yalnız **çağrı arayüzünü** modeller (§1a-4). Geri dönüş koşulu: playback motorun kendisine sahip olursa kapsam revizyonu zorunlu. — TASARIM İDDİASI
3. **K015-media/streaming kardeş dosyası** — akışla dinleme ayrı düğüm (media-master §9.6); bu dosya yalnız **yerel dosya çalmasını** kapsar. — TASARIM İDDİASI
4. **K015-media/offline · downloads** — çevrimdışı kullanılabilirlik ve indirme ayrı çocuklar (media-master §9.7/§9.8). — TASARIM İDDİASI
5. **K011-application tetikleme UI'ı** — "çal" düğmesinin kendisi K011'de; burada yalnız durum makinesine giren **komut** modellenir. — TASARIM İDDİASI
6. **K007-data kalıcılık şeması** — parça listesi/kuyruk şeması K007'de (media-master §2.2). — TASARIM İDDİASI

---

## §3 DURUM MAKİNESİ (7 durum) — TASARIM İDDİASI

| # | Durum | Anlamı | Çıkış geçişleri |
|---|---|---|---|
| S-1 | `idle` | kaynak yok, motor sessiz | `loading` (çal komutu) |
| S-2 | `loading` | konum çözümleniyor / ilk veri bekleniyor | `playing` · `error` |
| S-3 | `playing` | ses çıkışı aktif | `paused` · `buffering` · `ended` · `error` |
| S-4 | `paused` | konum korunur, çıkış sessiz | `playing` · `error` |
| S-5 | `buffering` | veri yetersiz, konum korunur | `playing` · `error` |
| S-6 | `ended` | kaynak sonuna ulaştı | `idle` · `loading` (sıradaki) |
| S-7 | `error` | geri alınamaz hata (§5) | `idle` (yeniden deneme ile `loading`) |

**Geçiş kuralı (TASARIM İDDİASI):** her geçiş tek kaynaktan tetiklenir (kullanıcı komutu · veri olayı · hata olayı);
çift-kaynaklı geçiş yasaktır. `buffering → paused` geçişi **tanımlı değildir** (duraklatma öncelikle `playing`/`paused` üzerinden).
Tüm durumlar `noexcept` geçiş fonksiyonlarıyla modellenir (bkz ornekler Ör-01).

---

## §4 VERİ AKIŞI ZİNCİRİ (media-master §14.1 — birebir) — TASARIM İDDİASI

```text
K011-application (kullanıcı "çal" der)
  → K012-services (playback-service — sahiplik §7/1)
    → K015-media/playback (dosya konumu)
      → K003-audio (ses motoru)
        → hoparlör/çıkış
```

> Kaynak: `media-master.md` §14.1 — bu dosyaya **birebir kopyalanmıştır** (değişiklik yok).
> Zincirdeki `sahiplik §7/1` ibaresi açık sorudur (§6/1); playback bu zincirde
> **yalnız "dosya konumu" adımını** üstlenir (§1a-1), motor adımını değil (§2/2).

---

## §5 HATA DURUMLARI — TASARIM İDDİASI

| # | Hata | Tetikleyici | Durum karşılığı | Davranış |
|---|---|---|---|---|
| H-1 | Kaynak bulunamadı | konum çözümleme başarısız | `error` → `idle` | kullanıcıya bildirim; yeniden dene |
| H-2 | Codec/format desteklenmiyor | decode reddi (media-master §12/1 ⚠️ UNKNOWN) | `error` | açık rapor; "bilinmeyen" uydurulmaz |
| H-3 | Dosya okuma hatası (disk/izin) | I/O istisnası | `error` | bölüm §6/1'e dokunmaz |
| H-4 | Veri yetersizliği | akış beklenirken boşluk | `buffering` | hata değil, **geçici durum** (§3 S-5) |
| H-5 | Motor çağrısı reddedildi | K003-sınırı (§2/2) | `error` | sahiplik belirsiz — log + rapor |
| H-6 | Süre sonuna erişim | `ended` | `ended` | hata değil, normal bitiş (§3 S-6) |

**Kural:** hata durumunda **konum korunur** (seek geri alınmaz) — TASARIM İDDİASI; kullanıcı "yeniden dene" ile `loading`'e döner.

---

## §6 AÇIK SORULAR (⚠️ — kapanışı gerekenler)

| # | Açık soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| 1 | playback sahibi K015 mi K012/playback-service mi? | ⚠️ VERIFICATION REQUIRED (media-master §7/1) | ADR/karar gelmeden **kesinleştirilmez** — bu dosya yalnız sınırı taşır |
| 2 | Desteklenen codec/format seti | ⚠️ UNKNOWN (media-master §12/1) | set kararı yazılana kadar H-2 "bilinmeyen" kalır |
| 3 | Kademe-3 bounded-context kapsamı | ⚠️ VERIFICATION REQUIRED (index FM) | ADR-097 K4 faz (C) |
| 4 | K012 ile sıra navigasyonu (sıradaki/önceki) kimin? | ⚠️ VERIFICATION REQUIRED | §7/1 ile birlikte çözülür |

---

## §7 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-16 | İlk yazım — §1-§6 (kapsam içi/dışı, durum makinesi, §14.1 zinciri birebir, hata durumları, açık sorular) | Vault Steward (subagent) |

## § BAĞLANTILAR (düz yol — wiki-link yok)

- `../media-master.md` (§7/1 · §9.5 · §12/1 · §14.1)
- `index.md` · `playback-web-arastirma.md` · `playback-ornekler.md`
- `../index.md`

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
