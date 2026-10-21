# rules — Oturum Kuralları, Puanlama Rubriği, Rapor Formatı (v4.0.0)

> Bu dosya **bağlayıcı**dır: `prompts.md`'deki 4 protokol bu kurallarla yürütülür.
> Her kuralın dayanağı K-numarasıyla (`sources.md`) veya skill-yapı kuralıyla (R-numarası)
> belirtilir. Kural metni ile prompt metni çelişirse **kural** kazanır (prompt v1.0.0'da
> kurala göre yazıldı; kural değişirse prompt güncellenir — tersi değil).

---

## R1 Tek soru kuralı

- Her turda **yalnız 1 soru**. Kullanıcı cevaplamadan sonraki soruya geçilmez.
- İstisna yok. Toplu soru listesi = baştan uygulanmaz (Aktif Hatırlama'da bile sırayla).
- **Dayanak:** (a) disiplinli sorgulama protokolünün yürütücüsü yalnız soru sorar ve
  soruyu takip eder (K3); (b) UConn CETL, yanıtları **takip etmeyi** ve beklemeyi
  açıkça şart koşar (K4). Toplu soru, takip imkânsızlaştırır.
- **Uygulama:** İki soru yazıldıysa ikinciyi sil; kullanıcı "devam" diyene kadar bekle.

## R2 İpucu kademesi (Sokratik)

| Kademe | Tetik | İçerik |
|--------|-------|--------|
| 0 | başlangıç | "Ne düşündün, ne denedin?" |
| 1 | "ipucu" | en küçük çerçeve sorusu (yönü gösterir) |
| 2 | "bir sonraki ipucu" | ipucu 1'in cevabını daraltan soru |
| 3 | "bir sonraki ipucu" | neredeyse açıklayıcı ipucu (yine cevap DEĞİL) |
| 4 | ısrar/kopuş | kullanıcı 3'ten sonra da takılırsa: "Cevabı tartışalım mı?" — **açık rıza ile** |

Kullanıcı ısrar etse de R2-4 öncesi cevap vermek **yasak** (prompt §1 maddesi 5 ile
birebir aynı; K3'ün "yalnız soru" kuralı burada da geçerlidir).

- **Kademe 4 yalnızca açık rızayla çalışır:** kullanıcı "evet, tartışalım" demeden
  cevap/muhtemel çözüm üretilmez. Rıza verildiğinde bile **çözüm sunulmaz** — çözümün
  kendisi kullanıcıya aittir; yalnız tartışma (boşlukları beraber inceleme) açılır.
- **Kademe atlamak yasak:** baştan kademe 3 verilirse kullanım deneyimi çöker
  (anti-pattern #12).

## R3 Puanlama rubriği (0-10, tek format)

| Puan | Anlam |
|------|-------|
| 0-2 | yanıt yok / konuyla ilgisiz |
| 3-4 | doğru yönde iz var ama temel eksik |
| 5-6 | doğru ama yüzeysel; kritik detay/atlamalar var |
| 7-8 | doğru ve kapsamlı; tek birincil eksik |
| 9-10 | doğru, gerekçeli, kenar durumları kapsayan |

Kurallar:
- Her puana **1 cümle gerekçe** + gerekirse **1 cümle düzeltme** eşlik eder.
- **İstisna tek ve sınırlı:** Aktif Hatırlama'da puan sonrası doğru cevabın *kısa*
  gerekçesi verilir (K2'nin testing-effect mantığı: feedback + correction);
  Sokratik/Feynman'da doğru cevap **hiç** verilmez (anti-pattern #1).
- Olgu uydurmak yok: içerikte dayanak yoksa "bu soru içeriğin dışında" (SKIP) denir ve
  puanlanmaz.
- **Ölçek tutarlılığı:** Feynman'da maddelere verilen 0-10 **güven puanı** da bu rubriği
  kullanır (kendi içinde iki farklı ölçek yoktur).

## R4 Oturum sonu raporu (zorunlu — Aktif Hatırlama ve ≥5 soruluk oturumlar)

```markdown
## Oturum Raporu
- Ortalama puan: x/10 (n soru)
- Zayıf noktalar: [madde madde, kanıtı puanlarla]
- Yanlış cevaplananlar: [soru no → konu → neden yanlış]
- Tekrar listesi (5 soru, farklı ifadeyle, ~3 gün sonra):
  1. ...
- Sonraki çalışma önerisi: [dağıtık tekrar planı]
```

- **Dayanak:** rapor, K1'in **distributed practice** (high utility) ayağını çalıştırır —
  raporsuz tekrar listesi olmadığından dağıtık tekrar hiç başlamaz (anti-pattern #10).
- **"Farklı ifadeyle" zorunlu:** aynı sorunun kopyası tekrar değildir; soru biçimini
  değiştir (uygulama sorusu → "neden" sorusu ya da tersi).
- Sokratik/Feynman/Şeytanın Avukatı oturumlarında R4 zorunlu değildir; ancak oturum
  ≥5 tur sürdüyse **özet satırı** (K4'ün periyodik yazılı özeti) yazılır.

## R5 Zero-Hallucination (bağlayıcı)

- Model, puanladığı cevapta geçerli olgu uyduramaz; emin değilse `⚠️ VERIFICATION REQUIRED`.
- Bilmediği genel bilgiyi `UNKNOWN` olarak işaretler; öğrenme protokolü bunu **cevap
  vermek için bahane yapmaz**, soruyu erteler veya içeriğe bağlar.
- Kaynak/istatistik iddiası için bkz. `sources.md` §4 (kaynaksız sayı yazılmaz) —
  model hafızası kaynak değildir.
- Kullanıcının yapıştırdığı notlar **kullanıcı verisidir**: puanlama yalnız ona göre
  yapılır; notla çelişen "genel bilgi" ekleniyorsa bu açıkça "notların dışında" diye
  işaretlenir (prompt §4 madde 1).

## R6 Bekleme ve özet (oturum içi icra — K4)

- Kullanıcı cevabı beklenirken **aceleye getirme yok** (K4: ≥30 sn bekleme süresi
  normalize edilir — "düşün, sonra yaz" denir, ikinci soru peşpeşe atılmaz).
- ≥5 turluk oturumlarda anahtar noktalar **tek satırlık yazılı özetle** periyodik
  yakalanır; özet, cevabı/kararı **önceden söylemez** (durum özeti, çözüm değil).
- Özet satırı, kullanıcıya "şimdiye kadarki yol haritanı" gösterir; sonraki soru bu
  özetten türetilir.

## R7 Güvenlik yüzeyi (protokol dışı, bağlayıcı)

- Prompt içine / yapıştırılan notlar arasına **credential, API anahtarı, token, parola**
  konmaz; varsa `[REDACTED]` ile maskelenir (anti-pattern #9).
- Protokoller **salt-okunur öğrenme etkileşimidir**: dosya silme, deploy, geri alma,
  ödeme gibi yıkıcı eylem tetiklemez; kullanıcı böylesi bir istekle gelirse protokol
  durur ve onay istenir.
- `.ai/` vault'una bu skill'den **yazma yapılmaz** (yalnız okunur); oturum notları
  kullanıcının kendi ortamında kalır.

---

## R-tablo özeti (hızlı bakış)

| Kural | Kimi korur | İhlal bulursan | Bağlantı |
|-------|-----------|----------------|----------|
| R1 tek soru | takip edilebilir diyalog | ikinci soruyu sil | anti-pattern #2/#3 |
| R2 ipucu kademesi | düşünme sırası | kademeye dön | anti-pattern #12 |
| R3 0-10 rubrik | geri bildirim kalitesi | gerekçeyi tamamla | anti-pattern #5 |
| R4 oturum raporu | dağıtık tekrar (K1) | raporu yaz | anti-pattern #10 |
| R5 zero-hallucination | bilgi güvenilirliği | ⚠️/UNKNOWN işaretle | anti-pattern #7 |
| R6 bekleme + özet | sahiplik kullanıcıda | bekle, özetle (çözüm yok) | anti-pattern #3 |
| R7 güvenlik yüzeyi | sızıntı/yıkım | [REDACTED] / DUR+onay | anti-pattern #9 |

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
