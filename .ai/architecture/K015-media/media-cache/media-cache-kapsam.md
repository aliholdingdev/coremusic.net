---
node_id: "K015-media/media-cache/media-cache-kapsam"
node_type: "module"
status: "draft"
parent_id: "K015-media/media-cache"
children: []
depends_on: ["K015-media/media-master", "K007-data", "K015-media/artwork", "K015-media/downloads", "K015-media/offline"]
evidence:
  - "media-master.md §9.9 (media-cache — 4 satır TASARIM İDDİASI) · §10.1 (yaşam döngüsü 6. aşama) · §12/3 ve §12/8 (boyut/TTL ve temizleme stratejisi UNKNOWN)"
  - "media-cache/index.md §GRUPLAR — bu dosya Kademe-2 gruba eklendi (2026-10-16)"
validation: "Draft — §1 medya-master §9.9 ile hizalandı (2026-10-16); TTL/boyut/temizleme eşikleri UNKNOWN olarak korundu, sayı uydurulmadı"
open_questions:
  - "Boyut/süre (TTL) politikası → ⚠️ UNKNOWN (media-master §12/3 · index FM) — bu dosyada KAPATMAZ"
  - "Temizleme stratejisi (LRU/LFU hangisi?) → ⚠️ UNKNOWN (media-master §12/8) — bu dosyada KAPATMAZ"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C — index FM)"
  - "offline sahipliği (§7/2) → ⚠️ VERIFICATION REQUIRED — bu dosyada KESİNLEŞTİRİLMEZ"
group: mimari
---

# K015-media · media-cache · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Modelleme Dışı, Ne Hariç
>
> **Etiket kuralı:** bu dosyanın **tüm satırları TASARIM İDDİASI**'dır; yalnız `media-cache`
> çocuk adı S2 kanıtlıdır (v2 §2.15 — media-master §9.9 ilk satırı).

---

## §1 KAPSAM İÇİ (modellenen) — TASARIM İDDİASI

### §1a Varlıklar (media-master §9.9 ile hizalı) — TASARIM İDDİASI

1. **Önbellek girdisi (cache entry)** — tek bir medya varlığının önbellekteki karşılığı;
   içerik kümesi media-master §9.9 "İçerik" satırı ile birebir: **artwork · metadata · parça önbelleği** —
   TASARIM İDDİASI (S2: çocuk adı "Medya Önbelleği").
2. **Önbellek anahtarı (cache key)** — bir girdinin bulunması için kullanılan eşleme
   (kaynak kimliği + varyant) — TASARIM İDDİASI (varyant ayrımı artwork §9.3 kesişimi).
3. **Tazelik kaydı (freshness)** — girdinin ne zaman taze sayılacağı; **süre değeri bu dosyada YOKTUR**
   → açık soru §6/1 — TASARIM İDDİASI (etiketsiz TTL/sayı YOK).
4. **Doğrulama (validation)** — tazelik bittikten sonra kaynağın hâlâ geçerli olup olmadığının
   sorulması (ETag/Last-Modified ile) — TASARIM İDDİASI (repo kanıtı: ornekler Ör-01 notu).
5. **Durum döngüsü** — `empty` (miss) → `filling` → `fresh` (hit) → `stale` → `evicted` — §3.
6. **Temizlik çağrısı** — girdinin politika ile dışarı atılması; **strateji adı seçilmedi**
   (LRU/LFU/ARC adayları yalnız web-arastirma §2'de karşılaştırılır) → açık soru §6/2 —
   TASARIM İDDİASI (media-master §9.9 "Temizlik" satırı: "LRU/LFU ⚠️ UNKNOWN").
7. **Kesişim kayıtları** — artwork varyant önbelleği (media-master §9.3 "Önbellek" satırı) ·
   isabet/kaçırma ölçümü (§15/4 — eşik UNKNOWN) · test senaryosu "önbellek TTL" (§13/5) —
   TASARIM İDDİASI (bu dosya yalnız sınırı taşır, sahiplenmez).

### §1b Kullanıcı Yüzeyi (arayüz envanteri) — TASARIM İDDİASI

| Yüzey | Ne taşır | Kanıt durumu |
|---|---|---|
| Hızlı açılış (önceden görülen parça/kapak) | `fresh` hit (§3 S-3) | TASARIM İDDİASI |
| Depolama uyarısı | §5/1 kota dolumu | TASARIM İDDİASI |
| "Önbelleği temizle" komutu | `fresh`/`stale` → `evicted` (elle) | TASARIM İDDİASI |
| Hata satırı | §5 durumları | TASARIM İDDİASI |
| (gizli) isabet oranı göstergesi | §15/4 ölçüm verisi — eşik UNKNOWN | ⚠️ TASARIM İDDİASI |

### §1c Zaman penceresi — TASARIM İDDİASI

- **Birincil:** 2026-10-16 vault anlık görüntüsü (media-master.md §9.9/§10.1/§12, media-cache/index.md).
- **Kanıt sınırı:** yalnız çocuk adı S2; kalan satırlar tasarım iddiasıdır (frontmatter `validation`).

---

## §2 KAPSAM DIŞI (bilinçli hariç — gerekçeli) — TASARIM İDDİASI

1. **K014-network (taşıma)** — ağ/protokol katmanı bu dosyanın değildir; §4'te yalnız
   "kaynaktan getir" adımı olarak anılır. Geri dönüş koşulu: önbelleğe özel taşıma kararı
   çıkarsa yalnız sınır satırı revize edilir — sessiz devir yasak. — TASARIM İDDİASI
2. **K015-media/downloads (yerel kopya)** — kalıcı indirme ayrı düğüm (media-master §9.7);
   önbellek **geçici** tutar, indirme **saklar**. Geri dönüş koşulu: indirme ile önbellek
   tek katmanda birleşirse §3 makinesi yeniden yazılır. — TASARIM İDDİASI
3. **K015-media/offline sahipliği (media-master §7/2)** — çevrimdışı kullanılabilirliğin
   sahibi belirsizdir ve **bu dosyada KESİNLEŞTİRİLMEZ** (açık soru §6/4); media-cache yalnız
   "hızlandırılmış erişim" (§10.1 6. aşama) ile ilgilenir. — TASARIM İDDİASI
4. **K007-data kalıcılığı** — önbellek kaydının kalıcı şeması K007'dedir (media-master §2.2);
   burada yalnız mantıksal girdi tanımlanır. — TASARIM İDDİASI
5. **Medya-dışı uygulama önbelleği** — repo'daki sayfa/APCu önbelleği ayrı katmandır
   (`shared/src/Cache/` — CacheManager · ApcuAdapter · MemoryAdapter · PageCacheAdapter,
   envanter: shared/AGENTS.md §2; 6 dosya glob 2026-10-16); bu dosya **medya** önbelleğini
   modeller. — TASARIM İDDİASI
6. **Ölçüm altyapısı (K017)** — isabet oranı ölçümünün toplanması K017'dedir; K015 yalnız
   veri üretir (media-master §15 alt notu). — TASARIM İDDİASI
7. **K011-application tetikleme UI'ı** — "temizle" düğmesinin kendisi K011'de; burada yalnız
   durum makinesine giren **komut** modellenir. — TASARIM İDDİASI

---

## §3 DURUM MAKİNESİ (5 durum) — TASARIM İDDİASI

| # | Durum | Anlamı | Çıkış geçişleri |
|---|---|---|---|
| S-1 | `empty` | kayıt yok = **MISS** | `filling` (okuma isteği) |
| S-2 | `filling` | kayıt yazılıyor (kaynak taze verisi geliyor) | `fresh` · `empty` (yazma başarısız → §5/2) |
| S-3 | `fresh` | kayıt taze = **HIT**, okuma serbest | `stale` (tazelik süresi doldu — değer §6/1) · `evicted` (temizlik) |
| S-4 | `stale` | tazelik bitti; doğrulama gerekir | `fresh` (doğrulama başarılı) · `filling` (yeniden dolum) · `evicted` |
| S-5 | `evicted` | politika/elle silindi (geçici durum) | `empty` (tek çıkış — miss döngüsüne kapanır) |

**Geçiş kuralı (TASARIM İDDİASI):** her geçiş tek kaynaktan tetiklenir (okuma isteği · zaman
yayını · yazma sonucu · temizlik komutu); çift-kaynaklı geçiş yasaktır. `stale → fresh`
yalnız **doğrulama başarılıysa** mümkündür; doğrulama başarısızsa yol `filling`
(yeniden dolum) veya `evicted`'dir. `empty → fresh` geçişi **tanımlı değildir** (taze girdi
her zaman `filling`'den geçer). Tüm durumlar ornekler Ör-04'teki makineyle gösterilir.

---

## §4 VERİ AKIŞI (media-master §10.1 6. aşama — hizalı) — TASARIM İDDİASI

```text
K011-application (medya isteği)
  → K015-media/media-cache (anahtar çözümleme — §1a-2)
    → MISS (S-1): kaynaktan getir
        → indirilmiş yerel kopya (downloads — sınır §2/2) VEYA çevrimiçi kaynak (K013/K014 — sınır §2/1)
      → HIT (S-3): doğrudan servis (hızlandırılmış erişim — media-master §10.1/6)
    → kayıt yaz (S-2) → fresh (S-3)
    → stale (S-4): doğrula → fresh / yeniden dolum
    → evicted (S-5) → empty (S-1)
```

> Kaynak: `media-master.md` §10.1'in 6. aşaması ("6 · önbellek — media-cache'te → hızlandırılmış
> erişim") ve §9.9 — bu dosyaya **hizalanmıştır**; media-master'da bu zincir için hazır bir §14
> bloğu **YOKTUR**, buradaki akış tasarımsal genişletmedir (etiket: TASARIM İDDİASI).
> `downloads`/`offline` adımları yalnız **kaynak** olarak anılır; sahiplikleri §2/2-3'tedir.

---

## §5 HATA DURUMLARI — TASARIM İDDİASI

| # | Hata | Tetikleyici | Durum karşılığı | Davranış |
|---|---|---|---|---|
| H-1 | Depolama kotası dolumu | yazma reddedildi | `filling` → `empty` | önbelleksiz devam (miss); otomatik temizlik **YOK** — strateji §6/2 kapana kadar |
| H-2 | Kayıt yazma hatası (disk/depo) | I/O istisnası | `filling` → `empty` | hata kullanıcıya yayılmaz; istek kaynaktan karşılanır |
| H-3 | Doğrulama başarısız (kaynak değişmiş/erişilemiyor) | stale iken doğrulama reddi | `stale` → `filling` · `evicted` | eski (stale) veri ile devam **kararı §6/2'ye bağlıdır** — burada varsayılmaz |
| H-4 | Anahtar/varyant uyuşmazlığı | istek yanlış varyantla eşleşti | `empty` | yeni anahtarla yeniden ara (MISS); yanlış kapak/metadata gösterilmez |
| H-5 | Önbelleklenen parça kaynağı bulunamadı | kaynak 404/eksik | `filling` → `empty` | bölüm §6/4'e dokunmaz; kullanıcıya standart hata |

**Kural:** hata durumunda **önbellek boş sayılır, bozuk girdi tutulmaz** — TASARIM İDDİASI;
eşik/boyut/süre değerleri yazılmaz (zero-hallucination), H-1'de otomatik silme §6/1-§6/2
kapanana kadar **yasaktır**.

---

## §6 AÇIK SORULAR (⚠️ — kapanışı gerekenler)

| # | Açık soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| 1 | Önbellek boyutu / TTL politikası (ne kadar süre, ne kadar alan?) | ⚠️ UNKNOWN (media-master §12/3 · index FM) | politika kararı yazılana kadar §3 `fresh` süresi ve §5/1 otomatik temizlik **kapatılmaz** — bu dosya eşik uydurmaz |
| 2 | Temizleme stratejisi (LRU · LFU · ARC · diğeri) | ⚠️ UNKNOWN (media-master §12/8) | strateji kararı (Vault Steward) — adaylar yalnız web-arastirma §2'de karşılaştırılır, **seçilmez** |
| 3 | Kademe-3 bounded-context kapsamı | ⚠️ VERIFICATION REQUIRED (index FM) | ADR-097 K4 faz (C) |
| 4 | offline ile sınır: çevrimdışı kullanılabilirlik kimin? | ⚠️ VERIFICATION REQUIRED (media-master §7/2) | §7/2 kararı — bu dosyada **kesinleştirilmez**, yalnız §2/3'te sınır taşınır |
| 5 | Önbellek isabet oranı (hit/miss) hedef eşiği | ⚠️ UNKNOWN (media-master §15/4) | ölçüm eşiği K015/K017 kararı — sayı yazılmaz |

**Kapanış kuralı:** bu dosya açık soruları **taşıır, kapatmaz**; eşik/boyut/süre/strateji
değerleri kanıt gelmeden yazılmaz (zero-hallucination).

---

## §7 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-16 | İlk yazım — §1-§6 (kapsam içi/dışı, 5 durumlu makine, §10.1/6 hizalı akış, hata durumları, açık sorular) | Vault Steward (subagent) |

## § BAĞLANTILAR (düz yol — wiki-link yok)

- `../media-master.md` (§7/2 · §9.3 · §9.9 · §10.1 · §12/3 · §12/8 · §15/4)
- `index.md` · `media-cache-web-arastirma.md` · `media-cache-ornekler.md`
- `../downloads/index.md` · `../offline/index.md` · `../artwork/index.md`
- `../index.md`

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
