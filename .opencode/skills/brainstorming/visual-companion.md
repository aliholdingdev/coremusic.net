# Visual Companion Guide

## Amaç

Brainstorming sırasında görsel soruları tarayıcıda göstermek için rehber.

## Ne Zaman Kullanılır

- Mockup/wireframe karşılaştırması
- Layout seçimi
- Mimari diyagram
- Renk/palet kararı

## Nasıl Kullanılır

1. Kullanıcıya "Bunu göstererek mi açıklayayım?" diye sor
2. Onay al
3. Tarayıcıda basit bir HTML sayfası oluştur
4. Seçenekleri görsel olarak sun

## Format

```html
<!DOCTYPE html>
<html>
<head><title>Visual Companion</title></head>
<body>
  <h1>Seçenek Karşılaştırması</h1>
  <div style="display: flex; gap: 20px;">
    <div style="border: 1px solid #ccc; padding: 10px;">
      <h3>Seçenek A</h3>
      <p>...</p>
    </div>
    <div style="border: 1px solid #ccc; padding: 10px;">
      <h3>Seçenek B</h3>
      <p>...</p>
    </div>
  </div>
</body>
</html>
```

---

*Visual Companion v1.0.0 — CoreMusic Brainstorming Helper*
*Last Updated: 2026-08-08*
