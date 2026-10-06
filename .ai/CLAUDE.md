---
title: "LLM Wiki — Anayasa"
type: constitution
created: 2026-10-06
updated: 2026-10-06
sources: []
tags: [wiki, anayasa, hafıza]
---

# CLAUDE.md — LLM Wiki Anayasası

## 1. Amaç

Bu wiki senin uzun süreli hafızandır.

**Her oturumun başında sırasıyla oku:**
1. `CLAUDE.md` (bu dosya)
2. `index.md`
3. `log.md` — yalnızca son 10 kayıt

## 2. Klasör Yapısı

| Klasör | Ne için | Kural |
|---|---|---|
| `raw/` | Ham kaynaklar (makale, transkript, not, PDF, web klibi) | **ASLA değiştirilmez**, sadece okunur |
| `sources/` | Her kaynağın özeti + tüm kaynakların ana tablosu (`özet.md`) | Ingest sırasında yazılır |
| `wiki/` | Kavram, kişi, araç, proje sayfaları | Ingest sırasında oluşur/güncellenir |
| `.decisions/` | ADR kararları (`accepted/`, `rejected/`, `draft/`) — **kayıtlı alt dizin** | Kendi `index.md` kataloğu vardır; wiki `index.md`'ye yalnız katalog satırı düşer |
| `.personas/` | Persona profilleri (6 grup) — **kayıtlı alt dizin** | Kendi `index.md` kataloğu vardır; wiki `index.md`'ye yalnız katalog satırı düşer |

Kök dosyalar: `CLAUDE.md` (anayasa) · `index.md` (katalog) · `log.md` (kayıt defteri).

**Kayıt kuralı (`.decisions/` ve `.personas/`):** Bu iki dizin wiki'nin parçasıdır; kök `index.md`'de
**"Kararlar (ADR)"** ve **"Personalar"** bölümleri altında katalog satırlarıyla temsil edilir.
Her iki dizin de `index.md`'de **her zaman** bulunur — yoksa kayıt tamamlanmamıştır.
Bireysel ADR ve persona dosyaları tek tek listelenmez; yetki kendi `index.md`'lerindedir
(`.decisions/index.md` · `.personas/index.md`).

## 3. Dosya Adı Kuralı

- Küçük harf, tire ile ayrılmış: `agent-memory.md`
- Türkçe karakter kullanılmaz (`ç, ğ, ı, ö, ş, ü` → dönüşümü yok, İngilizce slug yaz)
- Boşluk yasak

**İSTİSNA — `sources/özet.md`:** Bu tek dosyanın adı kullanıcı tarafından belirlenmiştir ve Türkçe
karakter içerir. Kural bu dosyaya **uygulanmaz**; yeniden adlandırılması yasaktır (`[[özet]]`
linkleri ve `sources/özet.md` referansları kırılır). Diğer tüm dosyalar kurala tabidir.

## 4. Sayfa Şablonu

Her `wiki/` sayfası ve `sources/*.md` şu şablonla başlar:

```markdown
---
title: Sayfa Başlığı
type: kavram | kisi | arac | proje | dokuman | kaynak
created: YYYY-MM-DD
updated: YYYY-MM-DD
sources: [kaynak-dosya-adi]
tags: [etiket1, etiket2]
---

İçerik...

## İlgili Sayfalar
- [[ilgili-sayfa]] — neden ilgili
```

- Frontmatter zorunlu alanları: `title`, `type`, `created`, `updated`, `sources`, `tags`
- Sayfanın **en altında** her zaman `## İlgili Sayfalar` bölümü bulunur
- `sources/` özet dosyaları aynı 6 zorunlu alanı taşır; ek alanlar: `raw_path`, `ingested`

## 5. Sayfalar Arası Bağlantı Kuralı

- Format: `[[sayfa-adı]]` (uzantısız, tireli slug)
- İlgili **her** sayfaya çapraz link verilir
- Bir sayfaya link veren en az 1 başka sayfa olmalı (yetim node yasak)
- Her sayfanın en az 1 çıkış linki olmalı (kör sayfa yasak)

## 6. Üç İşlem

### INGEST — yeni kaynak işle
`raw/` içindeki işlenmemiş kaynağı okuyup `sources/` özeti ve `wiki/` sayfalarını üretir/günceller. Prosedür: §7.

### QUERY — wikiden cevap ver
Soru gelir → `index.md` → ilgili `wiki/` sayfaları → `sources/` özeti → cevap. Cevap kaynak belirtir; kaynak yoksa `⚠ VERIFICATION REQUIRED` yazılır.

### LINT — sağlık kontrolü
16 maddelik denetim (yetim node, kırık link, eksik frontmatter, kaynaksız iddia, log formatı vb.). **LINT raporu yalnız bulgu üretir; düzeltme kullanıcı onayıyla yapılır.**

## 7. ZORUNLU KURAL — Her dosya işleminde

> Wikide dosya **oluşturduğunda, güncellediğinde veya sildiğinde**:
> 1. `index.md`'yi güncelle
> 2. `log.md`'ye kayıt ekle
>
> **Bu iki adım atlanamaz. Atlanan işlem tamamlanmamış sayılır.**

## 8. INGEST Prosedürü

1. `raw/` içindeki henüz `sources/` altında özeti olmayan dosyaları tespit et
2. Her dosyayı **baştan sona** oku
3. Kullanıcıya en önemli 3-5 çıkarımı kısaca söyle
4. `sources/` altında o kaynağın özet dosyasını oluştur (şablon §9)
5. Kaynaktan çıkan kavram, kişi, araç ve projeler için `wiki/` altında sayfa oluştur; sayfa **zaten varsa** yeni bilgiyi ekleyerek güncelle
6. Yeni bilgi eski bilgiyle çelişiyorsa sayfaya **`⚠ Çelişki`** notu ekle ve iki kaynağı da belirt
7. Sayfalar arasına `[[çapraz link]]` ekle
8. `index.md`'yi güncelle
9. `log.md`'ye tarihli `ingest` kaydı ekle (hangi kaynak, kaç sayfa oluştu, kaç sayfa güncellendi)

> Bir kaynak işlenince wiki'de **ortalama 5-15 sayfa etkilenmesi normaldir**, hedef budur.

## 9. sources/özet.md Kuralı

`raw/` altına yeni bir dosya eklendiğinde ve ingest yapıldığında `sources/özet.md` tablosuna **yeni satır eklenmeli** ve üstteki **kaynak sayısı ile son güncelleme tarihi** güncellenmelidir. **Bunu yapmadan ingest tamamlanmış sayılmaz.**

`sources/özet.md` sütunları: `Kaynak Adı | raw/ Dosya Yolu | Eklenme Tarihi | Tek Cümle Özet | Etkilenen Wiki Sayfaları | Özet Dosyası`

**İSTİSNA — kaynak olmayan dosyalar:** `raw/README.md` klasörün kendisini tanımlayan bir
kılavuzdur, ham kaynak değildir → tabloya satır almaz, ingest edilmez. Aynı şekilde `.ai/` kökünde
bulunan `CLAUDE.md` · `index.md` · `log.md` kaynak sayılmaz. Sayıma yalnızca **gerçek ham kaynak**
girer; istisna listesi bu paragraftır ve genişletilemez (yeni istisna = anayasa değişikliği).

## 10. TARİH KURALI

Tarihi **asla tahmin etme**. Her kayıttan önce terminalde gerçek tarihi al ve kullan:

```bash
date "+%Y-%m-%d %H:%M"
```

(Windows PowerShell karşılığı: `Get-Date -Format "yyyy-MM-dd HH:mm"`)

## 11. Zorunlu Bağlantılar

- [[index]]
- [[log]]
