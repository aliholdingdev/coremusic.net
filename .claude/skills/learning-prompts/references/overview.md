# overview — Teknik Haritası + Kanıt Notları

## 1. Dört teknik haritada

| # | Teknik | Mod | Çıktı | Temel dayanak |
|---|--------|-----|-------|----------------|
| 1 | Sokratik Yöntem | yönlendirme (cevap yok) | kullanıcının kendi çözümü | Eleştirel düşünme geleneği; yapay-zekâ uygulaması **popüler pedagoji, deneysel kanıtı sınırlı** → abartılmaz |
| 2 | Feynman Tekniği | anlatım + denetim | eksik/noksan raporu | "teaching effect" / self-explanation benzeri; kendin anlatma **orta** destek (Dunlosky 2013: self-explanation = moderate utility) |
| 3 | Şeytanın Avukatı | karşı argüman | karar sağlamlaştırma | Konuşmacı-yanlılığı ve teyit önyargısını hedefler (karar analizi; öğrenme araştırmasının konusu değil) |
| 4 | Aktif Hatırlama | sınav (recall) | puan + tekrar listesi | **Yüksek kanıt:** practice testing + distributed practice = high utility (Dunlosky vd. 2013) |

## 2. Kanıt notları (Truth Mode)

- **Dunlosky, J., Rawson, K. A., Marsh, E. J., Nathan, M. J., & Willingham, D. T. (2013).**
  "Improving Students' Learning With Effective Learning Techniques."
  *Psychological Science in the Public Interest*, 14(1), 4–58. DOI: 10.1177/1529100612453266.
  - Bulgu: **practice testing** ve **distributed practice** 10 teknik içinde "high utility";
    **rereading/highlighting** = "low utility" (tekrar okumak yerine kendini sınamak).
  - Teyit: 3+ bağımsız kaynak (SAGE journals, PubMed, ERIC/American Educator 2013) — 2026-10-09 araması.
- **Aktif Hatırlama'nın "yanlış cevap + tekrar" adımı** testing effect ile uyumludur
  (Roediger & Karpicke 2006 hattı) — ⚠️ VERIFICATION REQUIRED: bu oturumda Roediger
  makalesi web'den teyit edilmedi, yalnız Dunlosky 2013 içinde referans olarak geçti.
- **Sokratik ve Feynman tekniklerinin "X% başarısı" gibi sayısal iddiası YOKTUR** —
  sayısal iddia uydurmak H001 ihlalidir.
- **Kaynak makale (girdi):** Halil Özat, "Yapay Zekâyla Akıllanmak İçin 4 Prompt",
  9 Ekim 2026 — prompt metinlerinin orijinali; bu skill onları revize eder.

## 3. Teknik seçimi (1 soruluk karar)

```text
Karar mı, takılma mı, sınav mı?
- Karar/fikir → §3 Şeytanın Avukatı
- Takıldım   → §1 Sokratik
- "Anladım"  → §2 Feynman
- "Çalıştım, unutuyorum" → §4 Aktif Hatırlama
- Emin değil → kullanıcıya TEK soru: "şimdi en çok ne yapmak istiyorsun:
  karar vermek / takıldım / öğrenmek / sınamak?"
```

## 4. Entegrasyon

- Bu skill **prompt-maker'a girmez** (prompt-maker ham istek → yapılandırılmış görev promptu;
  burada hazır-kullanım öğrenme protokolleri var) → çakışma yok.
- CoreMusic bağlamında kullanım: `.ai/` konuları (BCNF, middleware, DSP) çalışılırken
  not/metin `…` yerine yapıştırılır; kaynak olarak vault dosyaları verilir → model
  vault dışı bilgi uyduramaz (Zero-Hallucination ile doğal uyum).
