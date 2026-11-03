---
title: "CoreMusic — RAG (Retrieval-Augmented Generation) & Akıllı Bilgi Erişim Kılavuzu (DOGE DOC)"
type: guide
category: documentation
version: 1.0.0
status: active
authority: "SSOT: .ai/RAG.md · companion: RAG.md · CONTEXT.md"
updated: 2026-11-01
---

# CoreMusic — RAG (Retrieval-Augmented Generation) Kullanıcı & Geliştirici Kılavuzu (DOGE DOC)

> **Amaç:** CoreMusic ekosisteminde token ve hız limitlerini (Toggl / Claude / LLM kotalarını) tüketmeden, projenin devasa dosya ve kural tabanından doğrudan nokta atışı bilgi çekilmesini (RAG) sağlamak.

---

## 1. RAG (Rock / Retrieval-Augmented Generation) Nedir?

**RAG**, büyük dil modellerinin (LLM) devasa bir projeyi körlemesine baştan sona okuyup hafızasını ve token limitlerini doldurması yerine, **yalnızca ihtiyaç duyulan bilgi parçalarını (chunk) bularak modele vermesi** prensibidir.

### Klasik Hata (Token İflası):
- AI her promptta tüm depoyu, tüm `.ai/` vault belgelerini ve CSS'leri okumaya kalkar.
- Sonuç: 1-2 mesaj sonra saatlik token limiti biter ("Toggl / Limit bitti"), AI yavaşlar ve eski kararları unutmaya (halüsinasyon) başlar.

### CoreMusic RAG Sistemi (Token Koruma):
- Proje **12 bağımsız modüle** ayrılmıştır.
- Her modülün başında tek sayfalık bir **`durum.md`** (~300-500 token) bulunur.
- AI, soruya göre **[`RAG.md`](file:///c:/www/coremusic.net/RAG.md)** yönlendiricisini kullanarak doğrudan hedef dosyayı çeker.
- Token tasarrufu: **%90'ın üzerinde kazanç**.

```text
[KULLANICI SORUSU]
        │
        ▼
[RAG ROUTER: RAG.md / .ai/RAG.md / keys.md]
        │
   ┌────┴───────────────────────────┐
   ▼                                ▼
[Hedef Modül: durum.md]    [Hedef Kaynak Dosya]
   │ (~400 token)                   │ (~1,500 token)
   └────────────────┬───────────────┘
                    ▼
           [LLM ÇALIŞMA ALANI]
        (Sadece ~2,000 Token ile
         Sıfır Hata & Yüksek Hız)
```

---

## 2. Kullanıcının RAG Yapma Adımları (Nasıl Bilgi Çekilir?)

Kullanıcı (Bayram Ali) veya orkestratör agent olarak projede bir geliştirme yaparken aşağıdaki adımları izleyin:

### Adım 1: Hangi Modülde Çalışacağınızı Belirleyin
CoreMusic 12 bağımsız çalışma yüzeyine sahiptir:
1. `home.coremusic.net` (Kullanıcı Arayüzü, Müzik Sayfaları, Web Oynatıcı)
2. `api.coremusic.net` (REST API, Controller, Route, OpenAPI)
3. `auth.coremusic.net` (Giriş, Kayıt, Şifre Sıfırlama, JWT, Güvenlik)
4. `admin.coremusic.net` (Yönetim Paneli, RBAC, Kullanıcı ve Sistem Yönetimi)
5. `assets.coremusic.net` (CSS ITCSS Katmanları, JS Modülleri, SVG İkonlar)
6. `media.coremusic.net` (Medya Ayrıştırma, Ulid, Stream İzinleri, Dosya Yolları)
7. `shared` (Ortak PHP Çekirdeği, PDO Veritabanı, Middleware Pipeline)
8. `app-cpp-projects` (Neva Engine, C++20, JUCE 9, ASIO, Class AB Amfi DSP)
9. `app-nodejs-projects` (Node.js yardımcı servisler, derleme araçları)
10. `bin` (Terminal CLI scriptleri, anahtar üretim araçları)
11. `.agents` (Özel agent tanımları ve iş akışı profilleri)
12. `.workflows` (Proje otomasyon ve görev süreçleri)

### Adım 2: Modülün `durum.md` Dosyasını Çağırın
Yeni bir sohbet açtığınızda AI'a tüm depoyu taratmayın; doğrudan şu komutu verin:

> *"Ekteki `[modul-adi]/durum.md` dosyasını oku. Bana tek cümleyle nerede kaldığımızı söyle ve 'Sırada' yazan adımdan devam et."*

### Adım 3: Nokta Atışı RAG Eşlemesi Yapın
Bir konuyla ilgili kural veya kod arıyorsanız aşağıdaki rota sözlüğünden yararlanın.

---

## 3. RAG Rota Sözlüğü (Hangi Konu → Hangi Dosya?)

| Yapmak İstediğiniz İş / Soru | Doğrudan Çekilecek Dosya (RAG Hedefi) |
|---|---|
| **Oturum nerede kaldı?** | [`.ai/durum.md`](file:///c:/www/coremusic.net/.ai/durum.md) veya ilgili `[subdomain]/durum.md` |
| **Kalıcı kurallar ve mühendislik ilkeleri** | [`.ai/.rules/kurallar.md`](file:///c:/www/coremusic.net/.ai/.rules/kurallar.md) (`KURAL-01..10`) |
| **Kod yazmadan önce hata listesi taraması** | [`.ai/.rules/hata-listesi.md`](file:///c:/www/coremusic.net/.ai/.rules/hata-listesi.md) (`HL-01..12`) |
| **Onaylanmış mimari kararlar (ADR)** | [`.ai/.rules/karar-kurallari.md`](file:///c:/www/coremusic.net/.ai/.rules/karar-kurallari.md) · [`.ai/brain.md`](file:///c:/www/coremusic.net/.ai/brain.md) |
| **Figma tokeni ve tasarım anahtarları** | [`.ai/.env.figma`](file:///c:/www/coremusic.net/.ai/.env.figma) (`FIGMA_TOKEN`) |
| **Tema ve CSS stilleri** | `assets.coremusic.net/Css/` (**YALNIZCA LIGHT MODE**) |
| **Veritabanı tabloları ve şema** | `.ai/.sql/mysql/*.sql` (18 BCNF şeması) |
| **Ses motoru ve donanım mimarisi** | `app-cpp-projects/` + `ADR-038` (**CLASS AB**) |
| **API Rotaları ve Versiyonlama** | `api.coremusic.net/config/routes.php` (ADR-113: v1+v2 tek route) |
| **Depo ve dosya haritası** | [`CONTEXT.md`](file:///c:/www/coremusic.net/CONTEXT.md) |
| **Karpathy ilkeleri & Cursor IDE** | [`CURSOR.md`](file:///c:/www/coremusic.net/CURSOR.md) · [`.claude/skills/karpathy-guidelines/`](file:///c:/www/coremusic.net/.claude/skills/karpathy-guidelines/) |

---

## 4. Kullanıcı İçin Hazır RAG Prompt Şablonları

### Kalıp A: Yeni Oturum Başlatırken (Sıfır Token Kaybı)
```text
home.coremusic.net/durum.md ve .ai/.rules/kurallar.md dosyalarını oku.
Nerede kaldığımızı tek cümleyle özetle.
Başka hiçbir dosya taramadan 'Sırada' yazan maddeden çalışmaya başla.
```

### Kalıp B: Yeni Sayfa / Bileşen Geliştirirken (UI RAG)
```text
Geliştireceğimiz sayfa: [sayfa_adi.php]
1. assets.coremusic.net/Css/01_Abstracts/ içindeki tokenleri referans al.
2. .ai/.rules/hata-listesi.md HL-01 (YALNIZCA LIGHT MODE) kuralına uy.
3. İlgili Figma mockup referansını oku ve kodu cerrahi olarak üret.
```

### Kalıp C: Backend / API İşi Yaparken (API RAG)
```text
shared/durum.md ve .ai/.rules/karar-kurallari.md dosyalarını oku.
ADR-112 (şema migration) ve ADR-114 (I* interface + final class repository)
kurallarına göre [X endpoint'ini] hazırla.
```

### Kalıp D: Oturum Kapatırken (Handoff Kaydı)
```text
Bu oturumda yaptıklarımızı [ilgili_modul]/durum.md ve .ai/durum.md dosyasına işle.
Kaldığımız yeri, bitenleri ve sıradaki adımı tek tek yaz.
.ai/log.md dosyasına bir satır kayıt düş.
```

### Kalıp E: Karpathy Standartlarında Kodlama & Refactoring (Cerrahi RAG)
```text
.claude/skills/karpathy-guidelines/SKILL.md dosyasını referans al.
Andrej Karpathy'nin 4 ilkesine (Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution) uy.
Varsayımlarını peşinen açıkla, aşırı soyutlamadan kaçın, yalnızca istenen satırlara cerrahi diff uygula.
```

---

## 5. Değiştirilemez 4 Kırmızı Çizgi

1. **YALNIZCA LIGHT MODE:** Projede Dark Mode henüz tasarlanmamıştır/yoktur. Dark mode varsayımı veya CSS dark class'ı yazmak kesinlikle yasaktır (`HL-01`).
2. **CLASS AB AMPLİFİKATÖR:** Donanım amfi mimarisi kesinlikle Class AB'dir (`HL-02`).
3. **SUBAGENT COMMIT YASAĞI:** Subagent'lar `git commit` atamaz; commit yetkisi yalnızca kullanıcıya / orkestratöre aittir (`HL-03`).
4. **TEK SAYFA BAĞLAMI:** Her sohbette tüm projeyi okutmak yasaktır; sadece ilgili `durum.md` ve hedef dosya okunur (`HL-11`).

---

*CoreMusic RAG & Akıllı Bilgi Erişim Kılavuzu (DOGE DOC) v1.0.0 — Hazırlayan: AI Mühendislik Orkestratörü — 2026-11-01*
