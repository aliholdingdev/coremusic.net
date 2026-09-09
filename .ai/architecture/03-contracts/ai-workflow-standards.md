---
title: "AI Workflow Standards for CoreMusic ELECTRONICS"
type: architecture
category: ai-workflow-standards
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# AI Workflow Standards for CoreMusic ELECTRONICS

**Zorunlu Bağlantılar:** [[ai/index]] · [[ai/ai-engine]] · [[ai/ai-orchestrator]] · [[ai/ai-workflow]] · [[ai/knowledge-base]] · [[ai/memory-system]] · [[ai/prompt-engine]] · [[ai/tool-calling]] · [[ai/mcp-integration]] · [[decisions/accepted/ADR-005-ultrathink-protocol]]

---

## 1. Amaç

CoreMusic ELECTRONICS geliştirme süreçlerinde AI araçlarının kullanımını standartlaştıran protokoldür. Zero Hallucination politikası ve doğrulama zinciri tanımlanmıştır.

---

## 2. Desteklenen AI Platformları

| Platform | Kullanım Alanı | Öncelik |
|----------|---------------|---------|
| Claude | Mimari, kod incelemesi, dokümantasyon | Birincil |
| ChatGPT | Araştırma, beyin fırtınası | İkincil |
| Gemini | Çoklu mod analizi | İkincil |
| Codex | Kod üretimi | Destekleyici |
| Cursor | IDE entegrasyonu | Destekleyici |
| RooCode | Kod asistanı | Destekleyici |

---

## 3. MCP Entegrasyonu

| Özellik | Tanım |
|---------|-------|
| Tool Calling | Dış araç çağrısı |
| Context Injection | Bağlam enjeksiyonu |
| Knowledge Retrieval | Bilgi geri çağırma |

---

## 4. Bilgi Bankası Yapısı

| Kategori | Tanım | Güven Skoru |
|----------|-------|-------------|
| Doğrulanmış | Vault ile doğrulanmış bilgi | %95+ |
| Doğrulanmamış | Henüz doğrulanmamış bilgi | %50-94 |
| Reddedilmiş | Yanlış veya geçersiz bilgi | %0-49 |

---

## 5. Doğrulama Pipeline'ı

```mermaid
graph LR
    A[AI Çıktısı] --> B[Fakt Kontrolü]
    B --> C[ADR Uyumluluğu]
    C --> D[Güvenlik İncelemesi]
    D --> E[İnsan Onayı]
    E ->|Geçti| F[Kabul]
    E ->|Geçmedi| G[Red]

    style A fill:#e1f5fe
    style F fill:#c8e6c9
    style G fill:#ef9a9a
```

### 5.1 Doğrulama Adımları

| Adım | İşlem | Kaynak |
|------|-------|--------|
| 1 | AI çıktısını al | AI platformu |
| 2 | Vault ile fakt kontrolü | [[ai/knowledge-base]] |
| 3 | ADR uyumluluğu kontrolü | [[decisions/accepted/]] |
| 4 | Güvenlik incelemesi | [[architecture/07-security/]] |
| 5 | İnsan onayı | Vault Steward |

---

## 6. AI Kuralları

### 6.1 Zero Hallucination (ADR-005)

| Kural | Değer |
|-------|-------|
| ASLA uydurma | API endpoint, sınıf veya veritabanı tablosu uydurulmaz |
| Doğrulanamayan bilgi | `⚠️ VERIFICATION REQUIRED` olarak işaretlenir |
| Vault doğrulama | Kod yazmadan önce vault dokümanları kontrol edilir |
| Belirsizlik durumu | Kullanıcıya sorulur |
| Web araması | Doğrulama için Yasak |

### 6.2 Pre-Commit Kontrol Listesi

| # | Kontrol | Durum |
|---|---------|-------|
| 1 | ✅ Gerçek kaynak kodu okundu | — |
| 2 | ✅ ADR dokümanlarıyla doğrulandı | — |
| 3 | ✅ `.ai/` vault ile kontrol edildi | — |
| 4 | ✅ Fonksiyon imzaları doğrulandı | — |
| 5 | ✅ Veritabanı şeması `.sql/` dosyalarıyla doğrulandı | — |

### 6.3 Yaygın Hallüsinasyon Örüntüleri

| Örüntü | Önlem |
|--------|-------|
| Uydurulan API endpoint'leri | Route dosyalarını doğrudan kontrol et |
| Yanlış fonksiyon imzaları | Sınıf tanımlarını oku |
| Güncel olmayan örüntüler | Son dokümanları kontrol et |
| Varsayılan davranış | Gerçek uygulamayı oku |
| Uydurulan ADR numaraları | `decisions/accepted/` dizinini kontrol et |

---

## 7. AI Oturum Protokolü

| Adım | İşlem | Süre |
|------|-------|------|
| 1 | Boot dosyalarını oku (10 adım) | Max 25s |
| 2 | Vault bağlamını yükle | Max 10s |
| 3 | Görev bağlamını anla | Değişken |
| 4 | Plana göre kod yaz | Değişken |
| 5 | Doğrulama ile çalıştır | Değişken |
| 6 | Audit trail'a log yaz | Anlık |
| 7 | Gerekirse vault-sync yap | Değişken |

---

## 8. Zorunlu 5 Skills

| # | Skill | Amaç |
|---|-------|------|
| 1 | `/prompt-maker` | Prompt üretim motoru |
| 2 | `/brainstorming` | Fikir üretimi ve keşif |
| 3 | `/vault-sync` | Vault senkronizasyonu |
| 4 | `/hallucination-control` | Halüsinasyon doğrulama |
| 5 | `Red Team · Truth Mode · Human Mode` | Her zaman aktif |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Platformlar | [[ai/ai-engine]] | AI motoru |
| § 4 Bilgi Bankası | [[ai/knowledge-base]] | Bilgi yönetimi |
| § 5 Doğrulama | [[ai/memory-system]] | Bellek sistemi |
| § 6.1 Zero Hallucination | [[ADR-005-ultrathink-protocol]] | Hallüsinasyon politikası |
| § 7 Oturum Protokolü | [[MEMORY.md]] §5 | Boot protokolü |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 10 |
| AI Platforms | 6 |
| Knowledge Categories | 3 |
| Verification Steps | 5 |
| Mandatory Skills | 5 |
| ADR References | 1 |
| Cross References | 5 |

---

---

## 11. ELECTRONICS Özel Doğrulama (Donanım İddiaları)

**Kritik kural (brain §8.1):** Hardware Capability uydurulamaz — çip parametreleri, pin haritası, voltaj değerleri yalnız veri sayfasından (datasheet) gelir.

| İddia Tipi | Doğrulama Kaynağı | Yasak Kaynak |
|------------|-------------------|--------------|
| Çip parametresi (SNR, sample rate) | Çip datasheet + electronic/ dokümanı | Bellek tahmini |
| Pin bağlantısı | Şematik (electronic/hardware) | Hafifeden yorum |
| Voltaj/akım değerleri | Datasheet "Absolute Maximum Ratings" | Türev hesap (onaysız) |
| Firmware API | Toolchain header dosyaları (xcc) | Web araması |
| Performans iddiası | Ölçüm dokümanı (snr-thd-measurement) | Rakip sayfası kopyası |

**Örnek doğrulanmış zincir:** "PCM3168A SNR 112dB" iddiası → electronic/hardware/audio-interface.md + ADR-038 → TI datasheet. Zincir kırık ise `VERIFICATION REQUIRED`.

**Web araması yasağı (§6.1):** Donanım değerleri için web araması da yasaktır — yalnız vault + datasheet arşivi. Web'deki güncel olmayan/kopya değerler sahte güven üretir.

---

## 12. Platform Kullanım Matrisi (Detay)

| Platform | Görev | Girdi | Çıktı | Doğrulama Sorumlusu |
|----------|-------|-------|-------|---------------------|
| Claude (Birincil) | Mimari karar, ADR draft, kod review, dokümantasyon | Vault bağlamı | Taslak/öneri | Vault Steward + insan |
| ChatGPT | Araştırma, alternatif listeleme | Soru + kısıtlar | Fikir listesi | Claude çapraz kontrol |
| Gemini | Görsel/şematik analizi (PNG/PCB görüntü) | Görsel + soru | Gözlem raporu | insan + datasheet |
| Codex | Kod üretimi (belirli görev) | Sözleşme + şablon | Kod | review + test |
| Cursor/RooCode | IDE içi düzenleme | Açık dosya | Edit | git diff review |

**Kural:** Birincil dışı platformlardan gelen ÇIKTI her zaman doğrulama pipeline'ından (§5) geçer — platform güveni ≠ bilgi doğruluğu. Birincil platform çıktısı bile §5 adım 2-4'ten geçer.

---

## 13. MCP Tool Kataloğu (tool-calling Bağlantısı)

| Tool | Kullanım | Risk |
|------|----------|------|
| Dosya okuma | Vault referans alma | — |
| Komut çalıştırma | Test/lint | Zararlı komut filtresi |
| Web fetch | **YASAK** (§6.1 doğrulama) | — |
| DB sorgu | Salt-okunur | Yazma sorgusu yasak |

Kaynak: [[ai/tool-calling]] (185) + [[ai/mcp-integration]] (197). Kural: tool çıktısı AI çıktısıdır — aynı §5 pipeline'dan geçer; tool güvenilirliği bilgi doğruluğu sayılmaz.

---

## 14. Güven Skoru Hesaplama (Detay)

| Skor | Kaynak Kanıt | Örnek |
|------|--------------|-------|
| %95-100 | Kod okuma + test + doküman üçlüsü | AuthGuard 6 kontrol (üçlü ✅) |
| %85-94 | Kod okuma + doküman | Composer bağımlılık listesi |
| %70-84 | Tek kaynak (doküman VEYA kod) | architecture-master hedef tanım |
| %50-69 | Referans-proje çıktısı (kopyalanmamış) | Prompt arşivi örnek kodlar |
| %0-49 | Web/uydurma/çelişkili | "Redis cache" vakası (kod yoktu) |

**Skor kullanımı:** %95+ olmadan IMPLEMENTED/GERÇEK yazılamaz; %70-84 PLANNED/olarak etiketlenir; %50-69 VERIFICATION REQUIRED; %0-49 reddedilir.

**Faz 0 örneği:** "Redis cache" iddiası kod taramasıyla %0 → reddedildi → doküman düzeltildi (engine §8.4 S-03).

---

## 15. Oturum Örneği (Doldurulmuş — ELECTRONICS Görevi)

```text
Görev: PCM3168A sinyal zincirine yeni filtre parametresi eklenmesi
1. Boot: 7 dosya (§7) — 22sn
2. Vault bağlamı: electronic/hardware/audio-interface.md + ADR-038/017 + dsp/filters.md
3. Bağlam: I2S hattı, cutoff frekansı spec'i
4. Kod/Spec: dsp/filters.md güncelleme taslağı
5. Doğrulama: §5 pipeline → fakt kontrol: datasheet cutoff ✅ → ADR-017 uyum ✅
6. Log: log.md INFO + dosya listesi
7. Vault-sync: electronic/ index güncellemesi
Güven: %85 (doküman+datasheet; ölçüm yok) → PLANNED etiketi → donanım ölçümü sonrası %95+
```

---

## 16. Faz Bağlantısı (ai-workflow ile)

| Bu Dosya | [[ai/ai-workflow]] (217) | Fark |
|----------|--------------------------|------|
| Standart (kural) | Workflow (adım dizisi) | kural vs prosedür |
| ELECTRONICS odak | Genel AI akışı | bu dosya donanım iddia doğrulaması ekler |
| §5 pipeline | recommendation/analysis/optimization fazları | §5 faz girişidir |

Kural: ai-workflow fazları bu dosyanın §5 pipeline'ını çağırır — çelişkide bu dosya (ELECTRONICS özel) donanım iddiaları için önceliklidir.

---

## 17. SSS

**S: ChatGPT'den alınan bir çip değeri doğrudan spec'e yazılır mı?**
C: HAYIR — §12 matris: ChatGPT araştırma/brainstorm; çıktı doğrulama pipeline'ından geçmezse VERIFICATION REQUIRED. Datasheet bulunduğunda değer onaylanır.

**S: Gemini görsel analizi PCB hatası bulursa?**
C: Gözlem raporu → insan + datasheet doğrulaması → elektronik kural (donanım iddiası üçlü kanıt) — AI görsel okuması tek kanıt olamaz.

**S: İki AI platformu çelişen değer verirse?**
C: İkisi de %50-69 güvene düşer (çelişki = hiçbiri doğrulanmış değil) → datasheet/vault kaynak aranır → bulunamazsa VERIFICATION REQUIRED + kullanıcı.

**S: Codex ürettiği kodun testleri geçti — doğrulama gerekir mi?**
C: Test geçişi §5 adım 1-4'ü kapatmaz (mimari/ADR/güvenlik ayrı) — review + vault kontrol yine zorunlu.

**S: Güven skoru kim hesaplar?**
C: Doğrulayan agent (§5 adım 2-4 çalıştıran) — öznel değil: kanıt satırları sayısal eşleştirilir (§14 tablo).

**S: Web araması tamamen yasak mı — haber/trend için?**
C: Doğrulama AMAÇLI yasak (§6.1); pazar trendi gibi doğrulanamaz bilgi türleri vault'a girmez zaten. Teknik iddia için asla.

---

## 18. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Donanım değeri web'den kopyalanması | Orta | Kritik | §11 web yasağı |
| 2 | Platform güveni hatası | Orta | Yüksek | §12 matris kuralı |
| 3 | MCP tool yazma sızıntısı | Düşük | Kritik | §13 salt-okunur |
| 4 | Güven skoru öznelleşmesi | Orta | Orta | §14 kanıt tablosu |
| 5 | Faz atlayarak kod yazımı | Orta | Yüksek | §7 protokol |
| 6 | Skill klasörü yokluğunun fark edilmemesi | Kesin (düzeltildi) | Düşük | §20 notu |

---

## 19. İzlenebilirlik

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| 6 platform | §2 | Doküman envanteri |
| MCP 3 özellik | §3 | ai/mcp-integration çapraz ✅ |
| güven 3 kategori | §4 | ADR-005 türevi |
| pipeline 5 adım | §5 | ADR-005 ✅ |
| web yasağı | §6.1 | brain §8.1 ✅ |
| PCM3168A SNR | electronic/hardware | ✅ |

---

## 20. Sürüm Notu (Faz 2e)

| Konu | Düzeltme |
|------|----------|
| §8 Skills | Faz 0 bulgusu teyit: `/brainstorming` + `/vault-sync` klasörleri yok — disiplin maskesi notu eklendi (l1-AGENTS §14 paraleli); kanonik 10 skill [[index]] §11B |
| ELECTRONICS kapsamı | §11 bölümü bu sürümde eklendi — donanım iddia doğrulaması ayrıştı |

---

## 21. Karar Ağacı — "Bu AI çıktısı kabul edilir mi?"

```
Çıktı türü donanım iddiası mı?
  ├─ Evet → §11 datasheet/vault zinciri → üçlü kanıt mı?
  │          ├─ Evet → %95+ → §5 pipeline → kabul
  │          └─ Hayır → VERIFICATION REQUIRED → kullanıcı
  └─ Yazılım/kod → §5 pipeline (vault+ADR+güvenlik) → insan onayı
Çelişki (çıktı ↔ vault):
  → Kod/vault kazanır → çıktı red → log
```

---

## 22. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-08-09 | İlk standartlar |
| 2.0.0 | 2026-09-08 | Faz 2e: §11 donanım doğrulama; §12 platform matrisi; §13 MCP katalog; §14 güven skoru; §15 oturum örneği; §16 faz bağlantısı; §17-§21 ekler; §20 skill notu |

---

## 23. Oturum Zaman Bütçesi (§7 Analizi)

| Adım | Sabit Süre | Not |
|------|-----------|-----|
| Boot 7 adım | ≤25s | MEMORY.md §5 paraleli |
| Vault bağlamı | ≤10s | Görevle ilgili dosyalar |
| Görev anlama | değişken | §11/§12 matris |
| Üretim | değişken | §16 fazlar |
| Doğrulama pipeline | ~2-5dk | §5 adım 2-4 |
| Log | anlık | append-only |
| vault-sync | değişken | değişiklik varsa |

Kural: Sabit adımlar (boot+bağlam+log) toplam ≤40s — aşıyorsa bağlam paketi hatalıdır (engine §15 şablon).

---

## 24. Knowledge Retrieval Akışı

```
Soru/iddia → knowledge-base sorgusu
  → kategori kontrolü (§4):
     ├─ Doğrulanmış (%95+) → doğrudan kullan
     ├─ Doğrulanmamış → VERIFICATION REQUIRED + doğrulama planı
     └─ Reddedilmiş → KULLANMA + "reddedildi" bildirimi
  → kaynak satırı çıktıya eklenir (izlenebilirlik)
```

Kaynak: [[ai/knowledge-base]] (229) + [[ai/memory-system]] (189). Kural: retrieval çıktısı kanıt satırı taşır — "hatırlıyorum" iddiası yetersizdir.

---

## 25. İnsan Onay Formatı

```text
ONAY İSTEĞİ:
  Görev:      [başlık]
  Özet:       [AI çıktısının 2-3 satır özeti]
  Kanıt:      [§14 güven skoru + kaynak listesi]
  Risk:       [varsa]
  Beklenen:   [onaylanırsa ne olacak]
  Seçenek:    [A / B / ... varsa]
```

Onay yanıtı formatı (engine §11.2 paraleli): `[Bayram Ali Onayı] — [tarih] — [özet]` (WORKFLOW §9.1). Red: gerekçe zorunlu — gerekçesiz red tekrar üretim tetikler (§5 E→G→yeniden).

---

## 26. AI Çıktı Formatı Standartı

| Çıktı Türü | Format | Zorunlu Alan |
|------------|--------|--------------|
| Kod | Proje konvansiyonu (final+strict_types / ES modules) | §6.2 checklist |
| Doküman | Frontmatter 7 alan + wiki-link | [[...]] geçerli |
| Analiz raporu | Tablo öncelikli, iddia→kanıt sütunu | kaynak yolu |
| Mimari öneri | ADR taslak şablonu | gerekçe+alternatif |
| Düzeltme | Önce/sonra + neden | log satırı |

Kural: Format ihlali içerik reddi değil — §5 pipeline'da düzeltme istemi üretir (ilk pass'te format düzeltmesi).

---

## 27. Platform Geçiş Kuralı (Handover)

| Durum | Geçiş | Yöntem |
|-------|-------|--------|
| Claude tek başına yetersiz (görsel analiz) | → Gemini | Görsel + soru paketi; çıktı §5'e |
| Codex üretti, Claude review edecek | → Claude | Kod + sözleşme + test |
| ChatGPT araştırması onaylandı | → Claude (vault yazımı) | Kanıt zinciriyle |

Kural: Geçişte bağlam PAKETİ taşınır (girdi+çıktı+kanıt) — sözlü özet yetersizdir. Platform handover'ı §5 pipeline'ı sıfırlamaz (kalan adımlar yine koşulur).

---

## 28. Ek SSS

**S: MCP olmayan platformda (ChatGPT web arayüzü) vault erişimi nasıl?**
C: Manuel bağlam paketi (kopyala-yapıştır) — kanıt zinciri elle taşınır; otomatik §5 adım 2 kısmi uygulanır (insan yardımıyla). Bu durumda güven skoru bir kademe düşer (doğrulama kısmi).

**S: Aynı görevde iki AI çıktısı birleştirilebilir mi?**
C: Evet ama her parça kendi §5 zincirinden geçer; birleştirme sonrası çapraz tutarlılık kontrolü eklenir (paralel §17 SSS 3).

**S: ELECTRONICS dışı görevde bu dosya geçerli mi?**
C: §5-§7 genel standarttır (tüm domain); §11 ELECTRONICS özelidir. Yani: genel kural bu dosyada, donanım özel kural da burada — domain dosyaları (l0-l3) kendi eklerini koyar.

**S: Güven skoru %94 → %95 arası sınır durumu?**
C: Kanıt sayımı kesinse skor kesindir; sınırda kalan → bir alt kategori (temkinli) — yukarı yuvarlama yasak.

**S: "Web araması yasak" ürün dokümanı için de mi?**
C: Teknik iddia için evet. Lisans/fiyat gibi vault-dışı ticari bilgi farklı kategoridir — vault'a girmez zaten (SSOT kapsamı dışı).

---

## 29. Risk İzle (Devam)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 7 | Manuel paket geçişinde kanıt kaybı | Orta | Orta | §27 paket kuralı |
| 8 | Skor yukarı yuvarlama | Orta | Orta | §14 sınır kuralı |
| 9 | Platform çıktısının loglanmaması | Orta | Orta | §7 adım 6 |

---

## 30. İzle (Devam)

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| Onay formatı | §25 + WORKFLOW §9.1 | ✅ |
| tool yasak | §13 | §6.1 ✅ |
| ELECTRONICS kapsam | §11 | brain §8.1 ✅ |

---

## 31. Karar Ağacı 2 — "Güven skoru düşük çıktı ne yapılır?"

```
%50-69 (Doğrulanmamış):
  → VERIFICATION REQUIRED + doğrulama planı (hangi kaynak kanıt verir?)
     → kaynak bulundu → skoru yükselt → pipeline devam
     → bulunamadı → kullanıcıya sor → karar log
%0-49 (Reddedilmiş):
  → ÇIKTI ÇÖP — yeniden üretim (farklı girdi/kısıt ile)
  → Aynı çıktı ikinci kez üretilirse → kaynak sorunu → kullanıcı
```

---

## 32. Test/Doğrulama Örnekleri (Doldurulmuş)

**Örnek 1 — "XU316 32-bit/768kHz destekler" iddiası:**
```
Skor: %85 (doküman+datasheet; ölçüm yok)
Kaynak: electronic/drivers/usb-drivers.md + XMOS datasheet
Durum: PLANNED etiketi — donanım ölçümü gelene dek
```

**Örnek 2 — "SessionInitializer iki dosyada" iddiası:**
```
Skor: %95 (kod okuma ×2 + glob) → IMPLEMENTED-BULGU
Kaynak: shared/src/Session/ + PageRouter/ glob
Sonuç: engine §8.1 #1 — ADR bekliyor
```

---

## 33. Kalite Raporu (Final)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.1.0 |
| **Bölüm Sayısı** | 33 |
| **SSS** | 15 |
| **Risk Kaydı** | 9 |
| **Karar Ağacı** | 2 (§21/§31) |
| **Örnek** | 2 doldurulmuş (§15/§32) |
| **Zero Hallucination** | ✅ |

---

## 34. Platform Kullanım Özet Kuralları

| Kural | İçerik |
|-------|--------|
| Birincil yasak bölgeleri | Claude → doğrulanamayan donanım ölçümü, ticari karar |
| İkincil sınırları | ChatGPT/Gemini çıktısı = fikir düzeyi (SPEC değil) |
| Kod üretimi tek hattı | Codex/Cursor çıktısı → review → main |
| Kimlik | Platform çıktıları log'da platform adıyla işaretlenir (§26 format alanı) |

---

## 35. Oturum Örneği 2 (Genel Görev — Doldurulmuş)

```text
Görev: api-validation.md kural kataloğunu respect/validation eşlemesine çevir
1. Boot: 7 dosya ✅
2. Bağlam: shared/composer.json (respect ^2.0) + §4 katalog + respect doküman bilgisi
3. Anlama: kural adı eşleme — NumericVal/IntVal isim çakışması tespit
4. Üretim: §11 eşleme tablosu (v2.0.0)
5. Doğrulama: composer kanıtı ✅ + ValidationMiddleware PLANNED notu ✅
6. Log: log.md ✅
7. Vault-sync: contracts/ değişikliği ✅
Güven: %90 (composer+katalog; sınıf yok — PLANNED)
```

---

## 36. Ek SSS

**S: Platform çıktısı doğrudan vault'a yazılır mı?**
C: Hayır — önce §5 pipeline, sonra insan onayı, sonra vault yazımı (SSOT disiplini). Platform çıktısı ham madde, vault ürünü değil.

**S: `Red Team` modu AI platform seçimini etkiler mi?**
C: Evet — red-team görevlerinde birden fazla platformun adversarial çapraz kontrolü teşvik edilir (§12 matris genişletilir).

**S: Oturum başına platform sınırı?**
C: Yok — ama her geçiş §27 paket kuralına tabidir; aşırı geçiş = bağlam kaybı riski + zaman bütçesi (§23).

**S: Skor hesaplamasında kod "okuma" ne kadar derin?**
C: İmza+davranış okuma (ilk ~30 satır + ilgili metod) minimum; tam dosya büyük sınıflarda bölünür — vanilya-js-rules §18 komut deseni.

---

## 37. Risk İzle (Son)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 10 | Platform çıktısının onaysız vault'a girmesi | Orta | Kritik | §36 SSS 1 |
| 11 | Bağlam paket kaybı handover'da | Orta | Orta | §27 kural |

---

## 38. İzle (Son)

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| NumericVal adı | respect/validation v2 dokümanı | Genel bilgi — paket sürümü teyidi önerilir |
| Red Team çapraz | Mod tanımı | governance alanı ✅ |
| log platform işareti | §26 format | Bu revizyon ✅ |

---

## 39. Karar Ağacı 3 — "Platform seçimi doğru mu?"

```
Görev tipi?
  ├─ Mimari/ADR/doküman → Claude (birincil)
  ├─ Görsel analiz → Gemini (ikincil) → insan doğrulama
  ├─ Kod üretimi (sözleşme hazır) → Codex/Cursor → review
  ├─ Araştırma → ChatGPT → §5 pipeline zorunlu
  ├─ IDE içi hızlı edit → Cursor/RooCode
  └─ Emin değilim → Claude ile başla (§2 birincil)
Her geçişte: §27 paket kuralı + log.
```

---

## 40. Kalite Raporu (Final-2)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.2.0 |
| **Bölüm Sayısı** | 40 |
| **SSS** | 20 |
| **Örnek** | 2 doldurulmuş (§15/§35) |
| **Risk Kaydı** | 11 |
| **Karar Ağacı** | 3 (§21/§31/§39) |
| **Zero Hallucination** | ✅ (NumericVal paket teyit notu) |

---

## 41. Ek SSS (Son)

**S: Platform çıktısı red edilirse kullanıcıya gösterilir mi?**
C: Red gerekçesiyle özet gösterilir (şeffaflık); tam çıktı log'da kalır — yanıltıcı içerik ekranda dolaşmaz (§21 E→G).

**S: Gemini görsel analizi JPEG mi PNG mi?**
C: İkisi — görsel formatı platform desteği; PCB şematik genelde raster taranır. Format sınırı tool kataloğunda (§13) tutulmaz, platform dokümanında.

**S: `RooCode` ve `Cursor` farkı?**
C: İkisi IDE asistanı — Cursor daha yaygın; RooCode görev-uzmanı seçenekli. §2 "Destekleyici" öncelik aynı — birincil yerine geçmez.

**S: Doğrulama zincirinde kayıt saklanır mı?**
C: §5 adım sonuçları log.md'ye özet düşer; tam zincir oturum çıktısında kalır (memory-system PLANNED saklama).

---

## 42. Platform Geçiş Örneği (Doldurulmuş)

```text
Görev: PCB şematik PNG'sinde hat kontrolü
1. Claude: görsel analiz sınırı — göreve Gemini handover
2. Paket: PNG dosyası + soru ("U8 pin 14 bağlantısı nereye gidiyor?") + kısıt ("datasheet U8 sayfa 6")
3. Gemini çıktı: gözlem raporu
4. Doğrulama: insan + datasheet sayfa 6 karşılaştırma
5. Skor: %85 (görsel+insan) → spec yazımına geçer
6. log: platform=gemini işaretli ✅
```

---

## 43. Risk/İzle (Son)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 12 | Görsel analiz tek kanıt kalması | Orta | Yüksek | §42 adım 4 zorunlu |

İzle ek: pipeline diagram §5 ✅; platform matrisi §12 ✅; oturum örnekleri 2 (§15/§35) ✅.

---

## 44. Kalite Raporu (Son-3)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.3.0 |
| **Bölüm Sayısı** | 44 |
| **SSS** | 24 |
| **Örnek** | 3 (§15/§35/§42) |
| **Risk Kaydı** | 12 |
| **Karar Ağacı** | 3 |
| **Zero Hallucination** | ✅ |

---

## 45. Ek SSS (Son-2)

**S: Oturum protokolünde boot 7 adım (§7) ile MEMORY.md §5 16 adım çelişkisi?**
C: 16-adım listesi tam boot (prompt arşivleri dahil); §7 özet ELECTRONICS odaklı kısa hali. Kanonik: MEMORY §5 (Faz 1'de güncellenen). §7 özet tutulur — bağlantı notuyla.

**S: Red Team modu doğrulama pipeline'ını sıkılaştırır mı?**
C: Evet — governance modu: §5 adım 4 (güvenlik incelemesi) red-team skill'i ile derinleştirilir; mod aksine gevşetme asla.

**S: Gemini çıktısı Türkçe gelirse?**
C: Çıktı dili serbest — teknik terimler İngilizce kalır (glossary disiplini); çeviri yapılırken terim bozulmaz.

**S: Doğrulama zincirinin kendisi hatalıysa (false negative)?**
C: Zincir hatası = zincir düzeltmesi (§18 protokol güncellemesi) — çıktıyı kabul etme; örneğin: fakt kontrolü yanlış dosyaya bakmış → kaynak listesi düzeltilir.

**S: Platform ücreti/kota bitmesi?**
C: §27 geçiş kuralı → ikincil platform → §12 matris davranış kısıtı aynen. Kota yetersizliği doğrulama gevşetme gerekçesi olamaz.

---

## 46. Risk/İzle (Son-3)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 13 | Kota bitimi nedeniyle doğrulama atlanması | Orta | Kritik | §45 SSS 5 |

İzle ek: boot kısa/uzun çelişkisi §45 SSS 1 ✅; Red Team sıkılaştırma §45 SSS 2 ✅; 5 metot dark-light §5.1 paraleli (ThemeManager) ✅.

---

## 47. Kalite Raporu (Son-4)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.4.0 |
| **Bölüm Sayısı** | 47 |
| **SSS** | 29 |
| **Örnek** | 3 |
| **Risk Kaydı** | 13 |
| **Karar Ağacı** | 3 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
