# checklist — ADIM 5 Kalite Kapısı (oturum kapanışı + skill teslimi)

> İki kullanımdır: **(A) oturum kapanışı** — her öğrenme oturumunun sonunda;
> **(B) skill teslimi** — SKILL.md/references/examples değişince.
> A grubu tamam olmadan oturum **kapanmış sayılmaz**; B grubu BLOCK verirse teslim yok.
> Çıktı: her madde `PASS` / `BLOCK (+neden)` — BLOCK varken "bitti" denmez (kanıt zorunlu).

---

## A. Oturum kapanışı (öğrenme protokolü çalıştırıldıysa)

- [ ] **Doğru teknik seçildi** (overview §3 tablo; emin olunysa 1 soru soruldu —
      anti-pattern #11 yok).
- [ ] **Prompt `references/prompts.md`'den alındı**, `…` yer tutucuları konuyla dolduruldu
      (boş `...` kalmadı; kullanıcı bilgisiyle, uydurmayla değil).
- [ ] **Tek soru kuralı** oturum boyunca ihlal edilmedi (R1 · anti-pattern #2/#3).
- [ ] **Cevap yasağı** korundu (Sokratik/Feynman'da çözüm sızmadı — anti-pattern #1);
      Aktif Hatırlamada düzeltme **yalnız puan sonrası kısa gerekçe** ile yapıldı (R3 istisnası).
- [ ] **Puanlar 0-10 rubriğine** ve gerekçeli (R3 · anti-pattern #5); şık/ipucu sırası
      bozulmadı (anti-pattern #4/#12).
- [ ] **Zero-Hallucination:** kanıtsız iddia/istatistik yok; olmayan bilgi
      `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` ile işaretlendi (R5 · anti-pattern #7).
- [ ] Aktif Hatırlama'da **yanlışlar farklı ifadeyle tekrar soruldu** + **R4 oturum
      raporu** yazıldı (anti-pattern #10).
- [ ] **Şeytanın avukatı**nda her itiraz **1-10 puanlı**, zayıf olan öyle etiketlendi,
      sağlam taraflar da yazıldı (anti-pattern #8); K5/K7 çelişkisi gizlenmedi.
- [ ] **Feynman** oturumunda maddeler 1-4 sırayla ve tam uygulandı; yalnız övgü/yalnız
      eksik dengesizliği yok (anti-pattern #6/#13).
- [ ] **Güvenlik yüzeyi:** prompt/notlarda credential yok, `[REDACTED]` uygulandı;
      yıkıcı eylem tetiklenmedi (R7 · anti-pattern #9).
- [ ] Yasaklı kalıplar tarandı (`anti-patterns.md` 13 madde — ihlal yok veya
      düzeltici eylem §D ile uygulandı).

## B. Skill teslimi (dosya değişikliği sonrası)

- [ ] **Frontmatter:** yalnız `name` · `description` · `license` · `metadata`;
      `name: learning-prompts` sabit; description "Use when …" ile başlıyor,
      ≤1024 karakter; **Türkçe tetikleyiciler yerinde** ('sokratik', 'feynman',
      'şeytanın avukatı', 'aktif hatırlama', 'beni sına', 'sınav yap',
      'cevap verme soru sor', 'öğrenmemi sağla', 'sokratik yöntem', 'feynman tekniği').
- [ ] **metadata:** version 4.0.0 · previous-version 1.0.0 · format claude-skill-v3 ·
      author Bayram Ali (ULTRATHINK Engineering) · category · tags · updated 2026-10-21.
- [ ] **SKILL.md:** H1 + § yapısı (Genel Bakış · Tetikleyiciler · Otonom Çalışma
      Protokolü · Kurallar · Güvenlik-Truth Mode · Kalite Kapısı · İlgili dosyalar) ·
      satır sayısı **500-2000** aralığında (ölçüldü) · dolgu/tekrarla doldurma YOK.
- [ ] **Footer (N9):** son satır `*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*`
      ve tüm references/examples dosyalarında aynı.
- [ ] **N4 — orphan yok:** SKILL.md'deki § link tablosu **tüm** `references/` ve
      `examples/` dosyalarını içerir; linki olmayan dosya = hata.
- [ ] **N5 — döngü:** `examples/` altında ≥1 dosya tam **girdi → oturum → çıktı** döngüsü
      gösteriyor (ham istek + yürütülen turlar + kapanış/rapor).
- [ ] **Zorunlu dosyalar mevcut:** `references/sources.md` · `changelog.md` ·
      `prompts.md` · `checklist.md` + domain derinliği (`overview.md` · `rules.md` ·
      `anti-patterns.md`) — toplam dizin ≥1000 satır (ölçüldü).
- [ ] **Truth Mode (§6):** teknik/olgu iddiası taşıyan her satır K-numarası ya da
      `⚠️ VERIFICATION REQUIRED` taşıyor; yeni iddia için `sources.md`'ye K girişi eklenmiş
      (erişim tarihli); "kanıtlanmış" abartısı yok (anti-pattern #7).
- [ ] **N8 — sidecar yok:** hedef dizinde `CLAUDE.md` yok (skill kuralının evi SKILL.md).
- [ ] **Yasaklar:** `git commit`/`git add` atılmadı (orkestratöre ait) · `.ai/`'e yazma
      yok · klasör adı/frontmatter `name` değişmedi · başka skill dizinine yazılmadı ·
      uydurma API/kuram/kaynak yok.
- [ ] **Değişiklik listesi hazır:** hangi dosya değişti / hangisi yeni — çıkış
      raporunda dosya→satır tablosuyla (ölçüm kanıtı) sunuldu.

---

## Çıktı biçimi (checklist çalıştırıldığında)

```markdown
Checklist: [A: n/n PASS · B: n/n PASS] → SONUÇ: PASS | BLOCK
BLOCK varsa: madde no + neden + düzeltici eylem (tek satır)
```

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
