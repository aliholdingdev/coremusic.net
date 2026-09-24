---
title: "CoreMusic — Katman README Şablonu (K{n})"
type: template
category: template
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-24
---

# Katman README Şablonu (K{n})

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]] · [[../architecture/adlandirma-kurali]] · [[../architecture/katman-baglilik-matrisi]] · [[../architecture/katman-sayim-rehberi]]

> **Kullanım:** `.ai/architecture/k{n}-<slug>/README.md` dosyaları bu şablondan üretilir (Guardrail #16). `{{PLACEHOLDER}}` alanlarını doldur; §7'deki dolu örnek kılavuzdur. README olmayan katman klasörü olmaz — her 21 katman (K0-K20) bu iskeleti taşır.

---

## §1 Amaç

Bir katman README'si üç işi aynı anda yapar: (1) katmanın **kimliğini** (K{n}, A grubu, klasör) tek satırda söyler, (2) **bileşen haritasını** (`K{n}-NN ↔ K{n}.a.b` eşlemesi) kanıt türü (ii) olarak sağlar, (3) **bağımlılık oklarını** matristen kopyalayarak katman ihlali denetiminin yerel kaynağını oluşturur.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Bu katmanın alt katmanları, bileşen kimlikleri, bağımlılık okları | Ana katman şeması (21 katman) — `../index.md` |
| Dosya haritası (klasördeki md listesi) | Bağımlılık izinlerinin kanonik tanımı — matris |
| Yerel kurallar ve sınır notları (ör. ADR-025 çağrısı) | ADR metinleri — `../adr/` |
| Sayıma katkı satırları (kanıt ii) | Sayım formülü/hedefi — sayım rehberi |

## §2 Kapsam

- **Dosya yolu:** `.ai/architecture/k{n}-<slug>/README.md`
- **Hedef derinlik:** README kendisi 3. seviye kanıt (ii) üretir; 500+ satır hedefi (şablonlar hariç kuralı bu dosya için de geçerlidir — yeni/tümdüzeltilen README ≥500 satır).
- **Eşzamanlılık:** `architecture-write.md` batch'i içinde Adım 4-1 sıra 2'de yazılır (kök md'den sonra, alt katman md'sinden önce).

## §3 Mimari (README İskeleti)

### §3.1 Künye Bloğu (zorunlu — H1'den hemen sonra)

```markdown
# K{n} — <Katman Adı>

**Künye:** K{n} · Alan: A{0-5} (K{a}-K{b}) · Klasör: `k{n}-<slug>/` · Durum: active
**Bağımlılık (matris kanonik):** K{n} → <hedefler> · <kaynaklar> → K{n}
**İlgili ADR:** [[../adr/ADR-0xx-...]] (varsa)
```

### §3.2 Bölüm Sırası (silinemez)

| # | Bölüm | İçerik | Karşılık gelen kanıt |
|---|-------|--------|----------------------|
| 1 | Künye | K{n}, A grubu, klasör, durum | — |
| 2 | Amaç | Bu katman neyi sahiplenir (2-4 cümle) | — |
| 3 | Alt Katmanlar | K{n}.a listesi (2. seviye, 1'den başlar) | 2. seviye düğüm |
| 4 | Bileşen Haritası | `K{n}-NN ↔ K{n}.a.b` tablosu | **Kanıt (ii)** — sayım girdisi |
| 5 | Bağımlılık Okları | Matristen kopya + ok türü etiketi | İhlal denetimi |
| 6 | Dosya Haritası | Klasördeki md'ler (ad + 1 satır amaç) | Kanıt (i) |
| 7 | Yerel Kurallar | Sınır/istisna notları (ADR bağlantılı) | — |
| 8 | İlgili Dosyalar | Wiki-link'ler `[[relative/path]]` | Cross-ref |

### §3.3 Bileşen Haritası Tablo Formatı (kanonik — K0 örneği formu)

```markdown
| Kimlik | Bileşen | Kapsam Kanıtı | Karşılık gelen düğüm | Kanıt türü |
|--------|---------|---------------|----------------------|------------|
| K{n}-01 | <bileşen adı> | <plan §2.x / index §y / CLAUDE §z> | K{n}.a.b | (i)|(ii)|(iii) |
```

**Kural (adlandirma §5.1):** her `K{n}-NN` satırında karşılık gelen `K{n}.a.b` yazılır; karşılığı bilinmiyorsa hücreye `⚠️ VERIFICATION REQUIRED` yazılır — **uydurulmaz**.

## §4 Kurallar

1. **Adlandırma:** tüm düğüm adları `^K([0-9]|1[0-9]|20)(\.[1-9][0-9]*){0,3}$` regex'ine uyar; ara segment 0 yok (K7.0.x red); 4. seviye yalnız kanıtla.
2. **Bağımlılık:** oklar **yalnız** `../katman-baglilik-matrisi` §2.1-§2.2'den kopyalanır; matriste olmayan ok yazılmaz (yeni ok → matris satırı + gerekirse ADR).
3. **Ok türü etiketi:** her ok `bağımlılık | çağrı | gösterim` etiketli (ADR-025: K8.2 → K15 = çağrı).
4. **A grubu:** README'de A etiketi **raporlamadır**; denetim K düzeyinde yapılır.
5. **Dosya silme yok** — dosya haritası çıkarılabilir, dosya silinemez (In-Place #4).
6. **500+ satır:** yeni/tümdüzeltilen README ≥500 satır (bu şablonun kendisi 100-250 bandındadır — kapsam dışı).
7. **Frontmatter:** 7 zorunlu alan; README'de `type: architecture-readme` kullanılır.

## §5 Workflow

```text
ŞABLONU SEÇ (bu dosya) → KÜNYEYİ DOLDUR (K{n}, A, klasör)
→ Alt Katmanlar: adlandirma-kurali §2.4 kök adı + matris §10 klasör listesi
→ Bileşen Harimatı: klasördeki mevcut dosyalar + README satırları (uydurma yok)
→ Bağımlılık: matristen kopya + ok türü etiketi
→ Dosya Haritası: ls çıktısından
→ Yerel Kurallar: ilgili ADR-023…026 bağlantıları
→ DENETİM (§6) → UTF-8 verify → log append → senkron
```

**Agent eşlemesi:** README'nin katmanı → [[../AGENTS.md]] §5/WORKFLOW §2.4'teki sorumlu agent; sınır aşımında handover protokolü.

## §6 Doğrulama (checklist)

- [ ] 7 alanlı frontmatter + H1 `K{n} — <ad>` biçiminde
- [ ] §3.2'deki 8 bölüm eksiksiz (sıra korunur)
- [ ] Künye: K{n} + A grubu + klasör doğru eşleşiyor (A0=K0-K5 … A5=K16-K20)
- [ ] Bileşen haritası: her satırda `K{n}-NN ↔ K{n}.a.b`; `⚠️ VERIFICATION REQUIRED` olan uydurma düğüm yok
- [ ] Bağımlılık okları matriste birebir mevcut; ok türü etiketli
- [ ] Regex: `K\d+(\.\d+){4,}` = 0 · `K7\.0` = 0 · küçük-k = 0
- [ ] Wiki-link'ler `[[relative/path]]` ve hedefleri diskte var
- [ ] ≥500 satır (yeni/tümdüzeltilen) · dosya silinmedi
- [ ] `vault-utf8-writer verify` OK · log append · senkron (session-save + vault-post-update)

**Ölçüm notu:** README satır sayısı ≠ düğüm sayısı; sayım birimi düğümdür (ADR-026), satır değil.

## §7 Referanslar — Dolu Örnek (k6-guvenlik, kurgusal gösterim)

```markdown
---
title: "K6 — Güvenlik README"
type: architecture-readme
category: architecture
version: 1.0.0
status: active
authority: "SSOT — katman README (Guardrail #16: katman-readme-template)"
updated: 2026-09-24
---

# K6 — Güvenlik

**Künye:** K6 · Alan: A1 (K6-K7) · Klasör: `k6-guvenlik/` · Durum: active
**Bağımlılık (matris kanonik):** K6 → K7 (middleware'e arayüz) · bağımlı ok yok
**İlgili ADR:** [[../adr/ADR-025-k8-2-k15-siniri]] (komşu sınırlar için)

## Amaç
K6, tüm ekosistemin güvenlik politikasını sahiplenir: kimlik doğrulama,
CSRF (`csrf_token`), CSP nonce/strict-dynamic, rate limit (APCu) ve şifreleme
(Argon2id, AES-256-GCM). Politika uygulaması K7 middleware'inde çalışır; K6
politikanın SAHİBİDİR, K7 taşıyıcısıdır.

## Alt Katmanlar
| Alt | Ad | Not |
|-----|----|-----|
| K6.1 | Kimlik & Oturum | session, login, 2FA |
| K6.2 | Kripto & Sırlar | Argon2id, AES-256-GCM, REDACTED |

## Bileşen Haritası (kanıt ii)
| Kimlik | Bileşen | Kapsam Kanıtı | Karşılık gelen düğüm | Kanıt türü |
|--------|---------|---------------|----------------------|------------|
| K6-01 | CSRF token üretimi | CLAUDE §5 K6 satırı | K6.1.1 | (ii) |
| K6-02 | CSP nonce/strict-dynamic | ADR-012 | K6.1.2 | (ii) |
| K6-03 | Rate limit (APCu) | ADR-013 | K6.2.1 | (ii) |

## Bağımlılık Okları
| Ok | Tür | Kaynak |
|----|-----|--------|
| K6 → K7 | bağımlılık | matris §2.1 |

## Dosya Haritası
| Dosya | Amaç |
|-------|------|
| auth-middleware.md | Kimlik doğrulama akışı |
| csrf-strategy.md | `csrf_token` stratejisi |

## Yerel Kurallar
- Sır asla README'ye yazılmaz → `[REDACTED]` (REDACTED politikası).
- Yeni güvenlik oku → önce matris satırı, sonra README.

## İlgili Dosyalar
[[../index]] · [[../katman-baglilik-matrisi]] · [[../adlandirma-kurali]] · [[../adr]]
```

*(Örnek iskelet gösterim amaçlıdır; gerçek k6 içeriği diskteki dosyalardan türetilir ve uydurma satır içermez.)*

---

**Registry notu:** Bu şablon `.ai/.templates/index.md`'ye kayıtlıdır; şablonsuz katman README'si üretilmez (Guardrail #16).

*CoreMusic Katman README Şablonu v1.0.0 — Authority: Bayram Ali / Vault Steward — 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
