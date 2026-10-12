---
node_id: K015-media
node_type: domain
role: bagimliliklar
status: draft
date: 2026-10-12
version: 1.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.15 + ADR-097 §5.2"
---

# K015-media — media-bagimliliklar.md

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
| 4 | K014-network | Akış/indirme taşıması | taşıma | Çevrimdışı davranış bu bağımlılıktan beslenir |

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
| 4 | Taşıma | akış/indirme paketleri | K014-network | ⚠️ UNKNOWN (akış biçimi) |
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

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
