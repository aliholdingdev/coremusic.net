# overview — Teknik Haritası, Kanıt Haritası, Teknik Seçimi

> Bu dosya **neyin ne kadar kanıtlandığını** taşır (Truth Mode — skill-maker §6).
> K-numaralarının tam tanımı: `sources.md`. K-numarası olmayan iddia bu skill'de **yazılmaz**;
> yazılamayan iddia `⚠️ VERIFICATION REQUIRED` taşır.

---

## 1. Dört teknik haritada

| # | Teknik | Mod | Çıktı | Temel dayanak (K) | Kanıt düzeyi |
|---|--------|-----|-------|-------------------|--------------|
| 1 | Sokratik Yöntem | yönlendirme (cevap yok) | kullanıcının kendi çözümü | K3 (yalnız-soru kuralı) · K4 (icra: bekleme + yazılı özet) | **Otoriter uygulama geleneği** — deneysel başarı yüzdesi doğrulanmadı → "kanıtlanmış" denmez |
| 2 | Feynman Tekniği | anlatım + denetim | eksik/noksan raporu + güven puanı | K8 (4 adım standardı + dürüst itiraf) · K1 (self-explanation = moderate) | **Parça kanıtlı, paket kanıtsız** — Feynman 4-adım yöntemi yayımlamadı |
| 3 | Şeytanın Avukatı | karşı argüman (puanlı) | karar sağlamlaştırma / kanıt listesi | K5 (consider-the-opposite, 1984) · K6/K7 (yakın denemeler karışık) | **Klasik pozitif + yakın yeniden denemeler karışık** → "garanti" denmez |
| 4 | Aktif Hatırlama | sınav (recall) | puan + yanlış tekrarı + dağıtık tekrar listesi | K1 (practice testing + distributed = HIGH) · K2 (testing effect sayıları) | **En yüksek kanıt** — bu skill'in çekirdeği |

**Sıralama gerekçesi (kanıt düzeyi):** #4 > #3 (klasik) > #2 (parça) > #1 (geleneğe dayalı).
Kanıt düzeyi, **kullanışlılık sırası değildir** — Sokratik bir oturumda "cevap yok" modu,
cevap istenen bir durumda yanlış seçimdir (yanlış-sorun-hatası, anti-pattern #11).
Doğru eşleşme, **duruma** göre yapılır (§4).

---

## 2. Kanıt haritası (Truth Mode — yük taşıyan cümleler)

### 2.1 Birincil doğrulanmış (bu turda açıldı, 2026-10-21)

| K | Ne doğrulandı (kısa) | Bu skill'de nerede kullanılıyor |
|---|----------------------|--------------------------------|
| **K1** Dunlosky vd. 2013 | practice testing + distributed = **HIGH**; self-explanation = MODERATE; rereading = **LOW** | §1 tablo · prompts §4 gerekçe · R4 rapor dayanağı |
| **K2** Roediger & Karpicke 2006 | 1 hafta sonra **%56 vs %42**; 5 dakikada tersi; tekrar-okuma güveni şişirir | prompts §4 (feedback + tekrar) · R3 istisnası · **v1.0.0'daki ⚠️ bu turda kalktı** |
| **K3** Paul & Elder 2006 | "yalnız soru sorabilirsin" kuralı birebir · 6 soru kategorisi | prompts §1 cevap yasağı · R1 dayanağı |
| **K4** UConn CETL | ≥30 sn bekleme · periyodik yazılı özet · "sanki bilmiyormuş gibi" rol | R6 · prompts §1 icra notları |
| **K5** Lord, Lepper & Preston 1984 | consider-the-opposite önyargıyı azalttı; **"tarafsız ol" telkini işe yaramadı** | prompts §3 yapı taşı (yapısal itiraz, genel telkin yok) |
| **K6** Tappin vd. 2023 | consider-the-opposite **tek başına zayıf/boş** çıkabildi (kayıtlı rapor) | §2 dürüstlük notu · anti-pattern #7 |
| **K7** Goderis vd. 2023 | sonuç **bağlama bağlı** (Study 2 pozitif / Study 1 etkisiz) | §2 dürüstlük notu · anti-pattern #7 |
| **K8** TES 2025 [POPÜLER] | 4 adım standardı + "büyük-ölçekli çalışma yoktur" itirafı | prompts §2 konumlandırma (tarihsel gerçek) |

### 2.2 İkincil / ⚠️ işaretli (skill metnine sayısal etki TAŞINMAZ)

| İddia (aktarıldığı hâliyle) | Durum | Kullanım |
|---|---|---|
| Self-explanation sayısal etki büyüklükleri (Chi/Bisra hatı) | ⚠️ VERIFICATION REQUIRED (K9) | yalnız "moderate" düzeyinde, K1'e yaslanır |
| Öğretme-beklentisi (protégé) etkisi sayısal etkisi (Nestojko/Kobayashi hattı) | ⚠️ VERIFICATION REQUIRED (K9) | yalnız destekleyici not (K8 ile örtüşen nitel yön) |
| Sokratik yöntemin "X% başarı" etkisi | doğrulanmadı | skill metninde **yok**; "otoriter geleneğe (K3/K4)" dayanır |
| "Feynman tekniği %X artırır" | kaynak yok | reddedildi (`sources.md` §4) |

### 2.3 Çelişki kuralı (gizlenmez)

- **K5 vs K6/K7:** klasik kanıt pozitif (1984), yakın yeniden denemeler karışık (2023) →
  oturumda ikisi de söylenir; tek taraflı sunum anti-pattern #7'dir.
- **K2'nin çift yönü:** testing effect uzun vadede kazandırır, **hemen sonra** (5 dk)
  okuma kazanabilir → bu yüzden protokol "hemen öğren" değil, **kalıcılık** iddiasıyla
  kuruludur; kısa-vade kopyası çekmek K2'ye aykırıdır.
- **K1 moderate vs high:** Feynman'ın parçası (self-explanation) practice testing'den
  zayıf sınıftadır → Aktif Hatırlama, Feynman'dan **önce** gelirse priorite tutarlı olur
  (kullanım sırası: prompts §5).

---

## 3. Teknik seçimi (tek soruluk karar)

```text
Karar mı, takılma mı, sınav mı?
- Karar/fikir → §3 Şeytanın Avukatı
- Takıldım   → §1 Sokratik
- "Anladım"  → §2 Feynman
- "Çalıştım, unutuyorum" → §4 Aktif Hatırlama
- Emin değil → kullanıcıya TEK soru: "şimdi en çok ne yapmak istiyorsun:
  karar vermek / takıldım / öğrenmek / sınamak?"
```

**Durum↔protokol eşleşmesi (anti-pattern #11'in panzehiri):**

| Durum | Doğru protokol | Yanlış protokol (kaçın) |
|-------|----------------|--------------------------|
| "Cevabı ver" (hızlı çözüm) | kullanıcıyı protokolü **öner**; "cevabı ver" isteği ile Sokratik aynı anda yapılamaz | Sokratik yürütürken cevap sızdırmak (ikisi birden imkânsız) |
| Takıldım ama ipucu istemiyorum | §1 (kademe 0-1) | §4 sınavı (takılmayı sınavla gizlemek) |
| "Öğrendim" ama emin değilim | §2 anlatım denetimi | §1 (anlatım denetimi soru-cevapla yapılmaz) |
| Karar verdim, karşı görüş istemiyorum | §3 — ama kullanıcı sonuçta karar sahibidir | kararın kendisini modele vermek |

---

## 4. Entegrasyon ve sınır

- **Bu skill prompt-maker'a girmez:** prompt-maker ham istek → yapılandırılmış görev
  prompt'u üretir (workflow sorusu); burada ise **hazır-kullanım öğrenme protokolleri**
  ve yürütme kuralları vardır. Çakışma durumunda: kullanıcının niyeti "öğrenme/sınamak"
  ise bu skill, "görev promptu tasarla" ise prompt-maker geçerlidir.
- **CoreMusic bağlamı:** `.ai/` konuları (BCNF, middleware, DSP, security) çalışılırken
  not/metin `…` yerine yapıştırılır → puanlama yalnız yapıştırılan içeriğe göre yapılır
  (R5) → model vault dışı bilgi uyduramaz; `.ai/`'e bu skill'den **yazma yok** (R7).
- **Kaynak bağımlılığı:** teknikler büyük harfle "kanıtlanmış" diye **satılmaz**; kanıt
  düzeyi §1 tabloda, dürüstlük notları §2'de, abartı yasağı anti-pattern #7'dedir.

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
