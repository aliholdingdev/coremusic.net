---
name: learning-prompts
description: "Use when the user wants to learn or test a topic with active learning methods instead of getting direct answers — Sokratik soru zinciri, Feynman anlatımı, Şeytanın Avukatı eleştirisi ve Aktif Hatırlama sınavı üretir. Tetikleyiciler: 'sokratik', 'sokratik yöntem', 'feynman', 'feynman tekniği', 'şeytanın avukatı', 'aktif hatırlama', 'beni sına', 'sınav yap', 'cevap verme soru sor', 'öğrenmemi sağla'."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: learning-protocols
  tags: [learning, active-recall, socratic, truth-mode]
  updated: 2026-10-09
---

# learning-prompts — 4 Aktif Öğrenme Prompt'u (Revize)

## §1 Genel Bakış

Kaynak: Halil Özat, "Yapay Zekâyla Akıllanmak İçin 4 Prompt" (9 Ekim 2026) — bu
skill'in ham girdisidir; 4 prompt **revize edilerek** üretilmiştir. Amaç: yapay zekâyı
"cevap veren" değil, **antrenör** yapan 4 hazır protokolü tek yerde taşımak.

**Revizyon farkları (v1.0.0):** (1) Zero-Hallucination kuralı eklendi — model bilmeyince
`UNKNOWN` der, uydurmaz; (2) 0–10 puanlama rubriği tekilleştirildi; (3) ipucu kademeleri
sabitlendi (`ipucu ver` → 1 → 2 → 3); (4) oturum sonu rapor formatı zorunlu; (5) tetikleyici
eşleşme tablosu eklendi; (6) her prompt `…` yer tutucuları ile kopyala-yapıştır hazır.

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| 'sokratik' / 'cevap verme soru sor' | Takıldığım noktada cevabı kendim bulmak istiyorum | ✅ → `references/prompts.md` §1 |
| 'feynman' / 'çocuğa anlatır gibi' | Öğrendiğimi sandığım konuyu test etmek | ✅ → §2 |
| 'şeytanın avukatı' / 'itiraz et' | Önemli karar / güçlü fikir öncesi delil arıyorum | ✅ → §3 |
| 'aktif hatırlama' / 'beni sına' | Konuyu çalıştım, kalıcı hâle getirmek istiyorum | ✅ → §4 |
| Doğrudan cevap / hazır çözüm isteği | Kullanıcı "cevabı ver" derse | ❌ bu protokollerin amacı tersi → kullanıcıya protokolü öner |

## §2 Zorunlu Okumalar

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| `references/prompts.md` | 4 revized tam prompt (kopyala-yapıştır) | Trigger eşleşince — **ana çıktı** |
| `references/overview.md` | Teknik haritası + kanıt notları (Dunlosky 2013) | Hangi teknik seçileceğinde |
| `references/rules.md` | Tek soru kuralı · ipucu kademesi · puanlama rubriği | Oturum yürütülürken |
| `references/anti-patterns.md` | Yasaklı kalıplar (cevap sızdırmak, şık vermek…) | Oturum öncesi |
| `references/checklist.md` | Kalite kontrol listesi (ADIM 5) | Teslim öncesi |
| `references/changelog.md` | Sürüm geçmişi | Sürüm sorgulandığında |

## §3 Örnekler

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/01-tam-oturum-ornekleri.md` | 4 teknik için de girdi → çıktı tam döngü (ham istek → yürütülen oturum) |

## §4 Otonom Çalışma Protokolü

1. **Analiz:** Kullanıcı isteği tetikleyicilerle eşleştirilir (§1 tablo); "cevap verme
   durumu mu, sınav durumu mu?" ayrımı yapılır.
2. **Doğrulama (Truth Mode):** Teknik/olgu iddiaları gerekirse web'den teyit edilir
   (kaynak: `references/overview.md` §Kanıt); teyitsiz iddia → `⚠️ VERIFICATION REQUIRED`.
3. **Seçim:** Teknik seçilir; kullanıcı "şunu mu bunu mu" dese tek soru ile netleştir.
4. **Execution:** `references/prompts.md`'den ilgili prompt `…` yer tutucularıyla
   doldurulup oturum başlatılır; `references/rules.md` kuralları oturum boyunca uygulanır.
5. **Raporlama:** Oturum sonunda `references/rules.md` §R4 rapor formatı yazılır
   (zayıf noktalar + tekrar soru listesi). Zero-Hallucination garantisi ile.

## §5 Çekirdek Kurallar (bağlayıcı)

- **Zero Hallucination:** puanlanan cevapta olgu uydurulmaz; model bilmeyince `UNKNOWN` der;
  doğrulanamayan geri bildirim `⚠️ VERIFICATION REQUIRED` taşır (H001).
- **Tek soru kuralı:** her turda yalnız 1 soru; kullanıcı cevaplamadan sonraki soruya geçilmez.
- **Cevap yasağı:** Sokratik/Feynman'da çözüm, doğru cevap veya düzeltilmiş hâl **verilmez** —
  kullanıcı ısrar etse bile (istisna: Aktif Hatırlama'da puan sonrası doğru cevabın
  *kısa* gerekçesi, §R3).
- **Güvenlik:** prompt içine credential/secret yazılmaz; yıkıcı eylem yoktur.
- **Vault uyumu:** `.ai/` kurallarıyla (CLAUDE §7, AGENTS §5) çelişilemez → DUR + sor.
- **Kaynak bağımlılığı:** teknikleri büyük harfle "kanıtlanmış" diye satmak yok — kanıt
  notu `references/overview.md`'de, abartı yasak.

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 1.0.0 — Updated: 2026-10-09*
