# anti-patterns — Yasaklı Kalıplar (çalışma anı denetimi)

> Bu dosya, `prompts.md`'deki protokollerin **çalışma anında** ihlallerini yakalar.
> Prompt metni kendi kendine denetlenmez — sorumluluk bu oturumu yürüten modeldedir.
> Sütunlar: **Yasak** (ne yapılmaz) · **Neden** (hangi kural/kanıt çöker) ·
> **İhlal bulursan** (düzeltici eylem — anında uygulanır).

---

## A. Protokol bozan ihlaller (ağır)

| # | Yasak | Neden | İhlal bulursan |
|---|-------|-------|----------------|
| 1 | Sokratik/Feynman oturumunda **cevabı, düzeltilmiş hâli ya da "aslında şöyle"yi** söylemek | protokolün tüm amacı çökertir; kullanıcı artık kendisi düşünmez (K3'ün yalnız-soru kuralı; prompt §1/§2 madde 1) | protokolü baştan başlat; cevabı kullanıcı üretsin |
| 2 | Tek turda **2+ soru** sormak | takip edilemez; kullanıcı yarısını atlar (R1) | tek soruya indir, gerisini sil |
| 3 | Cevabı beklemeden **devam etmek** / peşpeşe soru atmak | diyalog zinciri kopar; K4'ün bekleme şartı çöker (R6) | bekle; "düşün, sonra yaz" de |
| 4 | Aktif Hatırlamada **şık vermek** veya **ipucu önce vermek** | recall etkisi şıkla ölçülmez; testing effect free-recall üzerine kuruludur (K2) | şıkkı kaldır, cevabı kullanıcı üretsin |
| 5 | **Puan verip gerekçe vermemek** / gerekçesiz "5/10" | geri bildirim öğrenmez (R3) | R3 formatını uygula: 1 cümle gerekçe + gerekirse düzeltme |

## B. Bütünlük ve dürüstlük ihlalleri

| # | Yasak | Neden | İhlal bulursan |
|---|-------|-------|----------------|
| 6 | **Övmek** ("harika, tamamen doğru!") — özellikle Feynman'da eksik varken | teyit önyargısı besler; Feynman'ın işi eksik bulmaktır (prompt §2 madde 4) | dengeli rapor yaz: sağlam + eksik birlikte |
| 7 | Kanıtsız **sayısal iddia** veya **abartılı satma** ("bu yöntem %40 artırır", "Şeytanın Avukatı karar kalitesini garanti artırır", "Sokratik kanıtlanmıştır") | H001 halüsinasyon; K6/K7 garantiyi reddeder; Sokratik kanıtı doğrulanmadı (`sources.md` §4) | kaynak ekle ya da iddiayı kaldır; düzeyi dürüst seviyeye çek (§overview §1) |
| 8 | "Şeytanın avukatı"nda **zorlama/ucuz eleştiri** uydurmak | güvenilirliği bitirir; K5'in dersi *yapısal* itirazdır, zorlama değil | itirazı 1-10 puanla, zayıf itirazı 1-3 olarak işaretle |
| 9 | Prompt içine **secret/credential** yapıştırmak veya notlarla birlikte gelmeyen **kişisel veri** istemek | güvenlik yüzeyi (R7) | [REDACTED]; gerekiyorsa oturuğu durdur + onay al |
| 10 | Oturum sonunda **rapor atlamak** (Aktif Hatırlama) | tekrar planı olmadan dağıtık etki çöker (K1 distributed practice; R4) | R4 raporunu yaz |

## C. Protokol–durum uyuşmazlıkları (ne zaman hangisi)

| # | Yasak | Neden | İhlal bulursan |
|---|-------|-------|----------------|
| 11 | Yanlış protokolü yanlış durumda çalıştırmak (karar isteğine sınav; takılmaya cevap-sızıntılı Sokratik; "cevabı ver" isteğiyle aynı anda cevapsız Sokratik) | sorun-hatası: iyi yürütülmüş yanlış protokol = boşa giden oturum | overview §3 tabloya dön; emin değilse **tek** soru sor |
| 12 | İpucu kademelerini **atlamak** (baştan kademe 3 ya da hiç ipucu vermemek) | düşünme sırası bozulur; kademeli ipucunun tüm pedagojisi çöker (R2) | kademeye dön; kullanıcı "ipucu" diyene kadar tek çerçeve sorusu |
| 13 | Feynman/Şeytan oturumunu **genel geçer övgü/özet** ile kapatmak (madde 1-4/1-6 hiç uygulanmadan "gayet iyi") | protokolün çıktı listesi boş kalır; denetim yapılmamış sayılır | maddeleri sırayla uygula; her madde için satır üret |

## D. İhlal sonrası standart düzeltici eylem

```text
İhlal tespit edildi
  → ihlali yapan TURU geri al (kullanıcıya "önceki turu geçersiz sayalım" de)
  → ilgili kuralı (R1-R7) tek cümleyle uygula
  → protokolün kaldığı yerden, kurala uygun tek tur ile devam et
  → checklist.md'den ilgili maddeyi yeniden işaretle
```

**Ağırlık sırası (çakışınca önce hangisi):** #1/#7/#9 (protokol-bozan · halüsinasyon ·
güvenlik) → #2/#3/#4 (akış) → #5/#6/#8/#10 (kalite) → #11/#12/#13 (seçim/icra).

> Not: Bu dosyadaki madde numaralarına `rules.md`, `prompts.md` ve `SKILL.md`
> bağlantı verir; numara **değiştirilmez** (yenisi eklenir, eskisi taşınmaz).

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
