---
node_id: "K015-media/downloads/downloads-kapsam"
node_type: "module"
status: "draft"
parent_id: "K015-media/downloads"
children: []
depends_on: ["K015-media/media-master", "K014-network", "K015-media/offline", "K011-application"]
evidence:
  - "media-master.md §9.7 (downloads — 4 satır TASARIM İDDİASI) · §14.3 (indirme zinciri — §4'e birebir) · §12/3 (boyut/TTL UNKNOWN)"
  - "downloads/index.md §GRUPLAR — bu dosya Kademe-2 gruba eklendi (2026-10-16)"
validation: "Draft — §4 zinciri media-master §14.3 ile birebir karşılaştırıldı (2026-10-16); saklama/TTL eşikleri UNKNOWN olarak korundu, sayı uydurulmadı"
open_questions:
  - "Saklama politikası/sınırı → ⚠️ UNKNOWN (media-master §12/3 · index FM) — bu dosyada KAPATMAZ"
  - "Kademe-3 bounded-context kapsamı → ⚠️ VERIFICATION REQUIRED (faz C — index FM)"
group: mimari
---

# K015-media · downloads · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · Kapsam Sınırı: Ne Modelleme Dışı, Ne Hariç
>
> **Etiket kuralı:** bu dosyanın **tüm satırları TASARIM İDDİASI**'dır; yalnız `downloads`
> çocuk adı S2 kanıtlıdır (v2 §2.15 — media-master §9.7 ilk satırı).

---

## §1 KAPSAM İÇİ (modellenen) — TASARIM İDDİASI

### §1a Varlıklar (media-master §9.7 ile hizalı) — TASARIM İDDİASI

1. **İndirme görevi (download job)** — bir kaynağın yerel kopyasının alınması niyeti; kuyruğa giren birim — TASARIM İDDİASI (S2: çocuk adı "İndirme").
2. **Kuyruk durumu** — görevin §3'teki yaşam döngüsü (queued → active → …) — TASARIM İDDİASI (media-master §9.7 "Hata" satırı: "yarıda kesilirse resume").
3. **Resume meta verisi** — yarıda kesilen görevin kaldığı yerden sürmesi için taşınan bilgi (başlangıç baytı, varlık tanımlayıcısı) — TASARIM İDDİASI (media-master §9.7 resume satırı; detay: ornekler Ör-02).
4. **Yerel depo yazımı** — tamamlanan/kısmi dosyanın diske yazımı ve tamamlanma kontrolü — TASARIM İDDİASI (media-master §14.3: "dosya → yerel depo").
5. **Saklama politikası kalem'i** — ne kadar süre/boyut tutulacağı **bu dosyada tanımlanmaz**; açık soru §6/1 — TASARIM İDDİASI (etiketsiz eşik YOK).
6. **İndirme → çevrimdışı el sıkışması** — tamamlanan görevin `offline` için hazır hâle gelmesi (yalnız **çıktı** yönü; çevrimdışı mantığının sahibi §2/2) — TASARIM İDDİASI (media-master §14.3 son adım).

### §1b Kullanıcı Yüzeyi (arayüz envanteri) — TASARIM İDDİASI

| Yüzey | Ne taşır | Kanıt durumu |
|---|---|---|
| "İndir" komutu | görev oluşturma (§1a-1) | TASARIM İDDİASI |
| Kuyruk listesi | §3 durumları + ilerleme | TASARIM İDDİASI |
| Duraklat/devam/İptal | `paused` · `resumed` · `failed` geçişleri | TASARIM İDDİASI |
| Hata satırı | §5 durumları + yeniden deneme | TASARIM İDDİASI |
| Depo uyarısı | §5/3 kota dolumu | TASARIM İDDİASI |

### §1c Zaman penceresi — TASARIM İDDİASI

- **Birincil:** 2026-10-16 vault anlık görüntüsü (media-master.md §9.7/§14.3/§12, downloads/index.md).
- **Kanıt sınırı:** yalnız çocuk adı S2; kalan satırlar tasarım iddiasıdır (frontmatter `validation`).

---

## §2 KAPSAM DIŞI (bilinçli hariç — gerekçeli) — TASARIM İDDİASI

1. **K014-network (taşıma)** — paket/protokol/çevrimiçilik katmanı bu dosyanın değildir; §4 zincirinde yalnız **adım** olarak taşır. Geri dönüş koşulu: indirmeye özel taşıma kararı (ör. protokol seçimi) çıkarsa yalnız sınır satırı revize edilir — sessiz devir yasak. — TASARIM İDDİASI
2. **K015-media/offline + K014/offline-sync sınırı** — çevrimdışı kullanılabilirlik ve bağlantı-kopması kuyruğunun sahibi **bu dosya değildir** (media-master §7/2: sahiplik ⚠️ belirsiz — **kesinleştirilmez**); downloads yalnız `completed` çıktısını üretir. Geri dönüş koşulu: §7/2 kapanırsa sınır satırı birlikte değerlendirilir. — TASARIM İDDİASI
3. **K015-media/media-cache** — önbellek (tekrar-indirmeme) ayrı düğüm (media-master §9.9); bu dosya yalnız **yerel kopya** ile ilgilenir. — TASARIM İDDİASI
4. **K007-data kalıcılığı** — indirme kaydının şeması K007'dedir (media-master §2.2); burada yalnız mantıksal kaynak tanımlanır. — TASARIM İDDİASI
5. **Akış (streaming) yüzeyi** — repo'daki byte-range davranışı akış içindir (`home.coremusic.net/include/Stream/MusicStreamHandler.php:10-17`, okundu 2026-10-16); **diske indirme endpoint'i repo'da 0 isabet** — bu dosya repo iddiası üretmez. — TASARIM İDDİASI
6. **K011-application tetikleme UI'ı** — "indir" düğmesinin kendisi K011'de; burada yalnız kuyruğa giren **komut** modellenir. — TASARIM İDDİASI

---

## §3 DURUM MAKİNESİ (6 durum) — TASARIM İDDİASI

| # | Durum | Anlamı | Çıkış geçişleri |
|---|---|---|---|
| S-1 | `queued` | görev kuyrukta, ağ işi başlamadı | `active` · `failed` (iptal) |
| S-2 | `active` | indirme sürüyor (akış açık) | `paused` · `completed` · `failed` |
| S-3 | `paused` | kullanıcı/durum durdurdu, meta korunur | `resumed` · `failed` (iptal) |
| S-4 | `resumed` | `paused`/`failed` sonrası kaldığı bayttan yeniden başladı → teknik olarak `active` akışına döner | `active` · `failed` |
| S-5 | `failed` | geri alınamaz/tekrar denenecek hata (§5) | `resumed` (yeniden deneme) · `queued` (baştan) |
| S-6 | `completed` | dosya yerel depoda, bütünlük kontrolü geçti | — (son; temizlik §6/1 politikasına bağlı) |

**Geçiş kuralı (TASARIM İDDİASI):** her geçiş tek kaynaktan tetiklenir (kullanıcı komutu · ağ olayı · depo olayı);
çift-kaynaklı geçiş yasaktır. `resumed` bilinçli **ayrı durumdur** (media-master §9.7 "resume" vurgusu):
kaldığı yerin bilgisi taşınmazsa geçiş `failed → queued` (baştan) olur. `completed → active` geçişi
**tanımlı değildir** (yeniden indirme = yeni görev). Tüm geçişler ornekler Ör-01'deki kuyruk makinesiyle gösterilir.

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
> Zincirde downloads **yalnız 2. adımın** sahibidir (§1a-1/§1a-4); taşıma §2/1, çevrimdışı sonuç §2/2'dir.

---

## §5 HATA DURUMLARI — TASARIM İDDİASI

| # | Hata | Tetikleyici | Durum karşılığı | Davranış |
|---|---|---|---|---|
| H-1 | Yarıda kesilme (ağ/istemci) | akış koptu | `failed` → `resumed` | kaldığı bayttan devam (resume — §1a-3); desteklenmiyorsa §5/6 |
| H-2 | Aralık çözümlenemedi / istenemeyen aralık | sunucu aralık isteğini reddetti | `failed` | baştan dene (`queued`) — sunucu tam gövdeye düşebilir (kaynak: research A-1/A-3) |
| H-3 | Kaynak sürümü değişti (resume sırasında) | varlık tanımlayıcısı uyuşmadı | `failed` | kısmi dosya geçersiz sayılır → `queued` (baştan); eski parça korunmaz |
| H-4 | Depo kotası dolumu | yerel depo yazımı reddedildi | `failed` | kullanıcıya "alan yetersiz" raporu; §6/1 politikası kapatılmadan otomatik temizlik **YOK** |
| H-5 | İstek tekrar sınırı | sunucu yavaşlaması istedi | `queued` (beklemeli) | bekleme süresi sunucunun önerisiyle taşınır; sabit sayı uydurulmaz → research A-6 |
| H-6 | Sunucu geçici hatası | 5xx yanıt | `failed` → `resumed` | sınırlı yeniden deneme; deneme sayısı **UNKNOWN** (§6/3) |

**Kural:** hata durumunda **kaldığı yer bilgisi korunur** (H-3 hariç) — TASARIM İDDİASI; korunamıyorsa görev
`queued`'a düşer ve kullanıcıya neden gösterilir. H-4'te otomatik silme **yasaktır** (§6/1 kapanmadan).

---

## §6 AÇIK SORULAR (⚠️ — kapanışı gerekenler)

| # | Açık soru | Etiket | Kapanış koşulu |
|---|---|---|---|
| 1 | Saklama politikası/sınırı (TTL + boyut) | ⚠️ UNKNOWN (media-master §12/3) | politika kararı yazılana kadar H-4'te otomatik temizlik yapılmaz — bu dosyada **kapatılmaz** |
| 2 | Eşzamanlı indirme üst sınırı (kaç görev `active`?) | ⚠️ UNKNOWN — sayı yok | kuyruk kapasite kararı gelene kadar uydurma eşik yazılmaz |
| 3 | Yeniden deneme sayısı / bekleme üst sınırı | ⚠️ UNKNOWN | deneme politikası kararı (H-5/H-6) |
| 4 | Resume meta verisinin saklama yeri (bellek/DB) | ⚠️ VERIFICATION REQUIRED (K007 sınırı) | kalıcılık kararı (§2/4) |
| 5 | Kademe-3 bounded-context kapsamı | ⚠️ VERIFICATION REQUIRED (index FM) | ADR-097 K4 faz (C) |

**Kapanış kuralı:** bu dosya açık soruları **taşıır, kapatmaz**; eşik/boyut/süre değerleri kanıt gelmeden yazılmaz (zero-hallucination).

---

## §7 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-16 | İlk yazım — §1-§6 (kapsam içi/dışı, 6 durumlu makine, §14.3 zinciri birebir, hata durumları, açık sorular) | Vault Steward (subagent) |

## § BAĞLANTILAR (düz yol — wiki-link yok)

- `../media-master.md` (§7/2 · §9.7 · §12/3 · §14.3)
- `index.md` · `downloads-web-arastirma.md` · `downloads-ornekler.md`
- `../index.md`

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
