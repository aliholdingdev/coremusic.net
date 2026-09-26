---
title: "CoreMusic - Test Senaryoları - Persona Eşleme Matrisi (68 Persona × 6 Senaryo × 3 Cihaz)"
type: reference
category: testing
version: 1.0.0
status: active
authority: reference
updated: 2026-09-26
author: docs-md
source: ADR-023 §5.4 (şart 1c) + eski vault (read-only)
---

# Test Senaryoları — Persona Eşleme Matrisi

> **Kapsam:** 68 persona × 6 senaryo × 3 cihaz = **1.224 test case** için grup/mood bazlı eşleme tablosu.
> Persona ayrıntıları bu dosyada **bulunmaz** → [[personas/index]] §6 kataloguna bakın.
> Mood tanımları → [[personas/mood-taxonomy]].
> Kanıt/araştırma → [[personas/research-bank]] (🔄 BAŞKA AJAN YAZIYOR).

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[.templates/index]]

---

## 1. Genel Bakış

### 1.1 Amaç

Bu dosya, ADR-023 şart 1c kapsamında `.ai/.personas/` kök belgelerinden biridir:
QA agent'ların hangi persona grubu için hangi senaryoyu hangi mood ile çalıştıracağını
**tek tabloda** görmesini sağlar. Persona dosyaları (68 adet) ayrı ayrı yazılmaktadır;
bu dosya onların **eşleme kataloğu**dur, tekrarı değildir.

### 1.2 Kapsam ve Kapsam Dışı

| ✅ Kapsam içi | ❌ Kapsam dışı |
|---|---|
| 6 senaryo × 6 grup eşleme tabloları | Persona yaşam öyküleri (→ persona dosyaları) |
| Cihaz adaptasyon matrisi (mobile/tablet/desktop) | Mood kuralları (→ [[personas/mood-taxonomy]]) |
| WCAG 2.2 AA kontrol listeleri | Ham araştırma verisi (→ [[personas/research-bank]]) |
| Test case sayım matematiği | Senaryo kod kontrol listeleri (→ `test-senaryolari/*.md`) |
| Grup bazlı kısıt/kalıcı davranışlar | Yeni persona ekleme (→ ADR-088+) |

### 1.3 Sayım Özeti

| Grup | Persona sayısı | Yaş aralığı | Senaryo kapsamı |
|---|---|---|---|
| Kız çocuk | 17 | 4–11 ⚠️ | Auth, Discovery, Playback (+Settings kısıtlı) |
| Erkek çocuk | 12 | 4–11 ⚠️ | Auth, Discovery, Playback (+Settings kısıtlı) |
| Genç kız | 17 | 12–17 | 6 senaryo tam |
| Genç erkek | 12 | 12–17 | 6 senaryo tam |
| Yetişkin kadın | 5 | 25–45 | 6 senaryo (sosyal kısıtlı) |
| Yetişkin erkek | 5 | 25–45 | 6 senaryo (sosyal kısıtlı) |
| **Toplam** | **68** | 4–45 | — |

⚠️ **VERIFICATION REQUIRED:** Yaş aralığı 4–11 (eski index) vs 6–11 (eski mapping başlığı)
çelişkisi var; kanıt → [[personas/research-bank]].

---

## 2. Şablon ve Bağlantı Kuralları

### 2.1 Docs-MD Şablonu (§3.3 zorunlu blok)

> Bu belge `.ai/.templates/documentation/docs-md-template.md` iskeletiyle
> (§1–§8) üretilmiştir; persona alan kuralları `.ai/.templates/personas/persona-template.md`'den
> devralınmıştır (§3.5.4 küme adı, §4.5 kaynak etiketleme, ≥500 satır).

### 2.2 Wiki-Link Formatı

| Bağlantı türü | Örnek | Kural |
|---|---|---|
| Kök belgeler | `[[CLAUDE.md]]` | Zorunlu Bağlantılar satırında |
| Kardeş dosyalar | `[[personas/index]]`, `[[mood-taxonomy]]` | `.ai/.personas/` içi göreli |
| Persona dosyaları | `[[personas/kiz-cocuk/ada-celik]]` | `.ai/.personas/<grup>/<slug>` |
| Senaryo dosyaları | `[[test-senaryolari/muzik-kesfi]]` | `.ai/.personas/test-senaryolari/` (✅ 6 dosya mevcut → §6.1) |

⚠️ **VERIFICATION REQUIRED:** Eski vault'ta `personas/` öneki, yeni vault `.ai/.personas/`
için ne olacağı kesinleşmedi (bkz. §8 belirsizlik-1).

---

## 3. Bağlam, Roller, Kullanım

### 3.1 Kullanıcılar

| Rol | Bu dosyayı nasıl kullanır |
|---|---|
| QA agent | §4–§5 tablolarından grup+senaryo satırını okur → kontrol listesini uygular |
| Developer | §6 cihaz matrisini viewport hedefi olarak kullanır |
| Arch Lead | §7 sayım matematiğini ADR-023 §5.4 kabul kriteriyle karşılaştırır |
| Vault-docs | §8 kaynak/versiyon hattını günceller |

### 3.2 ADR-023 İlişkisi (§5.4 şart 1c)

ADR-023 şart 1c şunu ister: persona envanteri `.ai/.personas/` altına taşınır,
kök belgeler `index.md`, `mood-taxonomy.md`, `test-scenarios-mapping.md` + `research-bank.md`
olarak yazılır. Bu dosya üçüncü sıradadır; `research-bank.md` **başka ajan** tarafından
yazılıyor (🔄) ve bu oturumda dokunulmaz.

### 3.3 Beslenen Kaynaklar (girdi)

1. Eski vault `personas/test-scenarios-mapping.md` (read-only içerik kaynağı) — 711 satır
2. ADR-023 §2.2a 20'lik persona matrisi + §5.4 kabul kriterleri
3. `.ai/.templates/documentation/docs-md-template.md` §1–§8 iskeleti
4. Eski `test-senaryolari/` 6 senaryo dosyası (read-only; karşılıkları §6.1'de → `.ai/.personas/test-senaryolari/`)

### 3.4 Harici/Üretici

- **Yazar:** docs-md (bu oturum, subagent)
- **Onay:** Arch Lead ⏳ (ADR-023 hâlâ active)
- **Üretim verisi:** yok — tüm sayımlar vault içi dosyalardan geldi

---

## 4. Karar: Eşleme Matrisi

### 4.1 Ana Karar

**Karar:** Her senaryo için "Ortak + Grup-bazlı" iki katmanlı kontrol listesi kullanılır;
persona bazlı sapmalar (mood/yaş) ek satır olarak işaretlenir, 68 ayrı liste tekrarlanmaz.

**Gerekçe:** 68 × 6 = 408 tam liste imkânsızdır; ortak katman ~%70 kapsam verir,
grup katmanı kalan %30'u (COPPA, yaş kilidi, yetişkin gizlilik) kapsar.

**Seçenekler:**
- A) Persona başına tam liste (408) — reddedildi: tekrar + bakımı imkânsız
- B) Ortak + grup katmanı (6 + 6) — **seçildi**
- C) Sadece grup katmanı — reddedildi: ortak davranışlar (auth akışı vb.) test edilmez

### 4.2 Senaryo Tanımları (6)

| # | Senaryo | Eski karşılığı | Uygulanabilir persona | Kısıt |
|---|---|---|---|---|
| S1 | Authentication | senaryo-01 | 68/68 | çocukta veli PIN'i |
| S2 | Music Discovery | senaryo-02 | 68/68 | çocukta içerik filtresi |
| S3 | Playback | senaryo-03 | 68/68 | çocukta ses limiti |
| S4 | Download | senaryo-04 | 41/68 | 29 çocuk ❌; 12–17 yaş kilidi ⚠️ |
| S5 | Settings | senaryo-05 | 55/68 | çocukta veli hesabına bağlı |
| S6 | Social | senaryo-06 | 46/68 | 29 çocuk ❌; yetişkin ⚠️ kısıtlı |

### 4.3 Grup × Senaryo Uygulanabilirlik Matrisi

| Grup (n) | S1 | S2 | S3 | S4 | S5 | S6 |
|---|---|---|---|---|---|---|
| Kız çocuk (17) | ✅ | ✅ | ✅ | ❌ | ⚠️ veli | ❌ |
| Erkek çocuk (12) | ✅ | ✅ | ✅ | ❌ | ⚠️ veli | ❌ |
| Genç kız (17) | ✅ | ✅ | ✅ | ✅ yaş ⚠️ | ✅ | ✅ |
| Genç erkek (12) | ✅ | ✅ | ✅ | ✅ yaş ⚠️ | ✅ | ✅ |
| Yetişkin kadın (5) | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ kısıtlı |
| Yetişkin erkek (5) | ✅ | ✅ | ✅ | ✅ | ✅ | ⚠️ kısıtlı |

### 4.4 Senaryo × Grup Beklenen Davranış / Doğrulama

#### S1 — Authentication

| Persona grubu | Mood örnekleri | Beklenen davranış | Doğrulama yöntemi | WCAG etkisi |
|---|---|---|---|---|
| Çocuk (29) | Lider, Kaşif, Enerjik, Sporcu, Utangaç | Basit akış, büyük buton (48px+), veli PIN popup'ı | Görsel + veli e-posta doğrulama | 2.5.8 hedef boyu ✅ |
| Genç (29) | Romantik, Melankolik, Sosyal, Hip-Hop, Gamer | E-posta doğrulama (24s), yaş kilidi 16 ⚠️ | Form validasyonu + e-posta akışı | 3.3.1 hata tanıma |
| Yetişkin (10) | Anne/Baba, Profesyonel, Romantik | 2FA opsiyonel, password manager autocomplete | Otomatik dolma + 2FA akışı | 1.3.5 giriş amacı |

#### S2 — Music Discovery

| Persona grubu | Mood örnekleri | Beklenen davranış | Doğrulama yöntemi | WCAG etkisi |
|---|---|---|---|---|
| Çocuk (29) | Kaşif, Meraklı, Yaratıcı | Explicit kapalı, yalnız onaylı türler, büyük kapak (200px+) | Filtre çıktısı denetimi | 1.4.1 renk tek başına |
| Genç (29) | Enerjik, Moody, Danssever, Trend | Trend listeleri, mood-kişiselleştirilmiş öneri | Öneri yenileme + sonuç sayısı | 4.1.2 rol/ad |
| Yetişkin (10) | Profesyonel, Melankolik | Gelişmiş filtre (yıl, süre, BPM ⚠️), fuzzy arama | Typo ile arama + filtre birikimi | 2.4.7 focus görünürlüğü |

#### S3 — Playback

| Persona grubu | Mood örnekleri | Beklenen davranış | Doğrulama yöntemi | WCAG etkisi |
|---|---|---|---|---|
| Çocuk (29) | Enerjik, Sporcu, Dans Enerjik | Ses limiti 80dB ⚠️, büyük play (64px+), veli PIN override | Ses + tuş boyutu ölçümü | 2.5.5 hedef boyutu |
| Genç (29) | Hip-Hop, Rock, Gamer, Melankolik | 31-band EQ, hız 0.5–2.0x, kuyruk | Preset kaydı + klavye kısayolları | 2.1.1 klavye |
| Yetişkin (10) | Anne/Baba, Profesyonel | Gapless, FLAC ⚠️, çıktı cihazı seçimi | Cihaz değiştirme + geçiş süresi | 1.4.12 metin aralığı |

#### S4 — Download

| Persona grubu | Mood örnekleri | Beklenen davranış | Doğrulama yöntemi | WCAG etkisi |
|---|---|---|---|---|
| Çocuk (29) | — | ❌ Uygulanamaz (erişim reddi görünür) | Erişim reddi UI'ı | 1.4.3 kontrast |
| Genç (29) | Enerjik, Sporcu, Sosyal | Kuyruk, ilerleme %, çevrimdışı oynatma, yaş kilidi 16 ⚠️ | Ağ kesme testi + kuyruk durumu | 4.1.3 durum mesajları |
| Yetişkin (10) | Profesyonel, Melankolik | Toplu indirme, kalite seçimi, cihazlar arası senkron | Toplu seçim + senkron turu | 2.2.1 zaman sınırı |

#### S5 — Settings

| Persona grubu | Mood örnekleri | Beklenen davranış | Doğrulama yöntemi | WCAG etkisi |
|---|---|---|---|---|
| Çocuk (29) | Utangaç, Lider, Kaşif | Yalnız avatar/tema; PIN değiştirme yok | Kısıtlı alan denetimi | 1.4.11 UI kontrastı |
| Genç (29) | Moody, Romantik, Danssever | Gizlilik (profil görünürlüğü), dinleme durumu, yaş gizleme | 3 görünürlük modu geçişi | 3.2.3 tutarlı navigasyon |
| Yetişkin (10) | Anne, Baba, Profesyonel | GDPR dışa aktarma ⚠️, bildirim frekansı, tüm cihazlardan çıkış | CSV/JSON çıktı + oturum kırma | 3.2.2 giriş onayı |

#### S6 — Social

| Persona grubu | Mood örnekleri | Beklenen davranış | Doğrulama yöntemi | WCAG etkisi |
|---|---|---|---|---|
| Çocuk (29) | — | ❌ Uygulanamaz | Erişim reddi UI'ı | 1.4.3 kontrast |
| Genç (29) | Sosyal, Enerjik, Hip-Hop, Gamer | Paylaşım, takip, yorum, mesaj, akış | Akış + moderasyon akışı | 1.3.1 bilgi ilişkisi |
| Yetişkin (10) | Anne/Baba, Romantik | ⚠️ Kısıtlı: paylaş/takip/yorum var; mesaj ❌, akış ❌ | Kısıt reddi görünür | 2.1.1 klavye |

### 4.5 Mood Bazlı Öncelik Satırları (seçki)

| Küme (mood) | Etkilediği senaryo | Öncelikli kontrol |
|---|---|---|
| Arabesk / Dans türevleri (7) | S2, S3 | Tür filtresi + EQ preset eşleşmesi |
| Melankolik (rol) | S2, S5 | Gece modu / düşük kontrast tercihi |
| Enerjik (temel) | S3, S6 | Hız + görsel geri bildirim animasyonu |
| Lider (çocuk rol) | S1, S5 | Sosyal prova: yetki devri görünür mü |
| Profesyonel (yetişkin) | S2, S4, S5 | Gelişmiş filtre + toplu indirme + dışa aktarma |

Tam küme listesi (25 küme) → [[personas/mood-taxonomy]] §2; ADR-023 §2.2a uyumu → aynı dosya §3.6.

### 4.6 Ortak Katman Kontrol Listeleri (grup-bağımsız)

#### S1 Auth — Ortak (68/68)

```
Cinsiyet Seçimi Akışı
  [ ] Cinsiyet butonu görünür
  [ ] Hero animasyonu (0.9s crossfade)
  [ ] Cinsiyet localStorage'a yazıldı
  [ ] Seçimden sonra form beliriyor

E-posta Doğrulama
  [ ] Zorunlu alan
  [ ] Format doğrulama (RFC 5322 ⚠️)
  [ ] Hata mesajı açıklayıcı
  [ ] Büyük/küçük harf duyarsız karşılaştırma

Parola Doğrulama
  [ ] Min 8 karakter
  [ ] Caps lock uyarısı
  [ ] Kopyala-yapıştır serbest
  [ ] Göster/gizle geçişi

CSRF Token
  [ ] Formda token var (anahtar: csrf_token)
  [ ] Token POST header'da gönderiliyor
  [ ] Timing-safe karşılaştırma (hash_equals)
  [ ] Başarılı giriş sonrası token yenileniyor

Oturum Yönetimi
  [ ] Giriş sonrası session_regenerate_id()
  [ ] Çerez: HttpOnly, Secure, SameSite=Lax
  [ ] Zaman aşımı (30 dk varsayılan ⚠️)
  [ ] Çok sekmeli çıkış senkronu

Hız Sınırı
  [ ] 5 başarısız deneme → 429
  [ ] Retry-After header (60 sn ⚠️)
  [ ] IP başına sınırlama
```

#### S2 Discovery — Ortak (68/68)

```
Arama Kutusu
  [ ] Placeholder görünür
  [ ] Debounce (300ms)
  [ ] Yazarken sonuç geliyor
  [ ] Temizle (×) butonu

Tür Filtreleme
  [ ] Tüm türler seçilebilir
  [ ] Çoklu seçim destekli
  [ ] Animasyon akıcı (jank yok)

Sanatçı/Albüm Gözatma
  [ ] Alfabetik sıralama
  [ ] Grid düzeni duyarlı
  [ ] Daha fazla yükle / sayfalama

Öneriler
  [ ] "Tadına göre" bölümü
  [ ] Mood-kişiselleştirmesi
  [ ] Yenile butonu
```

#### S3 Playback — Ortak (68/68)

```
Oynat/Duraklat
  [ ] Buton durum değiştiriyor
  [ ] Klavye kısayolu: Boşluk
  [ ] İkon değişiyor (oynat ↔ duraklat)

Atlama Kontrolleri
  [ ] Sonraki (→) / Önceki (←)
  [ ] Klavye ok tuşları çalışıyor

Ses Kontrolü
  [ ] Kaydırıcı 0–100%
  [ ] Klavye ↑/↓
  [ ] Sessize alma (M) + ikon değişimi

Arama Çubuğu (Seek)
  [ ] Tıklama ile atlama
  [ ] Sürükleyerek atlama
  [ ] Klavye ← → (5sn adımlar)

Şimdi Çalıyor
  [ ] Parça adı · Sanatçı · Süre · Kapak
```

#### S5 Settings — Ortak (55/68 uygulanabilir; ortak katman herkese)

```
Profil Yönetimi
  [ ] Kullanıcı adı düzenleme
  [ ] Bio/hakkında düzenleme
  [ ] Profil fotoğrafı yükleme
  [ ] Görünen ad değiştirme

Tema Ayarları
  [ ] Açık koyu mod
  [ ] Sistem tercihi
  [ ] Tema değişimi anlık (yeniden yükleme yok)

Hesap Güvenliği
  [ ] Parola değiştirme (çocukta ❌ — veli hesabı)
  [ ] Giriş geçmişi
  [ ] Aktif oturumlar
  [ ] Tüm cihazlardan çıkış

Bildirimler
  [ ] Uygulama içi bildirimler
  [ ] E-posta frekansı
  [ ] Belirli bildirim türünü susturma
```

### 4.7 Mood → Senaryo Öncelik Çaprazı (QA hızlı referans)

| Mood kümesi | Birincil senaryo | İkincil senaryo | Tipik hata modu |
|---|---|---|---|
| Enerjik (13) | S3 Playback | S6 Social | Animasyon jank / gecikme hissi |
| Romantik (7) | S2 Discovery | S5 Settings | Öneri kalitesi + gece modu |
| Kaşif (5) | S2 Discovery | S1 Auth | Filtre kaybı, geri dönüş korumaz |
| Melankolik (5) | S5 Settings | S2 Discovery | Düşük kontrast tercihi ihmal |
| Sosyal (5) | S6 Social | S4 Download | Mesaj/akış kısıtları belirsiz |
| Sporcu (4) | S3 Playback | S4 Download | Çevrimdışı senkron kaybı |
| Hip-Hop (3) | S3 Playback | S2 Discovery | EQ preset eşleşmiyor |
| Lider/Meraklı (4) | S1 Auth | S5 Settings | Yetki devri görünmüyor |
| Gamer (2) | S3 Playback | S6 Social | Kısayol çakışması |
| Anne/Baba/Profesyonel (3) | S5 Settings | S4 Download | GDPR/veli kontrolü eksik |
| Arabesk/Dans türevi (8) | S2 Discovery | S3 Playback | Tür filtresinde kayıp |

*Sayılar §4.7'e özel envanter sayımıdır (§5.3'ten türetilmiştir); mood tanımları → [[personas/mood-taxonomy]].*

### 4.8 Grup Bazlı Kalıcı Notlar

| Grup | Kalıcı kısıt | Senaryoya etkisi |
|---|---|---|
| Çocuk (29) | 48px+ buton, sadeleştirilmiş UI, karmaşık sosyal yok, Premium'da veli popup'ı | S1/S2/S3 tam; S4/S6 ❌; S5 veli hesabına bağlı |
| Genç (29) | Yaş kilidi 16'ya kadar kademeli açılım ⚠️; e-posta doğrulama zorunlu | S4/S6 yaşa bağlı kademeli |
| Yetişkin (10) | Sosyal ⚠️ kısıtlı (mesaj ❌, akış ❌); ayarlar tam | S6 tek taraflı; S5'te veri dışa aktarma |

---

---

## 5. Sonuçlar (Türetilmiş Matrisler)

### 5.1 Cihaz Adaptasyonu

> ⚠️ **VERIFICATION REQUIRED:** Cihaz çözünürlükleri (375×812, 1024×1366, 1920×1080)
> eski vault'tan gelir; gerçek cihaz kırılımı kanıtı → [[personas/research-bank]].

#### Mobile (375×812) ⚠️

| Grup | Dokunma hedefi | Navigasyon | Klavye | Test odağı |
|---|---|---|---|---|
| Çocuk | 48×48px min | Hamburger | Sınırlı | Sadece ekran içi tuşlar |
| Genç | 44×44px | Tap + swipe | Fiziksel destek | Swipe geri/ileri |
| Yetişkin | 44×44px | Tüm jestler | Tam | Kısayollar + otomatik dolma |

- Dikey → yatay geçiş: düzen anında adapte olmalı
- Yatay kaydırma **hiç** olmamalı (1.4.10 reflow)

#### Tablet (1024×1366) ⚠️

| Grup | Düzen | Navigasyon | Periferi |
|---|---|---|---|
| Çocuk | Yan yana grid | Tab | Stylus destek |
| Genç | Esnek grid (2–3 kolon) | Tap + klavye | Klavye + trackpad |
| Yetişkin | Çok kolon (3–4) | Tüm yöntemler | Fare + klavye |

#### Desktop (1920×1080) ⚠️

| Grup | Düzen | Navigasyon | Hover |
|---|---|---|---|
| Çocuk | Ortalanmış (max 1200px) | Fare + klavye | Büyük vuruş alanı |
| Genç | Tam genişlik esnek grid | Tüm yöntemler | Yumuşak geçiş |
| Yetişkin | Max-width (1400px) | Klavye-öncelikli | Detaylı hover bilgisi |

### 5.2 WCAG 2.2 AA Kontrol Listesi (senaryo bağımsız)

**Renk / Kontrast:**
- [ ] 4.5:1 normal metin · 3:1 UI bileşenleri (⚠️ WCAG spesifikasyonu → research-bank)
- [ ] Bilgi yalnızca renge dayanmıyor
- [ ] Protanopi / deuteranopi / tritanopi simülasyonu geçti ⚠️ (prevalans → research-bank)

**Klavye:**
- [ ] Tab sırası DOM sırasıyla mantıklı
- [ ] Focus görünür (3px outline min)
- [ ] Klavye tuzağı yok · skip-link mevcut

**Ekran okuyucu:**
- [ ] ARIA etiketleri doğru · landmark (`main`, `nav`, `footer`)
- [ ] Başlıklar semantik (h1→h6) · form etiketleri bağlı · anlamlı alt metin

**Motor / Hedef boyutu:**
- [ ] Tıklama hedefi ≥48×48px (çocuk) / ≥44×44px (genç-yetişkin) ⚠️
- [ ] Hover yerine focus alternatimi var
- [ ] Zamanlı etkileşim duraklatılabilir
- [ ] Sürükle-bırak için klavye eşdeğeri

### 5.3 Test Case Sayım Matematiği

**Brüt:** 68 persona × 6 senaryo × 3 cihaz = **1.224**

**Net (uygulanabilirlik düşülmüş):**

| Senaryo | Uygulanabilir | ×3 cihaz | Toplam |
|---|---|---|---|
| S1 Auth | 68 | 3 | 204 |
| S2 Discovery | 68 | 3 | 204 |
| S3 Playback | 68 | 3 | 204 |
| S4 Download | 41 | 3 | 123 |
| S5 Settings | 55 | 3 | 165 |
| S6 Social | 46 | 3 | 138 |
| **TOPLAM** | **346** | | **1.038** |

⚠️ **VERIFICATION REQUIRED:** 41/55/46 sayıları eski vault'taki tablodan geldi;
grup kısıtlarından yeniden hesapla (68−29=39 ❌ eski 41 ile tutarsız — belirsizlik-3).

### 5.4 Erişilebilirlik Test Aparatları

- Görme: Chromatic Vision Simulator / tarayıcı renk körü modu
- Motor: yalnız trackpad · yalnız klavye · tek el · ses/switch kontrol ⚠️
- Birey temsili: her gruptan en az 1 persona ile tam WCAG turu

---

## 6. Katalog: Bağlantılı Dosyalar

### 6.1 Senaryo Dosyaları (gerçek hedef: `.ai/.personas/test-senaryolari/`)

| Senaryo | Wiki-link | Durum / eşleşme |
|---|---|---|
| S1 Auth | [[test-senaryolari/browser-navigasyon]] | ✅ mevcut — BNV-003 (Login/Kayıt) + BNV-004 (Session) + adımlar 8–11; ⚠️ eşleşme belirsiz (auth'a adanmış ayrı dosya yok) |
| S2 Discovery | [[test-senaryolari/muzik-kesfi]] | ✅ mevcut — dosya beyanı: "Mapping Karşılığı: S2 Music Discovery" (kesin) |
| S3 Playback | [[test-senaryolari/arabesk-dans-mood-gecis]] | ✅ mevcut — dosya beyanı: "S2 birincil, S3 ikincil" + EQ preset/oynatma geçişi; ⚠️ kısmi eşleşme |
| S4 Download | [[test-senaryolari/a11y-erisilebilirlik]] | ⚠️ **VERIFICATION REQUIRED** — eşleşme belirsiz: kuyruk/çevrimdışı oynatma kapsamına adanmış dosya YOK; offline + emülasyon adımları olan artan dosya |
| S5 Settings | [[test-senaryolari/playlist-olusturma]] | ✅ mevcut — dosya beyanı: "S2 + S5 (kısıtlı ayarlar)" + §3.5.2 (mapping §4.6-S5 ortak katman) |
| S6 Social | [[test-senaryolari/sosyal-paylasim]] | ✅ mevcut — dosya beyanı: "Mapping Karşılığı: S6 Social" (kesin) |

Not: `test-senaryolari/a11y-erisilebilirlik.md` dosyası WCAG çapraz kesittir (tüm senaryolara uygulanır); S4 Download'a adanmış senaryo dosyası bulunmadığı için bu satırda kullanılmıştır → adanmış dosya ADR-088+ ile eklenmelidir.

Kaynak: eski vault `test-senaryolari/` (6 dosya, read-only).

### 6.2 Kök Persona Belgeleri

| Wiki-link | Rol |
|---|---|
| [[personas/index]] | 68 dosyalık katalog (§6.3 tablo) |
| [[personas/mood-taxonomy]] | 25 küme tanımı + ADR-023 §2.2a hizası |
| [[personas/research-bank]] | 🔄 başka ajan — tüm ⚠️ iddiaların kanıt hedefi |
| [[personas/test-scenarios-mapping]] | Bu dosya |

### 6.3 Karar / Şablon / Günlük

- [[decisions/accepted/ADR-023-persona-driven-testing]] — §2.2a matris, §5.4 şart 1c
- [[templates/documentation/docs-md-template]] — §1–§8 iskeleti
- [[templates/personas/persona-template]] — §3.5.4, §4.5, ≥500 satır kuralı
- [[log]] — bu yazımın append kaydı

---

## 7. Doğrulama

### 7.1 Bu Dosyanın Doğrulama Ölçütleri

| Ölçüt | Hedef |
|---|---|
| Satır sayısı | ≥500 |
| Kodlama | UTF-8, BOM yok, mojibake yok |
| Frontmatter | 7 zorunlu alan (type=reference, category=testing) |
| Wiki-link biçimi | `[[relative/path]]` |
| Gerçek-dünya iddiaları | her biri `⚠️ VERIFICATION REQUIRED` + research-bank göstergesi |
| Kırık bağlantı | §6.1'de 6/6 hedef diskte mevcut (2026-09-26); S1/S3/S4 eşleşmeleri ⚠️ eşleşme belirsiz |

### 7.2 Uygulama Sırasında Doğrulanacak (QA agent)

1. §4.3 matrisindeki ✅/⚠️/❌ her hücre UI'da birebir tekrar üretiliyor mu
2. §5.2 WCAG listesi senaryonun tamamında geçiyor mu
3. §5.3 sayımını üreten koşu: 346 senaryo-kombinasyonu × 3 cihaz = 1.038
4. Çocuk senaryolarında ❌ (Download/Social) erişim reddi **açıklayıcı** görünüyor mu (1.3.1)
5. Yaş kilidi 16 ⚠️ iddiası ürün kararına bağlı — ADR-023 §2.2a ile çelişmiyor mu

### 7.3 Yıkıcı Değişiklik Yasağı

- Bu dosya **yeni değildir**; ADR-023 şart 1c onaylanana kadar `status: active`, `version: 1.0.0`
- Persona ekleme/çıkarma → yalnız yeni ADR-088+ ile
- Dosya adı değişimi → In-Place Refactoring kuralı gereği onay gerektirir

---

## 8. Kaynaklar ve Geçmiş

### 8.1 Kaynaklar

| # | Kaynak | Tür | Not |
|---|---|---|---|
| 1 | `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` | Vault | §2.2a, §5.4 |
| 2 | Eski vault `personas/test-scenarios-mapping.md` (711 satır) | Read-only | İçerik kaynağı, birebir kopya YOK |
| 3 | Eski vault `test-senaryolari/` (6 dosya) | Read-only | §6.1 listing |
| 4 | `.ai/.templates/documentation/docs-md-template.md` | Şablon | §1–§8 iskeleti |
| 5 | `.ai/.templates/personas/persona-template.md` | Şablon | ≥500, §3.5.4, §4.5 |
| 6 | `.ai/index.md` §12 | Vault | Persona satırı, kırık `personas/*` bağlantıları |

### 8.2 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-09-26 | İlk yazım (ADR-023 şart 1c); eski 711 satırlık dosyadan yeniden yapılandırma |

### 8.3 Belirsizlikler (⚠️ VERIFICATION REQUIRED listesi)

1. **Wiki-link öneki:** `[[personas/...]]` (eski) vs `.ai/.personas/` (yeni hedef) — persona-template'teki kısaltma biçimi henüz Vault-docs ile hizalanmadı.
2. **Çocuk yaş aralığı:** 4–11 (index) vs 6–11 (mapping başlığı) çelişkisi → research-bank.
3. **Sayım tutarsızlığı:** Download uygulanabilir 68−29=39 iken eski tablo 41 diyor (2 persona fazlası; muhtemel yaş kilidi kısmi uygulama) → QA agent yeniden hesaplamalı.
4. **Cihaz/teknik iddialar:** viewport boyutları, 80dB limiti, FLAC/MQA, BPM filtresi, COPPA/KVKK/GDPR eşikleri, renk körlüğü prevalansları → research-bank bekleniyor.
5. **`test-senaryolari/` 6 dosya** `.ai/.personas/test-senaryolari/` altında mevcut; §6.1 bağlantıları gerçek dosyalara bağlandı. **S1/S3/S4 eşleşmeleri ⚠️ eşleşme belirsiz** (auth/playback/download'a adanmış ayrı dosya yok → S4 için ⚠️ VERIFICATION REQUIRED).

---
*Bu belge docs-md şablonuyla üretilmiştir. Değişiklikler `.ai/log.md`'ye append edilir; tüm gerçek-dünya iddiaları [[personas/research-bank]] içinde doğrulanana kadar ⚠️ işaretlidir.*
