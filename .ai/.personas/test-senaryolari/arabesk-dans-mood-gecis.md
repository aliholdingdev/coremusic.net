---
title: "CoreMusic — Test Senaryosu: Arabesk-Dans Mood Geçişi (ADN)"
type: test-scenario
category: testing
version: 1.0.0
status: active
authority: "Test Senaryosu — SSOT: personas/test-senaryolari/arabesk-dans-mood-gecis"
updated: 2026-09-26
---

# CoreMusic — Test Senaryosu: Arabesk-Dans Mood Geçişi (ADN)

**Zorunlu Bağlantılar:** [[personas/index]] · [[personas/methodology]] · [[personas/research-bank]] · [[personas/test-scenarios-mapping]] · [[personas/persona-template]] · [[ADR-023-persona-driven-testing]] · [[personas/test-senaryolari/a11y-erisilebilirlik]]

---

| Alan | Değer |
|------|-------|
| Dosya Adı | `arabesk-dans-mood-gecis.md` |
| Dosya Yolu | `.ai/.personas/test-senaryolari/arabesk-dans-mood-gecis.md` |
| Dosya Tipi | Test senaryosu (6 senaryodan 1'i) — **karar DEĞİLDİR** (ADR değil) |
| Hedef Kitle | QA Engineer (birincil), UI Designer (mood UI/animasyon), Vault Steward (denetim) |
| Yapı | H1 + Zorunlu Bağlantılar + §1 Amaç → §7 Referanslar (7 bölüm + 7 alanlı frontmatter) |
| ADR-023 Karşılığı | 20 persona matrisi **satır 16 — Mood-geçiş kişisi** (Arabesksever ↔ Danssever; §2.2a; kaynak: eski vault `test-senaryolari/arabesk-dans-mood-gecis.md`) |
| Mapping Karşılığı | **Arabesk/Dans türevi kümesi → S2 birincil, S3 ikincil** (mapping §4.5/§4.7); tipik hata modu: **"Tür filtresinde kayıp"** |
| Test Seviyeleri | Seviye 1 AI Rol · Seviye 2 Browser MCP · Seviye 3 Playwright · Seviye 4 Rapor |
| Eşik Kaynağı | `[[personas/research-bank]]` **P8 (metrik)** + **P5 (WCAG)** + **P6 (KVKK/veli `⚠️ DERIVED`)**; BPM değerleri `[[personas/mood-taxonomy]]` §3.4 — **hepsi `⚠️`** |
| Adım Sayısı | **14** (asgari 10 — §5.3) |
| Test Blokları | ADN-001 … ADN-010 (10 blok — §3.1) |
| Zorunlu Blok | §4.0 Şablon-Önce Kural Bloğu (silinemez) |
| Eski Kaynak | `coremusic.net.old/.ai/personas/test-senaryolari/arabesk-dans-mood-gecis.md` (254 satır, **salt okunur**) — iskelet alındı, mood-taxonomy §3.4 + P8/P5/P6 ile yeniden bağlandı ve genişletildi |
| Kayıt Kuralı | Değişiklik Geçmişi **append-only** |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); dosya/klasör adı ASCII |
| Authority | `Test Senaryosu — SSOT: personas/test-senaryolari/arabesk-dans-mood-gecis` |
| Governance | Red Team · Human Mode · Truth Mode |
| Versiyon | 1.0.0 (ilk üretim — 2026-09-26) |

---

## §1 Amaç

Bu dosya, CoreMusic'in **Arabesk ↔ Dans mood geçiş test senaryosudur**: iki zıt tür kutbu (duygusal/slow arabesk ↔ yüksek tempolu dans/EDM) arasında, mood kaydı, tür filtresi, EQ preset eşleşmesi, öneri değişimi, animasyon/performans davranışı ve 7 arabesk/dans türev kümenin (Arabesksever · Danssever · Arabesk Kaşif · Arabesk Meraklı · Dans Sporcu · Arabesk Melankolik · Dans Enerjik) kişi bazlı beklenilerinin hangi adımlarla test edileceğini tanımlar. Eski vault'taki 254 satırlık senaryo **kopyalanmamış**; iskelet `[[personas/mood-taxonomy]]` §3.4 (7 türev küme — BPM değerleri `⚠️`) + research-bank P8/P5/P6 ile **yeniden bağlanarak genişletilmiştir** (ADR-005 Zero Hallucination).

| Boyut | Değer |
|-------|-------|
| Ne taşır | ADN test blokları, ≥10 adımlık adım tablosu (Adım · Eylem · Beklenen · Doğrulama · Metrik · WCAG), mood geçiş matrisi, 7 küme × persona tablosu, KVKK/veli adımı, Playwright kod bloğu |
| Ne taşımaz | Küme tanımları/OCEAN eğilimleri (→ mood-taxonomy), BPM eşiklerinin kaynağı (→ mood-taxonomy `⚠️` işaretli), metrik eşikleri (→ research-bank P8), test akışı (→ methodology), karar (→ ADR-023) |
| Neden yazıldı | Eski senaryo mood-taxonomy ve research-bank **önce** yazılmıştı → BPM/geçiş iddiaları kaynağa bağlandı |
| Kim okumalı | Mood-geçiş/öneri testi çalıştıran her ajan (QA birincil, UI ikincil) |
| Kanıt zinciri | `mood-taxonomy` §3.4 (küme+BPM `⚠️`) → `research-bank` P6/P8/P5 → `methodology` → **bu dosya** → `ADR-023` satır 16 (gate) |

### §1.1 Bu Dosya Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Dosya | Bu Dosya Kullanılmaz |
|---------|-------------|----------------------|
| Arabesk↔Dans geçiş test adımları | ✅ Bu dosya | — |
| Küme adı/tanımı/OCEAN/BPM aralığı | `[[personas/mood-taxonomy]]` §3.4 (`⚠️` BPM) | ❌ (taşınır) |
| Metrik eşikleri (LCP/INP/CLS…) | `[[personas/research-bank]]` P8.1 | ❌ (taşınır) |
| WCAG kriter numaraları/eşikleri | `[[personas/research-bank]]` P5.2 | ❌ (taşınır) |
| Veli onayı politikası | `[[personas/research-bank]]` P6.4 `⚠️ DERIVED` | ❌ (taşınır) |
| Senaryo × küme önceliği | `[[personas/test-scenarios-mapping]]` §4.5/§4.7 | ❌ |
| Coverage gate | `[[ADR-023-persona-driven-testing]]` satır 16 | ❌ |

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Mood kaydı: Arabesk ↔ Dans geçişi ve geri dönüşü (state) | Mood veri modeli/şeması (→ architecture/k5-veri-yonetimi) |
| Tür filtresi + EQ preset eşleşmesi (mapping §4.5 öncelikli kontrol) | EQ/DSP işleme zinciri (→ k3-ses-motoru) |
| Öneri değişimi: geçiş sonrası liste tutarlılığı | Öneri algoritması tasarımı (→ muzik-kesfi senaryosu) |
| Animasyon/performans: geçiş jank'sızlığı (INP/CLS) | Animasyon tasarım sistemi (→ ui-design) |
| 7 türev küme × persona beklenileri (§3.1.3) | Persona üretimi (→ persona dosyaları) |
| BPM filtre aralıkları (`⚠️` — mood-taxonomy) | BPM ölçüm/analiz altyapısı |
| Çocuk küme kişileri (Elifsu/Yiğit Can/Egehan/Mina) + KVKK/veli onayı | Veli onayının hukuki yorumu (→ P6 `⚠️ DERIVED`) |
| A/B varyant testi kapsamı (`⚠️ VERIFICATION REQUIRED` — research-bank'ta YOK) | A/B altyapısı/istatistik yöntemi (→ data) |

### §2.2 Hedef Kitle

| Persona | Bu senaryoda rolü |
|---------|-------------------|
| QA Engineer (`qa`) | Birincil yürütücü |
| UI Designer (`ui`) | Mood geçiş animasyonu/visual doğrulama |
| Embedded/DSP Engineer | EQ preset eşleşmesi referans kontrolü (ikincil) |
| Vault Steward | Denetim — §6 envanter |

### §2.3 Kapsam Dışı İstisnalar

| # | İstisna | Nereye Gider |
|---|---------|--------------|
| 1 | A/B test varyantları ve kazanan varyant istatistiği | `⚠️ VERIFICATION REQUIRED` (research-bank'ta YOK; eski dosyadan devralındı — §6.5) |
| 2 | EQ preset sayısı/tanımı (31-band vb.) | `⚠️ VERIFICATION REQUIRED` (mapping §4.4-S3 "31-band EQ" kaynağı bankada yok) |
| 3 | BPM ölçüm doğruluğu (parçanın gerçek BPM'i) | `⚠️ VERIFICATION REQUIRED` — BPM aralıkları zaten `⚠️` (mood-taxonomy §3.4) |

---

## §3 Mimari — Senaryo Yapısı

### §3.0 Dizin ve Bağımlılık Ağacı

```
.ai/.personas/test-senaryolari/arabesk-dans-mood-gecis.md  ← bu dosya (SSOT: senaryo)
├── [[personas/mood-taxonomy]] §3.4                       (7 türev küme + BPM ⚠️)
├── [[personas/test-scenarios-mapping]] §4.5/§4.7         (öncelik çaprazı + tipik hata)
├── [[personas/research-bank]] P6.4                       (veli onayı — DERIVED)
├── [[personas/research-bank]] P8.1–P8.5                  (metrik — VERIFIED)
├── [[personas/research-bank]] P5.2                       (WCAG kriter numaraları)
├── [[personas/methodology]] §2.2, §3.3, §3.4
└── [[ADR-023-persona-driven-testing]] satır 16           (mood-geçiş kişisi + gate)
```

### §3.1 Test Blokları (ADN-001 → ADN-010)

| # | Blok | Kapsam | Birincil Kriter / Not |
|---|------|--------|----------------------|
| ADN-001 | Mood Kaydı & İlk Geçiş | Arabesksever → Danssever kaydı; durum görünür | INP ≤ 200 ms · 4.1.3 `⚠️` |
| ADN-002 | Tür Filtresi Geçişi | Filtre Arabesk ↔ Dans arası; **filtre kaybı yok** | mapping §4.7 tipik hata |
| ADN-003 | EQ Preset Eşleşmesi | Geçişte preset otomatik eşleşir mi? | `⚠️ VERIFICATION REQUIRED` (§2.3) |
| ADN-004 | Öneri Listesi Tutarlılığı | Geçiş sonrası öneriler yeni kümeyle tutarlı | CLS ≤ 0.1 |
| ADN-005 | Ani Geri Geçiş | Dans → Arabesk anlık dönüş; state korunur | INP ≤ 200 ms |
| ADN-006 | Geçiş Animasyonu | Jank'sız crossfade; prefers-reduced-motion uyumu | CLS ≤ 0.1 · 2.4.7 `⚠️` (hareket azaltma numarası P5.5) |
| ADN-007 | Performans / Ağ Altında | Throttling altında geçiş (P8.3) | LCP ≤ 2500 ms · TBT ≤ 50 ms |
| ADN-008 | Çocuk Küme Geçişi (KVKK) | Elifsu/Yiğit Can/Egehan/Mina: yaşa uygun filtre + **veli onayı** | 2.5.8 · KVKK `⚠️ DERIVED` |
| ADN-009 | Genç Küme Geçişi | Zeliha/Buse/Furkan/Atakan beklenileri (§3.1.3) | mapping §4.5 |
| ADN-010 | Edge Case'ler & A/B Kapsamı | Boş kütüphane, kayıpsız geri dönüş, A/B kapsam red'i | `⚠️ VERIFICATION REQUIRED` (A/B) |

### §3.1.1 Blok Detayları (Açıklama — Başarı Kriteri)

**ADN-001 — Mood Kaydı:** Kullanıcı Arabesksever iken Danssever'e geçer; yeni küme UI'da görünür (seçili mood göstergesi). Başarı: durum mesajı görünür, yenilemede korunur.

**ADN-002 — Tür Filtresi:** mapping §4.5: Arabesk/Dans türevleri (7) → "Öncelikli kontrol: **Tür filtresi + EQ preset eşleşmesi**"; mapping §4.7 tipik hata modu: **"Tür filtresinde kayıp"**. Başarı: geçişte eski tür filtresi sessizce uygulanmış kalmaz; filtre durumu açık veya temizlenir — **ikisi de gözlemlenip raporlanır**.

**ADN-003 — EQ Preset:** Preset eşleşmesi beklentisi mapping'ten gelir; preset **sayısı/tanımı** research-bank'ta yok → kapsam doğrulaması `⚠️ VERIFICATION REQUIRED` (§2.3-2). Sonuç bilgi sayılır, PASS/FAIL sayılmaz.

**ADN-004 — Öneri Tutarlılığı:** Geçiş sonrası "Tadına göre"/öneri listesi yeni kümeyle tutarlı (Arabesk → vokal/duygusal öncelik; Dans → yüksek tempo). Nicel "doğru öneri oranı" eşiği **YOK** → yalnızca gözlemsel tutarlılık raporlanır (`⚠️ VERIFICATION REQUIRED` — §6.5).

**ADN-005 — Ani Geri Geçiş:** Dans → Arabesk anlık dönüşte eski liste anında geri gelir; eski/yeni karışık liste **FAIL**.

**ADN-006 — Animasyon:** Crossfade akıcı; `prefers-reduced-motion` (hareket azaltma) tercihinde animasyon kapanır — bu davranış A11Y senaryosundan gelir; kriter numarası P5.5 kapsamında tek tek açılmadığından `⚠️ VERIFICATION REQUIRED`.

**ADN-007 — Performans:** P8.3 throttling altında geçiş: INP ≤ 200 ms, TBT ≤ 50 ms, CLS ≤ 0.1 (yer tutucu korunur).

**ADN-008 — Çocuk (KVKK):** 4 çocuk küme kişi geçişi yaşa uygun: BPM filtresi çocuk UI'ında **yoktur** (sade akış); explicit/uygunsuz tür ret edilir. **KVKK/veli onayı adımı zorunlu** (§5.3/10): P6.4 `⚠️ DERIVED` (TMK m.11=18 · COPPA 13 · GDPR m.8=16; "KVKK 16 diyor" YANLIŞTIR — P6.5). Butonlar 48px+ `⚠️ DERIVED`.

**ADN-009 — Genç:** §3.1.3 tablosundaki küme beklenileri adım adım doğrulanır; yaş kilidi 16 `⚠️ VERIFICATION REQUIRED`.

**ADN-010 — Edge/A&B:** Boş kütüphanede geçiş, ağ kesintisinde geçiş, A/B varyant kontrolü (yalnız kapsam red'i — §2.3-1).

### §3.1.2 Edge Case'ler

| # | Edge Case | Beklenen |
|---|-----------|----------|
| 1 | Kütüphane boşken mood geçişi | Boş-durum ekranı; hata yok; mood kaydı yine görünür |
| 2 | Geçiş ortasında ağ kesilir | Yarım liste yok; eski durum veya net hata |
| 3 | Ardışık 5 hızlı geçiş (Arabesk↔Dans) | Son durum tutarlı; animasyon yığını birikmez (INP) |
| 4 | Sayfa yenilenir (reload) | Kayıtlı mood korunur |
| 5 | Çocukta BPM filtresi aranırsa | Filtre yok/erişilemez; gizli zorba yok |
| 6 | Veli onayı reddedilir | Çocuk geçişleri kısıtlı kalır; hesap değişmez |

### §3.1.3 Küme × Persona Beklenileri (mood-taxonomy §3.4 — taşınan)

| # | Küme | Grup (kişi) | Mood-taxonomy test etkisi (taşınan) | BPM (mood-taxonomy — hepsi `⚠️`) | Adım |
|---|---|---|---|---|---|
| 19 | Arabesksever | Genç kız (Zeliha) | Duygusal UI, slow tempo, vokal öncelikli öneri | **60–100 BPM `⚠️`** | 9 |
| 20 | Danssever | Genç kız (Buse) | Hızlı navigasyon, canlı renkler, yüksek BPM filtresi | **120–160 BPM `⚠️`** | 9 |
| 21 | Arabesk Kaşif | Kız çocuk (Elifsu) | Çocuk dostu UI, aile paylaşımı, içerik filtresi | (BPM yok — çocuk keşif) | 10 |
| 22 | Arabesk Meraklı | Erkek çocuk (Yiğit Can) | Aile hesabı, ebeveyn kontrolü (yetkinlik sınırı) | (BPM yok — ortak dinleme) | 10 |
| 23 | Dans Sporcu | Erkek çocuk (Egehan) | Egzersiz odaklı, BPM bazlı öneri, **büyük dokunma hedefi** | **130–180 BPM `⚠️`** (çocukta filtre yok) | 10 |
| 24 | Arabesk Melankolik | Genç erkek (Furkan) | Karanlık tema, slow geçişler, gece modu | **60–90 BPM `⚠️`** | 9 |
| 25 | Dans Enerjik | Kız çocuk (Mina) + Genç erkek (Atakan) | Koreografi videoları, workout playlist, yüksek BPM | **120–160 BPM `⚠️`** (çocukta sadeleşir) | 9, 10 |

> Bu tablo mood-taxonomy §3.4/§3.6'dan **taşınan** özettir; BPM aralıkları araştırma tarafından **`⚠️` işaretlenmiştir** — bu dosyada doğrulanmış eşik gibi kullanılamaz. 25. küme eski tabloda 2 satır olsa da tek kümedir (mood-taxonomy §3.4 notu).

### §3.2 WCAG Kriter — Eşik Tablosu (research-bank P5 — `VERIFIED`)

| Kriter | Başlık | Eşik / Değişken | Durum |
|--------|--------|-----------------|-------|
| 1.4.3 | Contrast (Minimum) | Metin ≥ 4.5:1; büyük ≥ 3:1 | `VERIFIED` |
| 1.4.11 | Non-text Contrast | UI bileşenleri ≥ 3:1 | `VERIFIED` |
| 1.4.13 | Content on Hover or Focus | AA | `VERIFIED` |
| 2.4.7 | Focus Visible | AA | `VERIFIED` |
| 2.4.11 | Focus Not Obscured (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.7 | Dragging Movements | AA — WCAG 2.2 **YENİ** | `VERIFIED` |
| 2.5.8 | Target Size (Minimum) | ≥ 24×24 CSS px (5 istisna) | `VERIFIED` |
| 3.2.6 | Consistent Help | A | `VERIFIED` |
| 3.3.7 | Redundant Entry | A | `VERIFIED` |
| 3.3.8 | Accessible Authentication (Minimum) | AA — WCAG 2.2 **YENİ** | `VERIFIED` |

### §3.3 Persona / Mood Eşlemesi (`[[personas/test-scenarios-mapping]]`)

| Grup (n) | Bu senaryoda | Kısıt |
|---|---|---|
| Çocuk (29) | S2 ✅ / S3 ✅ — geçiş yaşa uygun, sade | BPM filtresi yok; veli onayı `⚠️ DERIVED` |
| Genç (29) | Geçiş tam — küme beklenileri §3.1.3 | Yaş kilidi 16 ⚠️ |
| Yetişkin (10) | Geçiş tam (Arabesk Melankolik/Profesyonel köprüsü) | S6 kısıtlı bu senaryoda etkisiz |

Mood çaprazı (mapping §4.5): **Arabesk/Dans türevleri (7) → S2, S3**; öncelikli kontrol: tür filtresi + EQ preset eşleşmesi. mapping §4.7: Arabesk/Dans türevi (8) → S2 birincil, S3 ikincil; **tipik hata: tür filtresinde kayıp**.

### §3.4 Cihaz / Breakpoint Matrisi (research-bank P8.5 — `VERIFIED`)

| # | Viewport | # | Viewport |
|---|----------|---|----------|
| 1 | 320 × 568 | 6 | 1280 × 720 |
| 2 | 375 × 667 | 7 | 1600 × 1200 |
| 3 | 375 × 812 | 8 | 1920 × 1080 |
| 4 | 414 × 896 | 9 | 2560 × 1440 |
| 5 | 768 × 1024 | — | — |

### §3.5 Emülasyon Ayarlar (P8 — `VERIFIED`)

| Ayar | Değer | Kaynak |
|------|-------|--------|
| Ağ gecikmesi / down / up | **150 ms · 1.6 Mbps / 750 Kbps** | P8.3 |
| TBT | **> 50 ms** long task bloklar | P8.3 |
| Cihaz registry | `playwright.devices` (userAgent, screenSize, viewport, hasTouch, deviceScaleFactor, isMobile, colorScheme, locale, timezoneId) | P8.4 |
| Viewport override | `page.setViewportSize()` | P8.4 |
| Offline | `use: { offline: true }` | P8.4 |
| CDP ağ | `Network.emulateNetworkConditions` | P8.4 |

### §3.1.4 Geçiş Durum Matrisi (Bu Senaryoya Özgü)

| # | Geçiş | Başlangıç mood | Hedef mood | Beklenen | Adım |
|---|-------|----------------|-----------|----------|------|
| T1 | İlk geçiş | Arabesksever | Danssever | Mood kaydı + filtre denetimi + öneri değişimi | 3–5 |
| T2 | Ani geri dönüş | Danssever | Arabesksever | Eski liste anında; karışık liste FAIL | 7 |
| T3 | Ardışık hızlı geçiş ×5 | İkili | İkili | Son durum tutarlı; birikmiş animasyon yok | 8 |
| T4 | Reload sonrası | Herhangi | Kayıtlı olan | Mood korunur | 4 (Edge) |
| T5 | Çocukta geçiş | Çocuk kümesi | Çocuk kümesi | Sade akış; BPM filtresi yok; veli onayı `⚠️` | 10–11 |

---

## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

Bu dosya `.templates/documentation/docs-md-template.md`'ye göre yazılmıştır; bu blok **silinemez**. Yeni ADN bloğu eklenirken önce şablon, sonra §4.x (Guardrail #16).

### §4.1 Bağlayıcı Kurallar

| # | Kural |
|---|-------|
| 1 | Küme/BPM adları mood-taxonomy'den taşınır; yeniden tanımlanmaz/normalize edilmez |
| 2 | BPM aralıkları `⚠️` → doğrulanmış eşik gibi **kullanılamaz** |
| 3 | `VERIFIED` ↓ `⚠️` olur; `⚠️` ↑ `VERIFIED` olmaz (ADR-005) |
| 4 | Frozen ADR'ler (001–037) değiştirilmez; dosya adı değişikliği onay ister |
| 5 | Her değişiklik `log.md` append-only |

### §4.2 Frontmatter — 7 Zorunlu Alan

`title` · `type` · `category` · `version` · `status` · `authority` · `updated`.

### §4.3 Dil, Kod Adı ve Mojibake (UTF-8)

Yazım yalnız `vault-utf8-writer.mjs`; PowerShell yazma cmdlet'leri **YASAK**.

### §4.4 Wiki-Link ve Bağlantı Formatı

`[[relative/path/to/file]]` — zorunlu 7 bağlantı H1 altında (§7.1).

### §4.5 Kaynak Etiketleme — Durum Aktarımı (ADR-005)

| Durum | Bu Dosyada |
|-------|-----------|
| `VERIFIED` (P5/P8) | Taşınır + P# kaynağı |
| `⚠️` (mood-taxonomy BPM) | `⚠️` ile taşınır — eşik yapılmaz |
| `⚠️ DERIVED` (P6.4) | Türetme olduğuyla taşınır |
| `⚠️ VERIFICATION REQUIRED` | Aynen taşınır; doğrulanmadan iddia edilmez |

### §4.6 Derinlik Standardı (500+)

≥ 500 satır; ekleme sonrası §6.1 tekrar çalıştırılır.

### §4.7 REDACTED, KVKK ve Sür Politikası

Gerçek kişi/iletişim/anahtar verisi yazılmaz (`[REDACTED]`); persona adları persona dosyalarından gelir; **16 yaş altı veli onayı `⚠️ DERIVED`** (P6.4); "KVKK 16" gerekçesi yasak (P6.5); dosya adı değişmez.

### §4.8 Yasak / Doğru Tablosu

| ❌ Yasak | ✅ Doğru |
|----------|---------|
| BPM aralığını "doğrulanmış eşik" saymak | mood-taxonomy `⚠️` aynen taşınır |
| EQ preset sayısını kesinleştirmek | `⚠️ VERIFICATION REQUIRED` |
| A/B "kazanan varyant" sonucu yazmak | `⚠️ VERIFICATION REQUIRED` (kapsam red'i) |
| PowerShell ile yazma | `vault-utf8-writer.mjs` |
| 500 satır altı | §4.6 |

---

## §5 Workflow

### §5.1 Genel Akış

```
PREPARE (§5.2) → EXECUTE (§5.3 + §5.4) → REPORT (§5.5)
```

### §5.2 PREPARE — Hazırlık Adımları

| # | Hazırlık | Çıktı |
|---|----------|-------|
| P1 | mood-taxonomy §3.4 (7 küme + BPM `⚠️`) oku | Küme listesi |
| P2 | mapping §4.5/§4.7 oku | Öncelik + tipik hata |
| P3 | P8 eşikleri + P5.2 oku | Eşik listesi |
| P4 | P6.3–P6.5 oku (veli onayı) | `⚠️ DERIVED` notu |
| P5 | 8 test kişisi seç (4 çocuk + 4 genç) + yetişkin köprüsü | Hesap listesi |
| P6 | Viewport + throttling seç | Koşu profili |

### §5.3 Adım Tablosu (14 adım — asgari 10)

| Adım | Eylem | Beklenen | Doğrulama | Metrik (research-bank P8 değeri) | WCAG kriteri |
|------|-------|----------|-----------|----------------------------------|--------------|
| 1 | Throttling uygula (150 ms · 1.6/0.75 Mbps) | Ağ koşulu aktif | Network Conditions | 150 ms · 1.6/0.75 (`VERIFIED`) | — |
| 2 | Zeliha (Arabesksever) ile ana sayfayı yükle | Mood görünür; FCP/LCP kaydedilir | Performance panel | FCP ≤ 1 sn · LCP ≤ 2500 ms (`VERIFIED`) | — |
| 3 | Mood'u Danssever'e geçir (ADN-001) | Yeni mood görünür + durum mesajı | UI + durum | INP ≤ 200 ms (`VERIFIED`) | 1.4.3 |
| 4 | Tür filtresini denetle (ADN-002) — filtre kaybı var mı? | Kayıp yok **veya** açık temizleme; raporlanır | Filtre durumu | Tipik hata: mapping §4.7 | 2.5.8 |
| 5 | Öneri listesini gözden geçir (ADN-004) | Yeni kümeyle gözlemsel tutarlılık (nicel eşik YOK `⚠️`) | Liste DOM + CLS | CLS ≤ 0.1 (`VERIFIED`) | — |
| 6 | EQ preset eşleşmesini sorgula (ADN-003) | Kapsam red'i — `⚠️ VERIFICATION REQUIRED` | Kapsam notu | Preset tanımı `⚠️` | — |
| 7 | Ani geri geçiş: Dans → Arabesk (ADN-005) | Eski liste anında; karışık liste FAIL | Liste + süre | INP ≤ 200 ms (`VERIFIED`) | 2.4.7 |
| 8 | Geçiş animasyonunu 3 kez tekrarla (ADN-006) | Jank'sız; reduced-motion'da kapalı `⚠️` | Görsel + tercih | CLS ≤ 0.1 (`VERIFIED`) | 2.4.7 `⚠️` (hareket numarası P5.5) |
| 9 | Genç dörtlüsü (Zeliha/Buse/Furkan/Atakan) beklentileri uygula (ADN-009) | §3.1.3 satır 19/20/24/25 doğrulanır | Kişi başı ekran | BPM `⚠️` (eşik değil) | 2.5.8 |
| 10 | **KVKK/veli onayı (çocuk dörtlüsü — ADN-008)**: geçişi tetikle | Onay akışı görünür; ret kalıcı kısıtlama | Ekran + onay `⚠️` | Veli onayı `⚠️ DERIVED` (P6.4) | 2.5.8 (48px+ ⚠️) |
| 11 | Çocukta BPM filtresi ara (ADN-008) | Filtre yok/erişilemez (sade akış) | UI taraması | BPM `⚠️` | — |
| 12 | 320×568 + 768×1024'de geçiş tekrarı | Mood UI her viewport'ta kullanılabilir | 2 viewport | 2 breakpoint (`VERIFIED`) | 2.5.8 ≥ 24 px (`VERIFIED`) |
| 13 | Throttling altında tekrar (ADN-007) | Eşikler korunur | Performance panel | LCP ≤ 2500 ms · TBT ≤ 50 ms (`VERIFIED`) | — |
| 14 | Rapor (Seviye 4) — küme/etiket dökümü | Geç/kal + §6.5 sayımı | §6.1 | P8.1 (`VERIFIED`) | §6.2 |

### §5.4 Playwright / CDP Kod Bloğu (P8 — `VERIFIED` API adlar)

```js
const { devices } = require('@playwright/test');

// Genç erkek (Atakan — Dans Enerjik) koşusu (P8.4 registry)
const context = await browser.newContext({ ...devices['Pixel 5'] });
const page = await context.newPage();

// Viewport override (P8.4)
await page.setViewportSize({ width: 375, height: 812 });

// Çocuk koşusunda ağ kesintisi (P8.4: use: { offline: true })

// CDP ağ emülasyonu (P8.4) — P8.3 mobil preset değerleri
const cdp = await context.newCDPSession(page);
await cdp.send('Network.emulateNetworkConditions', {
  offline: false,
  latency: 150,                              // P8.3
  downloadThroughput: 1.6 * 1024 * 1024 / 8, // 1.6 Mbps
  uploadThroughput: 750 * 1024 / 8,          // 750 Kbps
});

// ❌ YASAK: P8'de doğrulanmamış API'ler (geolocation, ses/BPM ölçüm API'si,
//    A/B varyant seçimi API'si P8'de YOK) → kullanılırsa: // ⚠️ VERIFICATION REQUIRED
```

### §5.5 REPORT (Seviye 4)

| Alan | İçerik |
|------|--------|
| Ortam | Tarayıcı+sürüm `⚠️ VERIFICATION REQUIRED`, viewport, throttling |
| Metrik | FCP/LCP/INP/CLS/TBT vs P8.1 |
| Küme sonuçları | §3.1.3 — 7 küme/8 kişi kişi başı durum |
| Tipik hata | Filtre kaybı (mapping §4.7) tespit edildi mi? |
| KVKK | Veli onayı `⚠️ DERIVED` notu |
| Etiket dökümü | §6.5 |

### §5.6 Hata Modları

| # | Hata Modu | Tipik Neden | Müdahale |
|---|-----------|-------------|----------|
| 1 | Filtre kaybı (eski tür sessizce uygulanmış kalır) | State temizliği yok | UI — mapping §4.7 tipik hata |
| 2 | Karışık liste (eski+yeni) | Öneri cache | Backend + UI |
| 3 | Geçişte CLS > 0.1 | Yer tutucu yok | UI |
| 4 | Çocukta BPM filtresi sızdı | Yaş filtresi yok | **CRITICAL** → Security |
| 5 | Veli onayı atlanabilir | Akış zorunlu değil | **CRITICAL** → Security + KVKK |

---

## §6 Doğrulama

### §6.1 Kontrol Listesi

- [ ] Frontmatter 7 alan
- [ ] Zorunlu 7 wiki-link
- [ ] §1–§7 + §4.0
- [ ] Adım tablosu ≥ 10 satır, 6 sütun (§5.3 → 14)
- [ ] KVKK/veli adımı mevcut (§5.3/10)
- [ ] BPM değerleri `⚠️` ile taşındı (eşik yapılmadı)
- [ ] Eşikler P5/P8/P6 etiketli
- [ ] ≥ 500 satır; mojibake 0; BOM yok
- [ ] log.md append-only giriş

### §6.2 WCAG Kriter Durum Tablosu (boş — test sonunda doldurulur)

| Kriter | Adım(lar) | Durum | Kanıt |
|--------|-----------|-------|-------|
| 1.4.3 | 3 | ⬜ | — |
| 1.4.11 | 4 (filtre ikonları) | ⬜ | — |
| 1.4.13 | 3 (hover ipucu varsa) | ⬜ | — |
| 2.4.7 | 7, 8 | ⬜ | — |
| 2.4.11 | 7 | ⬜ | — |
| 2.5.7 | 4 (sürükleme filtresi varsa) | ⬜ | — |
| 2.5.8 | 4, 9, 10, 12 | ⬜ | — |
| 3.2.6 | 3 (yardım konumu) | ⬜ | — |
| 3.3.7 | 10 (onay tekrarı yok) | ⬜ | — |
| 3.3.8 | — (auth yok) | N/A | — |

### §6.3 EXCLUDED Listesine Atıf (research-bank §6.4)

Kapsam/41-55-46 sayı iddiaları kullanılmaz; `EXCLUDED` kayıtlar atlanır.

### §6.4 Quality Report (Bu Dosyanın Kendisi)

| Metrik | Değer |
|--------|-------|
| Versiyon | 1.0.0 |
| Bölüm | §1–§7 |
| Blok | ADN-001 … ADN-010 (10) |
| Adım | 14 (§5.3) |
| Küme | 7 türev küme / 8 kişi (§3.1.3) |
| Status | Red Team · Human Mode · Truth Mode |

### §6.5 Bu Dosyadaki ⚠️ Etiketleri (Toplam)

**⚠️ VERIFICATION REQUIRED — 12 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | A/B test varyantları + kazanan istatistiği | §2.3-1 (eski dosyadan devralındı) |
| 2 | EQ preset sayısı/tanımı (31-band vb.) | §2.3-2 / mapping §4.4-S3 |
| 3 | BPM ölçüm doğruluğu | §2.3-3 |
| 4 | 7 BPM aralığı (60–100 · 120–160 · 130–180 · 60–90 · 120–160) | mood-taxonomy §3.4 (hepsi `⚠️`) |
| 5 | Nicel "öneri tutarlılık oranı" eşiği | §3.1.1-ADN-004 |
| 6 | Hareket azaltma kriter numarası (P5.5'te açılmadı) | §3.1.1-ADN-006 / P5.5 |
| 7 | Yaş kilidi 16 | mapping §4.8 |
| 8 | 4.1.3 durum mesajı numarası | P5.5 |
| 9 | Tarayıcı sürüm envanteri | P8.7 |
| 10 | 41/55/46 sayıları | mapping §5.3 |
| 11 | Oturum zaman aşımı 30 dk | mapping §4.6 |
| 12 | RFC 5322 e-posta doğrulama | mapping §4.6 |

**⚠️ DERIVED — 4 adet:**

| # | Konu | Kaynak |
|---|------|--------|
| 1 | 16 yaş altı → veli onayı (politika sonucu) | P6.4 |
| 2 | Çocuk 48px+ buton hedefi | mapping §4.4 |
| 3 | Çocuk BPM filtresizliği = yaşa uygun politika uygulaması | P6.5 (ADR-088+'a taşınabilir) |
| 4 | Persona'ya özel metrik hedefi yazılsa | P8.7 |

---

## §6.6 Test Çalıştırma Kontrol Listesi & Test Koşusu Şablonu

### §6.6.1 Çalıştırma Öncesi (Run Gate)

| # | Kontrol | Kaynak |
|---|---------|--------|
| 1 | mood-taxonomy §3.4 (7 küme + BPM `⚠️`) okundu mu? | mood-taxonomy |
| 2 | mapping §4.5/§4.7 (öncelik + tipik hata) okundu mu? | mapping |
| 3 | 8 test kişisi (4 çocuk + 4 genç) + yetişkin hazır mı? | §5.2-P5 |
| 4 | P6 veli notu (`⚠️ DERIVED`) hazır mı? | P6.4 |
| 5 | Viewport + throttling seçildi mi? | P8.5/P8.3 |
| 6 | P5.2 + P8 eşikleri okundu mu? | P5/P8 |
| 7 | A/B kapsam red'i rapora yazılacak mı? | §2.3-1 |
| 8 | §4.0 blok korunuyor mu? | Guardrail #16 |

### §6.6.2 Test Koşusu — [TARIH]

**Ortam:** Tarayıcı+sürüm `⚠️ VERIFICATION REQUIRED` · throttling 150 ms/1.6/0.75 · viewport §3.4 · Seviye 1–4.

**Metrik Sonuçları:** FCP ≤ 1 sn · LCP ≤ 2500 ms · INP ≤ 200 ms · CLS ≤ 0.1 · TBT ≤ 50 ms → ölçüm/geç-kal.

**Küme Sonuçları:** 7 küme / 8 kişi — kişi başı PASS/FAIL + tipik hata (filtre kaybı) tespiti.

**KVKK:** Veli onayı akışı `⚠️ DERIVED` (adım 10).

**Sonuç:** `[GEÇ / KAL]` — neden + eskalasyon (§5.6).

---

## §7 Referanslar

### §7.1 Wiki-link Referanslar

| Bağlantı | Rol |
|----------|-----|
| [[personas/index]] | Persona ana indeksi |
| [[personas/methodology]] | Test metodolojisi |
| [[personas/research-bank]] | P5/P6/P8 eşik kaynağı |
| [[personas/test-scenarios-mapping]] | Grup × senaryo matrisi |
| [[personas/persona-template]] | Persona alan şablonu |
| [[ADR-023-persona-driven-testing]] | Karar + coverage gate (satır 16) |
| [[personas/test-senaryolari/a11y-erisilebilirlik]] | Kardeş senaryo (a11y derinliği) |

### §7.2 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yapan |
|-------|-------|-----------|-------|
| 2026-09-26 | 1.0.0 | İlk üretim — eski iskelet (254 satır) mood-taxonomy §3.4 + P5/P6/P8 ile yeniden bağlandı ve genişletildi; 14 adım, 10 blok, 7 küme/8 kişi tablosu + KVKK adımı | Vault Documentation Specialist |
