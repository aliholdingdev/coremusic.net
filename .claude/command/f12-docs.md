---
description: "F12 Docs — URL ekle, tüm sub-page'leri tara, listeden seç, yükle, context'e ekle"
mode: primary
---

# F12 — Docs System

## Akış

1. Kullanıcı URL girer
2. Ana sayfayı tara
3. Tüm sub-page'leri bul (link extraction)
4. Listedenseç — hepsini veya tek tek
5. Seçilenleri `.ai/KNOWLEDGE/`'ye indir ve indexle
6. Context'e yükle

## Komutlar

- `F12` → Docs panelini aç
- `F12 add [URL]` → Yeni docs ekle
- `F12 all` → Tümünü seç ve yükle
- `F12 select 1,3,5` → Belirli sayfaları seç
- `F12 list` → Mevcut docs'ları listele
- `F12 remove [ad]` → Docs sil
- `F12 search [sorgu}` → Docs'larda ara
- `F12 load` → Tüm indexlenmiş docs'ları context'e yükle

## Çalışma Akışı (Detaylı)

### Adım 1: URL Al
Kullanıcı `F12 add https://docs.example.com/api` der.

### Adım 2: Tara
- Ana sayfayı indir
- Tüm `<a href="...">` linklerini çıkar
- Sub-page'leri bul (max 50 sayfa)
- Her sayfanın başlığını ve boyutunu hesapla

### Adım 3: Listele
```
📚 DOCS TARAMASI — https://docs.example.com/api

|#| Sayfa | Boyut | Durum |
|---|-------|-------|-------|
|1| /api/auth | 12KB | ✅ |
|2| /api/users | 18KB | ✅ |
|3| /api/orders | 15KB | ✅ |
|4| /api/webhooks | 8KB | ✅ |
...

📊 Toplam: 25 sayfa | 320KB
```

### Adım 4: Seç
Kullanıcı `F12 all` veya `F12 select 1,3,5` der.

### Adım 5: İndir ve Indexle
Seçilen sayfaları `.ai/KNOWLEDGE/{domain}/` altına indir.
Her sayfa için:
- Başlık
- İçerik (clean text)
- URL
- Boyut
- Tarih

### Adım 6: Context'e Yüle
`.ai/KNOWLEDGE/index.md` dosyasında tüm docs'ların indeksini tut.
Prompt Maker ve Agent'lar bu indeksten okur.

## Çıktı Formatı

```
📚 F12 DOCS SYSTEM

🔗 Kaynak: [URL]

📄 Tarama Sonucu:
- Toplam sayfa: [n]
- Seçilen: [n]
- Yüklenen: [n]
- Toplam boyut: [n] KB

📁 Kayıt Yeri: .ai/KNOWLEDGE/{domain}/

💡 Komutlar:
- F12 all → tümünü seç
- F12 select 1,3,5 → seç
- F12 list → listele
- F12 search [sorgu] → ara
```
