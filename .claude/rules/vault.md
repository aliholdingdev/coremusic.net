---
type: rules
category: vault
title: "CoreMusic — Vault Rules"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Vault Rules

**See also:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[index.md]] · [[keys.md]]

---

## 1. Amaç

`.ai/` vault yönetimi için **kurallar ve prosedürler**. opencode.json'daki `@.claude/rules/vault.md` referansını karşılar.

---

## 2. SSOT Prensibi

| Kural | Açıklama |
|-------|----------|
| Tek kaynak | Tüm bilgiler `.ai/` vault'tan okunur |
| Harici bilgi | Doğrulanmamış harici bilgi REDDEDİLİR |
| ADR öncelik | ADR kararları vault üstündedir |
| Çelişki durumu | Vault'ta çelişki varsa DUR ve kullanıcıya sor |

---

## 3. Dosya Oluşturma Kuralları

| Kural | Açıklama |
|-------|----------|
| Template zorunlu | `.ai/.templates/index.md`'den uygun template seç (Guardrail #16) |
| Frontmatter zorunlu | 7 zorunlu alan: type, category, title, date, updated, status, version |
| Wiki-link formatı | `[[dosya/yolu]]` formatında çapraz referans |
| In-place değişiklik | Dosya adı/konumu onay olmadan DEĞİŞTİRİLMEZ |
| Append-only log | `log.md`'de geçmiş satırlar silinemez |

---

## 4. 12-Fazlı Vault Refactoring

| Faz | Amaç | Hard Gate |
|-----|------|-----------|
| 1 | Repository Discovery | — |
| 2 | AI Knowledge Discovery | — |
| 3 | Existing Markdown Analysis | — |
| 4 | Conflict Detection | — |
| 5 | Duplicate Detection | — |
| 6 | Gap Detection | — |
| 7 | **Improvement Proposal** | ✅ HARD GATE |
| 8 | Document Refactoring (In-Place) | — |
| 9 | Cross Reference Update | — |
| 10 | Index Update | — |
| 11 | Validation | — |
| 12 | Quality Report & Vault Sync | — |

---

## 5. ADR Yaşam Döngüsü

```
Draft → Review → Active → Frozen
```

| Aşama | Değişiklik | Onay |
|-------|------------|------|
| Draft | Tamamen düzenlenebilir | Gerekmez |
| Review | Kısıtlı değişiklik | Tech Lead |
| Active | Sadece minor güncelleme | Arch Lead |
| Frozen | Hiçbir değişiklik (001-037) | — |

---

## 6. Hallüsinasyon Kontrolü

| Durum | Aksiyon |
|-------|---------|
| Doğrulanamayan bilgi | `VERIFICATION REQUIRED` yaz |
| Eski/yanlış bilgi | Düzelt veya sil |
| Kırık wiki-link | Doğru dosya yolunu bul |
| Eksik frontmatter | 7 zorunlu alanı ekle |

---

## 7. Güvenlik Sınırları

| Veri Türü | Vault'a Yazılabilir mi? | Loglanırken |
|-----------|--------------------------|-------------|
| API Key | ❌ ASLA | `[REDACTED]` |
| DB Password | ❌ ASLA | `[REDACTED]` |
| JWT Secret | ❌ ASLA | `[REDACTED]` |
| Session Token | ❌ ASLA | `[REDACTED]` |
| ADR Kararı | ✅ | Doğrudan |
| Dosya Yolu | ✅ | Doğrudan |

---

## 8. Log Formatı

```
[YYYY-MM-DD HH:MM:SS] [LEVEL] [AGENT] [ACTION] Açıklama
```

| Level | Kullanım |
|-------|----------|
| INFO | Normal operasyonlar |
| WARN | Olası sorunlar |
| ERROR | Hata durumları |
| CRITICAL | Sistem durması, güvenlik ihlali |

---

## 9. Dosya Boyutu Sınırları

| Dosya | Max Satır | Rotasyon |
|-------|-----------|----------|
| `log.md` | 1000 | 950'de arşivle |
| `MEMORY.md` | 1000 | Modüler bölme |
| `brain.md` | 1000 | Arsivleme |
| `index.md` | 1000 | Kategori bölme |

---

## 10. Vault Sync Protokolü

**Başlangıç (5 Soru):**
1. Son session'dan bu yana ne değişti?
2. Yeni ADR var mı?
3. Kod değişikliği oldu mu?
4. Vault'ta eski bilgi var mı?
5. Skills durumu nedir?

**Bitiş (5 Adım):**
1. Değişiklikleri vault'a yaz (in-place)
2. `log.md`'ye timestamp ekle
3. MEMORY.md session state güncelle
4. Wiki-link'leri doğrula
5. Hallüsinasyon sweep

---

## 11. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Süreçler | [[WORKFLOW.md]] |
| Ana anayasa | [[CLAUDE.md]] |
| Master katalog | [[index.md]] |
| Keyword haritası | [[keys.md]] |
| Template registry | [[.templates/index]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
