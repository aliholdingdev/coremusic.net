---
name: karpathy-guidelines
description: "Use when writing, reviewing, refactoring, or planning code to avoid LLM pitfalls (overcomplication, bloated abstractions, orthogonal edits, hidden assumptions) — Enforces Andrej Karpathy's 4 behavioral principles (Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution) with verifiable success criteria and surgical precision. Tetikleyiciler: 'karpathy', 'karpathy guidelines', 'karpathy kuralları', 'think before coding', 'simplicity first', 'surgical changes', 'goal-driven', 'overengineering önle', 'sadeleştir', 'cerrahi değişiklik', 'varsayım yapma'."
license: MIT
metadata:
  version: 4.0.0
  previous-version: 1.0.0
  format: claude-skill-v3
  author: "Bayram Ali (ULTRATHINK Engineering) / Andrej Karpathy observations"
  category: engineering-guidelines
  tags: [karpathy, simplicity, surgical-changes, goal-driven, truth-mode, anti-overengineering]
  updated: 2026-11-01
---

# karpathy-guidelines — LLM Kodlama Hatalarını Önleme ve Davranışsal Mühendislik Direktifleri

> **Özet:** Bu skill, Andrej Karpathy'nin büyük dil modellerinin (LLM) kodlama alışkanlıkları üzerine yaptığı gözlemlerden türetilmiş **4 temel davranış ilkesini** (Think Before Coding · Simplicity First · Surgical Changes · Goal-Driven Execution) CoreMusic ve otonom mühendislik standartlarına entegre eder.
> 
> **Temel Amaç:** Yanlış varsayımlarla ilerlemeyi, gereksiz soyutlama ve kütüphane şişkinliğini (bloated abstractions), görevin dışındaki kod ve yorumlara dokunmayı (orthogonal side-effects) ve doğrulanamayan "çalıştı varsayımı" hatalarını kökten engellemek.

---

## §1 Dört Temel İlke (The Four Principles)

| İlke | Önlediği LLM Hatası | Altın Kural |
|---|---|---|
| **1. Think Before Coding** | Yanlış varsayımlar, gizlenen kafa karışıklığı, ödünleşimlerin sunulmaması | **Varsayım yapma. Şüpheyi gizleme. Ödünleşimleri açıkla.** |
| **2. Simplicity First** | Aşırı mühendislik, gereksiz soyutlama, 1000 satırlık inşaat | **Sorunu çözen minimum kod. Spekülatif hiçbir şey yok.** |
| **3. Surgical Changes** | İlgisiz kodlara/yorumlara dokunma, stil bozma, yetim kod bırakma | **Yalnızca dokunman gereken yere dokun. Kendi izini temizle.** |
| **4. Goal-Driven Execution** | Gevşek "çalıştı" iddiaları, doğrulanamayan adımlar | **Başarı kriterini testle tanımla. Doğrulanana kadar döngüde kal.** |

---

## §2 İlke Detayları ve Uygulama Protokolü

### 1. Think Before Coding (Kodlamadan Önce Düşün)

**"Don't assume. Don't hide confusion. Surface tradeoffs."**

Modeller genellikle kullanıcı adına sessizce varsayımda bulunur ve sormadan uygular. Bu ilke açık muhakemeyi zorunlu kılar:
- **Varsayımlarını Açıkça Belirt:** Şüphe duyduğun veya birden fazla yol olan her durumda tahmin etme; kullanıcıya sor.
- **Seçenekleri ve Ödünleşimleri Sun:** Birden fazla yorumlama veya mimari yaklaşım varsa sessizce birini seçme; artı/eksi yönleriyle seçenekleri ortaya koy.
- **Daha Basit Bir Yol Varsa İtiraz Et (Push Back):** İstenen şey gereksiz karmaşıksa veya daha basit bir çözüm mevcutsa bunu açıkça söyle.
- **Kafan Karıştığında Dur:** Belirsiz veya çelişkili bir talimat aldığında kod yazmayı durdur; neyin belirsiz olduğunu net bir dille sor.

### 2. Simplicity First (Önce Sadelik)

**"Minimum code that solves the problem. Nothing speculative."**

Aşırı mühendislik (overengineering) eğilimini durdur:
- Talep edilmeyen hiçbir özelliği (feature) ekleme.
- Yalnızca tek bir yerde kullanılan kodlar için soyutlama (interface, factory, abstract class) üretme.
- İstenmeyen esneklik, parametre veya konfigürasyon seçenekleri uydurma.
- İmkânsız senaryolar için karmaşık hata yakalama ağları örme.
- **Kıdemli Mühendis Testi:** "Kıdemli bir mühendis bu koda aşırı karmaşık der mi?" Yanıt evet ise kodu basitleştir. 200 satırda yazılan kod 50 satırda çözülebiliyorsa yeniden yaz.

### 3. Surgical Changes (Cerrahi Değişiklikler)

**"Touch only what you must. Clean up only your own mess."**

Mevcut kod üzerinde çalışırken cerrahi hassasiyet göster:
- Görevinle doğrudan ilgisi olmayan komşu fonksiyonları, yorumları, girintileri veya formatları "iyileştirmeye" kalkma.
- Bozuk olmayan hiçbir şeyi refactor etme.
- Kodun mevcut stiline, adlandırma konvansiyonuna ve mimarisine birebir uy.
- İlgisiz ölü kod fark edersen silme; raporla ve dokunma.
- **Yetim Kod Temizliği:** Yalnızca kendi yaptığın değişikliğin boşa çıkardığı import, değişken ve fonksiyonları temizle.
- **Cerrahi Test:** Diff'teki her değiştirilmiş satır doğrudan kullanıcının isteğine dayandırılabilmelidir.

### 4. Goal-Driven Execution (Hedefe Dayalı Yürütme)

**"Define success criteria. Loop until verified."**

Buyurgan (imperative) görevleri doğrulanabilir başarı hedeflerine dönüştür:

| Kullanıcı İsteği | Doğrulanabilir Hedefe Dönüşüm |
|---|---|
| "Validasyon ekle" | "Geçersiz girdiler için testler yaz, ardından testlerin geçtiğini doğrula" |
| "Hatayı çöz" | "Hatayı yeniden üreten bir test yaz, düzeltmeyi yap ve testin geçtiğini doğrula" |
| "X modülünü refactor et" | "Refactoring öncesi ve sonrasında tüm testlerin yeşil kaldığını doğrula" |

Çok adımlı görevlerde kısa bir yürütme planı tanımla:
```text
1. [Adım 1] → Doğrulama: [Test / Kontrol]
2. [Adım 2] → Doğrulama: [Test / Kontrol]
3. [Adım 3] → Doğrulama: [Test / Kontrol]
```

---

## §3 Gerçek Hayat Karşılaştırmalı Örnekleri (Anti-Patterns vs Karpathy Standards)

### Örnek 1: Gizli Varsayım Tuzağı (Hidden Assumptions)
- **Kullanıcı İsteği:** "Kullanıcı verilerini dışa aktaracak bir özellik ekle."
- ❌ **LLM Hatası:** Doğrudan `export_users()` yazıp tüm tabloyu JSON/CSV olarak masaüstüne kaydeder. Sayfalama, yetkilendirme ve hassas alan filtrelemesini sessizce varsayar.
- ✅ **Karpathy Standardı:**
  ```text
  Uygulamadan önce netleştirmem gereken noktalar var:
  1. Kapsam: Tüm kullanıcılar mı, yoksa filtrelenmiş bir alt küme mi?
  2. Format ve Aktarım: Tarayıcıdan dosya indirme mi, arka plan e-postası mı, yoksa bir API endpoint'i mi?
  3. Güvenlik: Hangi alanlar dahil edilecek? (Şifre özeti, token gibi hassas veriler hariç tutulmalı).
  En yalın yaklaşım: Sayfalanmış JSON dönen bir API endpoint'i eklemektir. Tercihiniz nedir?
  ```

### Örnek 2: Aşırı Soyutlama Tuzağı (Over-Abstraction)
- **Kullanıcı İsteği:** "İki sayıyı toplayan bir fonksiyon yaz."
- ❌ **LLM Hatası:** `MathOperationInterface`, `AdditionStrategy`, `CalculatorFactory` ve `OperationResult` sınıfları kurarak 150 satır üretir.
- ✅ **Karpathy Standardı:**
  ```php
  function add(int|float $a, int|float $b): int|float {
      return $a + $b;
  }
  ```

### Örnek 3: İlgisiz Kod Temizliği Tuzağı (Orthogonal Edits)
- **Kullanıcı İsteği:** "Login controller'daki rate limit süresini 60 saniyeden 120 saniyeye çıkar."
- ❌ **LLM Hatası:** Rate limit süresini değiştirirken altındaki login methodunun PHPDoc bloklarını siler, girintileri değiştirir, `remember_me` değişkenini yeniden adlandırır.
- ✅ **Karpathy Standardı:** Diff yalnızca ilgili satırı (`$rateLimitWindow = 120;`) içermelidir. Başka tek bir bayta dokunulmaz.

---

## §4 Doğrulama Kontrol Listesi

Her kod yazımı öncesinde ve sonrasında şu 4 soruya "EVET" denmelidir:
1. `[ ]` **Varsayım Testi:** Kullanıcının söylemediği bir kararı kendi başıma varsaymadan uyguladım mı?
2. `[ ]` **Sadelik Testi:** Yazdığım kod daha az satırla, tek kullanımlık soyutlamalar olmadan çözülebilir mi?
3. `[ ]` **Cerrahi Test:** Diff'te kullanıcının istemediği tek bir satır, silinmiş alakasız yorum veya değiştirilmiş format var mı?
4. `[ ]` **Hedef Testi:** Yapılan değişikliğin çalıştığı somut bir test veya konsol çıktısıyla kanıtlandı mı?

---

## §5 Destekleyici Belgeler ve Örnekler (References)

- **Ayrıntılı Kod Örnekleri & Anti-Pattern Karşılaştırmaları:** [`references/EXAMPLES.md`](references/EXAMPLES.md) — 4 ilkenin her biri için hatalı ve doğru kod örnekleri (Python / Generic).
- **Orijinal Dokümantasyon:** [`references/README.md`](references/README.md) — Andrej Karpathy kaynaklı kılavuzun tam metni.
- **Cursor IDE Kuralı:** [`.cursor/rules/karpathy-guidelines.mdc`](file:///c:/www/coremusic.net/.cursor/rules/karpathy-guidelines.mdc)
- **Cursor IDE Kök Pointer:** [`CURSOR.md`](file:///c:/www/coremusic.net/CURSOR.md)

---

*CoreMusic Karpathy Guidelines Skill v4.0.0 — Derived from Andrej Karpathy observations on LLM coding pitfalls.*
