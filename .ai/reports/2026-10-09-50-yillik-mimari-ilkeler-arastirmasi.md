---
type: report
category: architecture
title: "CoreMusic — 50+ Yıllık Kanıtlanmış Yazılım Mimarisi Prensipleri (Araştırma Raporu)"
date: 2026-10-09
status: active
version: 1.0.0
authority: "SSOT (araştırma raporu)"
---

# CoreMusic — 50+ Yıllık Kanıtlanmış Yazılım Mimarisi Prensipleri (Araştırma Raporu)

> **Durum:** Araştırma raporu — bağlayıcı DEĞİLDİR. §9 karar önerileri taslaktır, ADR DEĞİL.
> **Zero-Hallucination:** Bu rapor yalnızca araştırmanın içeriğini yapılandırır; yeni iddia eklenmez.
> Doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` işaretiyle korunur.
> Biçim (her prensip): **prensipl · yıl · kaynak URL · ders · CoreMusic uyumluluk · güven**.

**Bölüm dizini:**

- §1 Amaç · §2 Yöntem & güven anahtarı
- §3 Klasik Prensipler (1968–1990) — 3.1 … 3.7
- §4 Orta Dönem (1990–2010) — 4.1 … 4.9
- §5 Modern Dersler (2010–2026) — 5.1 … 5.7
- §6 Geçersiz Kananlar & Karşı Kanıt — 6.1 … 6.6
- §7 CoreMusic'e Özgür Dersler (10 madde)
- §8 Çelişkiler & Boşluklar
- §9 Karar Önerileri (taslak — ADR DEĞİL)
- §10 Kaynakça (21 URL)

---

## §1 Amaç

CoreMusic'in mevcut mimari kararlarını, 1968–2026 arası **kanıtlanmış** yazılım mimarisi
prensipleriyle karşılaştırmak.

**Mevcut mimari bağlam (disk kanıtı):**

- PHP 8.4 `strict_types` modular monolith
- Vanilla JS + ITCSS (ADR-001, frozen)
- PDO — ORM yasak (ADR-002, frozen)
- Multi-DB · 18 BCNF şema (ADR-003 / ADR-040)
- Multi-Domain SPA (ADR-004)
- API Gateway / BFF (ADR-084)
- Event-driven PSR-14 altyapı (ADR-086)
- Multi-provider sync · Outbox + WAL (ADR-081)
- Mimari katmanlar: `.ai/architecture/K000–K020`

**Amaç üç katmanlıdır:**

1. Klasik · orta · modern dönem prensiplerini kaynak URL'leriyle envanterlemek.
2. "Geçersiz kılan" tezleri ve karşı kanıtı ayırmak (moda ≠ prensip).
3. CoreMusic'e özgü, uyumluluk işaretli dersler çıkarmak.

**Kapsam dışı:** kod değişikliği · ADR yazımı · vault dosyalarının düzenlenmesi.

---

## §2 Yöntem & Kaynak Güven Anahtarı

**Yöntem:** Birincil kaynak öncelikli (yazarın kendi metni: Dijkstra EWD, Parnas, Fielding,
Cockburn, Fowler bliki). İkincil kaynak (Wikipedia, haber) yalnız birincil yoksa kullanıldı.

**Güven anahtarı:**

| İşaret | Anlamı |
|---|---|
| 🟢 | Birincil kaynak veya doğrudan alıntılanabilir metin |
| 🟡 | İkincil kaynak / özet; birincil metin doğrulanmadı |
| ⚠️ | `⚠️ VERIFICATION REQUIRED` — atıf/kanıt doğrulanamadı, iddia KULLANILMAMALI |

**CoreMusic uyumluluk anahtarı:**

| İşaret | Anlamı |
|---|---|
| UYUMLU | Mevcut ADR (001/002/003/004/040/081/084/086) ile örtüşüyor |
| KISMEN | Yönde uyumlu, kapsam/genişlik farkı var |
| UYUMSUZ-artık-geçersiz | Prensipler karşı kanıtla geçersiz kılındı / çağ dışı |

**ADR haritalama (karşılaştırma yüzeyi — bu raporda ADR DEĞİŞTİRİLMEZ):**

| ADR | Konu | Rapor'daki karşılığı |
|---|---|---|
| ADR-001 (frozen) | Vanilla JS + ITCSS | §3.7 · §4.1 · §5.4 |
| ADR-002 (frozen) | PDO — ORM yasak | §3.4 · §5.3 · §6.6 |
| ADR-003 / ADR-040 | Multi-DB · 18 BCNF | §3.4 · §4.9 · §6.6 |
| ADR-004 | Multi-Domain SPA | §3.6 · §4.2 |
| ADR-081 | Outbox + WAL (multi-provider sync) | §4.7 · §4.9 · §7.4 |
| ADR-084 | API Gateway / BFF | §3.3 · §4.2 · §5.1 |
| ADR-086 | Event-driven PSR-14 | §4.3 · §7.5 |

---

## §3 Klasik Prensipler (1968–1990)

### 3.1 Go-to yasağı / yapısal programlama — Dijkstra, 1968

- **Kaynak:** https://dl.acm.org/doi/10.1145/362929.362947
- **Ders:** Akış kontrolü okunabilirlik ve kanıtlanabilirlik belirler; goto gibi "her yere giden"
  kontrol akışı muhakemeyi bozar.
- **CoreMusic:** UYUMLU — PHP 8.4 strict_types, katmanlı akış (K000–K020).
- **Güven:** 🟢

### 3.2 Katmanlı mimari — EWD196 (Dijkstra) / OSI referans modeli, ~1968–1980

- **Kaynak:** https://cs.utexas.edu/~EWD/transcriptions/EWD01xx/EWD196.html
- **Ders:** Katman yalnız altındakine bağımlıdır; her katman kendi iç soyutlamasını saklar.
- **CoreMusic:** UYUMLU — `.ai/architecture/K000–K020` katman hiyerarşisi.
- **Güven:** 🟢

### 3.3 Bilgi gizleme — Parnas, "On the Criteria To Be Used in Decomposing Systems into Modules", 1972

- **Kaynak:** https://dl.acm.org/doi/10.1145/361598.361623 · https://prl.khoury.northeastern.edu/img/p-tr-1971.pdf
- **Ders:** Modül sınırı = gizlenecek **karar** sınırıdır; tasarım, kararları gizleme başarısıdır.
- **CoreMusic:** UYUMLU — modüler monolith + API Gateway (ADR-084).
- **Güven:** 🟢

### 3.4 İlişkisel veri modeli — Codd, 1970

- **Kaynak:** https://dl.acm.org/doi/10.1145/362384.362685
- **Ders:** Veri, tutarlı bir model üzerinde normalizelenir; erişim yolu modeli bozmaz.
- **CoreMusic:** UYUMLU — 18 BCNF şema (ADR-003 / ADR-040).
- **Güven:** 🟢

### 3.5 UNIX felsefesi — McIlroy, "A Quarter Century of Unix", 1978

- **Kaynak:** https://en.wikipedia.org/wiki/Unix_philosophy
- **Ders:** Küçük, tek amaçlı bileşenler; birleşim, birbirine geçmiş araçlardan daha esnektir.
- **CoreMusic:** KISMEN — tek deploy içinde tek amaçlı modüller; Unix pipe'ı yok.
- **Güven:** 🟡

### 3.6 İstemci-sunucu modeli — 1980'ler

- **Kaynak:** https://en.wikipedia.org/wiki/Client%E2%80%93server_model
- **Ders:** Sorumluluk sunucuda toplanır; istemci yalnız gösterim ve niyet taşır.
- **CoreMusic:** UYUMLU — Multi-Domain SPA (ADR-004) + BFF (ADR-084).
- **Güven:** 🟡

### 3.7 Üç katmanlı (three-tier) mimari — 1990'lar

- **Kaynak:** https://www.ibm.com/think/topics/three-tier-architecture
- **Ders:** Gösterim, iş kuralı ve veri fiziksel olarak ayrılır; katmanlar bağımsız ölçeklenir.
- **CoreMusic:** UYUMLU — Vanilla JS ITCSS ön yüz · PHP çekirdek · multi-DB.
- **Güven:** 🟡

**Dönem notu:** Bu yedi prensip hâlâ geçerliliğini koruyan çekirdektir; hiçbiri "moda"
nedeniyle değil, karşı kanıt nedeniyle çürütülmemiştir.

---

## §4 Orta Dönem (1990–2010)

### 4.1 MVC — Smalltalk-80 Model-View-Controller (Reenskaug), 1979

- **Kaynak:** https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller
- **Ders:** Model, görünüm ve kontrol ayrılır; kullanıcı eylemi modele iletilir.
- **CoreMusic:** KISMEN — sunucu tarafında MVC yok; SPA'da model/view ayrımı ITCSS + JS ile dolaylı.
- **Güven:** 🟡 (orijin 1979/1980 tartışması — §8)

### 4.2 REST — mimari stil (Fielding doktorası), 2000

- **Kaynak:** https://ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm
- **Ders:** Kaynak kimliği · stateless · önbelleklenebilirlik · katmanlı sistem · isteğe bağlı kod.
- **CoreMusic:** UYUMLU — API Gateway/BFF (ADR-084); stateless API dersleri §7.6.
- **Güven:** 🟢

### 4.3 SOA / ESB'nin dersleri — Manes, 2009

- **Kaynak:** https://www.infoworld.com/article/2314984
- **Ders:** Merkezi ESB karmaşıklaşırsa "aklı tek yerde toplama" etkisi yaratır; sözleşme
  sahipliği dağılmalıdır.
- **CoreMusic:** UYUMSUZ-artık-geçersiz — ESB yok; PSR-14 olay altyapısı (ADR-086).
- **Güven:** 🟡

### 4.4 Amazon API-first mandate — Bezos, 2002

- **Kaynak:** https://highscalability.com · https://thenewstack.io
- **Ders:** Her iç modül de bir API gibi davranırsa sınır zorlanır.
- **CoreMusic:** KISMEN — modül sınırları ADR-084/086 ile zorlanıyor; tek deploy.
- **Güven:** 🟡 — **2006 "monolith memo" iddiası: ⚠️ VERIFICATION REQUIRED**

### 4.5 DDD — Domain-Driven Design (Evans), 2003

- **Kaynak:** ISBN 0321125215 (O'Reilly)
- **Ders:** Bounded context · ubiquitous language · model ortak dili; yetki sınırı = model sınırı.
- **CoreMusic:** UYUMLU — 18 BCNF DB + modül sınırları ile örtüşür.
- **Güven:** 🟡

### 4.6 Hexagonal / Ports & Adapters — Cockburn, 2005

- **Kaynak:** https://alistair.cockburn.us/hexagonal-architecture · AWS dokümantasyonu
- **Ders:** Uygulama çekirdeği, giriş/çıkış adaptörlerinden bağımsızdır; bağımlılıklar içe akar.
- **CoreMusic:** UYUMLU — Dependency Rule (K000–K020).
- **Güven:** 🟢

### 4.7 Transactional Outbox

- **Kaynak:** https://microservices.io/patterns/data/transactional-outbox.html · https://debezium.io/blog/
- **Ders:** İki DB yazımını (domain + event) tek transaction'a sığdır; ayrıca poller/CDC yayınlar.
- **CoreMusic:** UYUMLU — Outbox + WAL (ADR-081).
- **Güven:** 🟡

### 4.8 CQRS — Command Query Responsibility Segregation (Young), 2010

- **Kaynak:** https://cqrs.files.wordpress.com/2010/11/cqrs_documents.pdf — **erişim ⚠️ VERIFICATION REQUIRED**
- **Ders:** Yazma ve okuma modelleri ayrılabilir; ama bu bir **spektrum**dur, zorunlu mimari değil.
- **CoreMusic:** KISMEN — yalnız okuma sorgusu düzeyinde (§9.3).
- **Güven:** ⚠️ (birincil PDF erişimi açılmadı)

### 4.9 CAP — Brewer ikilemi / Gilbert-Lynch ispatı, 2000 / 2002

- **Kaynak:** https://dl.acm.org/doi/10.1145/564585.564601 · https://www.cs.cmu.edu/~dga/15-712/GL-cap.pdf
- **Ders:** Bölünmüş tolerans seçilince tutarlılık ile erişilebilirlik arasında seçim vardır;
  "CAP'i seçtim" tek başına anlamsızdır.
- **CoreMusic:** KISMEN — multi-DB; WAL + outbox kararı bu ikilemi ele alıyor.
- **Güven:** 🟢

**Dönem notu:** 4.4–4.8 arasındaki tüm "dağıtık sistem" prensipleri, tek deploy hedefleyen
CoreMusic'te **sınır** olarak değil **zorunluluk** olarak uygulanır (bkz. §9.2, §9.3).

---

## §5 Modern Dersler (2010–2026)

### 5.1 Fallacies of Distributed Computing — Deutsch / Gosling, 1994 (modern tartışma)

- **Kaynak:** https://en.wikipedia.org/wiki/Fallacies_of_distributed_computing
- **Ders:** "Ağ güvenilirdir · gecikme yoktur · bant yeterlidir" varsayımları yanlıştır;
  her uzak çağrıyı düşman say.
- **CoreMusic:** UYUMLU — tek deploy = ağ yüzeyi minör; yine de BFF sınırı.
- **Güven:** 🟡 — **"Ousterhout" atfı ⚠️ doğrulanamadı (§8)**

### 5.2 Twelve-Factor App — Wiggins / Heroku, ~2011

- **Kaynak:** https://12factor.net
- **Ders:** Yapılandırma ortamdan gelir · süreç stateless · log akıştır · tek deploy artefaktı.
- **CoreMusic:** UYUMLU — stateless API, tek deploy, env-tabanlı config.
- **Güven:** 🟡

### 5.3 OWASP Defense-in-Depth — katmanlı savunma, güncel

- **Kaynak:** https://cheatsheetseries.owasp.org · https://devguide.owasp.org/en/02-foundations/03-security-principles/
- **Ders:** Tek bir savunma hattına güvenme; her katmanda (gateway · controller · DB) ayrı kontrol.
- **CoreMusic:** UYUMLU — security katman sırası (§7.9).
- **Güven:** 🟢

### 5.4 MonolithFirst — Fowler bliki, 2015

- **Kaynak:** https://martinfowler.com/bliki/MonolithFirst.html
- **Ders:** Önce modüler monolit; mikroservise ancak modülerlik kanıtlandığında geç.
- **CoreMusic:** UYUMLU — doğrudan mevcut mimari tarifi.
- **Güven:** 🟢

### 5.5 Microservices — Fowler & Lewis, 2014

- **Kaynak:** https://martinfowler.com/articles/microservices.html
- **Ders:** Mikroservis bir sonuçtur, hedef değil; hizmet ayrımı operasyon olgunluğuna bağlı.
- **CoreMusic:** KISMEN — CoreMusic bilinçli olarak mikroservise gitmiyor.
- **Güven:** 🟢

### 5.6 Modular Monolith — arXiv 2024 + Grzybek primer

- **Kaynak:** https://arxiv.org/abs/2401.11867 · https://kamilgrzybek.com
- **Ders:** Tek deploy + **zorlanabilir** modül sınırları: derleme/runtime sınırı, paylaşılan
  DB yok, net sahiplik.
- **CoreMusic:** UYUMLU — doğrudan ADR-001/004/081/084/086 örtüşmesi.
- **Güven:** 🟢

### 5.7 Amazon Prime Video — mikroservis → monolith dönüşümü, 2023

- **Kaynak:** https://thenewstack.io · https://devclass.com
- **Ders:** Bazı iş yüklerinde tek süreç maliyet/performans üstünlüğü sağlar;
  **ton farkı var (§8)**.
- **CoreMusic:** UYUMLU — mevcut mimari lehine argüman.
- **Güven:** 🟡 (ton farkı not edilecek)

---

## §6 Geçersiz Kananlar & Karşı Kanıt

### 6.1 "SOA öldü" → BAŞARISIZ çürütme

- **Kanıt:** SOA'nın temel fikri (sözleşme · servis sınırı) REST / ara-gateway mimarilerinde
  yaşıyor; yalnız ESB biçimi terk edildi (§4.3).
- **Güven:** 🟡

### 6.2 "Monolith dead" → BAŞARISIZ

- **Kanıt:** MonolithFirst (§5.4) + Modular Monolith 2024 (§5.6) + Prime Video 2023 (§5.7)
  karşı kanıt.
- **Güven:** 🟢

### 6.3 "Mikroservis ekonomik üstün" → KISMEN

- **Kanıt:** Prime Video (§5.7) bazı iş yüklerinde tek süreç üstünlüğünü gösterdi; genel
  geçerlilik iddiası zayıf.
- **Güven:** 🟡

### 6.4 "Dağıtık sistemler üzerine 8 saçmalık geçersiz" → ZAYIF

- **Kanıt:** İddia yayıldı; sistematik karşı kanıt (her madde için tekrarlanabilir ölçüm)
  sunulmadı.
- **Güven:** 🟡

### 6.5 "CQRS her yerde kullanılmalı" → BAŞARISIZ çürütme

- **Kanıt:** CQRS tam hali (ayrı write/read store) çoğu uygulama için fazla karmaşıklık;
  spektrum olarak geçerli (§4.8, §9.3).
- **Güven:** ⚠️ (birincil PDF erişimi yok — §8)

### 6.6 "SQL / normalizasyon öldü" → BAŞARISIZ

- **Kanıt:** Codd 1970 (§3.4) + CoreMusic'in 18 BCNF şeması (ADR-003/040) karşı kanıt.
- **Güven:** 🟢

---

## §7 CoreMusic'e Özgür Dersler (10 madde)

1. **Parnas (1972): modül sınırı = gizlenecek karar sınırı.** Modüller dosya sayısına göre
   değil, "hangi kararı saklıyoruz" sorusuyla çizilir. → UYUMLU 🟢 (§3.3)
2. **Modular Monolith (2024): sınırlar zorlanabilir olmalı.** Paylaşılan DB tablosu, doğrudan
   modül-içi require, gizli global state sınırı deler. → UYUMLU 🟢 (§5.6)
3. **CQRS bir spektrumdur.** CoreMusic'te doğru olan: yazma modeli tek, okuma sorgusu
   düzeyinde ayırma (view/reports sorguları); ayrı write-store gerekmez. → KISMEN ⚠️ (§4.8)
4. **Transactional Outbox: cross-DB yazmada zorunlu.** İki veritabanına atomik yazılamaz;
   domain yazımı + outbox aynı transaction, yayın poller/CDC ile. → UYUMLU (ADR-081) 🟡 (§4.7)
5. **Her uzak çağrıyı düşman varsay (Fallacies).** Ağ/kuyruk/göç hata verir; BFF + PSR-14 +
   outbox bu yüzden var. → UYUMLU 🟡 (atıf ⚠️, §5.1)
6. **Stateless API + önbellek (REST/12-factor).** Sunucu oturumu saklamaz; oturum/izleyici
   istemciden gelir, cache yanıtın özüdür. → UYUMLU 🟢 (§4.2, §5.2)
7. **İlke modadır.** SOA/ESB, "monolith dead", "CQRS her yerde" dalgaları geçti; kalan
   çekirdek: katman · gizleme · normalizasyon · sözleşmeli sınır. → İlkesel 🟢 (§6)
8. **MonolithFirst yol haritası.** Mikroservis, modülerlik kanıtlandıktan SONRA düşünülür;
   CoreMusic'te tetikleyici = modül sınırı ihlal sıklığı. → UYUMLU 🟢 (§5.4)
9. **OWASP Defense-in-Depth: savunma katman sırası.** Gateway (rate-limit/CSRF) →
   controller (doğrulama) → DB (parametreli sorgu, PDO). Tek katman yetersizdir. → UYUMLU 🟢 (§5.3)
10. **Twelve-factor süzgeç.** 12 madde tek tek hedef değil; CoreMusic için anlamlı olanlar:
    env-config · stateless süreç · tek deploy artefaktı · log akışı. → UYUMLU 🟡 (§5.2)

---

## §8 Çelişkiler & Boşluklar

### Çelişkiler

1. **Amazon 2006 "monolith memo"** — birincil kaynak (Bezos mektubu / iç memo) bulunamadı;
   yalnız ikincil anlatım var. **⚠️ VERIFICATION REQUIRED**
2. **Fallacies "Ousterhout" atfı** — kaynak atfı doğrulanamadı. Alternatif ve güvenli atıf:
   *A Philosophy of Software Design* (2018), https://web.stanford.edu/~ouster
   **⚠️ VERIFICATION REQUIRED**
3. **Prime Video 2023 ton farkı** — thenewstack.io ile devclass.com aynı olayı farklı tonla
   veriyor ("tam dönüşüm" vs "parçalı bileşen optimizasyonu"). Birincil AWS blog teyidi yok.
   **⚠️ VERIFICATION REQUIRED**
4. **MVC orijini** — 1979 (Reenskaug, Norveç) / 1980 (Smalltalk-80 uygulaması) tarihlemesi
   kaynaklara göre farklı yazıyor.

### Boşluklar

1. **CQRS PDF erişimi açılmadı**
   (https://cqrs.files.wordpress.com/2010/11/cqrs_documents.pdf) — §4.8 satırının güveni ⚠️.
2. **SOA post-mortem yüzdesel veri yok** — "ESB proje başarısızlık oranı" gibi sayısal kanıt
   toplanamadı; §6.1 sonucu niteldir.
3. **Prime Video için maliyet/latans sayısal karşılaştırma** (öncesi/sonrası) rapora alınmadı.

---

## §9 Karar Önerileri (taslak — onay bekliyor, ADR DEĞİL)

| # | Taslak karar | Dayanak | Uyumluluk |
|---|---|---|---|
| 9.1 | **Modül sınırı = gizlenecek karar (Parnas'72).** Her yeni modül, açıkça "hangi kararı gizliyor" sorusuyla kurulur; cevap yoksa modül değil, sınıf/klasör. | §3.3 · §4.5 · §4.6 | UYUMLU |
| 9.2 | **Tek deploy + zorlanabilir modül sınırları (Modular Monolith'24).** Sınırlar kod incelemesiyle değil, yapıcı kural ile korunur: paylaşılan DB tablosu yasağı, cross-modül doğrudan çağrı yasağı, yalnız gateway/olay üzerinden iletişim. | §5.6 · §5.4 · §4.4 | UYUMLU |
| 9.3 | **Cross-DB yazmada outbox; CQRS yalnız okuma sorgusu düzeyinde.** İki DB'ye atomik yazılmaz → domain + outbox tek transaction (ADR-081). CQRS ayrı write-store olarak DEĞİL; rapor/liste/view sorgularının yazma modelinden bağımsız optimize edilmesi olarak. | §4.7 · §4.8 · §3.4 | UYUMLU / KISMEN |

**Bu bölüm bağlayıcı değildir; ADR'ye dönüşümü için Vault Steward onayı gerekir.**

---

## §10 Kaynakça (21 URL)

1. https://dl.acm.org/doi/10.1145/362929.362947 — Dijkstra, *Go To Statement Considered Harmful*, 1968
2. https://cs.utexas.edu/~EWD/transcriptions/EWD01xx/EWD196.html — Dijkstra, EWD196, katmanlı mimari
3. https://dl.acm.org/doi/10.1145/361598.361623 — Parnas, bilgi gizleme, 1972
4. https://prl.khoury.northeastern.edu/img/p-tr-1971.pdf — Parnas TR-1971 teknik rapor
5. https://dl.acm.org/doi/10.1145/362384.362685 — Codd, ilişkisel model, 1970
6. https://en.wikipedia.org/wiki/Unix_philosophy — UNIX felsefesi / McIlroy, 1978
7. https://en.wikipedia.org/wiki/Client%E2%80%93server_model — İstemci-sunucu modeli, 1980'ler
8. https://www.ibm.com/think/topics/three-tier-architecture — Three-tier mimari, 1990'lar
9. https://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller — MVC (Reenskaug), 1979
10. https://ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm — Fielding, REST, 2000
11. https://www.infoworld.com/article/2314984 — Manes, ESB/SOA dersleri, 2009
12. https://highscalability.com — Amazon API mandate, 2002
13. https://thenewstack.io — Prime Video 2023 · Amazon API anlatımı
14. https://en.wikipedia.org/wiki/Fallacies_of_distributed_computing — Fallacies ⚠️ (Ousterhout atfı doğrulanamadı)
15. https://12factor.net — Twelve-Factor App, ~2011
16. https://cheatsheetseries.owasp.org — OWASP Cheat Sheet Series (Defense-in-Depth)
17. https://devguide.owasp.org/en/02-foundations/03-security-principles/ — OWASP güvenlik prensipleri
18. https://martinfowler.com/bliki/MonolithFirst.html — MonolithFirst, 2015
19. https://martinfowler.com/articles/microservices.html — Microservices, Fowler & Lewis, 2014
20. https://arxiv.org/abs/2401.11867 — Modular Monolith, arXiv 2024 (primer: https://kamilgrzybek.com)
21. Birleşik ek kaynaklar — Transactional Outbox: https://microservices.io/patterns/data/transactional-outbox.html · https://debezium.io/blog/ · CQRS PDF (⚠️ erişilmedi): https://cqrs.files.wordpress.com/2010/11/cqrs_documents.pdf · CAP: https://www.cs.cmu.edu/~dga/15-712/GL-cap.pdf · Hexagonal: https://alistair.cockburn.us/hexagonal-architecture · Prime Video (ton farkı): https://devclass.com · Alternatif atıf: https://web.stanford.edu/~ouster

**Sayım notu:** 21 ana kayıt; 21. satırda birleşik alt kaynaklar toplanmıştır (outbox ×2 · CQRS PDF · CAP · hexagonal · devclass · Ousterhout alternatifi).

---

*Rapor sonu — 2026-10-09 · authority: SSOT (araştırma raporu) · ADR DEĞİL.*
