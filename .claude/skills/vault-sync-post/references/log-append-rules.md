# vault-sync-post — log.md Append Kuralları

> `log.md` = append-only denetim izi (audit trail). Kural otoritesi: kök `AGENTS.md` §3/§9 ·
> `.ai/AGENTS.md` §25.3 #3 · `.workflows/vault-sync.md` Aşama 6 + §6 · CHECKLIST C5.
> Gerekçe (web): git-scm resmi dokümantasyonu (`https://git-scm.com/docs/git-log.html`,
> erişim 2026-10-21) — denetim kaydı üzerine yazmaz, yalnızca ekler; geçmiş revize edilmez.
> Disk kanıtı: `.ai/log.md` 2026-10-21 son ölçümü = **219 satır** (append-only — sayı
> gün içinde büyür; sabit sayım iddia edilmez).

---

## 1. Altın Kural

1. **Yalnız append.** Mevcut hiçbir satır silinmez, düzenlenmez, yeniden yazılmaz.
2. **Tek yazma kanalı:** `node .ai/scripts/vault-utf8-writer.mjs` (`append` modu).
   PowerShell yazım cmdlet'leri (Set-Content / Out-File / Add-Content / `echo >` / New-Item -Value)
   YASAK — Windows-1254 / BOM / UTF-16 bozulması üretir.
3. **Tarihli.** Her giriş `YYYY-MM-DD` (gerekiyorsa `YYYY-MM-DD HH:mm`) taşır.
4. **Gerçek.** Yazılan her iddia disk/çıktı kanıtına dayanır; kanıtsız = `⚠️ VERIFICATION REQUIRED`.
5. **Secret yok.** Anahtar/şifre/token `[REDACTED]` ile maskelenir; ham değer asla yazılmaz.

## 2. Üç Format — Hepsi Geçerli (disk kanıtı)

`.ai/log.md` içinde **üç format bir arada** yaşamaktadır (2026-10-21 tam dosya taraması).
Hiçbiri "yanlış" değildir; yeni girişlerde hangisinin kullanılacağı bağlama göre seçilir.
**Geçmiş formatlar retroaktif olarak "düzeltilmez"** (append-only ile çelişir).

| # | Format | Şema | Disk kanıtı | Ne zaman kullanılır |
|---|--------|------|-------------|---------------------|
| F1 | **Madde satırı** | `- YYYY-MM-DD \| konu \| detay` | ~35 giriş (en yaygın) | Hızlı, tek satırlık kayıt (bilgi notu, kapı A INFO satırı) |
| F2 | **Tarihli blok başlığı** | `## YYYY-MM-DD - konu` + serbest gövde | Ör. L60, L95 | Konu-bazlı bölüm; checklist kapanış girişi |
| F3 | **Zaman damgalı işlem bloğu** | `## YYYY-MM-DD HH:mm` + `**İşlem:**` ... alanlar | `.workflows/vault-sync.md` Aşama 6 (dokümante edilmiş) | Vault-sync kapanış bloğu — AŞAĞIDAKİ şablon |

### 2.1 Kapanış Bloğu Şablonu (F3 — vault-sync kapanışı)

Aşama 6 / C5 ile uyumlu blok. Alanlar görev türüne göre çoğaltılabilir; en az 4 satır gövde:

```markdown
## 2026-10-21 17:42

**İşlem:** <görev adı> — vault sync (session kapanışı)
**Agent:** <agent-adi>
**Files:** <değişen dosyalar, virgülle>
**Result:** <completed | partial | failed> — touchpoints tazelendi, doğrulama OK/NOT
**Sonraki adım:** <1 cümle — bir sonraki session nereden devam eder>
```

### 2.2 Sonraki Adım Zorunluluğu (CHECKLIST C5)

Kapanışta `log.md` sonuna **`Sonraki adım:`** satırı eklenir:
`## YYYY-MM-DD` başlığı + `Sonraki adım: <1 cümle>`. Bu satır yoksa kapanış C5'siz sayılır
(CHECKLIST.md: "C5'siz kapanış" yasaktır). 2026-10-21 son ölçümünde dosyada `Sonraki adım`
ifadesi 5 geçişte mevcuttur — yeni kapanışlar bunu sürdürür.

## 2.3 Format Seçim Karar Ağacı

```text
KAYIT TÜRÜ NE?
  ├─ Kapanış/vault-sync bloğu (C4/C5) → F3 damgalı blok + Sonraki adım satırı
  ├─ Konu-bazlı bölüm başlığı (rapor/ADR özeti) → F2 `## YYYY-MM-DD - konu`
  ├─ Hızlı tek satır (INFO/düzeltme/bilgi notu) → F1 madde satırı
  └─ Emin değil → F3 (en zengin; eksik alan `-` değil, gerçek değer ya da ⚠️)
```

**Timestamp kuralı:** F3'te saat `HH:mm` gerçek oturum saatidir (uydurma yok);
F1/F2'de saat isteğe bağlı. Birden fazla giriş aynı gün ekleniyorsa her biri kendi
başlığını taşır — tek başlık altında toplanmaz.

### 2.4 Gerçekçi F1 örnekleri (madde satırı)

```markdown
- 2026-10-21 | session init | boot zinciri OK; MEMORY §20 okundu
- 2026-10-21 | figma token taraması | 7 breakpoint, FAIL=0 (figma-tokens.ps1, kökten)
- 2026-10-21 | düzeltme: wiki-link kontrolü | sahte kırık link 6 — onarım YASAK (§13.7)
```

## 3. Tekilleştirme (Dedup) Kuralı

| Durum | Davranış |
|-------|----------|
| Aynı görev için bugün henüz giriş YOK | Append (normal) |
| Aynı görev için bugün GİRİŞ VAR ama görev devam etti | Append — yeni durumu anlatan **farklı** satır/blok yaz (aynı metni tekrarlama) |
| Aynı görev için bugün GİRİŞ VAR ve görev değişmedi | Append **YOK**; raporda "mevcut kayıt yeterli, tekrar eklenmedi" de |
| Aynı metin yanlışlıkla 2× eklendi | Silme YOK. `repair`/manuel not ile ikinci girişin üzerine `[DUPLICATE]` işareti yalnız yazar-orkestratör onayıyla ve yalnız **yeni eklenen** satırda yapılır; geçmişe dokunulmaz |

## 4. Yazma Prosedürü (adım adım)

1. **Ön kontrol:** `log.md` son 10 satırını oku — bugün aynı iş için giriş var mı? (dedup §3)
2. **Giriş metnini kur:** F1 (tek satır) veya F3 (blok); `Sonraki adım` C5 için zorunlu.
3. **Yaz:** `node .ai/scripts/vault-utf8-writer.mjs append <hedef> <metin>` — tam bayrak
   sözdizimi script başlığından doğrulanır (`⚠️` varsayım yok).
4. **Doğrula:** writer'ın `verify` modu + `log.md` son satırlarını geri oku (UTF-8 sağlığı:
   mojibake `Ã¶`/`Ã¼` (çift-kodlanmış ö/ü) gibi dizgeler YOK).
5. **Kanıt:** Eklenen satırı kapanış raporunda göster (C1).

## 5. Hata Modları ve Kurtarma

| Hata | Tespit | Kurtarma |
|------|--------|----------|
| **Overwrite** (dosya yeniden yazıldı / satır kayboldu) | `git diff .ai/log.md` → ek satır yerine silme/düzeltme görünüyor | `git checkout -- .ai/log.md` (veya writer yedeği `C:/temp/opencode/vault-backups`) → append'i tekrarla |
| **Duplicate satır** | Aynı tarih+konu 2× | §3 kuralı — geçmiş silinmez |
| **Mojibake / UTF-8 bozuk** | `verify` modu veya gözle `Ã…` | Writer dışı yazım tespit edildi → son yedekten geri al + tüm yazımı writer üzerinden tekrarla + `log.md`'ye CRITICAL not (append) |
| **Yanlış dosyaya yazıldı** | `verify` + diff | Yazımı geri al (git) + doğru hedefe append |
| **Secret sızdı** | Gözden geçirme / tarama | Satır silinmez; değer `[REDACTED]` ile maskelenir + anahtar iptali için Security'e eskalasyon (REDACTED kuralı) |

## 6. Rollback Yolu (özet)

```text
SORUN TESPİTİ
  → writer yedeği mi git mi?  (C:/temp/opencode/vault-backups · git checkout)
  → log.md'yi son iyi durumuna getir (append-only ihlali varsa)
  → düzgün append'i tekrarla (§4)
  → verify + rapor (C1 kanıtı)
  → (opsiyonel) CRITICAL notu append
```

## 7. Yapılmayan Şeyler (yasağı netleştir)

- Geçmiş satır silme / düzenleme / yeniden tarihleme — YASAK (append-only + git log semantiği).
- `log.md`'nin tamamını `write` moduyla yeniden yazmak — YASAK (içerik kaybı riski; yalnız `append`).
- Uydurma tarih/saat/sonuç — YASAK (Zero-Hallucination; kanıt yoksa `⚠️ VERIFICATION REQUIRED`).
- `.ai/log.md` dışına "log" yazıp geçmek — YASAK; kapanış C4'te `MEMORY.md` §20 de güncellenir.

## 8. Ek — Sık Yapılan log Hataları (bu protokolün düzelttiği)

| Hata | Neden yanlış | Doğrusu |
|------|--------------|---------|
| Geçmiş satırı "düzeltmek" (eski tarih/yanlış konu) | Append-only ihlali — denetim izi bozulur, `git diff` silme/düzeltme gösterir | Yeni satırda düzeltme notu: `- YYYY-MM-DD | düzeltme: <konu> | eski kayıt X idi; gerçeği Y` |
| Dosyanın tamamını `write` ile yenilemek | 200+ satırlık geçmişin tamamı yeniden yazılır; tek satır kaybı = veri kaybı | `append` modu; yalnız yeni blok eklenir |
| Blok içi alanları `—` ile boş göstermek | Sahte-dolu kayıt (session-log-template §4 yasağı) | Gerçek değer yaz; bilinmeyen `⚠️ VERIFICATION REQUIRED` |
| Kapanış bloğunda `Sonraki adım` unutmak | C5 ihlali — sonraki session nereden devam edeceğini bilmez | Şablonun son satırı (§2.1) |
| `## YYYY-MM-DD` başlığı olmadan serbest metin eklemek | Aranabilirlik/tutarlılık kaybı (C5 formatı) | F1 madde satırı veya F2/F3 başlıklı blok — asla başlıksız |
| Tarih uydurmak (session gününde değil) | Zero-Hallucination ihlali | Gerçek tarih/saat; bilinmiyorsa `YYYY-MM-DD` (saatsiz F1/F2) |

## 9. Terimler (bu dosyaya özgü)

| Terim | Anlam |
|-------|-------|
| **F1 / F2 / F3** | log.md'de geçerli üç format (§2) |
| **Append-only** | Yalnız ekleme; silme/düzeltme yok (W1 + ADR-042) |
| **Dedup** | Aynı iş için tekrar girişi önleme (§3) |
| **Writer** | `vault-utf8-writer.mjs` — tek yazma kanalı |
| **C5 satırı** | `Sonraki adım: <1 cümle>` — kapanışın son zorunlu satırı |
| **Flood / duplicate** | Aynı metnin iki kez eklenmesi (H2) |

## 10. Bu Dosyanın Kullandığı Kaynaklar

| Kaynak | Ne alındı |
|--------|-----------|
| `.ai/log.md` (219 satır, 2026-10-21 son ölçüm) | Format çeşitliliği kanıtı (F1+F2 bir arada) · `Sonraki adım` 5 geçiş |
| `.workflows/vault-sync.md` Aşama 6 | F3 blok formatı (`## YYYY-MM-DD HH:mm` + `**İşlem:**`) |
| `.ai/CHECKLIST.md` §C C5 | `Sonraki adım:` zorunluluğu |
| `https://git-scm.com/docs/git-log.html` (2026-10-21) | Append-only / geçmiş revize edilmez ilkesi |
| `.ai/AGENTS.md` §25.3 · §17 | Append-only kuralı · `[REDACTED]` maskeleme |

---
*CoreMusic skill reference — vault-sync-post/log-append-rules.md — Updated: 2026-10-21*
