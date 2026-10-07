---
name: {SKILL_ADI}
description: "Use when {NE_ZAMAN} — {KISA_GOREV}. Tetikleyiciler: '{TETIKLEYICI_1}', '{TETIKLEYICI_2}'."
license: MIT
metadata:
  version: 1.0.0
  format: claude-skill-v3
  author: {YAZAR}
  category: {CATEGORY}
  tags: [{TAG_1}, {TAG_2}, truth-mode]
  updated: {YYYY-MM-DD}
---

# {SKILL_BASLIK}

## §1 Genel Bakış
{SKILL_DETAYLI_ACIKLAMA}

Bu skill ne zaman yüklenir (trigger tablosu):

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| '{TETIKLEYICI_1}' | {NE_ZAMAN_1} | ✅ |
| '{TETIKLEYICI_2}' | {NE_ZAMAN_2} | ✅ |
| {KAPSAM_DISI_ORNEK} | Vault `.md` / prompt işi | ❌ → `.ai/.templates/` / `prompt-maker` |

## §2 Zorunlu Okumalar

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| `references/overview.md` | Mimari + kapsam | İlk kez / iskelet kurarken |
| `references/rules.md` | Kural, güvenlik ve format kısıtları | Üretim öncesi kısıt kontrolünde |
| `references/anti-patterns.md` | Yasaklı kalıplar | İçerik yazım öncesi |
| `references/checklist.md` | Kalite kontrol listesi | Teslim öncesi (ADIM 5) |
| `references/changelog.md` | Sürüm/format geçmişi | Sürüm davranışı sorgulandığında |

## §3 Örnekler

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/{ORNEK_DOSYA}.md` | Girdi → çıktı tam döngü (≥1 çalışmış örnek zorunlu — N5) |

## §4 Otonom Çalışma Protokolü
1. **Analiz:** Kullanıcı talebi incelenir.
2. **Doğrulama (Truth Mode):** İhtiyaç halinde web araştırması yapılır, bilgiler 2-3 kez
   çapraz teyit edilir; teyitsiz iddia → `// ⚠️ VERIFICATION REQUIRED`.
3. **Execution:** {ADIM_1}
4. **Execution:** {ADIM_2}
5. **Raporlama:** Sıfır halüsinasyon garantisi ile kullanıcıya çıktı sunulur.

## §5 Çekirdek Kurallar (bağlayıcı)
- **Zero Hallucination:** tahmin YASAK; deprecated/uyumsuz yapı reddedilir (H001).
- **Güvenlik:** API key/secret hardcode edilemez; yıkıcı komut (silme/deploy) kullanıcı
  onayına bağlıdır.
- **Vault uyumu:** `.ai/` kurallarıyla (CLAUDE §7, AGENTS §5) çelişilemez → DUR + sor.
- {DOMAIN_KURALLARI_ORNEGİ: strict_types=1 · raw PDO (ADR-002) · SELECT * yasak · OWASP}

Anti-overthink: kök AGENTS.md §5 (MAX THINKING 7 madde) geçerlidir.

*CoreMusic Skill v3.0 — metadata.version: 1.0.0 — Updated: {YYYY-MM-DD}*