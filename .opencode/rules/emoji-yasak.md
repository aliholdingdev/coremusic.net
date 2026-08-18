# Emoji Kullanım Yasağı

## Kesin Kural: Emoji KULLANMA

Frontend kodlamada emoji kullanımı **kesinlikle yasaktır**.

## Neden Emoji Kullanılmaz?

1. **Profesyonel görünüm**: Emoji profesyonel görünmez
2. **Tutarlılık**: Emoji farklı cihazlarda farklı görünür
3. **Erişilebilirlik**: Emoji screen reader'da düzgün çalışmaz
4. **Performans**: Emoji için ek font yüklenir
5. **Vault uyumsuzluğu**: Vault'ta tanımlanan PNG'ler var

## Ne Kullanılır?

Emoji yerine:
- ✅ PNG dosyaları (`.ai/.png/` klasöründen)
- ✅ SVG icon'lar
- ✅ Font icon'lar (Font Awesome, Material Icons)
- ✅ CSS ile oluşturulmuş icon'lar

## Emoji Örnekleri (YASAK)

```html
<!-- YASAK -->
<span>🎵</span>
<span>🎶</span>
<span>🎸</span>
<span>🎤</span>
<span>🎧</span>
<span>📻</span>
<span>📱</span>
<span>💻</span>
```

## Doğru Kullanım

```html
<!-- DOĞRU -->
<img src=".ai/.png/home-1024/Linux 1024 - Home Page.png" alt="Ana Sayfa">
<svg class="icon icon-home">...</svg>
<i class="fas fa-home"></i>
```

## Hata Durumu

- Emoji kullandıysan → SİL, PNG kullan
- PNG yoksa → DUR, kullanıcıya sor
- Kullanıcı emoji istiyorsa → Onay al, sonra kullan

## Kontrol Listesi

```
[ ] Emoji var mı? → SİL
[ ] PNG kullanıldı mı?
[ ] Vault okundu mu?
[ ] Onay alındı mı?
```
