# AGENTIC ORCHESTRATOR KATMANI — TAM REFERANS

Prompt Maker v9.0.0 | Opsiyonel katman | 2026-08-02

Bu dosya, `SKILL.md` §5'in tam açılımıdır. SKILL.md'yi zaten okudunuz varsayılarak yazılmıştır; burada tekrar edilmeyen kısa özetler için SKILL.md §5'e bakın.

**Kaynak notu:** Bu dosya, önceki `ai-agentic-orchestrator` v3.1.0 taslağının genişletilmiş ve CoreMusic prompt-maker ekosistemindeki mevcut dosyalarla (`multi-agent-patterns.md`, `10-web-research-protocol.md`, `validation-engine.md`, `09-quality-scoring-rubric.md`) çakışmayacak şekilde entegre edilmiş halidir. Aşağıdaki Vault dosya açıklamalarından `brain2.md` formatı doğrudan mevcut dokümantasyondan alınmıştır; `brain.md`, `MEMORY.md`, `keys.md`, `WORKFLOW.md` için verilen açıklamalar dosya adından ve genel proje bağlamından çıkarılmış makul varsayımlardır — CoreMusic deposundaki gerçek içerikleriyle çelişirse, gerçek içerik esas alınır.

---

## 1. AMAÇ VE KAPSAM

Orchestrator katmanı, `prompt-maker`'ı MASTER PROMPT üretiminin ötesinde, **herhangi bir CoreMusic görevinde** yüksek titizlik disiplini uygulayan bir modda çalıştırır. Üç şeyi garanti eder:

1. **Sıfır halüsinasyon** — her teknik iddia doğrulanır veya reddedilir, asla varsayılmaz.
2. **Doğru agent'a yönlendirme** — görev, CoreMusic'in 8 uzman ajanından doğru olan(lar)a atanır.
3. **Vault farkındalığı** — görev, projenin kendi kural dosyalarını okumadan başlamaz.

Bu üçü, MASTER PROMPT üretiminin kalitesini de yükseltir (üretilen prompt'un içeriği artık orchestrator'ın doğruladığı bilgilere dayanır) ama orchestrator'ın kendisi bir prompt üretmez — bir **çalışma disiplini**dir.

---

## 2. AKTİVASYON — DETAY

SKILL.md §2.2'deki tetikleyicilere ek olarak, orchestrator modu şu durumlarda **önerilir** (zorunlu değil, kullanıcı onayı gerekir):

```
- CoreMusic canlı ortamını etkileyen bir değişiklik isteniyor
- Güvenlik, ödeme, kimlik doğrulama gibi yüksek risk taşıyan bir domain
- Birden fazla agent'ın domain'ini kesen bir görev (ör. hem backend hem güvenlik)
- Kullanıcı daha önce bu oturumda bir halüsinasyon/hata fark etmiş ve düzeltilmesini istemiş
- Donanım/audio DSP gibi yanlış olduğunda pahalı olan bir alan (yanlış çip seçimi vb.)
```

Orchestrator modu **önerildiğinde**, kullanıcıya tek satırlık bir onay sorusu sorulur (ör. "Bu görev için orchestrator titizliği [Vault okuma + agent routing + halüsinasyon skorlama] uygulayayım mı, yoksa doğrudan mı ilerleyeyim?"). Kullanıcı "hayır" derse, çekirdek/normal davranışla devam edilir — orchestrator zorla dayatılmaz.

---

## 3. VAULT MANDATE — DOSYA BAZINDA AÇIKLAMA

Orchestrator modu tetiklendiğinde, göreve başlamadan önce aşağıdaki dosyalar (varsa) okunur. Sırayla, en genelden en spesifiğe:

### 3.1 `CLAUDE.md` (proje kökü)
Projenin AI-asistan davranış kurallarının üst düzey özeti. Genellikle: dil tercihi, kod stili, yasaklı pattern'lar, proje kimliği. Bu dosya varsa, orchestrator'ın kendi kuralları CLAUDE.md ile **çelişemez** — çelişki varsa CLAUDE.md önceliklidir ve kullanıcıya bildirilir.

### 3.2 `AGENTS.md` (proje kökü)
Agent tanımlarının kök seviyedeki özeti — hangi agent'ların var olduğu, temel sorumlulukları. `.ai/AGENTS.md`'nin kısa versiyonu gibi düşünülebilir; ayrıntı için oraya bakılır.

### 3.3 `WORKFLOW.md` (proje kökü)
İş akışı kuralları: commit mesaj formatı, branch stratejisi, PR süreci, hangi değişikliklerin onay gerektirdiği. Orchestrator, ADIM O5 (Continuous Execution) sırasında bu kurallara uyar.

### 3.4 `.ai/index.md`
`.ai/` dizini altındaki bilgi tabanının indeksi — hangi dosyanın nerede olduğunun haritası. Orchestrator, Vault taramasını bu index üzerinden yapar (rastgele dosya arama yerine).

### 3.5 `.ai/keys.md`
Config/erişim bilgilerinin **konumlarının** referansı (ör. "DB şifresi .env'de, ENV_DB_PASS anahtarında") — asla gerçek değerler değil. Orchestrator bu dosyayı okurken hiçbir zaman içindeki değerleri çıktıya yazmaz, sadece hangi mekanizmanın kullanılacağını öğrenir.

### 3.6 `.ai/AGENTS.md`
Agent routing'in detaylı matrisi — hangi domain hangi agent'a, hangi aktivasyon kelimeleriyle gider. §5'teki (SKILL.md) tablo bunun özetidir; tam profiller `references/multi-agent-patterns.md`'dedir.

### 3.7 `.ai/brain.md`
Projenin genel hafızası — önemli kararlar, öğrenilen dersler, tekrar sorulmaması gereken sorular. Append-only olması beklenir (mevcut `brain2.md` formatıyla tutarlı, bkz. 3.9). `prompt-maker`'ın kendi `brain2.md`'sinden farkı: `brain.md` tüm proje için genel, `brain2.md` sadece üretilen MASTER PROMPT'lar için özeldir.

### 3.8 `.ai/MEMORY.md`
Oturumlar arası bağlam — bir önceki oturumda neyin yarım kaldığı, hangi kararın beklemede olduğu. Orchestrator bir görevi devraldığında önce burayı kontrol eder ("daha önce bu konuda bir şey konuşulmuş mu?").

### 3.9 `.ai/log.md`
Kronolojik işlem günlüğü, append-only. Mevcut prompt-maker log formatıyla tutarlı bir giriş formatı önerilir:

```
## [YYYY-MM-DD HH:mm:ss]
### Task
### Modified Systems
### Updated Files
### Runtime Impact
### Security Impact
### Updated Wiki Pages
### ADR References
```

### 3.10 Vault Tarama Sınırı

Vault taraması  Orchestrator, `.ai/index.md`'yi kullanarak göreve en ilgili 15-20 dosyayı seçer; tüm depoyu taramaz. Bu sınır aşılırsa, tarama önceliklendirilir: önce yukarıdaki 9 zorunlu dosya, sonra görevin domain'ine özel dosyalar (ör. bir ödeme görevi için `.ai/wiki/payments/*`).

### 3.11 Erişim Yoksa Ne Olur

Bu dosyalardan biri veya birkaçı bulunamazsa (ör. bir dosya-sistemi bağlantısı yoksa, ya da dosya henüz oluşturulmamışsa):

- Orchestrator **durmaz**, ama hangi dosyaların okunamadığını açıkça belirtir.
- Eksik dosyanın içeriğini **varsaymaz** — o dosyanın kapsadığı bilgi alanında ekstra dikkatli olur (ör. `WORKFLOW.md` okunamadıysa, commit/PR kuralları hakkında iddiada bulunmaz, kullanıcıya sorar).
- Kullanıcıya dosyanın gerçek konumunu veya bir erişim mekanizması (ör. FileServer MCP, doğrudan yol) sağlayıp sağlayamayacağı sorulur.

---

## 4. HALÜSİNASYON GÜVEN SKORU — TAM PROSEDÜR

### 4.1 Skorun Kapsamı

Bu mekanizma **iddia bazlıdır** (claim-level), üretilen metnin bütününü değil, içindeki her doğrulanabilir teknik iddiayı ayrı ayrı değerlendirir. Örnek "iddia" birimleri:

```
- "X kütüphanesi Y fonksiyonunu destekler"
- "Z çipi N kanal ses çıkışı sürebilir"
- "PHP'de A fonksiyonu B davranışını gösterir"
- "OWASP şu an için C pratiğini önermektedir"
- "Versiyon D'de bu API deprecated'dır"
```

Genel yorumlar, mimari öneriler veya üslup tercihleri bu skorlamaya girmez — sadece **doğrulanabilir, yanlışsa gerçek zarar verecek** iddialar skorlanır.

### 4.2 Skorlama Kriterleri

```
90-100  Resmi/birincil kaynaktan bu oturumda doğrudan teyit edildi
        (ör. web araştırmasıyla üreticinin datasheet'i, resmi dokümantasyon)

75-89   Güvenilir ikincil kaynaktan teyit edildi (ör. saygın teknik blog,
        üreticinin resmi olmayan ama tutarlı topluluk dokümantasyonu)

60-74   Genel bilgiye dayanıyor, bu oturumda doğrulanmadı ama iç tutarlı
        ve yüksek riskli bir alan değil

40-59   Genel bilgiye dayanıyor VE ya yüksek riskli bir alanda (güvenlik,
        donanım, finans) ya da iç tutarsızlık şüphesi var

0-39    Kaynağı yok, hatırlanan/tahmin edilen bilgi, veya bu oturumda
        çelişkili sinyaller alındı
```

### 4.3 Eşik Aksiyonları

```
Skor ≥ 90     Doğrudan kullan, kaynağını (varsa) dipnot olarak ekle
Skor 60-89    Kullan AMA çıktıda "⚠️ VERIFICATION REQUIRED: [eksik kanıt]" işaretle
Skor < 60     REDDET — iddiayı çıktıya yazma, .ai/knowledge/rejected/ içine
              {tarih}-{konu}.md olarak kaydet, kullanıcıya "bu konuda emin
              değilim, doğrulamam gerekiyor" de
```

`.ai/knowledge/rejected/` girişi formatı:

```markdown
## [YYYY-MM-DD HH:mm:ss] — Reddedilen İddia
**İddia:** {tam iddia metni}
**Neden reddedildi:** {kaynak yok / çelişki / yüksek risk + düşük güven}
**Skor:** {0-59}
**Sonraki adım:** {kullanıcıdan ne isteniyor, veya hangi araştırma yapılmalı}
```

### 4.4 Worked Examples (Kalibre Edici Örnekler)

**H001 — Donanım (gerçek vaka):**
PCM5122, 2 kanallı bir DAC'tir; 8.1 surround (8 kanal) çıkışı **süremez**. 8 kanallı çıkış için PCM3168A (8-ch DAC) veya AKM AK4458 gibi çok kanallı bir çip gerekir. Skor: bu iddia teyit edilmeden kullanılırsa 0-39 aralığında olurdu (donanım seçimi yanlışsa maliyetli); üretici datasheet'iyle teyit edilirse 90-100.

**H002 — PHP davranışı:**
`array_unique()` varsayılan olarak `SORT_STRING` karşılaştırması kullanır; sayısal içerikli dizilerde tip farklılıkları (`"0"` vs `0`) beklenmedik birleşmelere yol açabilir. Bu, sık yanlış hatırlanan bir davranıştır — PHP resmi dokümantasyonuyla teyit edilmeden "elbette böyle çalışır" denip geçilmemelidir.

**H003 — Kriptografi:**
"AES-256 kullanıyoruz" ifadesi tek başına yeterli değildir; ECB modu pattern sızdırır ve güvensiz kabul edilir. Skorlanan iddia "AES-256" değil, "AES-256-GCM" (veya ChaCha20-Poly1305) olmalıdır — mod belirtilmeden yapılan güvenlik iddiaları düşük skorlanır.

**H004 — Framework/versiyon:**
Bir kütüphanenin "en güncel API'si" iddiası, bu oturumda web araştırmasıyla teyit edilmeden kullanılamaz — eğitim verisi tarihli bilgi, kütüphane hızlı güncellenen bir alan (ör. bir JS framework) için güvenilmez kabul edilir ve otomatik olarak 60'ın altına düşer.

**H005 — Standart/uyumluluk iddiası:**
"WCAG 2.2 AA gerektiriyor ki..." gibi bir standart iddiası, standardın kendisine (W3C) referans verilmeden yapılırsa 60-74 aralığında kalır (genel bilgiye dayanır); doğrudan W3C metniyle teyit edilirse 90+.

### 4.5 Quality Score ile Farkı (Tekrar, Netlik İçin)

| | Halüsinasyon Skoru | Quality Score (references/09) |
|---|---|---|
| Birim | Tek bir teknik iddia | Üretilen MASTER PROMPT'un tamamı |
| Ne zaman uygulanır | Orchestrator modunda, her iddia için sürekli | Çekirdek modda, ADIM 7 sonunda bir kez |
| Aksiyon eşiği | <60 reddet | <85/100 (kategori bazlı) yeniden işle |
| Amaç | Doğruluk (accuracy) | Bütünlük, netlik, üretim-hazırlık |

---

## 5. COREMUSIC 8-AGENT ROUTING — KARAR MANTIĞI

Ajanların tam profilleri için `references/multi-agent-patterns.md`'ye bakın; bu bölüm sadece **routing karar mantığını** açıklar.

### 5.1 Routing Karar Ağacı

```
1. Görev metnini domain anahtar kelimeleriyle eşleştir (SKILL.md §5.4 tablosu)
2. Tek domain eşleşiyorsa → o agent Lead Agent olur, devam et
3. Birden fazla domain eşleşiyorsa:
   a. Hangi domain'in "sahiplik" taşıdığını belirle (ör. bir API endpoint
      güvenlik gerektiriyorsa ama asıl iş Backend'e ait → Backend Lead,
      Security Engineer destek/review rolünde)
   b. Coordination pattern seç: Sequential (bağımlı adımlar) / Parallel
      (bağımsız adımlar) / Hierarchical (alt-görevlere bölünebiliyorsa) /
      Review & Approve (yüksek riskli değişiklik) — bkz. multi-agent-patterns.md
4. Hiçbir domain net eşleşmiyorsa → DUR, kullanıcıya hangi domain
   olduğunu sor (HARD-09, bkz. §7)
5. Seçilen agent(lar)ı ve coordination pattern'i §8'deki Output
   Protokolü'nün "1. Domain Ataması" bölümünde açıkça belirt
```

### 5.2 Çok-Agent Görevlerde Lead Belirleme

Lead agent, görevin **en çok değiştireceği** dosya/sistemin sahibi olan agent'tır. Diğerleri "destek" rolündedir ve kendi domain'lerine giren kısımları review eder, ama Lead'in çıktısını override etmez — anlaşmazlık varsa Review & Approve pattern'ine geçilir (bkz. multi-agent-patterns.md PATTERN 4).

---

## 6. 5 FAZLI ORCHESTRATION PIPELINE — TAM DETAY

SKILL.md §3.2'deki özet burada açılır. Her faz, kendi çıktı kriteriyle birlikte:

### FAZ O1 — Problem Definition & Scope Analysis

```
1. Kullanıcı isteğini analiz et, çekirdek domain'i belirle
2. Vault mandate dosyalarını oku (§3, maksimum 15-20 dosya)
3. Görev kapsamını tek cümlede özetle ve kullanıcıya doğrula
   (yanlış kapsam varsayımı, sonraki tüm fazları geçersiz kılar)
```
**Çıktı kriteri:** Kapsam cümlesi kullanıcı tarafından reddedilmedi.

### FAZ O2 — Mandatory Knowledge Acquisition (Vault + Web, Paralel)

```
1. Vault'tan ilgili dosyaları oku (§3.4 index üzerinden)
2. SKILL.md §5.5'teki hacim tablosuna göre web araştırması başlat
3. Vault ve web sonuçları çelişiyorsa: Vault önceliklidir (proje-özel
   kurallar genel best-practice'i ezer) — ama çelişki kullanıcıya bildirilir
```
**Çıktı kriteri:** Araştırma hacmi hedefine ulaşıldı VEYA kullanıcıya
neden ulaşılamadığı açıklandı.

### FAZ O3 — Agent Delegation

```
1. §5'teki karar ağacını uygula
2. Lead + destek agent(lar)ı belirle
3. Coordination pattern seç
4. HARD-09 kontrolü: agent ataması yapılmadan bir sonraki faza geçilmez
```
**Çıktı kriteri:** En az bir agent atanmış durumda.

### FAZ O4 — Ultrathink Validation & Planning

```
1. Multi-domain analiz: mimari, güvenlik, performans, UI/UX açılarından
   görevi değerlendir (ilgisiz olanları atla, hepsini zorla doldurma)
2. Edge-case discovery: "bu değişiklik neyi kırabilir?" sorusunu sor
3. Alternatif yaklaşımları karşılaştır (en az 2, tek çözüm dayatma)
4. Bir uygulama planı yaz
5. Planlama modu aktifse (kullanıcı onay istemişse) onay bekle;
   değilse FAZ O5'e geç
```
**Çıktı kriteri:** Plan yazıldı ve (gerekiyorsa) onaylandı.

### FAZ O5 — Continuous Execution & Self-Healing

```
1. Planı uygula: SOLID, Clean Code, Katmanlı Mimari, OWASP Top 10:2025
2. Sürekli test (varsa proje test altyapısı: PHPUnit, Vitest, Playwright)
3. Hata çıkarsa: self-heal (kendi hatasını analiz edip düzelt), sonsuz
   döngüye girme — 3 başarısız denemeden sonra kullanıcıya durum bildir
4. .ai/brain.md ve .ai/log.md güncelle (§3.7, §3.9 formatları)
```
**Çıktı kriteri:** Görev tamamlandı VE log/brain güncellendi VEYA
neden tamamlanamadığı açıkça raporlandı.

---

## 7. HARD RULE ENTEGRASYONU — TAM DETAY

`references/validation-engine.md`'nin mevcut `HARD-01`...`HARD-07` sistemine eklenen kurallar:

```
HARD-08: Halüsinasyon Skoru <60
  Koşul:   §4.2'deki skorlama 60'ın altında bir değer üretti
  Aksiyon: REDDET, .ai/knowledge/rejected/'a §4.3 formatıyla kaydet
  Mesaj:   "Bu bilgiyi doğrulayamadım, tahmin etmeyeceğim. [konu] için
            ek araştırma yapmamı ister misiniz?"

HARD-09: Agent Routing Atlanmış
  Koşul:   Orchestrator modu aktif (FAZ O3 başladı) ama görev hiçbir
           agent'a atanmadan FAZ O4'e geçmeye çalışılıyor
  Aksiyon: DUR
  Mesaj:   "Orchestrator modunda her görev bir agent'a atanmalı.
            Domain belirsiz: [seçenekleri listele], hangisi?"
```

Gelecekte eklenecek yeni hard rule'lar için şablon (validation-engine.md ile
aynı formatta, numaralandırma HARD-10'dan devam eder):

```
HARD-NN: {Kısa Ad}
  Koşul:   {Ne zaman tetiklenir}
  Aksiyon: {DUR / REDDET + logla / REDDET}
  Mesaj:   {Kullanıcıya gösterilecek tam mesaj}
```

---

## 8. ÇIKTI PROTOKOLÜ — TAM ÖRNEKLER

### 8.1 Şablon (SKILL.md §5.7'nin tekrarı, tam bağlamla)

```markdown
# 🎼 CoreMusic Orchestrator Aktif

**Durum:** Orchestration Mode Active
**Halüsinasyon Kontrolü:** STRICT (0-100 skorlama aktif)
**Web Araştırması:** Zorunlu (bkz. SKILL.md §5.5)

## 1. Domain Ataması
[Lead + destek agent(lar), coordination pattern]

## 2. Araştırma Protokolü
[Okunan Vault dosyaları + web kaynak sayısı/kategorisi]

## 3. Analiz & Plan
[FAZ O4 çıktısı — plan, alternatifler, edge-case'ler]

## 4. Uygulama
[Doğrudan uygulama ya da onay bekleme]
```

### 8.2 Worked Example — Tek Agent

**Görev:** "CoreMusic'te kullanıcı profil endpoint'ine rate limiting ekle."

```markdown
# 🎼 CoreMusic Orchestrator Aktif

**Durum:** Orchestration Mode Active
**Halüsinasyon Kontrolü:** STRICT
**Web Araştırması:** Zorunlu — Orta karmaşıklık (min 200 kaynak)

## 1. Domain Ataması
Lead: Backend Architect. Destek: Security Engineer (rate limiting bir
güvenlik kontrolü olduğu için review rolünde).

## 2. Araştırma Protokolü
Vault: CLAUDE.md, .ai/AGENTS.md, .ai/brain.md (rate limiting daha önce
tartışılmış mı diye kontrol edildi — bulunamadı). Web: PHP rate-limiting
middleware pattern'ları, OWASP API Security Top 10 rate-limiting maddesi.

## 3. Analiz & Plan
Middleware katmanına eklenecek (mevcut Handler → Service → Repository
mimarisini bozmadan). Alternatif 1: sabit pencere sayacı (Redis).
Alternatif 2: token bucket (Redis). Token bucket seçildi — patlama
trafiğini daha iyi tolere eder. Edge-case: birden fazla IP arkasında
tek kullanıcı (proxy) — X-Forwarded-For doğrulaması eklenecek.

## 4. Uygulama
Onay bekleniyor: Redis tabanlı token-bucket middleware, endpoint başına
60 istek/dakika. Onaylanırsa Backend Architect uygulamaya başlar.
```

### 8.3 Worked Example — Çok Agent (Reddedilen İddia İçeren)

**Görev:** "Ses çıkışı için yeni bir DAC seçip 8 kanallı ev sinema desteği ekleyelim."

```markdown
# 🎼 CoreMusic Orchestrator Aktif

**Durum:** Orchestration Mode Active
**Halüsinasyon Kontrolü:** STRICT
**Web Araştırması:** Zorunlu — Kritik donanım (min 500 kaynak)

## 1. Domain Ataması
Lead: Embedded/Firmware Engineer. Destek: Backend Architect (API
entegrasyonu), QA Engineer (donanım-yazılım entegrasyon testi).

## 2. Araştırma Protokolü
Vault: CLAUDE.md, .ai/AGENTS.md, 14-embedded-audio-electronics.md. Web:
DAC üretici datasheet'leri (çoklu kaynak), 8-kanal audio codec
karşılaştırmaları.

## 3. Analiz & Plan
⚠️ Reddedilen iddia: "PCM5122 8 kanal destekler" — bu iddia H001 (bkz.
references/00-agentic-orchestrator-layer.md §4.4) ile çelişiyor; PCM5122
2 kanallıdır. .ai/knowledge/rejected/2026-08-02-pcm5122-8ch.md'ye
kaydedildi. Doğrulanmış alternatif: PCM3168A (8 kanal, üretici
datasheet'i ile teyitli, skor 95).

## 4. Uygulama
Plan revize edildi: PCM3168A üzerinden devam edilsin mi, yoksa başka bir
8-kanal DAC mi tercih edilir? Onay bekleniyor.
```

---

### 8.4 Worked Example — Vault Erişimi Kısmen Başarısız

**Görev:** "Ödeme akışına 3D Secure ekle, orchestrator modunda."

```markdown
# 🎼 CoreMusic Orchestrator Aktif

**Durum:** Orchestration Mode Active (kısıtlı Vault erişimiyle)
**Halüsinasyon Kontrolü:** STRICT
**Web Araştırması:** Zorunlu — Kritik güvenlik/finans (min 500 kaynak)

## 1. Domain Ataması
Lead: Security Engineer. Destek: Backend Architect, QA Engineer.

## 2. Araştırma Protokolü
⚠️ Vault kısmi erişim: CLAUDE.md ve .ai/AGENTS.md okundu; `.ai/keys.md`
dosya-sistemi bağlantısı olmadığı için okunamadı. Bu nedenle mevcut
ödeme sağlayıcısının API anahtarı konumu hakkında hiçbir varsayımda
bulunulmuyor — kullanıcıdan teyit isteniyor. Web: PCI-DSS 3D Secure 2
gereksinimleri, sağlayıcıya özel entegrasyon dokümantasyonu.

## 3. Analiz & Plan
Plan, `.ai/keys.md` teyidi gelene kadar "anahtar yönetimi" adımı
BOŞLUK olarak işaretlendi; geri kalan akış (3DS challenge/frictionless
flow, webhook doğrulama) tamamlandı.

## 4. Uygulama
Onay + eksik bilgi bekleniyor: "`.ai/keys.md` dosyasına nasıl erişebilirim
(yol/mekanizma)? Bu olmadan anahtar yönetimi adımını tahmin
etmeyeceğim."
```

Bu örnek, §3.11'deki kuralın pratikte nasıl işlediğini gösterir: erişim
eksikliği görevi durdurmaz, ama etkilenen alanda sessiz varsayım da
yapılmaz.

### 8.5 Ek Kalibrasyon Örnekleri (H006-H008)

**H006 — Performans iddiası:** "Bu sorgu index kullanıyor" iddiası,
`EXPLAIN`/`EXPLAIN ANALYZE` çıktısı görülmeden 60'ın altında kalır —
sorgu planı varsayılamaz, çalıştırılıp doğrulanmalıdır.

**H007 — Tarayıcı uyumluluğu:** "Bu CSS özelliği tüm modern
tarayıcılarda destekleniyor" iddiası, caniuse.com veya MDN uyumluluk
tablosu kontrol edilmeden kullanılmaz — "modern" göreceli bir terimdir
ve WebKit/Chromium/Firefox arasında fark olabilir.

**H008 — Lisans/uyumluluk iddiası:** Bir üçüncü parti kütüphanenin
lisansı hakkında iddia (ör. "MIT, ticari kullanıma uygun"), kütüphanenin
kendi `LICENSE` dosyası veya paket kayıt sayfası görülmeden 60'ın altında
kalır — lisanslar sürüm bazında değişebilir.

---

## 9. ORCHESTRATOR MODUNUN KENDİ ÖZ-DENETİMİ

FAZ O5 tamamlandıktan sonra, orchestrator kendi çalışmasını şu kısa
listeyle denetler (bu, `references/09-quality-scoring-rubric.md`'nin
8 kategorili çıktı denetiminden ayrı, orchestrator'a özel bir kontrol
listesidir):

```
[ ] Vault mandate dosyaları okundu mu, okunamayanlar açıkça belirtildi mi?
[ ] En az bir agent atandı mı (HARD-09)?
[ ] Skor <60 olan hiçbir iddia çıktıya sızmadı mı (HARD-08)?
[ ] Araştırma hacmi SKILL.md §5.5 tablosuna uygun muydu?
[ ] .ai/brain.md ve .ai/log.md güncellendi mi?
[ ] Kullanıcıya sunulan çıktı §8.1'deki şablonu takip etti mi?
```

Bu listeden herhangi biri "hayır" ise, görev tamamlanmış sayılmaz —
eksik adım tamamlanır veya kullanıcıya neden tamamlanamadığı açıkça
raporlanır. Sessizce atlanmaz.

---

## 10. NE ZAMAN ORCHESTRATOR MODU KULLANILMAMALI

```
Görev tek dosyalık, geri alınması kolay, düşük riskli bir düzeltme
Kullanıcı zaten net bir agent/domain belirtmiş ve routing'e gerek yok
Zaman baskısı var ve görev gerçekten basit (Vault + 5 faz overhead'i
  görevin kendisinden daha uzun sürer)
Kullanıcı açıkça "orchestrator olmadan, direkt yap" demiş
```

Bu durumlarda SKILL.md §3.3'teki "sadece çekirdek" veya doğrudan görev
yürütme moduna dönülür.

---

## 10. SÜRÜM NOTU

Bu dosya, önceki `ai-agentic-orchestrator` SKILL.md'sinin (v3.1.0)
içeriğini kapsar ve genişletir. Tam kaynak/birleştirme kararları:
`CHANGELOG.md`.
