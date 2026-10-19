---
title: "Workflow"
type: persona-index
category: personas
version: 1.0.1
status: active
authority: reference
updated: 2026-09-29
---# Workflow## §5 Workflow

### §5.1 Persona Üretim / Taşıma Adımları (şart 1c akışı)

| Adım | Aksiyon | Çıktı | Süre |
|------|---------|-------|------|
| 1 | Bu kataloğu + `[[.templates/personas/persona-template]]` + `[[.personas/mood-taxonomy]]` oku | Küme adı + 11 alan havuzu | 5 dk |
| 2 | Hedef grup ve mood'u §6 tablosundan seç (persona adı **değiştirilmez**) | Seçilen dosya yolu | 2 dk |
| 3 | Eski vault persona dosyasını **salt-okunur** incele (içerik kaynağı) | Alan verisi | 10 dk |
| 4 | `persona-template`'i kopyala → `{{VARIABLE}}` doldur → 11/11 alan | Taslak persona | 30 dk |
| 5 | Her satıra kaynak etiketi yaz (§4.5); gerçek-dünya iddiası → `[[.personas/research-bank]]` | Kaynaklı dosya | 10 dk |
| 6 | Test Adımları tablosu ≥ 10 satır (Adım/Beklenen/Doğrulama) | Test planı | 10 dk |
| 7 | `vault-utf8-writer verify --file` → `lines ≥ 500`, mojibake 0 | UTF-8 raporu | <1 dk |
| 8 | `[[.personas/test-scenarios-mapping]]` §6'da çapraz kontrol + `log.md` append | Audit trail | 3 dk |

```text
KATALOG OKU → GRUP/MOOD SEÇ → ESKİ DOSYAYI SALT-OKUNUR OKU → ŞABLONLA ÜRET → 11/11 ALAN → KAYNAK ETİKETLE → VERIFY(≥500) → EŞLEME ÇAPRAZ KONTROL → LOG
```

### §5.2 Hata Modları

| Hata | Belirti | Düzeltme |
|------|---------|----------|
| Şablon okunmadan yazım | §6.1'de 3+ madde düşer | Şablondan yeniden üret (Guardrail #16) |
| Sayım uyuşmazlığı | §6 toplam ≠ 68 | Eski disk listesiyle karşılaştır → `log.md` + geri dönüş raporu |
| Mood adı uydurma | `mood-taxonomy`'de eşleşmiyor | Küme adını taksonomiden al; yoksa `⚠️ VERIFICATION REQUIRED` |
| Kırık wiki-link | Hedef diskte yok | §7.1'de `📋 planlanan` işaretle + `log.md` |
| Mojibake | `verify.mojibake > 0` | `vault-utf8-writer repair` |
| `research-bank.md`'ye dokunma | Eşzamanlı yazım riski | DUR → handover (AGENTS.md §9) — bu oturumda yasak |
| Yaş aralığı çelişkisi (4-11 vs 6-11) | §2.1 uyarısı | `[[.personas/research-bank]]` + Vault Steward onayı |

### §5.3 Bitiş Koşulları

- [ ] 3 kök dosya hedef yolda (`.ai/.personas/`), adı ASCII, `.md` uzantılı
- [ ] `vault-utf8-writer verify` temiz (BOM yok, mojibake 0, `lines ≥ 500`)
- [ ] `research-bank.md` bu oturumda oluşturulmadı/silinmedi
- [ ] `log.md` append edildi
- [ ] Wiki-link'ler geçerli ya da §7.1'de durum sütunuyla işaretli

---

