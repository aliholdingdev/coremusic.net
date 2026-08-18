# Frontend PNG & ASCII Art Kuralları

## Kural 1: UI Design Dosyalarını Oku (ZORUNLU)

Her frontend görevinde ÖNCE `.ai/ui-design/` dizinindeki md dosyalarını oku:

### Okunacak Dosyalar:
1. `00-mockup-index.md` → 18 PNG mockup indeksi (İLK OKUNACAK)
2. `01-component-inventory.md` → Bileşen envanteri
3. `02-implementation-plan.md` → Uygulama planı
4. `03-accessibility-gaps.md` → Erişilebilirlik açıkları
5. `04-vault-registration.md` → Vault kayıt
6. `screens/` → Ekran tanımları (ASCII art + ölçümler)
7. `tokens/` → Design token'lar
8. `reference/` → Referans dosyaları

## Kural 2: ASCII Art'ları Takip Et (ZORUNLU)

Her ekran için:
1. `screens/` dizinindeki md dosyasını oku
2. ASCII art tanımını oku
3. Piksel düzeyindeki ölçüleri not et
4. CSS'de bu ölçüleri aynen uygula

### ASCII Art Okuma Sırası:
```
1. Ekran adını bul (örn: "Home Page", "Login", "Register")
2. İlgili screens/ dosyasını oku
3. ASCII art section'ını bul
4. Piksel ölçümlerini çıkar
5. CSS'e uygula
```

## Kural 3: PNG Dosyalarını Oku ve Karşılaştır (ZORUNLU)

Her frontend görevinde `.ai/.png/` dizinindeki PNG dosyalarını oku ve md dosyalarıyla karşılaştır:

### PNG Okuma Sırası:
```
1. 00-mockup-index.md'den PNG dosya adını bul
2. .ai/.png/home-1024/ veya .ai/.png/shared-1024/ dizininden PNG'yi bul
3. PNG'yi oku (görsel analiz)
4. ASCII art ile karşılaştır
5. Ölçümleri doğrula
6. Fark varsa PNG'yi esas al
```

### PNG-ASCII Cross-Reference:
| md Dosyası | PNG Dosyası | Durum |
|------------|-------------|-------|
| `screens/B-home/dashboard.md` | `home-1024/Linux 1024 - Home Page.png` | Karşılaştır |
| `screens/A-auth/login.md` | `shared-1024/Linux 1024 - Login Girl.png` | Karşılaştır |
| `screens/A-auth/gender-select.md` | `shared-1024/Linux 1024 - Select Gender.png` | Karşılaştır |

## Kural 4: Emoji Kullanımı Yasak

- ❌ Emoji ile icon oluşturma
- ❌ Emoji ile illustration yapma
- ✅ Sadece `.ai/.png/` dosyalarını kullan
- ✅ PNG yoksa → DUR, kullanıcıya sor

## Kural 5: Mockup Ölçüleri (PNG Esas)

PNG'den okunan ölçüler (md'deki ASCII art ile karşılaştır):
- Header yüksekliği: 60px
- Footer yüksekliği: 90px
- Sidebar genişliği: 167px (sadece Göz At)
- İçerik bölge sınırları
- Boşluk hiyerarşisi (4px, 8px, 16px, 24px, 32px, 48px, 64px)

### Uyuşmazlık Durumu:
- PNG ≠ ASCII art → PNG'yi esas al
- PNG ≠ md ölçüsü → PNG'yi esas al
- ASCII art eksik → PNG'den tamamla

## Kural 6: Tema Uyumu

Mevcut PNG'ler "female" (pembe) temasıyla tasarlanmış:
- Accent: #ff4fd8
- Tema motoru: data-gender attribute'u
- Erkek/nötr temalar için PNG henüz yok

## Kural 7: Responsive Breakpoint'ler

- Mobile-First: 320px+
- Tablet: 768px+
- Desktop: 1024px+
- Wide: 1440px+
- Ultra-wide: 2560px+

## Kural 8: CSS Mimarisi

- ITCSS 9-layer
- BEM naming
- --cm-* token sistemi
- Mobile-First media queries
- CSS Grid/Flexbox

## Kural 9: JavaScript Kuralları

- Vanilla ES6+ (framework YASAK)
- var kullanma
- Fetch API: AbortController zorunlu
- innerHTML YASAK, textContent veya DOMParser

## Kural 10: Token Okuma

`tokens/` dizinindeki dosyaları oku:
- `design-tokens-master.md` → Tüm token'lar
- `color-palettes.md` → Renk paletleri
- `platform-tokens.md` → Platform-specific token'lar

Bu token'ları CSS'de `--cm-*` olarak kullan.
