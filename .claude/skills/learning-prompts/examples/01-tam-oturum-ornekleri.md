# examples/01 — Tam Oturum Örnekleri (Girdi → Oturum → Çıktı)

> **Dosyanın işi:** 4 protokolün de **tam döngüsünü** gösterir: ham kullanıcı girdisi →
> yürütülen tur(tur)lar → kapanış/rapor. Örnekler öğretim amaçlıdır; içlerindeki olgular
> örnek konuya aittir ve `rules.md` R5'e tabidir (örnek olgular kaynak iddiası değildir —
> kaynak iddiası taşıyan cümleler `sources.md`'ye K-numarası ile bağlıdır).
>
> Her örnekte **uygulanan kurallar** satır sonunda etiketlenmiştir (R1-R7, anti-pattern #n).

---

## Örnek A — Sokratik (girdi → 3 tur → kapanış)

**Girdi (kullanıcı — tam metin):**
```text
Şu an PDO prepared statement'ları üzerinde çalışıyorum ve "neden bazen
bindValue yerine bağlama parametresini sorgu içine gömüyormuş gibi davranıyor"
noktasında takıldım. Senden bir Sokrates gibi davranmanı istiyorum. Cevabı ya da
çözümü bana verme; onu kendi başıma bulmak istiyorum.
...
Şuna kadar yaptıklarım: sorguyu string birleştirme ile yazınca çalıştı,
prepare/bindValue ile yazınca sonuç farklı çıktı.
```

**Yürütülen oturum:**

| Tur | Modelin çıktısı | Uygulanan kural |
|-----|-----------------|-----------------|
| 1 | "Önce ne düşündün, ne denedin: bağla değişkenini ne olarak tanımladın ve sorguda hangi yer tutucu biçimini kullandın (isimli `:x` mi, konumsal `?` mi)?" | R1 tek soru · kademe 0 · anti-pattern #1 yok (cevap yok) |
| 2 (kullanıcı: "? ile bindValue(1, $x) kullandım") | "Puan yok, sıralamayı test edelim: `bindValue`'ya verdiğin sıra ile `?`'lerin sorgudaki sırası birebir aynı mı — sorguyu satır satır okuyup işaretle." | kademe 1 (çerçeve daralttı, cevap hâlâ yok) · R6 bekleme |
| 3 (kullanıcı: "eşleşiyor") | "Doğru yönde gidiyorsun (sıra tutuyor). Peki farklı çıktığında beklediğin ile aldığın değeri **nerede** karşılaştın — tip farkı (int vs string) olabilir mi?" | "doğru yönde" açıkça söylendi (R2/K3) · hâlâ cevap yok |

**Kapanış:** kullanıcı kendi hatasını bulduğunda oturum biter — model çözümü **yazmaz**
(anti-pattern #1). Kullanıcı ısrarla cevap isterse R2-4 uygulanır: "Cevabı tartışalım mı?"
(tek soru, açık rıza) — rıza gelmeden cevap yok.

**Yanlış yürütüm (kontrol için — NE YAPILMAZ):**
```text
✗ Tur 2 yerine: "Sorun şu: PDO'da ? yerine :name kullanmalısın, ayrıca
  fetchRow değil fetchAll çağırmalısın."  → anti-pattern #1 (çözüm sızdırdı)
✗ Tur 2'de: "Sıran eşleşiyor mu ve tip dönüşümü yaptın mı?"  → anti-pattern #2 (2 soru)
```

---

## Örnek B — Feynman (girdi → anlatım → 4 maddelik denetim → kapanış)

**Girdi (kullanıcı):**
```text
Şu an BCNF konusunu öğreniyorum. Sana bu konuyu, hiç bilmeyen bir çocuğa anlatır
gibi kendi kelimelerimle anlatacağım.
...
Anlatımım: "BCNF, 3NF'in daha sıkı hâli. Bir tabloda her non-key alan tam olarak
anahtara bağlı olmalı. Zaten 3NF de öyle; BCNF'de bir fark var ama pratikte pek
fark etmez, zaten herkes 3NF'e yakın tablo kuruyor."
```

**Yürütülen çıktı (maddeler 1-4 sırayla — anti-pattern #13 yok):**

```markdown
1. Yanlış anladığın yer (doğrusu söylenmedi — önce nedenini düşün): "pratikte pek
   fark etmez" iddianla, BCNF'in 3NF'ten **daha sıkı** olduğunu söylüyorsun — daha
   sıkı olan bir kuralın "fark etmemesi" için neyi görmemiz gerekir?
   (neden yanlış olabileceği sorusu: fark, hangi tablo deseninde ortaya çıkar?)
2. Konuyu gerçekten bilen birinin mutlaka değinip senin değinmediğin noktalar:
   - **Aday anahtarın tek başına belirlenmesi** (fonksiyonel bağımlılıkların kümesi)
   - **Sol taraf süperanahtar** ihlali durumu (BCNF'in ayrıştığı tam kritik nokta)
   - Aday anahtar birden çoksa hangisinin kırıldığı
3. Belirsiz/ezbere/terimin arkasına saklanan yerler: "herkes 3NF'e yakın kuruyor"
   — bu bir ölçüm değil, genelleme; hangi kaynağa dayanıyor?
4. Tek zor soru: `X → Y` bağımlılığı olan bir tabloda `X` aday anahtar değil ama
   `X` tek başına `Y`'yi belirliyor — bu tablo 3NF'te kalır mı, BCNF'e geçer mi?
   (Cevabını kendi türet; sonra güven puanını 0-10 ver.)

Güven puanları (anlatımına göre): madde 1 = 3/10 (tanım kafa içinde karışık),
madde 2 = 4/10 ( eksik), madde 3 = 5/10 (kaynaksız genelleme), madde 4 = ? (sen
cevaplayınca değerlendirilecek).
```

**Kapanış notu:** Doğrusu **verilmedi** (madde 1'de yalnız "düşünmeni sağlayacak soru" —
anti-pattern #1 yok); puanlar R3 rubriği; sayısal etki iddiası **yazılmadı
(anti-pattern #7** — "Feynman tekniği kanıtlanmıştır" denmedi; paket konumlandırması
`overview.md` §1'e bağlı).

---

## Örnek C — Şeytanın Avukatı (girdi → puanlı itiraz seti → kapanış)

**Girdi (kullanıcı):**
```text
Yeni servis için ORM kullanmalıyız; geliştirme hızı için. Sebeplerim: süre
kazancı, az boilerplate. Bu görüşe şu kanıtlarla ulaştım: son iki projede elle
yazdığım sorgularda zaman kaybettim.
```

**Yürütülen çıktı (maddeler 1-6 uygulanmış hâli):**

| # | İtiraz (madde) | Puan | Kural/Dayanak |
|---|----------------|------|---------------|
| 1 | **En güçlü karşı argüman:** bu repo'nun mimari kararı (ADR) `no ORM` diyor; "hız" gerekçesi tek başına bir ADR'yi **ezmez** — yeni ADR + gerekçe gerekir | **9/10** | madde 1 · kanıt: repo kararı |
| 2 | **Boşluk/varsayım:** "geliştirme hızı" ölçülmemiş — hangi görevde ne kadar kazanç? (süre ölçümü yok) | 7/10 | madde 2 |
| 3 | **Olası önyargı:** seçilmiş örneklerin görünürlüğü (son 2 proje = örnekleme + teyit önyargısı ihtimali) | 5/10 | madde 3 (K5 hattı: kaynak seçimi) |
| 4 | **Fikri değiştirecek kanıt:** aynı ekibin ORM'li dalında **ölçülmüş** teslim süresi farkı — spesifik: "4 hafta, aynı özellik, iki dal" | — (aranacak kanıt) | madde 4 · test edilebilir yazım |
| 5 | **Sağlam taraf:** repository katmanı yoksa tekrar eden sorgu kodu gerçek — bu senin güçlü tarafın | 6/10 | madde 5 (dengeli rapor — anti-pattern #6 yok) |
| 6 | "Önyargıyı kesin çözer" iddiası: **üretilmedi** — bu konudaki kanıt karışık (K5 klasik pozitif, K6/K7 yakın denemeler karışık) | — | madde 6 · anti-pattern #7 (abartı yok) |

**Kapanış:** karar **kullanıcıya ait** — model "kullan/ kullanma" demez; çıktı, karar
öncesi **kanıt ve karşı-koşul listesidir**.

---

## Örnek D — Aktif Hatırlama (girdi → 3 soru sırası → puan → R4 raporu)

**Girdi (kullanıcı):**
```text
Şu an middleware konusunu çalışıyorum. Notlarım aşağıda. Beni bu içerikten sına:
toplam 8 soru, tek tek, şık yok, zorluğu artır.
...
Notlarım: [2 sayfa middleware notu — CoreMusic PHP örneğiyle]
```

**Yürütülen soru-cevap (özet — sırayla, R1):**

**Soru 1 (temel):** "Bir istek middleware zincirinden geçmeden controller'a ulaşabilir mi?"
**Cevap:** "Hayır, hep birlikte çalışırlar."
**Puan:** **7/10.** "Doğru yön ama 'hep birlikte' belirsiz — zincirde biri `next()`
çağırmazsa ne olur, notlarında §3 bunu açıklıyor. (tek eksik)"

**Soru 4 (kullanıcı cevabı):** "Foreign key, iki tablo arasında ilişki kurar ve tekrar
eden veriyi engeller."
**Puan:** **5/10.** "Doğru yönde ama iki kritik parçayı atladın: (1) foreign key'in
asıl işlevi bütünlük **zorlamaktır** (yabancı anahtar olmayan değer yazılamaz) —
'engeller' dediğin şey bunun sonucu; (2) kendisi tekrarı engellemez, normalizasyon
eder. Notlarında bu ayrım §2'de geçiyor." *(bu düzeltme, R3'ün tek istisnasıdır:
puan sonrası kısa gerekçe — anti-pattern #1'e girmez)*

**Soru 7 (uygulama — zor):** "Rate-limit middleware'ü 429 dönerse ve kullanıcı
retry ederse, sıradaki hangi katman hangi sözleşmeyi bozabilir?" → **4/10**, gerekçe:
"uç noktayı gördün ama sözleşmeyi (JSON hata şeması) söylemedin — notlarında
hata-yanıtı bölümü var."

**Oturum sonu (R4 — zorunlu, anti-pattern #10 yok):**

```markdown
## Oturum Raporu
- Ortalama puan: 6.1/10 (8 soru)
- Zayıf noktalar: FK vs normalizasyon ayrımı (5/10) · 429-retry hata sözleşmesi (4/10)
- Yanlış cevaplananlar: S4 (bütünlük-zorlama) · S7 (hata sözleşmesi) → tekrar soruldu, 7/10
- Tekrar listesi (~3 gün sonra, farklı ifadeyle):
  1. "Bir tabloda NULL değerli foreign key satırı olabilir mi; hangi koşulda?" (uygulama)
  2. "Normalizasyon yapılmamış bir tabloyu BCNF'e taşırken hangi bağımlılıklar kırılır?"
  3. "429 yanıtını istemci tarafında nasıl test edersin?" (farklı soru biçimi)
  4. "FK ile UNIQUE arasındaki fark bir senaryoda nasıl belli olur?"
  5. "Middleware zincirinde bir katman 'next' demezse logging katmanı ne görür?"
- Sonraki çalışma önerisi: dağıtık tekrar — günde 2 soru, 3 gün (K1 distributed practice)
```

**Yanlış yürütüm (kontrol için):**
```text
✗ "Şıklar: a) evet b) hayır"                     → anti-pattern #4 (şık)
✗ "5/10" (gerekçesiz)                            → anti-pattern #5
✗ "Bu konuda literatür %40 artış gösteriyor"      → anti-pattern #7 (kaynaksız sayı)
✗ Raporu atlamak                                  → anti-pattern #10
```

---

## Örnek E — Yanlış durum → doğru protokol (anti-pattern #11 düzeltilmiş)

**Girdi:** "Bu migration'ı production'a alalım mı, sana soruyorum: cevap ver."
(Süreç/karar isteği + "cevap ver")

| | Davranış | Sonuç |
|---|----------|-------|
| ✗ | Sokratik başlatıp cevabı da sızdırmak | anti-pattern #1 + #11 — iki protokol aynı anda imkânsız |
| ✗ | Cevap sormadan "evet al" demek (yıkıcı eylem + onaysız) | R7 (yıkıcı eylem onay ister) + kanıtsız kesinlik |
| ✓ | **Tek soru:** "Bunu bir karar olarak mı tartışalım (Şeytanın Avukatı: artı/eksi/kanıt), yoksa migration'ın teknik detayını mı öğrenmek istiyorsun (Sokratik)?" → sonra protokolu seç | overview §3 · R1 tek soru · anti-pattern #11 kapandı |

---

**Not:** Örneklerde geçen kural numaraları (R1-R7) `references/rules.md`'ye, anti-pattern
numaraları `references/anti-patterns.md`'ye, K-numaraları `references/sources.md`'ye
bağlanır; hiçbiri uydurma kural/kaynak değildir.

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
