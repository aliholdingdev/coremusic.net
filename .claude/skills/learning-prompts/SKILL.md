---
name: learning-prompts
description: "Use when the user wants to learn or test a topic with active learning methods instead of getting direct answers — Sokratik soru zinciri, Feynman anlatımı, Şeytanın Avukatı eleştirisi ve Aktif Hatırlama sınavı üretir. Tetikleyiciler: 'sokratik', 'sokratik yöntem', 'feynman', 'feynman tekniği', 'şeytanın avukatı', 'aktif hatırlama', 'beni sına', 'sınav yap', 'cevap verme soru sor', 'öğrenmemi sağla'."
license: MIT
metadata:
  version: 4.0.0
  previous-version: 1.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: learning-protocols
  tags: [learning, active-recall, socratic, truth-mode]
  updated: 2026-10-21
---

# learning-prompts — 4 Aktif Öğrenme Protokolü (Otonom Oturum Yürütücüsü)

> **Tek cümlede:** Bu skill, yapay zekâyı "cevap veren" değil **antrenör** yapan 4 hazır
> protokolü (Sokratik · Feynman · Şeytanın Avukatı · Aktif Hatırlama) **onaylı kurallarla
> (R1-R7) ve kanıt disiplinyle (Truth Mode, K1-K9)** yürüten oturum kütüphanesidir.
>
> **Ne değildir:** kod editörü değildir · prompt-maker değildir (orada ham istek → görev
> prompt'u üretilir; burada hazır öğrenme protokolü **çalıştırılır**) · cevap veren
> asistan değildir (Sokratik/Feynman'da cevap vermek yasaktır).

---

## §1 Genel Bakış

### 1.1 Amaç ve ham girdi

| Alan | Değer |
|------|-------|
| Amaç | Öğrenme/sınamak isteyen kullanıcıya 4 protokolü **kopyala-yapıştır** hazırda vermek ve oturumu kurallara uygun yürütmek |
| Ham girdi (kaynak metin) | Halil Özat, "Yapay Zekâyla Akıllanmak İçin 4 Prompt" (9 Ekim 2026) — 4 ham prompt |
| İlk sürüm | 1.0.0 (2026-10-09) — revizyonlar: zero-hallucination, 0-10 rubrik, ipucu kademesi, rapor formatı |
| Bu sürüm | **4.0.0 (2026-10-21)** — komple yeniden yazım + Truth Mode zorunlu hâle getirildi (kaynak defteri `sources.md` K1-K9) |
| Dil | Türkçe oturum (kullanıcı diliyle); tetikleyiciler Türkçe |
| Değişmeyenler (dokunulmaz) | klasör adı `learning-prompts` · frontmatter `name` · Türkçe tetikleyiciler · MIT · Ham girdi künyesi |

### 1.2 Dört protokol — tek ekran haritası

| § | Protokol | Ne zaman | Çıktı | Kanıt düzeyi ( dürüst konumlandırma ) |
|---|----------|----------|-------|--------------------------------------|
| 1 | **Sokratik Yöntem** | Takıldım; cevabı **kendim** bulacağım | kullanıcının kendi çözümü | Otoriter uygulama geleneği (K3 · K4) — "kanıtlanmış" **denmez** |
| 2 | **Feynman Tekniği** | Öğrendiğimi sandığım konuyu **test** etmek | eksik/noksan raporu + 0-10 güven puanı | Paket sonradan popülerleştirildi (K8); **parçaları** kanıtlı (K1 moderate), paketin büyük-ölçekli etki kanıtı **yok** |
| 3 | **Şeytanın Avukatı** | Karar/güçlü fikir öncesi **karşı taraf** | puanlı itiraz seti + aranacak kanıt | Klasik kanıt pozitif (K5), yakın denemeler **karışık** (K6 · K7) — "garanti" **denmez** |
| 4 | **Aktif Hatırlama** | Çalıştım; **kalıcı** olsun, beni sına | puan + yanlış tekrarı + dağıtık tekrar listesi | **En yüksek kanıt**: practice testing + distributed = HIGH (K1 · K2) |

**Öncelik sırası (kanıt düzeyi):** #4 > #3 (klasik) > #2 (parça) > #1 (geleneğe dayalı).
**Önemli ayrım:** kanıt düzeyi ≠ kullanım sıklığı. Doğru sorunu çözmediğin sürece en
kanıtlı protokol de işe yaramaz (anti-pattern #11 → §3.3 eşleşme tablosu).

### 1.3 Kanıt hiyerarşisi (bu skill'de bağlayıcı sıra)

```text
1. Kullanıcının kendi verdiği notlar/kaynak (oturum içi veri)  — puanlama bununla sınırlı (R5)
2. K1 · K2 · K3 · K4 · K5 (birincil doğrulanmış — sources.md §1) — kuram/teknik iddiaları
3. K6 · K7 (karışık sonuç — dürüstlük notu ile)                 — abartı önleyici referans
4. K8 [POPÜLER] · K9 [İKİNCİL ⚠️]                              — yalnız destekleyici not
5. Model hafızası / web geneli                                 — KAYNAK DEĞİL; doğrulanmazsa sil
```

- Çelişki gizlenmez (ör. K5 vs K6/K7): oturumda **iki taraf da** söylenir.
- `⚠️ VERIFICATION REQUIRED` etiketli iddia skill metnine/oturuma **sayısal olarak**
  taşınmaz; yalnızca işaretli olarak kalır.
- Tam defter: `references/sources.md` (erişim tarihi 2026-10-21).

### 1.4 Ne değişti (v1.0.0 → v4.0.0, özet)

| Alan | 1.0.0 | 4.0.0 |
|------|-------|-------|
| SKILL.md | 77 satır | bu dosya (500-2000 satır aralığı — N3) |
| Kaynak | tek dayanak (Dunlosky) | K1-K9 defteri + güven etiketleri + çelişki kuralı |
| Kurallar | R1-R5 | **R1-R7** (+R6 bekleme/özet · +R7 güvenlik yüzeyi) |
| Anti-pattern | 10 | **13** + ağırlık sırası + düzeltici eylem |
| Roediger 2006 | ⚠️ doğrulanmadı | **K2 olarak doğrulandı — ⚠️ kalktı** |
| Örnek | kısmi döngüler | 4 protokol **tam girdi→çıktı** + yanlış yürütüm kontrolleri |

Geçmişin tamamı: `references/changelog.md`.

### 1.5 Terimler sözlüğü (oturumda geçen kavramlar)

| Terim | Ne demek | K/R bağlantısı |
|-------|----------|----------------|
| **Practice testing (aktif hatırlama)** | "yineleme oku" yerine kendini sınamak — sınav olmak öğrenmeyi kendisi artırır | K1 HIGH · K2 |
| **Testing effect** | Sınav eyleminin kendisinin kalıcı öğrenme kazancı (1 hafta sonra %56 vs %42) | K2 |
| **Distributed practice (dağıtık tekrar)** | Çalışmayı zamana yayma (günde 2 soru × 3 gün) — HIGH utility | K1 · R4 |
| **Rereading (tekrar okuma)** | Metni tekrar okumak — 10 teknik arasında LOW; kısa vadede güveni şişirir | K1 · K2 |
| **Self-explanation** | Kendine açıklama üretme (Feynman'ın 2-3. maddelerinin dayanağı) — MODERATE | K1 · K9 ⚠️ |
| **Consider-the-opposite** | Aynı kanıt ters çıksaydı da aynı kararı verir miydin — önyargıya karşı **stratejik** talimat | K5 (K6/K7 karışık) |
| **Rubrik (0-10)** | Tek puanlama ölçeği + zorunlu gerekçe — ölçekler birbirine karıştırılmaz | R3 |
| **Kademe (ipucu)** | Sokratik ipucunun 0→1→2→3 büyüyen sırası; 3'ten sonra ancak açık rıza | R2 |
| **Protégé / öğretme-beklentisi** | "Birine anlatacakmış gibi" hazırlığın da öğrenmeyi artırdığı iddiası | K8/K9 — ⚠️, sayısal etki taşınmaz |
| **Zero-Hallucination** | Olgu uydurmak yok: `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` | R5 · skill-maker §6 |
| **Truth Mode** | Her teknik/olgu iddiası K-numaralı kaynağa ya da ⚠️ etiketine bağlı | `sources.md` · §5 |
| **Oturum turu** | 1 soru (ya da 1 itiraz seti) + cevap + geri bildirim — zincirin bir halkası | R1 |

---

## §2 Tetikleyiciler

### 2.1 Ana eşleşme tablosu (N10 — description ile senkron)

| # | Tetikleyici (kullanıcı cümlesi / anahtar kelime) | Sınıf | Bu skill'in verdiği | Yönlendirme |
|---|--------------------------------------------------|-------|----------------------|-------------|
| T1 | 'sokratik' · 'sokratik yöntem' · 'cevap verme soru sor' | takılma + cevapsız mod | §1 Sokratik protokolü | `references/prompts.md` §1 |
| T2 | 'feynman' · 'feynman tekniği' · 'çocuğa anlatır gibi' | anlatım denetimi | §2 Feynman protokolü | `references/prompts.md` §2 |
| T3 | 'şeytanın avukatı' · 'itiraz et' · 'karşı argüman' | karar öncesi | §3 Şeytanın Avukatı | `references/prompts.md` §3 |
| T4 | 'aktif hatırlama' · 'beni sına' · 'sınav yap' · 'öğrenmemi sağla' | sınav/kalıcılık | §4 Aktif Hatırlama | `references/prompts.md` §4 |
| T5 | 'hangi tekniği kullanayım' · 'sınav mı takılma mı' | seçim belirsizliği | **tek** soru ile netleştirme | `overview.md` §3 |
| T6 | 'cevabı ver' · 'çözümü yaz' (tek başına) | doğrudan cevap | protokolün amacı tersi → protokolü **öner**, cevap yasağıyla çelişen modda **dur** | §3.4 |

### 2.2 Öncelik ve çakışma kuralları

1. **Kullanıcı niyeti > anahtar kelime.** "şeytanın avukatı" deyip de aslında "beni
   sınamanı istiyorum" diyen varsa T4 geçerlidir; belirsizlik = **tek soru** (T5).
2. **Birden çok tetikleyici aynı anda** (ör. "sokratik sorularla beni sınamaya al"):
   protokoller **zincirlenebilir**, ama her biri kendi kurallarıyla yürütülür;
   **önce hangisi** belirsizse tek soru sorulur (zincir sırası kullanıcıya aittir).
3. **T6 çatışması:** "cevap ver" + Sokratik aynı anda **yürütülemez** — kullanıcıya
   durum tek soruyla açılır: "cevabı hızlı mı almak istersin, yoksa bulmak mı
   istersin?" Cevap "hızlı al" ise bu skill **oturumu başlatmaz** (protokol seçilmedi);
   cevap "bulmak" ise T1 (anti-pattern #11 kapanır).
4. **Prompt-maker ayrımı:** kullanıcı "bana görev promptu tasarla / storyboarding"
   diyorsa prompt-maker; "beni sınamak / öğret / takıldım" diyorsa bu skill. İkisi de
   uyuyorsa önce kullanıcıya niyet sorulur (tek soru).

### 2.3 Tetiklenmeyen durumlar (bu skill'in kapsam dışı)

| Durum | Neden kapsam dışı | Ne yapılır |
|-------|-------------------|------------|
| Doğrudan cevap/çözüm/kod isteği (öğrenme niyeti yok) | protokoller cevap vermeyi reddeder | cevap verilir (normal görev) — protokol **başlatılmaz** |
| Kullanıcı 1. turda "boş ver, soru sorma, cevabı yaz" | cevap yasağı protokolün çekirdeği | protokol önerilmez; istek aynen karşılanır (öğrenme protokolü **dayatılmaz**) |
| Sınav içeriği yok + not da yok (Aktif Hatırlama) | sorular kaynağa dayanmazsa uydurma olur (R5) | "notlarını/yapıştır" diye tek istek; içerik gelmeden soru üretilmez |
| Yıkıcı/süreç kararı ("migration'ı sil, deploy et") | protokoller salt-okunur etkileşimdir (R7) | DUR + onay; protokol değil süreç izlenir |

### 2.4 Gerçekçi tetikleyici cümle setleri (eşleşme referansı)

> Bu cümleler **eğitimsel referanstır** — eşleşmede kelime kelime değil **niyet** okunur.
> Eşleşmeyen bir cümle geldiğinde §2.3 kapsam dışı tablosu veya tek soru (T5) geçerlidir.

| Protokol | Sık gelen cümleler (örnek — kapalı liste değil) |
|----------|--------------------------------------------------|
| §1 Sokratik | "cevabı verme, soruyla yönlendir" · "sokratik sorularla yürüt" · "nedenini kendim bulacağım, ipucu ver" · "çözümü söyleme, ben çözeceğim" |
| §2 Feynman | "konuyu çocuğa anlatır gibi özetleyeyim, sen denetle" · "anlatımımı kontrol et" · "bunu gerçekten bildiğimi anlatayım, eksiklerimi bul" · "feynman yapalım" |
| §3 Şeytanın Avukatı | "kararıma itiraz et" · "karşı argüman kur" · "görüşümün zayıf yerlerini göster" · "kararı sana danışmadan önce beni zorla" · "şeytanın avukatı ol" |
| §4 Aktif Hatırlama | "bu notlardan beni sına" · "8 soruluk sınav yap" · "puanla ve yanlışlarımı tekrar sor" · "unutmadan önce test et" · "soruları tek tek sor" |
| T5 (seçim) | "hangisini kullanayım" · "önce feynman mı aktif hatırlama mı" · "beni mi sınamalısın yoksa anlatımı mı denetlemeli" |
| T6 (çatışma) | "soru sorma, cevabı yaz" · "en baştan cevabı ver, öğrenmek istemiyorum" — burada protokol **önerilir**, dayatılmaz |

**Eşleştirme güveni düşükse** (kelimeler var ama niyet başka): tek soru —
"Şunu mu istiyorsun: (a) takıldığın yeri birlikte açmak, (b) anlatımını denetlemek,
(c) kararına itiraz etmek, (d) notlarından sınamak?" — dört seçenek tek sorudur (R1 uyumlu).

---

## §3 Otonom Çalışma Protokolü

> Her görev bu sıradan geçer. Adım arası **geçiş kriteri** yoksa bir sonraki adıma
> atlanmaz; adımlar kısa ve tekrarlanabilir yürütülür.

### 3.1 Adım adım akış

```text
(0) SKILL KONTROLÜ   → görev öğrenme/sınamak mı? (§2 tablo)          → değilse: kapat, kapsam dışı de
(1) ANALİZ           → tetikleyici eşleştirme + niyet sınıflandırma (§2.1)
(2) SEÇİM            → protokol seç; belirsizse TEK soru (T5)         → cevap gelmeden protokol kurulmaz
(3) TRUTH ÖN-KONTROL → bu oturumda olgu iddiası gerekecek mi?         → gerekecekse K-defteri hazırla (§5)
(4) EXECUTION        → prompts.md'den prompt + … yer tutucularını kullanıcı bilgisiyle doldur
(5) YÜRÜTME         → R1-R7 + anti-pattern denetimi tur tur          → ihlal = §D düzeltici eylem
(6) KAPANIŞ         → checklist A (oturum) → R4 raporu (gerekliyse)    → çıkış raporu (§6)
```

**Adım 0 ayrıntısı (skill eşleşmesi):** Bu dosya zaten yüklendiyse adım 0 "tetikleyici
gerçekten var mı?" sorusudur. Varsayım yok: 'beni sına' örneğin sıradan bir "kontrol et"
değildir — **öğrenme protokolü** kurulur; ama "kodu test et" ise yazılım testidir, bu
skill değil (yanlış sınıflandırma = anti-pattern #11).

**Adım 2 ayrıntısı (tek soru ekonomisi):** Belirsizlikte **tek** soru sorulur (R1 zaten
tek soru kuralıdır — seçim sorusu da bir sorudur). Aynı turda 2 seçenek + 1 açıklama
**yazılır** (iki soru sayılmaz; soru teki, seçenekleri eki).

**Adım 4 ayrıntısı (yer tutucu doldurma):**
- `…` alanları **kullanıcının verdiği bilgiyle** doldurulur; kullanıcı vermediyse
  boş bırakılıp 1. turda istenir ("şu an nerede takıldın?" sorusu = kademe 0).
- Model bilgisiyle **uydurma** yapılmaz (boş `…` ile devam = R5 ihlali değil, ama
  checklist B'de "doldurulmadı" notu düşülür ve 1. turda kapatılır).

### 3.2 Protokol başına yürütme kontrol listesi (özet — tam metin `prompts.md`'de)

**§1 Sokratik:**
- [ ] Cevap/düzeltilmiş hâl **hiç** yok (R2-4 öncesi) · tek soru (R1) · kademe takibi (R2)
- [ ] Kullanıcı doğru yoldaysa **açıkça** söylendi (K3 disiplin + anti-pattern #6)
- [ ] Olgu "bilmiyorum/UNKNOWN" (R5) — cevap vermek için kullanılmadı

**§2 Feynman:**
- [ ] Anlatım **notsuz** alındı (K8) · maddeler 1-4 **sırayla tam** (anti-pattern #13)
- [ ] Her maddeye 0-10 güven puanı (R3 ölçeği) · dengeli rapor (sağlam + eksik)
- [ ] "Kanıtlanmış" satışı yok; paket dürüstlüğü (K8/K1) korundu

**§3 Şeytanın Avukatı:**
- [ ] 6 madde tamamı · her itiraz 1-10 puanlı · zayıf = 1-3 etiketli (anti-pattern #8)
- [ ] "Tarafsız ol" genel telkini yok — **yapısal** itiraz (K5) · aranacak kanıt test edilebilir (madde 4)
- [ ] Sağlam taraflar yazıldı (madde 5) · K5/K6/K7 çelişkisi gizlenmedi (madde 6)

**§4 Aktif Hatırlama:**
- [ ] Sorular yalnız yapıştırılan içeriğe dayandı; dış bilgi "bu içerik dışında" (R5)
- [ ] Tek tek soru · şık yok · ipucu önce yok (anti-pattern #4)
- [ ] Puan + gerekçe + (tek istisna) kısa düzeltme (R3)
- [ ] Yanlışlar **farklı ifadeyle** tekrar · R4 raporu (anti-pattern #10)

### 3.3 Durum ↔ protokol eşleşme tablosu (anti-pattern #11 panzehiri)

| Kullanıcı durumu | Doğru protokol | Kaçın (yanlış protokol) |
|-------------------|----------------|--------------------------|
| "Cevabı ver" (hızlı çözüm, öğrenme niyeti yok) | protokol **önerilmez** — istek aynen karşılanır | Sokratik başlatıp cevap sızdırmak (ikisi birden imkânsız) |
| Takıldım, ipucu istiyorum | §1 (kademe 1→2→3) | §4 sınavı (takılmayı sınavla gizlemek) |
| Takıldım, cevabı tartışalım | §1 + R2-4 (**açık rıza**) | rızasız cevap vermek |
| "Öğrendim, gerçekten mi?" | §2 anlatım denetimi | §1 (denetim soru-cevapla yapılmaz) |
| "Çalıştım, unutuyorum" | §4 + R4 raporu | §2 (anlatım tekrarı kalıcılaştırmaz — sınav gerekir) |
| Karar öncesi karşı görüş | §3 | kararın kendisini modele vermek (karar kullanıcıya aittir) |
| Emin değilim | **tek** soru (T5) | protokol dayatmak / iki protokolü karıştırmak |

### 3.4 Çok-turlu oturum dinamiği (turn-taking)

| Kural | Uygulama | Dayanak |
|-------|----------|---------|
| Bekleme | Kullanıcı cevabı yoksa **aceleye getirme**; "düşün, sonra yaz" (≥30 sn normalize) | K4 · R6 |
| Zincirleme | Bir tur = 1 soru (ya da 1 itiraz seti); cevap → puan/geri bildirim → sonraki soru | R1 |
| Özet | ≥5 turluk oturumda anahtar nokta **tek satırlık yazılı özet** (çözüm söylemeden) | K4 · R6 |
| Kopuş | Kullanıcı "bırakalım / başka konu" derse oturum kapanır; kalan tekrar listesi R4'te not edilir | R4 |
| Geri dönüş | Aynı oturum **prompt metniyle** yeniden kurulabilir; puan/özet korunur (context kaybı varsa R4 raporu tekrar okunur) | R4 |
| İsrar | Kullanıcı cevabı ısrarla istiyorsa → T6 durumu: protokol mü, düz cevap mı — **tek soru** | §2.2 #3 |

### 3.5 Hata kurtarma (düzeltici eylem — anti-pattern §D)

```text
İhlal tespit edildi
  → ihlali yapan TURU geri al ("önceki turu geçersiz sayalım")
  → ilgili kuralı (R1-R7) tek cümleyle uygula
  → protokolün kaldığı yerden kurala uygun tek tur ile devam et
  → checklist A'dan ilgili maddeyi yeniden işaretle
```

- Ağırlık sırası: **#1/#7/#9** (protokol-bozan · halüsinasyon · güvenlik) →
  **#2/#3/#4** (akış) → **#5/#6/#8/#10** (kalite) → **#11/#12/#13** (seçim/icra).
- **3 başarısız tur:** aynı düzeltme 3 kez tekrarlanırsa oturum durur; şüpheli varsayım
  açıkça yazılır ("şu adımı tahmin ediyorum, yanlış olabilir") ve kullanıcıya üst karar
  sorulur (tek soru).

### 3.6 Örnek yürütmeye bakış (tam metin `examples/`)

| Örnek | Ne gösterir | Bağ |
|-------|-------------|-----|
| A | Sokratik: 3 tur (kademe 0→1) + **yanlış yürütüm** kontrastı | `examples/01-tam-oturum-ornekleri.md` |
| B | Feynman: BCNF anlatımı → 4 madde denetim + güven puanları | aynı |
| C | Şeytanın Avukatı: ORM kararı → 6 madde puanlı itiraz | aynı |
| D | Aktif Hatırlama: 3 soru sırası + puan + **R4 raporu** | aynı |
| E | Yanlış durum → doğru protokol (anti-pattern #11 düzeltilmiş) | aynı |

### 3.7 İlk tur şablonları (protokol kurulum anı — kopyala-yapıştır)

> Bu şablonlar **1. turun iskeletidir**: protokol seçildikten sonra kullanıcıya
> gönderilen ilk mesaj. `…` alanları §3.1-Adım 4 kuralıyla doldurulur (kullanıcı
> bilgisiyle; veri yoksa istenir). Tam prompt metni `prompts.md`'dedir — burada
> **oturum açılış çerçevesi** (başlık + protokol özeti + ilk soru) vardır.

**Şablon 1 — Sokratik açılışı:**
```text
[Sokratik mod açık] Cevabı vermiyorum — sorularla yürüteceğim.
Nerede takıldın, ne denedin?  ← (kademe 0 sorusu; kullanıcı verdiyse §1 prompt'u
tam metniyle kurulur ve doğrudan ilk yönlendirici soruya geçilir)
```

**Şablon 2 — Feynman açılışı:**
```text
[Feynman mod açık] Konuyu kendi kelimelerinle, hiç bilmeyene anlat; notsuz anlat.
Anlatımını 4 maddelik denetime sokacağım (yanlış anlama · eksik noktalar ·
ezber/terim saklanması · tek zor soru) + 0-10 güven puanları.
Anlatımın:
```

**Şablon 3 — Şeytanın Avukatı açılışı:**
```text
[Şeytanın Avukatı mod açık] Bana hak vermeyeceğim; 6 maddelik karşı taraf
( en güçlü itiraz · boşluk/varsayım · önyargı · aranacak kanıt · sağlam
taraflar · veri yoksa UNKNOWN ), her itiraz 1-10 puanlı.
Görüşün + sebeplerin + kanıtların:
```

**Şablon 4 — Aktif Hatırlama açılışı:**
```text
[Anımsama mod açık] Notlarını yapıştır; içerikten, tek tek, şıksız sınamaya
alacağım (zorluk artacak, puan 0-10 + gerekçe, sonunda yanlış tekrarı + R4 raporu).
Notlarım:
```

**Şablon 5 — Belirsizlik açılışı (T5 — tek soru):**
```text
Şimdi en çok ne yapmak istiyorsun: (a) takıldığın yeri birlikte açmak,
(b) anlatımını denetlemek, (c) kararına itiraz etmek, (d) notlarından sınamak?
```

**İlk turdan sonra:** oturum `prompts.md` ilgili protokolün tam metnine + §4 R-kurallarına
geçer; şablon **protokolün yerine geçmez**, yalnızca açılıştır.

### 3.8 Tipik zincir senaryoları (protokollerin meşru sıralaması)

> Protokoller **tek oturumda zincirlenebilir**; zincirde her protokol kendi kurallarıyla
> yürütülür ve **cevap yasağı zincir boyunca §1/§2'de geçersiz kılınamaz**.

| # | Senaryo | Sıra | Neden bu sıra |
|---|---------|------|----------------|
| Z1 | Takılma → gerçekten anlama → kalıcılık | §1 → §2 → §4 | önce soruyla aç (cevap yok), sonra anlatım denetle, sonra sınamakla kalıcılaştır — kanıt önceliğiyle de uyumlu (§1.2: #4 en yüksek, en sonda) |
| Z2 | "Yeni konu başlıyorum, sağlam kurayım" | §4 (ön-sınav) → boşlukları §1 ile aç | ön-sınav, neyi bilmediğini gösterir (testing effect'in giriş kullanımı, K2) |
| Z3 | Karar verildi, ekip içi savunma gerekiyor | §3 → §1 (itirazların kaynağına inme) | önce karşı taraf, sonra belirsiz varsayımın takılması |
| Z4 | Anlatım denetimi esnasında takılma | §2 madde 4 çıktısı → §1 (o soruyu cevapsız aç) | zor soru cevapsız kalmasın; ama çözüm yine verilmez (R2) |
| Z5 | Sınav sonrası zayıf konu derinleşmesi | §4 raporu → §1 (zayıf başlıkta takılma) | rapordaki "zayıf nokta" bir sonraki oturumun girdisidir (R4 → zincir girişi) |

**Zincir kuralı:** bir protokol biter → kapanış gerekliyse R4/checklist A → **sonraki
protokol yeni açılış şablonuyla (§3.7) başlar**; protokoller arasında puan/rapor verisi
taşınırsa bu açıkça "önceki oturumdan" diye etiketlenir (R5 — veri kaynağını kaybetme).

---

## §4 Kurallar (bağlayıcı — tam metin `references/rules.md`)

### 4.1 R1-R7 özet tablosu

| Kural | Tek cümle hüküm | İhlal bulursan | Dayanak |
|-------|-----------------|----------------|---------|
| **R1 Tek soru** | Her turda yalnız 1 soru; cevaplamadan sonraki soru yok | ikinci soruyu sil | K3 · K4 · anti-pattern #2/#3 |
| **R2 İpucu kademesi** | Sokratik'te 0→1→2→3; 3'ten sonra ancak **açık rıza** ile tartışma | kademeye dön; atlamak yasak | anti-pattern #12 |
| **R3 0-10 rubrik** | Her puana 1 cümle gerekçe (+ gerekirse düzeltme); ölçek tek | gerekçeyi tamamla | anti-pattern #5 |
| **R4 Oturum raporu** | ≥5 soruluk/Active-Recall oturumunda rapor zorunlu (zayıf + tekrar listesi + dağıtık plan) | raporu yaz | K1 · anti-pattern #10 |
| **R5 Zero-Hallucination** | Olgu uydurulmaz; bilinmeyen `UNKNOWN`/`⚠️ VERIFICATION REQUIRED`; puanlama yalnız kullanıcının notlarına göre | etiketle ya da iddiayı kaldır | skill-maker §6 · anti-pattern #7 |
| **R6 Bekleme + özet** | Aceleye getirme yok (≥30 sn); ≥5 turda periyodik tek-satır özet (çözüm söylemeden) | bekle; özetle | K4 · anti-pattern #3 |
| **R7 Güvenlik yüzeyi** | Credential yok `[REDACTED]`; protokoller salt-okunur — yıkıcı eylem tetiklenmez; `.ai/`'e yazma yok | DUR + onay | anti-pattern #9 |

### 4.2 Kural çelişkilerinin çözümü

- **Prompt metni vs kural:** kural kazanır (prompts.md başlık notu).
- **"Cevap yasağı" vs "R3 istisnası":** yalnız Aktif Hatırlama'da **puan sonrası kısa
  gerekçe** serbesttir; Sokratik/Feynman'da istisna **yoktur**.
- **Kanıt düzeyi vs kullanım:** kanıtsız protokol (Sokratik) **kullanılamaz değil** —
  yalnız "kanıtlanmış" diye **satılamaz** (anti-pattern #7 sınırı).
- **K5 vs K6/K7:** her iki taraf da oturumda söylenir; tek taraflı sunum yasak (§1.3).

### 4.3 Doğrudan yasaklar (tek satır — skill içi)

- Cevap/düzeltilmiş hâl sızdırmak (Sokratik/Feynman) · 2+ soru · cevapsız devam ·
  şık/ipucu önce · gerekçesiz puan · kanıtsız sayısal iddia · zorlama eleştiri ·
  secret istemek · rapor atlamak · yanlış protokol · kademe atlamak · boş-madde özet kapanışı.

### 4.4 Kural × protokol geçerlilik matrisi (hangi kural, nerede işler)

| Kural | §1 Sokratik | §2 Feynman | §3 Şeytanın Avukatı | §4 Aktif Hatırlama |
|-------|:-----------:|:----------:|:-------------------:|:------------------:|
| R1 tek soru | ✔ (çekirdek) | ✔ (madde 4 tek soru) | ✔ açılış/özet turu tek | ✔ (çekirdek — sırayla) |
| R2 ipucu kademesi | ✔ (uygulanır) | — (soru yok, denetim var) | — | ✔ kısmi (ipucu önce **yasak**) |
| R3 0-10 rubrik | ✔ "doğru yön" bildirimi | ✔ güven puanları | ✔ 1-10 itiraz puanı | ✔ puan + gerekçe (+ tek istisna düzeltme) |
| R4 oturum raporu | ≥5 turda özet satırı | kapanışta eksik raporu | — (çıktı itiraz setinin kendisi) | ✔ **zorunlu** |
| R5 zero-hallucination | ✔ (UNKNOWN/⚠️) | ✔ (madde 1-4 gerekçesi) | ✔ (madde 6) | ✔ (yalnız notlar) |
| R6 bekleme + özet | ✔ | ✔ | ✔ | ✔ |
| R7 güvenlik yüzeyi | ✔ | ✔ | ✔ | ✔ |

Okuma: ✔ = kural o protokolde uygulanır · — = o protokolde karşılığı yok (ama R5/R7
**hepsinde** bağlayıcıdır). Bu matris, oturum ortasında "bu kural burada geçerli mi?"
sorusunun tek cevabıdır; çelişik yorumda matris kazanır.

---

## §5 Güvenlik & Truth Mode

### 5.1 Zero-Hallucination (skill-maker §6 — bağlayıcı)

1. **Olgu uydurmak yasak.** Puanlama/geri bildirim yalnız kullanıcının verdiği notlara
   ve **K-numaralı** kaynaklara dayanır.
2. **Bilinmeyen = `UNKNOWN`**, doğrulanamayan iddia = `⚠️ VERIFICATION REQUIRED` —
   amaçlanan yerde **etiketle birlikte** yazılır; etiketsiz yarı-doğru **daha kötüdür**.
3. **Sayısal iddia tek tek kaynak ister** ("bu yöntem %X artırır" → kaynak yoksa sil —
   reddedilen iddialar `sources.md` §4).
4. **"Kanıtlanmış" kelimesi kilitlidir:** yalnız K1-K5 gibi birincil doğrulanmış
   iddialar için; Sokratik/Feynman/Şeytan için bu kelime **kullanılmaz** (§1.2 sütunu).
5. **Çelişki gizlenmez:** K5 vs K6/K7 gibi zıtlıklar oturumda açıkça söylenir.

### 5.2 Kaynak defteri K1-K9 (özet — tam tanım `references/sources.md`)

| K | Kaynak (kısa) | Güven | Bu skill'de |
|---|---------------|-------|-------------|
| K1 | Dunlosky vd. 2013 (PSPI 14(1) 4–58) | [BİRİNCİL] | practice testing/distributed = HIGH · rereading = LOW |
| K2 | Roediger & Karpicke 2006 | [BİRİNCİL] | testing effect: 1 hafta %56 vs %42 (v1 ⚠️ bu turda kalktı) |
| K3 | Paul & Elder 2006 (criticalthinking.org) | [BİRİNCİL] | "yalnız soru" kuralı · 6 soru kategorisi |
| K4 | UConn CETL Socratic Questions | [BİRİNCİL] | ≥30 sn bekleme · periyodik yazılı özet · rol |
| K5 | Lord, Lepper & Preston 1984 | [BİRİNCİL] | consider-the-opposite kazandı; "tarafsız ol" kazanmadı |
| K6 | Tappin vd. 2023 (CRSP) | [BİRİNCİL — karışık] | CTO tek başına zayıf/boş çıkabilir |
| K7 | Goderis vd. 2023 (JASP) | [BİRİNCİL — karışık] | bağlama bağlı: Study 2 pozitif / Study 1 etkisiz |
| K8 | TES 2025 "Feynman technique" | [POPÜLER] | 4 adım standardı + "büyük-ölçekli çalışma yok" itirafı |
| K9 | Chi/Bisra/Nestojko/Kobayashi hattı | [İKİNCİL ⚠️] | yalnız destekleyici not — sayısal etki **taşınmaz** |

**Doğrulama rejimi (oturum içi):** Yeni bir teknik/olgu iddiası gerekecekse (ör. kullanıcı
"şu makalede şunu yazıyor" derse): (a) K-defterinde karşılık var mı → varsa K-numarasıyla
kullan; (b) yoksa iddia **araştırılana kadar** `⚠️` ile taşınır ya da **taşınmaz**;
uydurma asla yok. Web research turu: 2-3 ayrı güvenilir kaynak (birincil öncelikli),
erişim tarihi `sources.md`'ye işlenir.

### 5.3 Güvenlik yüzeyi (R7)

| Yüzey | Kural | İhlal durumunda |
|-------|-------|-----------------|
| Prompt/not içeriği | credential/token/parola **konmaz**; karşılaşırsa `[REDACTED]` | maskele + oturumu durdur |
| Yıkıcı eylem (silme, deploy, ödeme, geri alma) | protokoller **tetiklemez**; kullanıcı isterse DUR | onay iste; protokol ile karıştırma |
| `.ai/` vault | bu skill'den **yazma yok** (yalnız oku) | geri al + rapor |
| Kişisel veri | notlarla gelmeyen kişisel veri **istemez** | isteği geri çek |
| Skill dışı dizin | başka skill dizinine yazma yok; hedef `.claude/skills/learning-prompts/` | yazılanı geri al |

---

## §6 Kalite Kapısı (ADIM 5 — PASS/BLOCK)

### 6.1 Ne zaman çalıştırılır

1. **Her oturum kapanışında** → `references/checklist.md` **A grubu** (oturum).
2. **Bu skill'de dosya değişikliğinde** → `references/checklist.md` **B grubu** (teslim).
3. BLOCK varken "bitti/tamam" **denmez**; BLOCK + neden + düzeltici eylem yazılır.

### 6.2 Çıktı biçimi

```markdown
Checklist: [A: n/n PASS · B: n/n PASS] → SONUÇ: PASS | BLOCK
BLOCK varsa: madde no + neden + düzeltici eylem (tek satır)
```

### 6.3 Teslim raporu (skill revizyonunda — bu sürüm dahil)

Çıkış raporunda **şunlar zorunludur** (kanıt = ölçüm, tahmin değil):

| Kalem | İçerik |
|-------|--------|
| Dosya → satır tablosu | her dosya + satır sayısı (ölçülen, tahmin edilen değil) |
| Silinen / üretilen | v1.0.0 içeriği hangi dosyada neye dönüştü · `sources.md` = yeni |
| Kaynaklar | K1-K9 erişim tarihi + güven etiketi (özet) |
| Checklist | A/B PASS/BLOCK (madde madde değil, sonuç + varsa BLOCK nedeni) |
| Sapmalar | istenenle üretilen arası fark (varsa) |
| ⚠️ kalemler | kalan `VERIFICATION REQUIRED` listesi (K9 · Sokratik deneysel kanıt · Feynman büyük-ölçekli kanıt) |

### 6.4 Bu sürümün kontrolü (4.0.0 teslim anı)

- N3: SKILL.md 500-2000 satır → **ölçülecek** (§7.2 tablo) · dizin toplamı ≥1000 →
  **ölçülecek**.
- N4: §7.1 link tablosu **tüm** references/examples dosyalarını kapsar → orphan yok.
- N5: `examples/01` tam girdi→oturum→çıktı döngüsü (A-E) → var.
- N8: hedef dizinde `CLAUDE.md` yok → var mı **ölçülecek**.
- N9: footer `metadata.version: 4.0.0` ile senkron → tüm dosyalarda aynı satır.
- N10: frontmatter yalnız 4 alan (`name` · `description` · `license` · `metadata`) → kontrol.
- Yasaklar: git yazma yok · `.ai/` yazma yok · ad değişikliği yok.

---

## §7 İlgili Dosyalar

### 7.1 Dosya link tablosu (N4 — bu tablo EKSİK dosya = hata)

| Dosya | Ne verir | Ne zaman okunur | § bağlantısı |
|-------|----------|-----------------|--------------|
| `references/prompts.md` | **4 tam protokol** (kopyala-yapıştır) + gerekçe + icra notları + sık hatalar + §5 seçim | tetikleyici eşleşince — **ana çıktı** | §2.1 · §3.2 |
| `references/rules.md` | R1-R7 tam metin + dayanak + çelişki çözümü + R-tablo özeti | oturum yürütülürken | §4 |
| `references/overview.md` | teknik haritası · kanıt haritası K1-K9 · çelişki kuralı · durum↔protokol tablosu | protokol seçilirken / konumlandırma gerekirse | §1.2 · §3.3 |
| `references/sources.md` | **Truth Mode kaynak defteri** K1-K9 + güven etiketleri + kaynak hiyerarşisi + reddedilen iddialar | olgu iddiası yazılacaksa / doğrulama turu | §5.2 |
| `references/anti-patterns.md` | 13 yasaklı kalıp (A/B/C grupları) + ağırlık sırası + §D düzeltici eylem | oturum öncesi ve ihlal anında | §3.5 · §4.3 |
| `references/checklist.md` | ADIM 5 kapıları: **A oturum kapanışı / B skill teslimi** + PASS/BLOCK biçimi | kapanış / teslim | §6 |
| `references/changelog.md` | 1.0.0 → 4.0.0 sürüm geçmişi + karşılaştırma tablosu | sürüm sorgulandığında | §1.4 |
| `examples/01-tam-oturum-ornekleri.md` | 4 protokol **tam döngü** (A-D) + yanlış durum→doğru protokol (E) | örnek gerektiğinde / şablona ihtiyaçta | §3.6 |

> **Okuma sırası (token tasarrufu):** tetikleyici → `prompts.md` ilgili § → gerekirse
> `rules.md` + `anti-patterns.md` → olgu iddiası varsa `sources.md` → kapanış `checklist.md` A.
> Toplu okuma (tüm references) **gereksizse yapılmaz**.

### 7.2 Ölçüm tablosu (teslim anında doldurulur — tahmin yazılmaz)

| Dosya | Satır (ölçüm) |
|-------|----------------|
| `SKILL.md` | *(ölçümle doldurulur)* |
| `references/prompts.md` | *(ölçümle doldurulur)* |
| `references/rules.md` | *(ölçümle doldurulur)* |
| `references/overview.md` | *(ölçümle doldurulur)* |
| `references/sources.md` | *(ölçümle doldurulur)* |
| `references/anti-patterns.md` | *(ölçümle doldurulur)* |
| `references/checklist.md` | *(ölçümle doldurulur)* |
| `references/changelog.md` | *(ölçümle doldurulur)* |
| `examples/01-tam-oturum-ornekleri.md` | *(ölçümle doldurulur)* |
| **Toplam (dizin)** | *(ölçümle doldurulur)* |

*(Not: Bu tablo, N3 ve §6.3 ölçüm şartını taşır; değerler teslim raporuna **ölçüm sonrası**
yazılır ve checklist B'de doğrulanır.)*

### 7.3 İlişkili skill'ler (sınır)

| Skill | İlişki | Sınır |
|-------|--------|-------|
| `prompt-maker` | ham istek → yapılandırılmış görev promptu | "öğrenme/sınamak" ise **bu** skill; "prompt tasarla" ise prompt-maker (§2.2 #4) |
| `skill-maker` | bu skill'in format/quality otoritesi (N1-N10, §5-§6) | bu revizyon onun checklist'ine tabidir |
| `learning-prompts` (kopyalar) | `.opencode/skills/` kapatıldı — tek ev `.claude/skills/` | başka dizine yazma yok (R7) |

### 7.4 Bu skill'e değişim protokolü (bakım kuralları)

| # | Kural | Neden |
|---|-------|-------|
| 1 | İçerik değişikliği `skill-maker` checklist'inden geçmeden **teslim sayılmaz** (§6.1-2) | N1-N10 bütünlüğü |
| 2 | Yeni teknik/olgu iddiası ekleniyorsa **önce** `sources.md`'ye K girişi (erişim tarihli, 2-3 kaynak) — sonra skill metnine | Truth Mode sırası: kaynak → metin, tersi yasak |
| 3 | Anti-pattern **numarası taşınmaz/ değiştirilmez**; yenisi eklenir | `rules.md`/`SKILL.md` referansları numaraya kilitli |
| 4 | Frontmatter alanları **4'ten artmaz**; `description` içindeki Türkçe tetikleyiciler **silinmez** (eklenebilir; ≤1024 karakter) | N10 + tetikleyici dokunulmazlığı |
| 5 | `metadata.version` artınca **tüm** dosyalardaki footer + `changelog.md` + `previous-version` birlikte güncellenir | N9 senkronu |
| 6 | N3 korunur: SKILL.md 500-2000 · dizin ≥1000 (ölçümle) · dolgu yasak | N3 |
| 7 | git commit/add **yok** — teslim farkı orkestratöre bırakılır | commit kuralı |
| 8 | `.ai/` vault'a bu skill'den yazma yok (yalnız oku) | R7 |

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
