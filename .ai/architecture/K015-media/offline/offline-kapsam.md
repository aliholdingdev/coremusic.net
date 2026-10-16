---
node_id: "K015-media/offline/offline-kapsam"
node_type: "module"
status: "draft"
parent_id: "K015-media/offline"
children: []
depends_on: ["K015-media/media-master", "K015-media/downloads", "K014-network", "K012-services"]
evidence:
  - "media-master.md §9.8 (offline — 4 satır TASARIM İDDİASI) · §7/2 (sahiplik ⚠️ belirsiz) · §14.3 (indirme zinciri — §4'e birebir)"
  - "offline/index.md §GRUPLAR — bu dosya Kademe-2 gruba eklendi (2026-10-16)"
validation: "Draft — §4 zinciri media-master §14.3 ile birebir karşılaştırıldı (2026-10-16); sahiplik (§7/2) açık soru olarak TAŞINDI, kesinleştirilmedi"
open_questions:
  - "Sahiplik: K015 mi K014/offline-sync mi K012 mi → ⚠️ VERIFICATION REQUIRED (media-master §7/2 · index FM) — bu dosyada KESİNLEŞTİRİLMEZ"
  - "Çevrimdışı kuyruk sahibi (K012 sınırı) → ⚠️ VERIFICATION REQUIRED (§7/2)"
  - "TTL / tazelik eşiği (stale tanımı) → ⚠️ UNKNOWN — sayı yok"
group: mimari
---

# K015-media · offline · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Modelleme Dışı, Ne Hariç
>
> **Etiket kuralı:** bu dosyanın **tüm satırları TASARIM İDDİASI**'dır; yalnız `offline`
> çocuk adı S2 kanıtlıdır (v2 §2.15 — media-master §9.8 ilk satırı).
> **Sahiplik kesinleştirme yasağı:** çevrimdışı kimin (§2/2) bu dosyada **karara bağlanmaz**.

---

## §1 KAPSAM İÇİ (modellenen) — TASARIM İDDİASI

### §1a Varlıklar (media-master §9.8 ile hizalı) — TASARIM İDDİASI

1. **Çevrimdışı hazır varlık (offline-ready item)** — indirilmiş veya önbelleğe alınmış parça/kapak/metadata (media-master §9.8 "İçerik" satırı) — TASARIM İDDİASI (S2: çocuk adı "Çevrimdışı Kullanılabilirlik").
2. **Bağlantı durumu** — istemcinin ağ görünürlüğü (`online` / `online-degraded` / `offline`) — TASARIM İDDİASI (detay: §3).
3. **Tazelik (stale) kavramı** — hazır varlığın kaynağı hâlâ geçerli mi sorusu; eşiği **UNKNOWN** (§6/3) — TASARIM İDDİASI (etiketsiz eşik YOK).
4. **Hazırlık el sıkışması** — `downloads`'un `completed` çıktısının çevrimdışı kullanılabilir hâle gelmesi (yalnız **giriş** yönü; indirme mantığı §2/1) — TASARIM İDDİASI (media-master §14.3 son adım).
5. **Çevrimdışı kullanılabilirlik bildirimi** — kullanıcının "ne hazır, ne bekliyor, ne silinmeli" sorusuna verilen durum (offline/index ELI15'i birebir) — TASARIM İDDİASI.

### §1b Kullanıcı Yüzeyi (arayüz envanteri) — TASARIM İDDİASI

| Yüzey | Ne taşır | Kanıt durumu |
|---|---|---|
| Çevrimdışı göstergesi | §3 bağlantı durumu rozeti | TASARIM İDDİASI |
| Hazır listeler | §1a-1 hazır varlıklar | TASARIM İDDİASI |
| Bekleyenler | indirme bekleyen (§2/1'den gelen) | TASARIM İDDİASI |
| "Ne silinmeli" uyarısı | §1a-3 tazelik + §6/1 politika | TASARIM İDDİASI |
| Senkron geri bildirimi | bağlantı döndüğünde güncelleme (K014/offline-sync sınırı — §2/2) | TASARIM İDDİASI |

### §1c Zaman penceresi — TASARIM İDDİASI

- **Birincil:** 2026-10-16 vault anlık görüntüsü (media-master.md §9.8/§7/2/§14.3, offline/index.md).
- **Kanıt sınırı:** yalnız çocuk adı S2; kalan satırlar tasarım iddiasıdır (frontmatter `validation`).

---

## §2 KAPSAM DIŞI (bilinçli hariç — gerekçeli) — TASARIM İDDİASI

1. **K015-media/downloads** — dosyanın diske alınması indirme düğümünün işidir; bu dosya yalnız **hazır olma durumunu** modeller (media-master §14.3 zincirinde downloads 2. adımdır). Geri dönüş koşulu: çevrimdışı için özel bir "yerel yazma" kararı çıkarsa yalnız sınır satırı revize edilir. — TASARIM İDDİASI
2. **K014/offline-sync (bağlantı kopması/kuyruk) + K012/offline kuyruğu sınırı** — bağlantı kopması kuyruğunun ve çevrimdışı senkronun sahibi **bu dosya değildir**; media-master §7/2 sahipliği **⚠️ belirsiz ilan eder (K015 mi K014 mü K012 mi) — burada KESİNLEŞTİRİLMEZ**. offline/index sınır satırı da "sınır belirsiz" der. Geri dönüş koşulu: §7/2 kapanırsa bu satır birlikte değerlendirilir; kapanmadan sahip ataması **üretilmez**. — TASARIM İDDİASI
3. **K014-network (taşıma/çevrimiçilik)** — ağ katmanı bu dosyanın değildir; §4 zincirinde yalnız **adım** olarak taşır. — TASARIM İDDİASI
4. **K007-data kalıcılığı** — çevrimdışı envanter kaydının şeması K007'dedir (media-master §2.2); burada yalnız mantıksal durum tanımlanır. — TASARIM İDDİASI
5. **K015-media/media-cache** — önbellek (tekrar-indirmeme) ayrı düğüm (media-master §9.9); "hazır" ile "önbellekte" burada **ayrı kavramdır** (§3'te yalnız bağlantı durumu modellenir). — TASARIM İDDİASI

---

## §3 DURUM MAKİNESİ (4 durum) — TASARIM İDDİASI

| # | Durum | Anlamı | Çıkış geçişleri |
|---|---|---|---|
| S-1 | `online` | ağ sağlıklı, tüm doğrulamalar çalışıyor | `online-degraded` · `offline` |
| S-2 | `online-degraded` | ağ var ama yavaş/hata veren (istanekler kısmen başarısız) | `online` · `offline` |
| S-3 | `offline` | ağ yok; yalnız §1a-1 hazır varlıklar kullanılabilir | `online-degraded` (bağlantı döndü) · `online` (doğrudan doğrulama — yalnız ağ gözlemlenirse) |
| S-4 | `offline-synced` | bağlantı döndükten sonra bekleyen değişiklikler dışarı aktarıldı; iç durum temiz | `online` · `online-degraded` · `offline` |

**Geçiş kuralı (TASARIM İDDİASI):** `offline → offline-synced` geçişi **dış olayla** (bağlantının dönmesi + bekleyen işin bitmesi) tetiklenir; `stale` bir **durum değil**, varlık düzeyinde bir özelliktir (S-5): hazır varlık kaynağı artık geçerli değilse kullanılır ama tazelik uyarısı taşır. `offline-synced` kalıcı bir durum değildir — yeni ağ olayıyla `online`/`online-degraded`'e döner. Geçişler ornekler Ör-01'deki durum makinesiyle gösterilir.

### §3a `stale` (varlık düzeyi işaret — durum makinesi dışı)

- Hazır varlık `stale` olabilir: kaynağı değişmiş/süresi dolmuş (media-master §9.8 "indirilen/önbelleğe alınan" içeriği; eşik: §6/3).
- `stale` varlık **silinmez** (silme kararı §6/1 politikasına bağlı — kapatılmadan otomatik temizlik YOK).
- Eşik/tarih hesabı yazılmaz: **UNKNOWN** (§6/3).

---

## §4 VERİ AKIŞI ZİNCİRİ (media-master §14.3 — birebir) — TASARIM İDDİASI

```text
K011-application ("indir")
  → K015-media/downloads
    → K014-network (taşıma)
      → dosya → yerel depo
        → K015-media/offline (çevrimdışı kullanılabilir)
```

> Kaynak: `media-master.md` §14.3 — bu dosyaya **birebir kopyalanmıştır** (değişiklik yok).
> Zincirde offline **yalnız son adımın** (çevrimdışı kullanılabilir) konumudur (§1a-4); giriş adımları §2/1-§2/3'tedir.

---

## §5 HATA DURUMLARI — TASARIM İDDİASI

| # | Hata | Tetikleyici | Durum karşılığı | Davranış |
|---|---|---|---|---|
| H-1 | Bağlantı kaybı | ağ gitti | → `offline` | yalnız hazır varlıklar sunulur; bekleyen işler §2/2 kuyruğuna devredilir (sahibi belirsiz) |
| H-2 | Ağ var ama yanıt yok/yavaş | istek başarısız | → `online-degraded` | kullanıcıya "bağlantı zayıf" gösterilir; hazır varlıklar önceliklidir |
| H-3 | Hazır varlık kaynağı değişmiş | doğrulama uyuşmadı | varlık `stale` (§3a) | kullanılabilir + uyarı; silme **YOK** (§6/1 kapanmadan) |
| H-4 | Eşzamanlı bağlantı değişimi (çakışma) | çevrimdışıyken başka cihaz değiştirdi | `offline-synced` öncesi | çözüm yolu §2/2'nin (sahiplik belirsiz — §6/2); çakışma çözümü burada tanımlanmaz |
| H-5 | Hazır varlık yetersiz | kullanıcı çevrimdışı ama içerik yok | — (durum değişmez) | "bu içerik çevrimdışı değil" bilgisi; indirme teklifi §2/1'e aittir |

**Kural:** çevrimdışı geçişte **veri kaybı üretmez** — TASARIM İDDİASI; bekleyen işler silinmez, kuyruğa devredilir (kuyruk sahibi §6/2).

---

## §6 AÇIK SORULAR (⚠️ — kapanışı gerekenler)

| # | Açık soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| 1 | Çevrimdışı sahipliği: K015 mi K014/offline-sync mi K012 mi? | ⚠️ VERIFICATION REQUIRED (media-master §7/2 · index FM) | üst karar yazılana kadar **kesinleştirilmez** — bu dosya sahip ATAMAZ |
| 2 | Çevrimdışı kuyruk/çakışma çözümü hangi düğümde? | ⚠️ VERIFICATION REQUIRED (§7/2 + §2/2) | §7/2 kapanışı |
| 3 | Tazelik (stale) eşiği / TTL | ⚠️ UNKNOWN — sayı yok | politika kararı; kanıt gelmeden gün/saat yazılmaz |
| 4 | Saklama politikası/sınırı (ne silinmeli) | ⚠️ UNKNOWN (media-master §12/3) | politika kararı — bu dosyada otomatik temizlik YOK |
| 5 | Kademe-3 bounded-context kapsamı | ⚠️ VERIFICATION REQUIRED (index FM) | ADR-097 K4 faz (C) |

**Kapanış kuralı:** bu dosya açık soruları **taşıır, kapatmaz**; özellikle **§6/1 sahiplik kararı burada verilmez** (zero-hallucination + media-master §7/2).

---

## §7 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-16 | İlk yazım — §1-§6 (kapsam içi/dışı, 4 durumlu makine + stale işareti, §14.3 zinciri birebir, hata durumları, sahiplik/TTL açık soruları) | Vault Steward (subagent) |

## § BAĞLANTILAR (düz yol — wiki-link yok)

- `../media-master.md` (§7/2 · §9.8 · §12/3 · §14.3)
- `index.md` · `offline-web-arastirma.md` · `offline-ornekler.md`
- `../index.md`

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
