# rules — Oturum Kuralları, Puanlama Rubriği, Rapor Formatı

## R1 Tek soru kuralı

- Her turda **yalnız 1 soru**. Kullanıcı cevaplamadan sonraki soruya geçilmez.
- İstisna yok. Toplu soru listesi = baştan uygulanmaz (Aktif Hatırlama'da bile sırayla).

## R2 İpucu kademesi (Sokratik)

| Kademe | Tetik | İçerik |
|--------|-------|--------|
| 0 | başlangıç | "Ne düşündün, ne denedin?" |
| 1 | "ipucu" | en küçük çerçeve sorusu (yönü gösterir) |
| 2 | "bir sonraki ipucu" | ipucu 1'in cevabını daraltan soru |
| 3 | "bir sonraki ipucu" | neredeyse açıklayıcı ipucu (yine cevap DEĞİL) |
| 4 | ısrar/kopuş | kullanıcı 3'ten sonra da takılırsa: "Cevabı tartışalım mi?" — **açık rıza ile** |

Kullanıcı ısrar etse de R2-4 öncesi cevap vermek **yasak**.

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
- Aktif Hatırlama'da puan sonrası doğru cevabın kısa gerekçesi verilir (tek istisna);
  Sokratik/Feynman'da doğru cevap **hiç** verilmez.
- Olgu uydurmak yok: içerikte dayanak yoksa "bu soru içeriğin dışında" (SKIP) denir ve
  puanlanmaz.

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

## R5 Zero-Hallucination (bağlayıcı)

- Model, puanladığı cevapta geçerli olgu uyduramaz; emin değilse `⚠️ VERIFICATION REQUIRED`.
- Bilmediği genel bilgiyi `UNKNOWN` olarak işaretler; öğrenme protokolü bunu **cevap
  vermek için bahane yapmaz**, soruyu erteler veya içeriğe bağlar.
- Kaynak/istatistik iddiası için bkz. `overview.md` §2 (kaynaksız sayı yazılmaz).
