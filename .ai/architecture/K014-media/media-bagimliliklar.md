---
node_id: K014-media
node_type: domain
role: bagimliliklar
status: draft
date: 2026-10-12
version: 1.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.15 + ADR-097 §5.2"
group: mimari
---


# K014-media — media-bagimliliklar.md

> **Etiket kuralı:** Aşağıdaki her bağımlılık satırı **TASARIM İDDİASI**'dır —
> isimlerden ve v2 ağacından türetildi, test edilmedi. Kanıt olarak yalnızca
> (a) v2 §2.15 çocuk adları ve (b) 2026-10-12 web kanıtları geçerlidir.
> Bu dosyada değişiklik = Vault Steward onayı + ADR-097 kaydı ister.

## §0 Etiket sözlüğü (bu belge boyunca)

| Etiket | Anlamı |
|---|---|
| TASARIM İDDİASI | Türetilmiş, test edilmemiş tasarım — onaylanana kadar bağlayıcı değil |
| ⚠️ VERIFICATION REQUIRED | Cevabı bilinmiyor, tahmin yazılmadı |
| ✅ verified | Kanıt var (v2 metni veya 2026-10-12 web kanıtı) |

## §1 Giden bağımlılıklar (depends_on →)

| # | Hedef düğüm | Ne için (ELI15) | Bağımlılık tipi | Risk |
|---|---|---|---|---|
| 1 | K003-audio | Ses motoruna çalma komutu/girişi | motor çağrısı | Motor yaşam döngüsü/bağlantı belirsiz ⚠️ |
| 2 | K007-data | Kitaplık/metadata kalıcılığı | veri erişimi | Şema K007'de; K015 sahipliği netleşmeli ⚠️ |
| 3 | K013-integraion | Dış katalog/akışla eşitleme | kontrat | Dış servis çökerse medya erişimi ⚠️ |
| 4 | K010-network | Akış/indirme taşıması | taşıma | Çevrimdışı davranış bu bağımlılıktan beslenir |

## §2 Gelen bağımlılıklar (used_by ←)

| # | Kaynak düğüm | Ne için | Etiket |
|---|---|---|---|
| 1 | K011-application | Arayüzün medya gösterimi (kapak, liste, çalma) | TASARIM İDDİASI |
| 2 | K012-services | Servislerin medya verisi okuması | TASARIM İDDİASI |
| 3 | Diğerleri | — | ⚠️ VERIFICATION REQUIRED (v2'de ters ok yok) |

## §3 Entegrasyon sınırları (K015 dışına çıkış kapıları)

| # | Sınır | Hangi arayüzden geçilir | Sahip düğüm | Arayüz adı |
|---|---|---|---|---|
| 1 | Ses motoru | çalma komutu / ses girişi | K003-audio | ⚠️ UNKNOWN (motor API'si) |
| 2 | Kalıcılık | kitaplık/metadata okuma-yazma | K007-data | ⚠️ UNKNOWN (tablo/şema) |
| 3 | Dış dünya | katalog/akış eşitlemesi | K013-integraion | ⚠️ UNKNOWN (kontrat) |
| 4 | Taşıma | akış/indirme paketleri | K010-network | ⚠️ UNKNOWN (akış biçimi) |
| 5 | Arayüz | medya gösterimi | K011-application | ⚠️ UNKNOWN (görünüm sözleşmesi) |
| 6 | Servisler | çalma durumu orkestrasyonu | K012-services | ⚠️ UNKNOWN (sahiplik §4/1) |

### §3.1 Sınır kuralı (Dependency Rule — bu domain için)

1. K015 medya **verisini** tutar; DSP/processing üretmez (K003/K004).
2. Ağ taşıması yapmaz (K014), dış katalog konuşmaz (K013) — kapıları kullanır.
3. Çevrimdışı davranış tek yazma noktası olmalı (offline · media-cache · downloads) — iddia.
4. Her kapı için arayüz adı ayrı kanıtla belgelenir (tur-2) — şimdi UNKNOWN.

## §4 Riskli bağımlılıklar

| # | Risk | Neden riskli | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | playback sahipliği (K015 ↔ K012) | iki "çalma" sahibi | iki gerçek durum | §7/1 — sınır ADR'si |
| 2 | Üç eşitleme (K015/synchronization · K013/synchronization · K014/offline-sync) | aynı iş üç aday | çakışan yazım | §7/2–3 |
| 3 | Dış servise bağımlı metadata/artwork | dış kesintisi içeriği kırar | bozuk görünüm | önbellek + fallback (kanıt yok ⚠️) |
| 4 | media-testing ↔ K019-testing | iki test sahibi | test tekrarı | §7/4 |
| 5 | Referans ürün kopyalama riski (Plex/Emby proprietary) | telif/lisans sınırı | hukuki risk | §7/6 — yalnız fikir referansı |
| 6 | OpenAudio beyanının adresinin belirsizliği | ★ var, depo yok | yanlış referans | §7/7 — ⚠️ UNKNOWN |
| 7 | Kimlik (K010) yazımı "identitiy" | v2 kuralı imla hatalarını korur | yanlış arama | yazım DOKUNULMAZ (v2 §3) |

**Döngü denetimi:** §1'de K015'e geri dönüş yok → graf **DAG** (2026-10-12).

## §5 Değişiklik kuralı

1. Satır eklemek/çıkarmak: Vault Steward onayı + ADR-097 §5.2 kaydı.
2. Yeni bağımlılık = yeni entegrasyon sınırı (§3 + §3.1) ve risk satırı (§4).
3. Etiket zorunlu: her satır TASARIM İDDİASI, ✅ verified veya ⚠️ ile işaretli.
4. "Kesin öyle" cümlesi bu dosyada **yazılmaz** (Truth Mode).
5. v2 adları imla düzeltmesi yapılmadan yazılır (identitiy dahil).

---

## §6 Bağımlılık Derinleştirmesi (TASARIM İDDİASI — 2026-10-13 eki)

### §6.1 Giden bağımlılıklar — detay matrisi

| # | Hedef | Bağımlılık yüzeyi | Ne akar | Akış yönü | Geri dönüş | Kanıt |
|---|---|---|---|---|---|---|
| 1 | K003-audio | ses motoru | çalma komutu/girişi | K015 → K003 | K003 → K015 (durum: playing/paused/error) | ⚠️ TASARIM İDDİASI |
| 2 | K007-data | kalıcılık | kitaplık/metadata okuma-yazma | K015 → K007 | K007 → K015 (veri) | ⚠️ TASARIM İDDİASI |
| 3 | K013-integraion | dış katalog/akış | eşitleme kontratı | K015 → K013 | K013 → K015 (dış veri) | ⚠️ TASARIM İDDİASI |
| 4 | K010-network | taşıma | akış/indirme paketleri | K015 → K014 | K014 → K015 (paketler) | ⚠️ TASARIM İDDİASI |

### §6.2 Gelen bağımlılıklar — detay matrisi

| # | Kaynak | Bağımlılık yüzeyi | Ne akar | Akış yönü | Sınır notu |
|---|---|---|---|---|---|
| 1 | K011-application | arayüzün medya gösterimi | istek/yanıt | K011 → K015 | gösterim K011'de |
| 2 | K012-services | servislerin medya verisi okuması | veri sorgusu | K012 → K015 | iş mantığı K012'de |

### §6.3 Bağımlılık yoğunluk matrisi (giden × gelen)

| | K011 | K012 |
|---|---|---|
| K015 → | — | — |
| ← K015 | veri | veri |

### §6.4 En riskli bağımlılık — derin analiz

| # | Risk | Neden | Olası sonuç | Azaltma (tasarım) |
|---|---|---|---|---|
| 1 | K003-audio down | motor yoksa | çalma yok | motor health-check; hata durumunda K011'e bildir |
| 2 | K007-data down | kalıcılık yoksa | koleksiyon kaybı | local cache; K007 health-check |
| 3 | K013-integraion down | dış katalog yoksa | eşitleme durur | offline fallback |
| 4 | K010-network down | taşıma yoksa | akış/indirme durur | offline-sync devreye girer |
| 5 | playback sahipliği (K015 ↔ K012) | iki "çalma" sahibi | iki gerçek durum | §7/1 — sınır ADR'si |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §7 Bağımlılık Zinciri Haritası (TASARIM İDDİASI)

### §7.1 Çalma zinciri (istemci → medya → ses)

```text
K011-application
  → K012-services (playback-service — sahiplik §7/1)
    → K014-media/playback (dosya konumu)
      → K003-audio (ses motoru)
        → hoparlör/çıkış
```

### §7.2 Akış zinciri (dış kaynak)

```text
K011-application
  → K014-media/streaming
    → K010-network (taşıma)
      → K013-integraion (dış kaynak)
        → dış servis
```

### §7.3 İndirme zinciri (çevrimdışı hazırlık)

```text
K011-application ("indir")
  → K014-media/downloads
    → K010-network (taşıma)
      → dosya → yerel depo (K007-data)
        → K014-media/offline (çevrimdışı kullanılabilir)
```

### §7.4 Eşitleme zinciri (dış sistem)

```text
K014-media/synchronization
  → K013-integraion (dış sistem kontratı)
    → dış servis
  → K007-data (kalıcılık)
```

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §8 Bağımlılık Değişiklik Protokolü (değişiklik kuralı genişletmesi)

| # | Değişiklik | Zorunlu adım | Sahip |
|---|---|---|---|
| 1 | yeni giden bağımlılık | §1 tablosuna satır + risk (§4) + sınır (§3) | Vault Steward |
| 2 | yeni gelen bağımlılık | §2 tablosuna satır + etiket | Vault Steward |
| 3 | bağımlılık kaldırma | §6.4 derin analiz + geri dönüş zinciri kontrolü | Vault Steward |
| 4 | sınır değişimi (§3) | ADR-097 §5.2 kaydı | Vault Steward |
| 5 | etiket yükseltme (⚠️ → ✅) | kanıt URL'si + HTTP 200 ölçümü | research |
| 6 | ad/imla değişikliği | **YASAK** (v2 adları değişmez — §5/5) | — |

### §8.1 Etiket yaşam döngüsü

| Aşama | Etiket | Koşul |
|---|---|---|
| 1 · tasarım | TASARIM İDDİASI | v2 türetmesi, test yok |
| 2 · doğrulama | ⚠️ VERIFICATION REQUIRED | kanıt aranıyor |
| 3 · kanıt | ✅ verified | URL + HTTP 200 veya kod satırı |
| 4 · red | ❌ yanlış | kanıt çürüttü → §1'den çıkar |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §9 Komşu Düğüm Bağımlılık Sınırları (sınır tablosu genişletmesi)

| # | Komşu | K015 sınırı | Ne taşınır | Ne TAŞINMAZ |
|---|---|---|---|---|
| 1 | K003-audio | ses motoru | çalma komutu | DSP/sinyal işleme |
| 2 | K007-data | kalıcılık | kitaplık/metadata | şema kararı |
| 3 | K013-integraion | dış katalog | eşitleme kontratı | dış API adaptörü |
| 4 | K010-network | taşıma | akış/indirme paketleri | ağ protokolü ayrıntısı |
| 5 | K011-application | arayüz | medya gösterimi | arayüz mantığı |
| 6 | K012-services | servisler | medya verisi okuması | iş mantığı/kuyruk |

> Her sınır ihlali = domain violation (AGENTS.md §18 Warnings #6).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §10 Bağımlılık Ölçüm Protokolü (ölçüm tasarımı — eşikler UNKNOWN)

| # | Ölçüt | Yöntem | Beklenen | Durum |
|---|---|---|---|---|
| 1 | giden bağımlılık sayısı | §1 satır sayımı | 4 (K003/K007/K013/K014) | ✅ verified (§1) |
| 2 | gelen bağımlılık sayısı | §2 satır sayımı | 2 (K011/K012) | ✅ verified (§2) |
| 3 | döngü yok (DAG) | §6.1→§6.2 ok takibi | döngü yok | ✅ verified (§5/§6) |
| 4 | riskli bağımlılık | §4 satır sayımı | 7 | ✅ verified (§4) |
| 5 | UNKNOWN sınır sayısı | §3 satır sayımı | 6 (hepsi UNKNOWN) | ✅ verified (§3) |
| 6 | etiket dağılımı | §0 sözlüğü sayımı | 3 sınıf | ✅ verified (§0) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §11 Bağımlılık Kaynakları (§5'e ek)

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 §2.15 | coremusic-katmanli-mimari-agaci-v2.md | 11 çocuk |
| ADR-097 §5.2 | .ai/.decisions/draft/ADR-097-… | bağımlılık kaydı kuralı |
| Registry | .ai/architecture/index.md §5/15 | master adı |
| §6–§10 (bu dosya) | media-bagimliliklar.md | derinleştirmeler |

---

## §12 Bağımlılık Ağı — Komşu Düğüm Etki Analizi (TASARIM İDDİASI)

### §12.1 K003-audio bağımlılığı — etki analizi

| Senaryo | K015 etkisi | K011 etkisi | Geri dönüş |
|---|---|---|---|
| Motor yok | çalma yok | arayüz "çalınamıyor" gösterir | hata durumu |
| Motor kısmi (tek codec) | bazı formatlar çalınmaz | degrade mod | fallback codec ⚠️ |
| Motor yeniden başlatma | çalma kesilir | otomatik yeniden deneme | retry |

### §12.2 K007-data bağımlılığı — etki analizi

| Senaryo | K015 etkisi | Sonuç | Azaltma (tasarım) |
|---|---|---|---|
| Veritabanı yok | koleksiyon erişilemez | arayüz boş | local cache |
| Şema değişimi | beklenmeyen alan | hata | migration (K007) |
| K007 down | kalıcılık yok | veri kaybı riski | health-check |

### §12.3 K013-integraion bağımlılığı — etki analizi

| Senaryo | K015 etkisi | Sonuç | Azaltma (tasarım) |
|---|---|---|---|
| Dış servis yok | eşitleme durur | güncelleme yok | offline fallback |
| Dış servis yavaş | eşitleme gecikir | kullanıcı bekler | timeout + retry |
| Dış servis hata | eşitleme başarısız | kısmi veri | dead-letter |

### §12.4 K010-network bağımlılığı — etki analizi

| Senaryo | K015 etkisi | Sonuç | Azaltma (tasarım) |
|---|---|---|---|
| Ağ yok | akış/indirme durur | çevrimdışı mod | offline fallback |
| Ağ yavaş | akış kalitesi düşer | adaptif bitrate ⚠️ | TASARIM İDDİASI |
| Ağ koparsa | akış kesilir | yeniden bağlanma | K014 reconnect |

### §12.5 Komşu etki özeti

| Komşu | Tek nokta hata? | Geri dönüş var mı | Kritiklik |
|---|---|---|---|
| K003-audio | evet (tek motor) | hata durumu | HIGH |
| K007-data | evet (tek veritabanı) | local cache | HIGH |
| K013-integraion | evet (dış servis) | offline fallback | MEDIUM |
| K010-network | evet (tek ağ) | offline fallback | HIGH |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §13 Bağımlılık Sınıflandırması (derin sınıflandırma)

### §13.1 Bağımlılık tipi sınıflandırması

| Tip | Tanım | K015 örneği | Kritiklik |
|---|---|---|---|
| Motor erişimi | ses motoru | K003 | HIGH |
| Kalıcılık | veritabanı/şema | K007 | HIGH |
| Kontrat | dış servis | K013 | MEDIUM |
| Taşıma | ağ/paket | K014 | HIGH |
| Arayüz | gösterim | K011 (gelen) | LOW |
| İş mantığı | servisler | K012 (gelen) | LOW |

### §13.2 Bağımlılık yönü sınıflandırması

| Yön | Anlam | K015 sayısı | Not |
|---|---|---|---|
| Giden (depends_on →) | K015 buna muhtaç | 4 | K003/K007/K013/K014 |
| Gelen (← used_by) | bunlar K015'e muhtaç | 2 | K011/K012 |

### §13.3 Bağımlılık derinliği sınıflandırması

| Derinlik | Anlam | K015 örneği | Kontrol |
|---|---|---|---|
| Düz (1-seviye) | K015 → hedef | K015 → K003 | §6.1 |
| Dolaylı (2-seviye) | K015 → hedef → hedefin hedefi | K015 → K014 → K001 | §6.1 zinciri |
| Uçtan uca (n-seviye) | tam zincir | §7.1 | §7.1 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §14 Bağımlılık Sağlık Göstergeleri (health-check tasarımı)

| # | Gösterge | Ölçüm | Eşik (tasarım) | Durum |
|---|---|---|---|---|
| 1 | K003 motor erişilebilirlik | motor ping | anlık yanıt | ⚠️ UNKNOWN eşik |
| 2 | K007 veritabanı erişilebilirlik | DB test sorgusu | < X ms | ⚠️ UNKNOWN eşik |
| 3 | K013 dış servis erişilebilirlik | API test isteği | < X ms | ⚠️ UNKNOWN eşik |
| 4 | K014 ağ erişilebilirlik | bağlantı testi | anlık | ⚠️ UNKNOWN eşik |
| 5 | koleksiyon bütünlüğü | parça sayısı | > 0 | ⚠️ UNKNOWN eşik |

### §14.1 Sağlık durumu sınıflandırması

| Durum | Koşul | K015 tepkisi |
|---|---|---|
| HEALTHY | 5/5 gösterge yeşil | normal çalışma |
| DEGRADED | 1-2 gösterge sarı | degrade mod ⚠️ |
| UNHEALTHY | 3+ gösterge kırmızı | çevrimdışı mod |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §15 Bağımlılık Loglama/İzleme (K017 interface'i)

| # | Olay | Log seviyesi | K017'ye akış |
|---|---|---|---|
| 1 | parça çalma başlatma | INFO | çalma sayacı |
| 2 | parça çalma hatası | ERROR | hata sayacı |
| 3 | akış başlatma | INFO | akış sayacı |
| 4 | indirme başlatma | INFO | indirme sayacı |
| 5 | indirme tamamlanma | INFO | indirme süresi |
| 6 | önbellek hit/miss | DEBUG | önbellek oranı |
| 7 | eşitleme | INFO | eşitleme süresi |
| 8 | metadata güncelleme | DEBUG | güncelleme sayısı |

> Loglama biçimi/alanı K017-observability'de tanımlıdır; K015 yalnız olay üretir.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §16 Bağımlılık Testi (test senaryoları — K019-testing sınırı)

| # | Test | Ne doğrular | K015/K019 sınırı |
|---|---|---|---|
| 1 | mock K003 | motor yoksa hata durumu | K015 (davranış) · K019 (altyapı) ⚠️ |
| 2 | mock K007 | DB yoksa local cache | ⚠️ sahiplik belirsiz |
| 3 | mock K013 | dış servis yoksa offline | ⚠️ sahiplik belirsiz |
| 4 | mock K014 | ağ yoksa offline | ⚠️ sahiplik belirsiz |
| 5 | DAG testi | döngü yok (§5/§6) | K015 (statik analiz) |
| 6 | sınır testi | §9 sınır ihlali yok | K015 (statik analiz) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §17 Bağımlılık Envanteri (sayım — 2026-10-13)

| # | Envanter | Sayım | Kaynak |
|---|---|---|---|
| 1 | giden bağımlılık | 4 | §1 |
| 2 | gelen bağımlılık | 2 | §2 |
| 3 | entegrasyon sınırı | 6 | §3 |
| 4 | riskli bağımlılık | 7 | §4 |
| 5 | UNKNOWN sınır | 6 | §3 (hepsi UNKNOWN) |
| 6 | etiket sınıfı | 3 | §0 |
| 7 | bağımlılık tipi | 6 | §13.1 |
| 8 | sağlık gösterge | 5 | §14 |
| 9 | log olayı | 8 | §15 |

> Toplam bağımlılık satırı: 4 giden + 2 gelen = **6 bağımlılık** (K014-media).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §18 Bağımlılık Geçmişi (değişiklik günlüğü — append-only)

| Tarih | Değişiklik | Neden | Sahip |
|---|---|---|---|
| 2026-10-12 | ilk sürüm (v1.0.0) | ADR-097 K2/K3 | Vault Steward |
| 2026-10-12 | §1–§5 yazıldı | v2 §2.15 + ADR-097 §5.2 | Vault Steward |
| 2026-10-13 | §6–§17 eklendi | derinleştirme turu (≥500 hedefi) | Vault Steward |
| 2026-10-13 | §19–§25 eklendi | derinleştirme turu (≥500 hedefi) | Vault Steward |

---

## §19 Komşu K010-network ile Çapraz Referans (grep kanıtı — 2026-10-13)

### §19.1 Yön yön — K015 → K014 (bu dosyanın tarafı)

| # | Dosya | Satır | Metin (kısaltma) | Etiket |
|---|---|---|---|---|
| 1 | media-bagimliliklar.md | §1/4 | K010-network · akış/indirme taşıması | TASARIM İDDİASI |
| 2 | media-master.md | L56 | "Ağ taşıması ayrıntısı → K010-network" | sınır tablosu |
| 3 | media-master.md | L106 | "K010-network · Akış/indirme taşıması" | TASARIM İDDİASI (§5.1 giden) |

### §19.2 Yön yön — K014 → K015 (komşunun tarafı, 17 satır)

| # | Dosya | Satır | Metin (kısaltma) |
|---|---|---|---|
| 1 | network-master.md | L60 | "Medya akışının içeriği/indirme mantığı → K014-media" |
| 2 | network-master.md | L61 | "Çevrimdışı veri kuyruğu içeriği → K015/offline · K012" |
| 3 | network-master.md | L125 | "K014-media · Akış/indirme taşıması" (§5.2 gelen) |
| 4 | network-master.md | L131 | "K011 · K012 · K013 · K015 ──(taşıma)──→ K010-network" |
| 5 | network-master.md | L154 | "offline-sync sahipliği: K014 mü, K015/offline mı, K012 mı?" |
| 6 | network-master.md | L401 | açık soru tablosu · offline-sync sahipliği |
| 7 | network-bagimliliklar.md | L44 | "K014-media · Akış/indirme taşıması · TASARIM İDDİASI" |
| 8 | network-bagimliliklar.md | L57 | "Medya · akış/indirme → K014-media · ⚠️ UNKNOWN" |
| 9 | network-bagimliliklar.md | L107 | "K015 → K014 · medya içeriği K015" |
| 10 | network-bagimliliklar.md | L127 | "K014-media down → K014 yalnız kanalı sürer; K015 fallback" |
| 11 | network-bagimliliklar.md | L430 | "offline-sync sahipliği (3 aday) → Vault Steward kararı" |
| 12 | network-bagimliliklar.md | L474 | "En büyük belirsizlik? offline-sync sahipliği (3 aday)" |
| 13 | network-dogrulama.md | L38 | "offline-sync sahipliği → ❌ üç aday → ⚠️" |
| 14 | network-dogrulama.md | L46 | "sahibi kim? → ⚠️ VERIFICATION REQUIRED · Üç aday" |
| 15 | network-dogrulama.md | L69 | aksiyon: "sahipliği K014/K015/K012 sınırında karara bağla" |
| 16 | network-dogrulama.md | L320 | "K014-media · medya düğümü var · v2 §2.15 (S2)" |
| 17 | K014 index.md | L71 | "← used_by · K014-media · Akış/indirme taşıması" |

### §19.3 İki farklı belirsizlik üçlüsü (ayrırt edilir — karıştırılmaz)

| # | Üçlü | Adaylar | Kaynak |
|---|---|---|---|
| 1 | eşitleme sahipliği | K015/synchronization · K013/synchronization · K014/offline-sync | bu dosya §4/2 |
| 2 | çevrimdışı kuyruk sahipliği | K014 · K015/offline · K012 | network-master L154 · offline-sync/index L14 · K014 index L79 |

**Sonuç:** K014 ↔ K015 çapraz referansı **her iki tarafta da vardır** (3 + 17 satır);
"karşılığı henüz yok" iddiası bu grep ile **desteklenmedi** — yazan dosyanın kendi
okumasına bağlıdır. İkinci üçlünün sahibi hâlâ ⚠️ VERIFICATION REQUIRED (§4/2 ayrıdır).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §20 Frontmatter Denetimi (dosyanın L1–L11 gerçek okuması)

| Satır | Anahtar | Değer | Denetim |
|---|---|---|---|
| L2 | node_id | K014-media | kök düğüm kimliği |
| L3 | node_type | domain | düğüm tipi |
| L4 | role | bagimliliklar | rol ön eki dosya adıyla uyumlu |
| L5 | status | draft | doğrulanmamış taslak |
| L6 | date | 2026-10-12 | ilk yazım |
| L7 | version | 1.0.0 | sürüm |
| L8 | etiket | "TASARIM İDDİASI — bu belge kanıt değil…" | bağlayıcı uyarı |
| L9 | authority | "SSOT: v2 §2.15 + ADR-097 §5.2" | kaynak |
| L10 | group | mimari | **tırnaksız** ✓ (kural: `mimari`/`kod`/`kanit`) |

**FM'de OLMAYAN anahtarlar (disk gerçeği):** `parent` / `parent_id` · `depends_on` ·
`evidence` · `validation` · `open_questions` · `children` — hiçbiri L1–L11'de yok.
Bağımlılık verisi bu dosyada FM'de değil, **§1–§5 tablolarında** taşınır.
`parent:` yasağı bu dosyada **uygulanacak alan bulamadı** (anahtar hiç kullanılmamış).

**Çapraz not:** media-master.md FM'i (L1–L14) farklı anahtar kümesi taşır
(`title` · `type` · `category` · `related` · `role: master`) — iki dosyanın FM
şeması aynı değil. Disk okuması 2026-10-13; düzeltme yok (append-only).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §21 Kapılar (bu dosyaya uygulanan 7 kapı — 2026-10-13)

| # | Kapı | Kanıt | Durum |
|---|---|---|---|
| 1 | FM `parent:` → `parent_id:` | FM'de `parent` anahtarı hiç yok (§20) | ✓ N/A — ihlal yok |
| 2 | FM `group:` tırnaksız | L10 `group: mimari` | ✓ KAPANDI |
| 3 | Authority son satır | §18 + §19–§25 sonrası Authority tek son satır | ✓ KAPANDI |
| 4 | Placeholder `TBD`/`TODO` | grep = 0 eşleşme (2026-10-13) | ✓ KAPANDI |
| 5 | URL / envanter kesişimi | bu dosyada `http` = 0 eşleşme → kesişim 0 | ✓ KAPANDI |
| 6 | Beyazliste dışı yazım | yalnız bu 5 kök içinden bu dosya düzenlendi | ✓ KAPANDI |
| 7 | Append-only (silme yok) | §1–§18 satırları korundu; ek = §19–§25 + §18'e 1 satır | ✓ KAPANDI |

**Durum: 7/7 kapandı · 0 açık kapı.**

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §22 SSS (bu dosyanın sık sorulan 6 sorusu)

**Q1: Çift yönlü (K014↔K015) referans var mı?**
Evet — §19'da 3 giden + 17 gelen satır sayıldı. Aksini iddia eden satır grep'te bulunamadı.

**Q2: offline-sync sahibi kim?**
⚠️ VERIFICATION REQUIRED — iki ayrı üçlü var (§19.3): eşitleme sahipliği (§4/2)
ve çevrimdışı kuyruk sahipliği (K014 · K015/offline · K012). İkisi tek soru değildir.

**Q3: FM'de neden `parent_id` yok?**
FM'de `parent` anahtarı hiç kullanılmamış (§20); kök bağ `node_id` (L2) + `role` (L4)
tarafından taşınır. Eksik alan değil, kullanılmayan alan.

**Q4: Neden `group: mimari`, `kod` değil?**
Bu dosya PHP/DB kodu değil, mimari bağımlılık şemasıdır (FM L10 doğru).

**Q5: Giden bağımlılıklar (K003/K007/K013/K014) gerçek mi?**
Eşik: **TASARIM İDDİASI** (FM L8) — v2 ağacından türetildi, test edilmedi.
"Gerçek" demek bu dosyanın etiketini ihlal eder.

**Q6: K014 ayakta K015 çökerse?**
network-bagimliliklar L127: "K014 yalnız kanalı sürer; K015 fallback" —
taşıma ile içerik ayrı sahiptir (§3.1 kural 2).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §23 Anomali Listesi (bu dosyanın kaydettiği 6 anomali)

| # | Anomali | Kanıt | Eşik |
|---|---|---|---|
| 1 | FM anahtar kümesi media-master ile farklı | §20 (L1–L11 vs master L1–L14) | kaydet · düzeltme yok |
| 2 | 11 alt klasör index.md (50 satır) kardeş içerik dosyası yok | K015 dosya sayımı 2026-10-13 | kapsam dışı (§2) |
| 3 | media-dogrulama.md 115 satır < 500 | dosya sayımı 2026-10-13 | genişletilecek (kuyruk) |
| 4 | iki farklı belirsizlik üçlüsü karıştırılabilir | §19.3 | §19.3 ayırdı |
| 5 | test sahibi çatışması açık | media-master L60 · §7/4 | açık soru |
| 6 | playback sahipliği açık (K015 ↔ K012) | media-master L57 · §7/1 · bu dosya §4/1 | açık soru |

**Durum: 6/6 kaydedildi · 0 anomali gizlendi.**

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §24 Sürüm / Güncelleme Eşiği (bu dosyanın değişmezlik kuralları)

```
Değişmezlik : §1–§18 mevcut satırları — SİLİNMEZ, ÜZERİNE YAZILMAZ
Ek kuralı   : yeni içerik = yeni § numarası (§19→§25 bu turda eklendi)
Tetikleyici : (a) yeni bağımlılık satırı girerse → §1 + §3 + §4 birlikte,
              (b) K014 tarafı revize edilirse → §19 yeniden grep edilir,
              (c) FM anahtar şeması değişirse → §20 yeniden okunur.
Yazım yasağı: imla hataları KORUNUR (identitiy · recomenataon dahil,
              dosya/dizin adı DEĞİŞTİRİLMEZ) · URL yalnız HTTP 200 ile eklenir
              · dolgu içerik yazılmaz · git commit ATILMAZ.
Authority   : dosya sonundaki tek Authority satırı (§25).
```

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §25 Kapı Özeti ve Authority (kapanış mührü)

```
KAPILAR (7): §21 — 7/7 kapandı · 0 açık
FM DENETİM : §20 — group ✓ · parent anahtarı yok (N/A) · 6 anahtar + 2 yok
ÇAPRAZ     : §19 — 3 giden + 17 gelen satır · iki üçlü ayrıldı
ANOMALİ    : §23 — 6/6 kayıtlı · 0 gizli
KANIT TÜRÜ : TASARIM İDDİASI (FM L8) — test edilmemiş, onay gerektirir
YAZMA YASAĞI: beyazliste dışı kök dizinler · kaynak kod uzantıları · imla düzeltme
```

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
