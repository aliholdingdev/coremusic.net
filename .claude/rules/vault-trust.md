# Vault Trust Protocol

## Temel Kural: Vault'a HER ZAMAN Güven

Vault (`.ai/` dizini) projenin **tek doğru kaynağıdır** (SSOT - Single Source of Truth).

## Güven Kuralları

### Kural 1: Vault Haklıdır
- Vault'taki bilgi her zaman doğrudur
- Vault kurallarını sorgulama
- Vault kararlarını değiştirme
- Vault'un dediğini yap

### Kural 2: Vault'taki Ölçüleri Uygula
- PNG mockup'lardaki ölçüleri aynen kullan
- ASCII art tanımındaki piksel değerlerini uygula
- Boşluk hiyerarşisini (4px, 8px, 16px, 24px, 32px, 48px, 64px) takip et
- Header, footer, sidebar yüksekliklerini aynen uygula

### Kural 3: ADR Kararlarına Uy
- Kabul edilmiş ADR'leri uygula
- Reddedilmiş ADR'leri kullanma
- Yeni karar için ADR oluştur

### Kural 4: Vault'taki Kod Kalıplarını Kullan
- Vault'ta tanımlanan mimari kalıpları takip et
- Vault'taki style guide'ı uygula
- Vault'taki testing pattern'larını kullan

### Kural 5: Emoji Kullanma
- Emoji ile icon/illustrasyon oluşturma
- Sadece vault'taki PNG dosyalarını kullan
- PNG yoksa DUR ve bildir

## Vault Dışında Kalma

- Vault'un dediğini yapma
- Kendi bildiğini okuma
- Tahmin etme
- Uydurma
- Hallucinate etme

## Hata Durumu

- Vault bulunamazsa → DUR, kullanıcıya bildir
- Vault okunamazsa → DUR, kullanıcıya bildir
- Vault çelişkiliyse → DUR, kullanıcıya bildir
- Vault eksikse → DUR, kullanıcıya bildir

## Soru Sorma Protokolü

Kod yazmadan ÖNCE:
1. Vault'u oku
2. Anlamadığın bir şey varsa sor
3. Eksik bilgi varsa sor
4. Onay al
5. Sonra kodu yaz
