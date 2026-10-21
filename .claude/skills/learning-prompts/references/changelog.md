# changelog — learning-prompts

> Sürüm geçmişi (Keep a Changelog ruhu · semver). Önceki tüm sürümler geri alınabilir
> (git-tracked); bu dosya `metadata.previous-version` ile `SKILL.md` arasında köprüdür.

---

## 4.0.0 — 2026-10-21 (komple yeniden yazım — onaylı reset)

**Kapsam:** 77 satırlık SKILL.md + 6 references + 1 examples dosyasının tamamı
**sıfırdan yeniden yazıldı**; yalnız `references/sources.md` bu sürümde **yeni** eklendi.

- **Truth Mode zorunlu hâle getirildi (skill-maker §6):** teknik/olgu iddiası taşıyan
  her satır artık K-numaralı kaynaka ya da `⚠️ VERIFICATION REQUIRED` taşır;
  `references/sources.md` K1-K9 kaynak defteri, güven etiketleri
  [BİRİNCİL] / [İKİNCİL] / [POPÜLER] ve kaynak hiyerarşisi (§4) ile birlikte eklendi.
- **Yeni/derinleştirilen dosyalar:**
  1. `references/sources.md` (yeni) — K1-K9, erişim tarihi 2026-10-21, reddedilen iddialar.
  2. `references/prompts.md` — 4 protokol her biri için gerekçe + K atıflı icra notları +
     sık hatalar + protokol-seçim tablosu (§5) ile derinleştirildi; prompt metinleri
     korundu.
  3. `references/rules.md` — R1-R5'e ek olarak **R6 (bekleme + periyodik özet, K4)** ve
     **R7 (güvenlik yüzeyi)** eklendi; her kurala dayanak sütunu + R-tablo özeti.
  4. `references/overview.md` — kanıt haritası K1-K9'a bağlandı; kanıt-düzeyi sıralaması,
     çelişki kuralı (K5 vs K6/K7 · K2'nin çift yönü · K1 high-vs-moderate) ve
     durum↔protokol eşleşme tablosu eklendi.
  5. `references/anti-patterns.md` — 10 → **13 madde** (A protokol-bozan · B bütünlük ·
     C durum-uyuşmazlığı); ağırlık sırası + standart düzeltici eylem (§D).
  6. `references/checklist.md` — tek liste → **A oturum kapanışı / B skill teslimi**
      ikili kapı; PASS/BLOCK çıktı biçimi.
  7. `references/changelog.md` — bu kayıt.
  8. `examples/01-tam-oturum-ornekleri.md` — 4 protokol için tam girdi→oturum→çıktı
     döngüsü (kapanış raporu dahil).
  9. `SKILL.md` — 77 → 500-2000 satır aralığına çıkarıldı; § yapısı genişletildi
     (Tetikleyiciler · Otonom Çalışma Protokolü · Kurallar · Güvenlik-Truth Mode ·
      Kalite Kapısı · İlgili dosyalar · N link tablosu).
- **Bu turda doğrulanan ve ⚠️'si KALDIRILAN iddia:** Roediger & Karpicke 2006 testing
  effect sayıları (1 hafta %56 vs %42) — PubMed + Purdue tam metin PDF ile teyit
  (K2; v1.0.0'da ⚠️ VERIFICATION REQUIRED idi).
- **Konumlandırma düzeltmeleri (abartı yasağı — anti-pattern #7):**
  - Sokratik: "kanıtlanmış" değil **"otoriter uygulama geleneği (K3/K4)"**.
  - Feynman: "kanıtlanmış Feynman yöntemi" değil **"sonradan popülerleştirilmiş paket;
    parçaları kanıtlı (K1/K8), paketin büyük-ölçekli etki kanıtı yok (K8 itirafı)"**.
  - Şeytanın Avukatı: **"klasik kanıt pozitif (K5), yakın denemeler karışık (K6/K7)"** —
    garanti iddiası reddedildi.
- **Korunanlar:** klasör adı `learning-prompts` · frontmatter `name` · Türkçe tetikleyiciler
  (hepsi description'da yerinde) · Ham girdi künyesi (Halil Özat, 9 Ekim 2026) ·
  MIT lisansı · N9 footer.
- **Format:** Claude Skill v3.0 (skill-maker §5 — N1..N10 uyumlu · N3: SKILL.md ≥500,
  dizin ≥1000 satır · N4: orphan yok · N5: örnek döngüsü · N8: sidecar yok ·
  N9: footer · N10: frontmatter 4 alan).

---

## 1.0.0 — 2026-10-09 (ilk sürüm)

- **Girdi:** Halil Özat, "Yapay Zekâyla Akıllanmak İçin 4 Prompt" (9 Ekim 2026) —
  4 ham prompt (Sokratik · Feynman · Şeytanın Avukatı · Aktif Hatırlama).
- **Revizyonlar (orijinaline göre):**
  1. Zero-Hallucination maddesi eklendi (Sokratik: "ısrar etsem bile cevap verme" +
     "uyduramazsan UNKNOWN"; Feynman/Şeytan: kanıtsız iddia `⚠️ VERIFICATION REQUIRED`).
  2. Puanlama tekilleştirildi: 1-10 yerine **0-10 rubrik** + gerekçe zorunluluğu (R3).
  3. İpucu kademeleri sabitlendi (0→1→2→3→açık rıza, R2).
  4. Aktif Hatırlama: yanlış-cevap tekrarına **dağıtık tekrar listesi** eklendi
     (Dunlosky 2013 — `overview.md` §Kanıt).
  5. Şeytanın Avukatı: "görüşe ulaştığım kanıtlar" alanı + "veri yoksa UNKNOWN" maddesi.
  6. Oturum sonu rapor formatı zorunlu (R4) · anti-pattern 10 madde · tetikleyici
     eşleşme tablosu (SKILL.md §1).
- **Doğrulama:** Dunlosky vd. 2013 (PSPI 14(1) 4–58, DOI 10.1177/1529100612453266)
  3+ kaynaktan teyit edildi (2026-10-09). Roediger & Karpicke 2006: ⚠️ VERIFICATION
  REQUIRED → **4.0.0'da K2 olarak doğrulandı ve kalktı**.
- **Format:** Claude Skill v3.0 (skill-maker §5 — N1..N10 uyumlu).

---

## Sürüm karşılaştırması (özet)

| Alan | 1.0.0 | 4.0.0 |
|------|-------|-------|
| SKILL.md | 77 satır | 500-2000 satır aralığı (ölçüldü) |
| Kaynak defteri | yok (dağınık K1 referansı) | `sources.md` K1-K9 + güven etiketleri |
| Kurallar | R1-R5 | R1-R7 (+R6 bekleme/özet, +R7 güvenlik) |
| Anti-pattern | 10 madde | 13 madde + düzeltici eylem şeması |
| Çelişki yönetimi | yok | açık çelişki kuralı (K5 vs K6/K7) |
| Konumlandırma | "Dunlosky 2013" tek dayanak | kanıt-düzeyi sıralaması + dürüstlük notları |
| Örnek | 3 örnek (kısmi döngü) | 4 protokol tam girdi→çıktı döngüsü |

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
