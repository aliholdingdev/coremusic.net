# prompts — 4 Aktif Öğrenme Protokolü (Kopyala-Yapıştır · K atıflı)

> **Dosyanın işi:** Bu dosya **çıktının kendisidir** — 4 tam prompt, `…` yer tutucularıyla
> kopyala-yapıştır hazır. Her protokolün altında: ne zaman kullanıldığı, **hangi kaynağa
> (K-numarası) dayandığı**, oturum icra notları ve sık hatalar. K-numaralarının tam
> karşılığı `sources.md`'dedir (Truth Mode: K-numarası olmayan iddia bu skill'de yazılmaz).
>
> **Ham girdi:** Halil Özat, "Yapay Zekâyla Akıllanmak İçin 4 Prompt" (9 Ekim 2026).
> Metinler v1.0.0'da revize edildi, v4.0.0'da kanıt gerekçeleriyle derinleştirildi.
>
> **Ortak kullanım kuralı:** Prompt içindeki `…` yer tutucuları **kullanıcı bilgisiyle**
> doldurulur; model bilgisiyle **uydurulmaz**. Boş `…` ile oturum başlatılmaz (checklist #2).

---

## §1 Sokratik Yöntem — "Cevabı kendim bulacağım"

> **Ne zaman:** Takıldığım bir noktada cevabı kendim bulmak istediğimde.
> **Kullanıcı tetikleyicileri:** 'sokratik', 'sokratik yöntem', 'cevap verme soru sor'.

### Gerekçe ve dürüst konumlandırma

- Sokratik sorgulama **sistematik ve disiplinli** bir sorgulama geleneğidir; yürütücü
  yalnız soru sorar — kural metni birebir (K3: "you are allowed only to ask questions").
- Pratik icra kuralları (planlanmış sorular · bekleme süresi · periyodik yazılı özet) K4'ten:
  UConn CETL, **en az 30 saniye bekleme** ve anahtar noktaların **periyodik yazılı özetini**
  önerir; eğitmen "sanki bilmiyormuş gibi" davranır ki sahiplik öğrenciye geçsin.
- **Dürüüstlük notu:** Sokratik yöntemin "başarı yüzdesi" gibi deneysel kanıtı bu oturumda
  doğrulanmadı → bu skill'de "kanıtlanmış" değil **"otoriter uygulama geleneği (K3/K4)"**
  diye konumlandırılır (`sources.md` §4 reddedilen iddialar).

### Tam prompt

```text
Şu an ... üzerinde çalışıyorum ve ... noktasında takıldım.
Senden bir Sokrates gibi davranmanı istiyorum. Cevabı ya da çözümü bana verme;
onu kendi başıma bulmak istiyorum.

Kurallar:
- Doğrudan cevap, düzeltilmiş hâl ya da hazır çözüm VERME.
- Önce bu konuda ne düşündüğümü ve neyi denediğimi sor.
- Beni doğru yöne götürecek yönlendirici soru sor; her seferinde YALNIZCA bir soru.
- Takılırsam en küçük ipucuyla başla. "bir sonraki ipucu" dersem bir seviye daha aç
  (ipucu 1 → 2 → 3; 3'ten sonra da bulamazsam kendi çözümümü tartışmaya açarız).
- Doğru yoldaysam bunu açıkça söyle. Yanlış yoldaysam, nerede saptığımı kendim fark
  etmemi sağlayacak bir soru sor.
- Ben ısrar etsem bile cevabı söyleme. Bilmem gereken bir olgu varsa ve uyduramazsan
  "bilmiyorum / UNKNOWN" de, yine de cevabı sen verme.
- Bir soru sorduğunda cevabımı beklemeden devam etme.

Şuna kadar yaptıklarım:
...
```

### Oturum icra notları (K3 · K4)

| # | Not | Kaynak |
|---|-----|--------|
| 1 | Soru tiplerini sırayla kullan: netleştirme → varsayım → kanıt → sonuç → bakış açısı → sorunun kendisi | K3 (6 kategori) |
| 2 | Kullanıcı cevapladıktan sonra **bekle** — 30 sn sessizlik normaldir, aceleye getirme | K4 |
| 3 | Tur 1'de önce "ne düşündün, ne denedin?" sor (K4'ün "önce mevcut anlayışı çekme" hattı) | K4 · rules R1 |
| 4 | Tartışılan anahtar noktayı oturum içinde **periyodik tek satırlık özetle** (yazılı özet) | K4 |
| 5 | Kullanıcı doğruladığında/yanıldığında bunu açıkça söyle — belirsiz övgü yok | anti-pattern #6 |

### Sık hatalar

- 2+ soru birden sormak → anti-pattern #2 (R1 ihlali).
- "Aslında şöyleymiş" diye cevabı sızdırmak → anti-pattern #1 (protokol çöker).
- İpucunu baştan 3. kademe vermek → rules R2 kademe sırası.

---

## §2 Feynman Tekniği — "Öğrendiğimi gerçekten öğrendim mi?"

> **Ne zaman:** Öğrendiğimi sandığım konuyu gerçekten öğrenip öğrenmediğimi test etmek için.
> **Kullanıcı tetikleyicileri:** 'feynman', 'feynman tekniği', 'çocuğa anlatır gibi'.

### Gerekçe ve dürüst konumlandırma (kritik)

- **Tarihsel gerçek (3 bağımsız kaynakla çaprazlandı, K8):** Richard Feynman **4 adımlı
  bir çalışma yöntemi yayımlamamıştır**. "Feynman tekniği" adı sonradan popülerleştirilmiş
  bir pakettir. Bu yüzden paket "kanıtlanmış Feynman yöntemi" diye **satılmaz**;
  parçalarının kanıtına (active recall · self-explanation · bilgi-boşluğu tespiti) yaslanır.
- K8 bizzat itiraf eder: tekniğin büyük-ölçekli bir çalışması yoktur; altındaki ilkeler
  bilişsel psikolojide araştırılmıştır (K8'in kendi cümlesi, `sources.md` §3).
- **Parça kanıtı:** self-explanation Dunlosky 2013'te **moderate utility**'dir (K1) —
  yani "işe yarıyor ama practice testing kadar değil". Protégé/öğretim-beklentisi yönü
  K9 ile **⚠️ VERIFICATION REQUIRED** işaretli olarak destekleyici nottur; sayısal etki
  bu skill'de taşınmaz.

### Tam prompt

```text
Şu an ... konusunu öğreniyorum. Sana bu konuyu, hiç bilmeyen bir çocuğa anlatır gibi
kendi kelimelerimle anlatacağım.

Anlatımımı bitirdiğimde şunları yap:
1. Yanlış anlattığım ya da yanlış anladığım yerleri göster. Doğrusunu hemen söyleme;
   önce neden yanlış olduğunu düşünmemi sağlayacak bir soru sor.
2. Bu konuyu gerçekten bilen birinin mutlaka bahsedeceği ama benim hiç değinmediğim
   noktaları listele.
3. Belirsiz kaldığım, ezbere konuştuğum ya da teknik terimlerin arkasına saklandığım
   yerleri işaretle.
4. Konuyu gerçekten anlayıp anlamadığımı ortaya çıkaracak tek bir zor soru sor.

Geri bildirimin her maddesine 0-10 arası bir güven puanı ver; kanıtlayamadığın iddiayı
yazma (yoksa "⚠️ VERIFICATION REQUIRED" işaretle). Anlatımımı övmek için değil,
eksiklerimi bulmak için değerlendir.

Anlatımım:
...
```

### Oturum icra notları

| # | Not | Kaynak |
|---|-----|--------|
| 1 | Anlatım **notsuz** üretilir (K8'in 4. adım standardı: ezber değil anlama) | K8 |
| 2 | Maddeler 1-4 **sırayla ve tam** uygulanır; "genel olarak iyi" diye özet geçilmez | prompt metni |
| 3 | Her geri bildirim maddesine 0-10 **güven puanı** (R3 rubriği ile aynı ölçek) | R3 |
| 4 | Eksik + sağlam birlikte yazılır — yalnız eksik yazmak yanlı, yalnız övmek teyit önyargısı | anti-pattern #5/#6 |
| 5 | Sayısal etki iddiası ("bu yöntem %X artırdı") YAZILMAZ → K8/K9'da garanti yok | anti-pattern #7 |

### Sık hatalar

- Anlatımı övmek (eksik varken) → anti-pattern #6.
- Doğrusunu anında söyleyip madde 1'i atlamak → anti-pattern #1.
- Kanıtsız "bu teknik kanıtlanmıştır" demek → `sources.md` §3 (K8 itirafı).

---

## §3 Şeytanın Avukatı — "Karar öncesi karşı taraf"

> **Ne zaman:** Önemli bir karar vermeden ya da güçlü bir fikrim olduğunda.
> **Kullanıcı tetikleyicileri:** 'şeytanın avukatı', 'itiraz et', 'karşı argüman'.

### Gerekçe ve dürüst konumlandırma

- Yapı taşı: **consider-the-opposite** stratejisi — "aynı kanıt ters çıksaydı da aynı
  değerlendirmeyi yapar mıydın?" talimatı önyargılı değerlendirmeyi azalttı (K5, 1984 klasik).
- **Kritik kontrast (K5):** "mümkün olduğunca tarafsız olun" gibi motivasyonel talimatlar
  işe yaramadı — **stratejik** talimat kazandı. Bu yüzden prompt'ta "tarafsız ol" değil,
  somut 6 adımlı strateji var.
- **Karışık kanıt dürüstlüğü (K6 · K7):** Yakın yeniden denemelerde consider-the-opposite
  tek başına **zayıf/boş** sonuç verebildi (K6 kayıtlı rapor) veya bağlama bağlı çalışabildi
  (K7, COVID bağlamı, Study 2 pozitif / Study 1 etkisiz). Dolayısıyla bu protokol
  "önyargıya karşı stratejik direnç" düzeyinde konumlandırılır; **karar kalitesini garanti
  artırdığı iddia edilmez** (anti-pattern #7 — abartı yasağı).

### Tam prompt

```text
... hakkında şöyle düşünüyorum: ...
Bu görüşe şu sebeplerle sahibim: ...

Bu görüşe şu kanıtlarla ulaştım (araştırmam / deneyimim):
...

Senden şeytanın avukatını oynamanı istiyorum. Bana hak vermeye çalışma:
1. Bu görüşe karşı kurulabilecek en güçlü argümanı, karşı tarafın en iyi savunucusu
   gibi kur.
2. Mantığımdaki boşlukları ve dayanaksız varsayımlarımı göster.
3. Bu görüşe varmamda etkili olmuş olabilecek önyargıları belirt (kaynak seçim,
   teyit önyargısı, medya yansıması vb.).
4. Fikrimi değiştirmem için hangi kanıtı görmem gerektiğini söyle — spesifik ve
   test edilebilir yaz.
5. Her itirazın gücünü 1-10 arasında puanla. Zorlama eleştiri uydurma. Görüşümün
   gerçekten sağlam olan taraflarını da kısaca belirt.
6. Kanıt sunamadığın iddiaları uydurma; elinde veri yoksa "bu nokta veriyle
   desteklenemiyor — UNKNOWN" de.
```

### Oturum icra notları

| # | Not | Kaynak |
|---|-----|--------|
| 1 | Madde 4'teki "test edilebilir kanıt" K5'in dersidir: somut karşı-koşul yaz, "daha fazla araştır" deme | K5 |
| 2 | "Tarafsız ol" gibi genel telkin verme — işe yaramadı; **yapısal** itiraz kur | K5 |
| 3 | Puanlar 1-10; zayıf itiraz 1-3 olarak açıkça etiketlenir (zorlama yok) | R3 · anti-pattern #8 |
| 4 | K6/K7 karışıklığı oturumda **gizlenmez**: "klasik kanıt pozitif, yakın denemeler karışık" | sources §4 çelişki kuralı |
| 5 | Sağlam taraflar madde 5'te zorunlu — yalnızca negatif yazmak dengesiz rapordur | anti-pattern #6 |

### Sık hatalar

- Zorlama/ucuz eleştiri uydurmak → anti-pattern #8.
- "Bu yöntem karar kalitesini artırır" diye satmak → anti-pattern #7 (K6/K7 reddeder).
- Kanıtsız iddia üretmek → madde 6 zorunlu (`UNKNOWN`).

---

## §4 Aktif Hatırlama — "Sına beni, unutmayayım"

> **Ne zaman:** Bir konuyu çalıştıktan sonra öğrendiklerimi kalıcı hâle getirmek için.
> **Kullanıcı tetikleyicileri:** 'aktif hatırlama', 'beni sına', 'sınav yap', 'öğrenmemi sağla'.

### Gerekçe ve dürüst konumlandırma (bu skill'in kanıt çekirdeği)

- **En yüksek kanıt seviyesi:** Dunlosky vd. 2013 (K1) — 10 teknik içinden **practice
  testing = high utility**, **distributed practice = high utility**; rereading = low.
  "Practice testing instead of rereading" ifadesi özet metninde birebir.
- **Testing effect sayıları (K2, bu turda doğrulandı — v1.0.0'daki ⚠️ kalktı):**
  1 hafta sonra test grubu **%56** recall vs tekrar-okuma **%42**; 5 dakikalık gecikmede
  tersi (o anlık okuma avantajlı). **Sonuç:** tekrar-okuma kısa vadede güveni şişirir,
  kalıcı öğrenmeyi değil — bu yüzden protokol "oku" değil "sına" der.
- Yanlış cevabı puanlayıp düzeltmek + birkaç gün sonra farklı ifadeyle tekrar sormak,
  hem testing effect (K2) hem dağıtık tekrar (K1) hattıdır.

### Tam prompt

```text
Şu an ... konusunu çalışıyorum. Aşağıya notlarımı / çalıştığım bölümü yapıştırıyorum.

Beni bu içerikten sına:
- Soruları sadece yapıştırdığım içeriğe dayandır (içinde olmayan bilgiyi soru diye sorma;
  dış bilgi gerekiyorsa önce "bu içerik dışında" diye işaretle).
- Toplam ... soru sor.
- Her seferinde tek soru sor; ben cevaplamadan bir sonraki soruya geçme.
- Zorluğu giderek artır: önce temel bilgi, sonra "neden"/"nasıl", en son öğrendiklerimi
  yeni bir duruma uygulamamı gerektiren sorular.
- Şık verme, cevabı kendim üreteyim. Ben cevaplamadan ipucu verme.
- Her cevabımı 0-10 arasında puanla ve eksik ya da yanlış kısmı açıkla. Doğru cevabı
  puanladıktan sonra KISA bir gerekçeyle düzelt (bu tek istisnadır).
- Yanlış yaptığım soruları oturumun sonunda farklı bir ifadeyle tekrar sor.
- Bitince: (a) zayıf olduğum noktaları özetle, (b) birkaç gün sonra tekrar çalışmam için
  5 soruluk tekrar listesi hazırla (dağıtık tekrar — Dunlosky 2013).

Notlarım:
...
```

### Oturum icra notları

| # | Not | Kaynak |
|---|-----|--------|
| 1 | "Yalnızca yapıştırılan içerik" sınırı **pozitif** bir kanıt kuralıdır: soru içeriğe dayanmıyorsa SKIP | R5 · anti-pattern #7 |
| 2 | Şık YOK, ipucu önce YOK — recall şıkla ölçülmez (K2'nin free-recall mantığı) | K2 · anti-pattern #4 |
| 3 | Puan + 1 cümle gerekçe + 1 cümle düzeltme zorunlu (bu protokolde doğru cevabın kısa gerekçesi **tek istisna**) | R3 |
| 4 | Yanlışlar oturum sonunda **farklı ifadeyle** tekrar sorulur (aynı sorunun kopyası değil) | K1/K2 |
| 5 | R4 oturum raporu **zorunlu** — rapor yoksa dağıtık tekrar planı da yok, etki kaybolur | R4 · anti-pattern #10 |

### Sık hatalar

- Şık vermek / ipucu önce vermek → anti-pattern #4.
- Puan verip gerekçe yazmamak → anti-pattern #5.
- Raporu atlamak → anti-pattern #10 (K1'in distributed practice şartı çöker).

---

## §5 Protokol Seçimi (tek ekran)

```text
Karar mı, takılma mı, "anladım" mı, "unutuyorum" mu?
- Karar / güçlü fikir      → §3 Şeytanın Avukatı
- Takıldım (çözüm bulacağım) → §1 Sokratik
- "Öğrendim, test et"       → §2 Feynman
- "Çalıştım, unutuyorum"    → §4 Aktif Hatırlama
- Emin değil                → TEK soru: "şimdi en çok ne yapmak istiyorsun:
                              karar vermek / takıldım / öğrenmek / sınamak?"
```

**Kullanım sırası önerisi:** karar öncesi → §3 · takıldım → §1 · öğrendim sanırım → §2 ·
çalıştım, unutuyorum → §4. Protokoller **serbestçe zincirlenebilir** (ör. §1 ile takılma
açılır → §2 ile anlatım sınanır → §4 ile kalıcılaştırılır), ancak her protokol kendi
kurallarıyla (R1-R5) yürütülür; zincirde bile cevap yasağı §1/§2'de geçersiz kılınamaz.

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
