# 🌐 skill-maker — Agentic Anti-Patterns (Kaçınılması Gerekenler)

## 1. Halüsinasyon (Tahmin Yürütme) - EN BÜYÜK HATA
**Hata:** AI, bir framework'ün sürümünü veya konfigürasyonunu webden aramadan kendi belleğinden yazar.
**Sonuç:** Eski veya yanlış bilgi (Örn: PCM5122 DAC 8.1 destekliyor sanması - H001 Kritik Reddi).
**Doğrusu:** Her yeni teknoloji bilgisi önce webden aranır ve 2-3 kaynaktan doğrulanır (Truth Mode).

## 2. Kural İhlali Yapan SKILL.md Frontmatter
**Hata:** YAML formatına `version`, `dependencies` gibi izin verilmeyen kök parametreler eklemek.
**Sonuç:** Skill Kiro IDE'de yüklenmez.
**Doğrusu:** Tüm bu bilgiler `metadata:` altına eklenmelidir.

## 3. Güvensiz Dosya İşlemleri (Hardcoding Secrets)
**Hata:** Yeni skill'in içine statik API Key veya veritabanı şifresi gömmek.
**Doğrusu:** Otonom sistemler credential'ları `.env` veya güvenli vault üzerinden alacak şekilde yönlendirilir.

## 4. İsimlendirme İhlali
**Hata:** `name: PHP Güvenlik Analizi` (Büyük harf ve boşluk içeriyor).
**Doğrusu:** `name: php-guvenlik-analizi` (Sadece lowercase ve tire). Klasör adı ile aynı olmak zorundadır.

## 5. Aşırı Uzun Dosya (2000 Satır Kısıtı İhlali)
**Hata:** `SKILL.md` dosyasını binlerce satır uzunluğunda, okunmaz bir spagettiye çevirmek.
**Doğrusu:** 2000 satır kısıtına uymak, karmaşıklığı `references/` veya `scripts/` klasörüne bölmek.
