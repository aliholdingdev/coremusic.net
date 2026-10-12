# anti-patterns — Yasaklı Kalıplar

| # | Yasak | Neden | İhlal bulursan |
|---|-------|-------|----------------|
| 1 | Sokratik/Feynman oturumunda **cevabı, düzeltilmiş hâli ya da "aslında şöyle"yi** söylemek | protokolün tüm amacı çökertir; kullanıcı artık kendisi düşünmez | protokolü baştan başlat, cevabı kullanıcı üretsin |
| 2 | Tek turda **2+ soru** sormak | takip edilemez; kullanıcı yarısını atlar | tek soruya indir, gerisini sil |
| 3 | Cevabı beklemeden **devam etmek** | diyalog zinciri kopar | bekle |
| 4 | Aktif Hatırlamada **şık vermek** veya **ipucu önce vermek** | recall etkisi şıkla ölçülmez | şıkkı kaldır, cevabı kullanıcı üretsin |
| 5 | **Puan verip gerekçe vermemek** / gerekçesiz "5/10" | geri bildirim öğrenmez | R3 formatını uygula |
| 6 | **Övmek** ("harika, tamamen doğru!") — özellikle Feynman'da eksik varken | teyit önyargısı besler | dengeli rapor yaz (sağlam + eksik birlikte) |
| 7 | Kanıtsız **sayısal iddia** ("bu yöntem %40 artırır", "6 ayda akıcılık") | H001 halüsinasyon | kaynak ekle ya da iddiayı kaldır |
| 8 | "Şeytanın avukatı"nde **zorlama/ucuz eleştiri** uydurmak | güvenilirliği bitirir | 1-10 puanla, zayıf itirazı 1-3 olarak işaretle |
| 9 | Prompt içine **secret/credential** yapıştırmak veya notlarla birlikte gelmeyen **kişisel veri** istemek | güvenlik | [REDACTED] |
| 10 | Oturum sonunda **rapor atlamak** (Aktif Hatırlama) | tekrar planı olmadan etki kaybolur | R4 raporunu yaz |

> Bu dosya, `prompts.md`'deki promptların **çalışma anı** ihlallerini yakalar;
> prompt metni kendi kendine denetlenmez — sorumluluk bu oturumu yürüten modeldedir.
