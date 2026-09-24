---
title: "CoreMusic — Mimari Yazım Akışı (Katman md Batch)"
type: workflow-instruction
category: architecture
version: 1.0.0
status: active
authority: SSOT
updated: 2026-09-24
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Katman md yazımı ve düzeltmesi
  - Batch scope kilidi (31 dosya / 0 silme)
  - Kabul kriteri raporlaması
reference:
  authority: ".ai/WORKFLOW.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/architecture/adlandirma-kurali.md"
    - ".ai/architecture/katman-baglilik-matrisi.md"
    - ".ai/architecture/katman-sayim-rehberi.md"
    - ".ai/architecture/adr/ADR-023-hibrit-derinlik.md"
    - ".ai/architecture/adr/ADR-024-surucu-firmware-birlesme.md"
    - ".ai/architecture/adr/ADR-025-k8-2-k15-siniri.md"
    - ".ai/architecture/adr/ADR-026-sayim-birimi-5000.md"
    - ".ai/.templates/index.md"
  templates:
    - ".ai/.templates/documentation/katman-readme-template.md"
    - ".ai/.templates/documentation/alt-katman-template.md"
    - ".ai/.templates/adr/adr-nygard-template.md"
    - ".ai/.templates/agents/agent-tartisma-turu-template.md"
update_policy:
  preserve_existing_structure: true
  require_approval_for:
    - "dosya adı değişikliği"
    - "dosya silme"
    - "kategori/klasör taşıma (yalnız ADR ile)"
    - "sayım hedefi değişikliği"
changelog:
  - version: 1.0.0
    date: 2026-09-24
    changes:
      - İlk oluşturma — 31 dosyalık batch / 0 silme kuralı + kabul kriterleri
      - K{n}.a.b(.c) adlandırma ve A0-A5/K matrisi denetimi bağlandı
      - UTF-8 yazım protokolü ve post-operation senkron zorunluluğu eklendi
---

# Mimari Yazım Akışı (Katman md Batch)

**Zorunlu Bağlantılar:** [[../WORKFLOW.md]] · [[../.ai/WORKFLOW.md]] · [[../.ai/CLAUDE.md]] · [[../.ai/AGENTS.md]] · [[../.ai/architecture/adlandirma-kurali]] · [[../.ai/architecture/katman-baglilik-matrisi]] · [[../.ai/architecture/katman-sayim-rehberi]] · [[../.ai/.templates/index]]

---

## §1 Amaç

`.ai/architecture/` altındaki katman md dosyalarını (README, alt katman dokümanı, kök kural dosyaları) **tek bir kilitli batch** içinde yazmak/düzeltmek: adlandırma dili, bağımlılık denetimi, sayım birimi, şablon zorunluluğu, UTF-8 protokolü ve senkron adımlarını tek akışta zorunlu kılmak.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/architecture/**/*.md` yazımı, düzeltmesi, yeniden yapılandırılması (satır edit) | Kod dosyaları (`shared/`, `src/` vb.) |
| 31 dosyalık batch scope kilidi + 0 silme | Dosya silme / adlandırma (→ onay + ADR) |
| Kabul kriteri listesi ve raporu | Faz planlaması (→ `.ai/engine.md`) |
| Wiki-link / cross-reference doğrulama | ADR metni üretimi (→ `adr-creation.md`) |
| Sayım raporlaması (düğüm ≥5000 takibi) | Sayım betiğinin kendisi (`.ai/scripts/`) |

---

## §2 Tetikleyiciler ve Önkoşullar

### §2.1 Tetikleyiciler

| # | Tetikleyici | Batch açılır mı? |
|---|-------------|-------------------|
| 1 | Yeni katman klasörü/README üretimi | ✅ Evet |
| 2 | Adlandırma düzeltmesi (L→K, K7.0.x, 5. seviye) | ✅ Evet |
| 3 | k-surucu → k2-surucu / firmware taşıma (ADR-024) | ✅ Evet (önce link baseline) |
| 4 | A0-A5 raporlama/README eşleme satırı tamamlama | ✅ Evet |
| 5 | Sayım sapması araştırması (K1 −17, K13 −30) | ✅ Evet (salt okunur + rapor) |
| 6 | ADR metni yazımı | ❌ Hayır → `adr-creation.md` |
| 7 | Vault kök/CLAUDE/AGENTS değişikliği | ❌ Hayır → `vault-sync.md` |

### §2.2 Önkoşullar (tümü yoksa DUR)

- [ ] Okuma listesi tamamlandı (§4) — eksik okuma = bağlamsız yazım = halüsinasyon riski
- [ ] İlgili ADR çakışması tarandı (ADR-023…026 + `.ai/.decisions/index.md`)
- [ ] Kullanıcı onayı ile batch kapsamı kesin (dosya listesi sabit)
- [ ] `vault-utf8-writer` erişilebilir (`node .ai/scripts/vault-utf8-writer.mjs scan` çalışıyor)
- [ ] Guardrail #16 için şablon seçildi (`.ai/.templates/index.md` §5) — katman README → `katman-readme-template`, alt katman → `alt-katman-template`

---

## §3 Batch Kuralı — 31 Dosya / 0 Silme

### §3.1 Kural Tablosu

| # | Kural | Değer | İhlali |
|---|-------|-------|--------|
| 1 | **Batch üst sınırı** | Tek batch = **en fazla 31 dosya** | Sınır aşımı → batch ikiye bölünür |
| 2 | **Silme** | **0 dosya** (kural #1 In-Place; onaya tabi istisna bile olsa bu akışta yok) | Silme denemesi → derhal dur + CRITICAL log |
| 3 | Dosya adı | Değiştirilmez (taşıma yalnız ADR-024 gibi onaylı ADR ile) | Revert |
| 4 | Kapsam kilidi | Dosya listesi batch başında sabitlenir; ortada ekleme yok (yeni ihtiyaç → yeni batch) | Kilit bozulursa batch iptal + yeniden plan |
| 5 | Sıra | Yazımdan önce link duyarlılığı: önce kök/README, sonra alt katman md, en son plan/eşleme satırları | Sıra ihlali → kırık link riski, denetim kırmızı |
| 6 | Rapor | Her batch sonunda §7 raporu üretilir | Rapor yoksa batch "tamamlanmış" sayılmaz |

### §3.2 31 Sayısının Anlamı ve Kilitlenmesi

**31**, tek yazım batch'inin **üst sınırıdır** (görev bağlayıcısı, 2026-09-24) — hedef listesi değil, tavan listesidir. Batch planı şu sırayla doldurulur:

```text
1. Hedefleri topla (tetikleyici §2.1)
2. Her hedefe satır: dosya yolu · işlem (yeni|satır-edit|şablonla-üret) · sorumlu agent (§2.4 eşlemesi)
3. Toplam ≤ 31 ise kilitle (batch-id + tarih + agent)
4. Toplam > 31 ise: en yüksek öncelikli 31'i kilitle, kalanı "bir sonraki batch" kuyruğuna yaz
```

⚠️ **VERIFICATION REQUIRED:** "`31` hangi dosyadan türetildi?" sorusunun vault'ta türetme kaydı yoktur; sayı görev bağlayıcısı olarak **kabul kriteri** (tavan) olarak uygulanır, envanter iddiası olarak kullanılmaz. Türetme istenirse üst karar gerekir.

### §3.3 Taşınan Dosya Sayıları (ADR-024 bağlamı — batch içinde geçerli)

| Klasör | Beklenen md | Not |
|--------|-------------|-----|
| `k-surucu/` (eski) | 0 | 12 dosya ADR-024 ile taşındı; klasör boş/kabuk kalır |
| `k2-surucu/` | 14 | 2 kabuk (README, CLAUDE) + 12 taşınan |
| `firmware/` | 8 | K1.f — kalıcı, taşınmaz |
| `adr/` | 4 | ADR-023…026 (kendi serisi) |

Bu sayılar §6 kabul kriterlerinde **sabit kontrol** olarak kullanılır; batch içinde oynarsa batch başarısız sayılır ve ADR/onay beklemeden devam edilmez.

---

### §3.4 Batch Planı Örneği (kılavuz — kopyalanacak şablon değil)

```text
batch-id   : arch-20260924-01
agent      : vault-steward
tetikleyici: §2.1-4 (A0-A5 README eşleme satırları)
kapsam     : 7 dosya / 31 sınırının altında → tek batch yeter

| # | Dosya                                   | İşlem        | Sorumlu agent | Öncelik |
|---|-----------------------------------------|--------------|---------------|---------|
| 1 | .ai/architecture/index.md               | satır-edit   | MO            | P0      |
| 2 | .ai/architecture/k6-guvenlik/README.md  | satır-edit   | security      | P1      |
| 3 | .ai/architecture/k7-middleware/README.md| satır-edit   | security      | P1      |
| 4 | .ai/architecture/k8-servis/README.md    | satır-edit   | backend       | P1      |
| 5 | .ai/architecture/k9-api-routing/README.md | satır-edit | backend       | P1      |
| 6 | .ai/architecture/k15-medya-streaming/README.md | satır-edit | data      | P2      |
| 7 | .ai/log.md                              | append       | —             | P3      |

kuyruk   : (boş — 31 sınırı aşılmadı)
kriterler: AC-01…AC-26 (§6.1 + §6.2 koşullular: AC-21, AC-26)
```

**Okuma sırası NOTU:** yukarıdaki "Dosya" sütunu yalnız örnek bir plandır; gerçek batch'te her satır [[../WORKFLOW.md]] §2.4 (katman → sorumlu agent eşlemesi) ve §2.2 önkoşullarına göre doldurulur. Plan değiştirilirse eski plan rapora "iptal edilen plan" olarak eklenir (silinmez).

---

## §4 Girdiler — Zorunlu Okuma Listesi

| Sıra | Dosya | Batch'te ne verir? |
|------|-------|--------------------|
| 1 | [[../.ai/CLAUDE.md]] | Guardrail #4/#16, §5 katman tablosu |
| 2 | [[../.ai/AGENTS.md]] | Domain boundary, A0-A5 bağlantısı, routing |
| 3 | [[../.ai/WORKFLOW.md]] | Süreç, faz kapıları |
| 4 | [[../.ai/architecture/adlandirma-kurali]] | K{n}.a.b.c grameri, regex, kanıt üçlüsü, L→K, K7.0.x red, K1.f |
| 5 | [[../.ai/architecture/katman-baglilik-matrisi]] | Kanonik bağımlılık okları, §10 envanter |
| 6 | [[../.ai/architecture/katman-sayim-rehberi]] | Sayım birimi, 5.105/5.152, sapmalar, script |
| 7 | [[../.ai/architecture/adr/ADR-023-hibrit-derinlik]] | Derinlik ve tavan kararı |
| 8 | [[../.ai/architecture/adr/ADR-024-surucu-firmware-birlesme]] | Taşıma ve link taraması |
| 9 | [[../.ai/architecture/adr/ADR-025-k8-2-k15-siniri]] | K8.2 → K15 çağrı oku |
| 10 | [[../.ai/architecture/adr/ADR-026-sayim-birimi-5000]] | Düğüm birimi, kanıt şeffaflığı, iki seri |
| 11 | [[../.ai/.templates/index]] | Şablon seçimi (Guardrail #16) |
| 12 | Hedef dosyaların tamamı (batch listesi) | Mevcut içerik — silinmeyecek olan |

**Okuma kuralı:** 1-11 tamamlanmadan 12'ye yazılmaz. Token aşımı durumunda 4, 5, 6, 10 **özet okunamaz — tam okunur**; bu dördü bağlayıcı kaynaktır.

---

## §5 Adımlar

### Adım 1 — Plan kilidi (10 dk)

1. §3.1'e göre hedef listesi (≤31) + işlem türü + sorumlu agent yazılır.
2. `batch-id` = `arch-YYYYMMDD-NN` biçiminde üretilir.
3. Kabul kriteri seti (§6) batch'e gömülür: hangi kriter hangi dosyada kontrol edilecek.
4. Kullanıcıya/onaya 1 satır özet gönderilir: "batch arch-…: N dosya, 0 silme, kriter M madde".

### Adım 2 — Şablon seçimi (5 dk)

| Hedef tipi | Şablon | Zorunluluk |
|------------|--------|------------|
| Katman README (`k{n}-*/README.md`) | [[../.ai/.templates/documentation/katman-readme-template]] | Yeni/üretilen README'de zorunlu |
| Alt katman md (`k{n}-*/<konu>.md`) | [[../.ai/.templates/documentation/alt-katman-template]] | Yeni dosyada zorunlu |
| Mimari ADR | [[../.ai/.templates/adr/adr-nygard-template]] veya `adr/adr-template` | ADR batch'inde |
| Tartışma kaydı (karar gerektiren değişiklik) | [[../.ai/.templates/agents/agent-tartisma-turu-template]] | 3 tur / 20 persona tetiklendiyse |

Şablon yoksa batch **DURUR** (Guardrail #16) — kendi başına iskelet uydurulmaz.

### Adım 3 — Ön tarama (salt okunur, 10 dk)

```powershell
# 5. seviye / sıfırlı segment / kırık aday (0 ihlal beklenir)
Get-ChildItem .ai\architecture -Recurse -Filter *.md |
  Select-String -Pattern 'K\d+(\.\d+){4,}|K7\.0'
# sabit dosya sayıları (0 / 14 / 8 / 4 beklenir)
(Get-ChildItem .ai\architecture\k-surucu  -Filter *.md -ErrorAction SilentlyContinue).Count
(Get-ChildItem .ai\architecture\k2-surucu -Filter *.md).Count
(Get-ChildItem .ai\architecture\firmware  -Filter *.md).Count
(Get-ChildItem .ai\architecture\adr       -Filter *.md).Count
```

Sonuçlar §7 raporunun "ÖN" sütununa yazılır (before/after kanıtı).

### Adım 4 — Yazım (batch sıra sırasıyla)

| Sıra | Kalem | Neden önce? |
|------|-------|-------------|
| 1 | Kök md (adlandirma / matris / sayım / index) | Alt dosyalar buna link verir |
| 2 | Katman README'leri | Alt katman md'leri README'ye eşlenir (K{n}-NN ↔ K{n}.a.b) |
| 3 | Alt katman md'leri | En derin içerik |
| 4 | ADR / plan eşleme satırları | Kanıt (iii) satırları en sonda güncellenir |

**Yazım araçları:** yalnız `vault-utf8-writer` (`write` | `insert-before-marker` | `append`) veya edit aracı. PowerShell yazım cmdlet'leri **YASAK** (§9). Her yazım sonrası `verify`.

### Adım 5 — Adlandırma denetimi

- [ ] Tüm düğümler regex'e uyar: `^K([0-9]|1[0-9]|20)(\.[1-9][0-9]*){0,3}$`
- [ ] 4. seviye yalnız kanıt (i)/(ii)/(iii) ile — kanıtsız varsa 3. seviyeye indir
- [ ] 0 ara segment (K7.0.x dahil), 0 sıfır dolgusu, 0 küçük-k önek
- [ ] L-önek kalıntısı 0 (yalnız `frontend-restructuring-plan.md` alıntıları + CLAUDE §32 atıfları hariç)
- [ ] K1.f yalnız harfli istisna; yeni harfli segment yok

### Adım 6 — Bağımlılık / katman ihlali denetimi

- [ ] Yeni/edited ok var mı → **önce matris satırı yoksa ok YOKTUR** (tanımsız bağımlılık = hata)
- [ ] K15 → K16-20 = 0; K12 → K8 dışı bağımlılık = 0; K15 → başka = 0
- [ ] K8.2 → K15 yalnız "çağrı" etiketiyle (ADR-025); K11 → K12 yalnız "gösterim"
- [ ] A0-A5 atamaları raporlama içindir; denetim K matrisinden okundu
- [ ] K16-K20 ↔ K1 çift yönlü dışında ikili çift yönlü yok

### Adım 7 — Cross-reference doğrulama

- [ ] Wiki-link biçimi `[[relative/path/to/file]]` — hedef dosya diskte var mı?
- [ ] `[[k-surucu/...]]` kalıntısı = 0
- [ ] Yeni eklenen link'ler §4 listesindeki kaynaklara mı yoksa ikinci ellere mi gidiyor? (SSOT: ikinci el yasak)
- [ ] Kırık link raporu: batch öncesi baseline ile batch sonrası karşılaştırılır; yeni kırık = 0

### Adım 8 — Sayım ve rapor

1. Düğüm sayımı **tahmin edilmez**; varsa `.ai/scripts/` sayım betiği çalıştırılır, yoksa §6.3'e göre raporlanır (tahmin ≠ ölçüm).
2. §7 raporu doldurulur (ÖN/SON sütunları, kriter sonuçları).
3. `log.md`'ye **append-only** giriş yazılır (`vault-utf8-writer append`).
4. Post-operation senkron: `session-save.mjs` → `vault-post-update.mjs` → doğrulama (log.md, MEMORY.md, project-state.md).

### Adım 9 — Kapanış ve kuyruk devri

1. Kriter sonucu: AC-01…AC-26 tamamı ✅ → batch `completed`; biri ❌ → batch `failed` (dosyalar geri alınmaz, düzeltme batch'i açılır).
2. Kuyrukta kalan hedefler (31 sınırına takılanlar) bir sonraki `arch-YYYYMMDD-NN+1` batch'ine devredilir.
3. §7 raporu `.ai/reports/batch-arch-YYYYMMDD-NN.md` dosyasına yazılır (yeni rapor md'si — AC-04/AC-05 tabi).
4. `log.md` append'i tek satır özet içerir: batch-id, dosya sayısı, silme 0, kriter sonucu.
5. Senkron doğrulaması (§5 Adım 8.4) ✅ değilse batch kapanmaz.

### §5.1 Zaman Bütçesi (toplam ~60 dk / 31 dosyalık batch üst sınırı)

| Adım | Bütçe | Aşım koşulu |
|------|-------|-------------|
| 1 Plan kilidi | 10 dk | Liste hâlâ değişiyorsa → kilitleme başarısız, DUR |
| 2 Şablon seçimi | 5 dk | Uygun şablon yoksa → Guardrail #16 DUR |
| 3 Ön tarama | 10 dk | İhlal > 0 ise düzeltmeden devam yok |
| 4 Yazım | 25 dk | Dosya başına ~1 dk (satır edit); yeni md'de süre serbest |
| 5-7 Denetimler | 5 dk | Herhangi bir ❌ → Adım 4'e dön |
| 8 Sayım + rapor | 3 dk | Ölçüm yoksa "tahmin" etiketi zorunlu |
| 9 Kapanış | 2 dk | Senkron ✅ değilse açık kalır |

---

## §6 Kabul Kriterleri (Acceptance Criteria)

### §6.1 Zorunlu Kriterler (tamamı geçmeden batch kapanmaz)

| # | Kriter | Ölçüt | Kanıt |
|---|--------|-------|-------|
| AC-01 | Dosya silme | 0 | git status / batch listesi diff |
| AC-02 | Dosya adı değişikliği | 0 (ADR-024 taşıması hariç — o da ad değişikliği değil içerik taşıması) | diff |
| AC-03 | Batch boyutu | ≤ 31 dosya | batch tablosu |
| AC-04 | Yeni/tümdüzeltilen md derinliği | ≥ 500 satır (şablonlar hariç 100-250) | satır sayacı |
| AC-05 | Frontmatter | 7 zorunlu alan (title, type, category, version, status, authority, updated) | kontrol |
| AC-06 | Adlandırma regex | 0 ihlal | §5 Adım 3 komutu |
| AC-07 | 5. seviye | 0 | regex `K\d+(\.\d+){4,}` |
| AC-08 | Kanıtsız 4. seviye | 0 (yalnız kanıt (i)/(ii)/(iii)) | kanıt tablosu |
| AC-09 | K7.0.x / sıfır segment | 0 | regex `K7\.0` |
| AC-10 | L-önek kalıntısı | 0 (mazeretli alıntılar hariç) | grep raporu |
| AC-11 | k-surucu / k2-surucu / firmware / adr dosya sayısı | 0 / 14 / 8 / 4 | sayım |
| AC-12 | Yeni kırık wiki-link | 0 | link taraması |
| AC-13 | Tanımsız bağımlılık oku | 0 (matris satırı olmayan ok yok) | matris diff |
| AC-14 | K15 → K16-20 / K12 → K8 dışı | 0 / 0 | matris taraması |
| AC-15 | Şablon zorunluluğu (yeni dosyalar) | Guardrail #16 — §2'den şablon atıflı | şablon başlık eşleşmesi |
| AC-16 | UTF-8 protokolü | PowerShell yazım cmdlet'i kullanılmadı; verify = OK | `vault-utf8-writer verify` |
| AC-17 | log.md girişi | append ile 1+ satır | log sonu |
| AC-18 | Post-op senkron | session-save + vault-post-update + 3 dosya doğrulama | script çıktıları |
| AC-19 | Sapma şeffaflığı | K1 −17 / K13 −30 hâlâ ⚠️ VERIFICATION REQUIRED işaretli | rapor satırı |
| AC-20 | Uydurma yok | Yeni her iddia kaynaklı; doğrulanamayan etiketli | hallüsinasyon taraması |

### §6.2 Koşullu Kriterler

| # | Koşul | Kriter |
|---|-------|--------|
| AC-21 | Yeni bağımlılık oku eklendiyse | Matris §2.1-§2.2'de satır var + ADR bağlantısı |
| AC-22 | Klasör taşıması varsa (ADR-024) | Önce+sonra link baseline raporu; `[[k-surucu/]]` = 0 |
| AC-23 | Tartışma tetiklendi (yeni mimari ilke) | 3 tur / 20 persona kaydı (`agent-tartisma-turu-template`) |
| AC-24 | Yeni ADR üretildiyse | Doğru seri + doğru numara + index.md kaydı |
| AC-25 | Sayım betiği çalıştırıldıysa | ≥5000 sonucu + kanıt türü kırılımı raporda |
| AC-26 | Kök (vault root) dosya dokunulduysa | Yalnız hedefli edit + root CLAUDE/WORKFLOW grep kanıtı |

### §6.3 Ölçüm Disiplini

| İzinli | Yasak |
|--------|-------|
| Sayım betiği çıktısı (ölçüm) | Elle yazılmış "yaklaşık düğüm" tahmini ölçüm diye sunmak |
| "Tahmin: X" — açıkça tahmin etiketiyle | Tahmini ölçüme çevirmek |
| İki değeri birlikte raporlamak (334 ↔ 340) | Birini sessizce silmek/üstünü yazmak |
| ≥5000 geçmediyse "kırmızı" rapor | Yeşil göstermek için kopya/boş düğüm eklemek |

---

## §7 Çıktı — Batch Raporu Formatı

```markdown
## BATCH RAPORU — arch-YYYYMMDD-NN

**Agent:** <agent-adi> · **Tarih:** YYYY-MM-DD · **Dosya:** N/31 · **Silme:** 0

### Dosya listesi (önce/sonra satır)
| # | Dosya | İşlem | Önce | Sonra |
|---|-------|-------|------|-------|
| 1 | .ai/architecture/k6-guvenlik/README.md | satır-edit | 512 | 528 |
| ... |

### Kabul kriterleri
| AC | Sonuç | Kanıt |
|----|-------|-------|
| AC-01 | ✅ | git status: 0 deletion |
| AC-06 | ✅ | regex ihlal 0 |
| ... |

### Sayım
- ÖN: <ölçüm> · SON: <ölçüm> · hedef ≥5000
- Sapmalar: K1 −17, K13 −30 → ⚠️ VERIFICATION REQUIRED (korundu)
- 334 (matris §10) ↔ 340 (disk 2026-09-24) → ⚠️ VERIFICATION REQUIRED (korundu)

### Log & senkron
- [x] log.md append
- [x] session-save.mjs
- [x] vault-post-update.mjs
- [x] log.md / MEMORY.md / project-state.md doğrulandı
```

**Rapor nereye?** `.ai/reports/` altına `batch-arch-YYYYMMDD-NN.md` (yeni rapor dosyası da §6.1 AC-04/AC-05'e tabidir — ≥500 satır değilse rapor değil, özet satırı olarak log'a eklenir).

### §7.1 Dolu Rapor Örneği (kısaltılmış — gerçek batch'te sütunlar tam doldurulur)

```markdown
## BATCH RAPORU — arch-20260924-01

**Agent:** vault-steward · **Tarih:** 2026-09-24 · **Dosya:** 7/31 · **Silme:** 0
**Durum:** completed · **Kriter:** 26/26 ✅

### Dosya listesi (önce/sonra satır)
| # | Dosya | İşlem | Önce | Sonra |
|---|-------|-------|------|-------|
| 1 | .ai/architecture/index.md | satır-edit | 188 | 194 |
| 2 | .ai/architecture/k6-guvenlik/README.md | satır-edit | 512 | 528 |
| 3 | .ai/architecture/k7-middleware/README.md | satır-edit | 504 | 519 |
| 4 | .ai/architecture/k8-servis/README.md | satır-edit | 510 | 526 |
| 5 | .ai/architecture/k9-api-routing/README.md | satır-edit | 507 | 523 |
| 6 | .ai/architecture/k15-medya-streaming/README.md | satır-edit | 515 | 531 |
| 7 | .ai/log.md | append | 1204 | 1207 |

### Kabul kriterleri (özet)
| AC | Sonuç | Kanıt |
|----|-------|-------|
| AC-01 silme 0 | ✅ | git status: 0 deletion |
| AC-03 ≤31 | ✅ | 7 dosya |
| AC-06 regex 0 ihlal | ✅ | Adım 3 komut çıktısı: boş |
| AC-11 sayılar 0/14/8/4 | ✅ | sayım komutu |
| AC-12 yeni kırık link 0 | ✅ | link diff: 0 |
| AC-19 sapma şeffaf | ✅ | K1 −17, K13 −30 ⚠️ korundu |
| AC-20 uydurma yok | ✅ | tüm iddialar §4 kaynaklı |

### Sayım
- ÖN: (betik yok — ölçüm yok) · SON: (betik yok) · tahmin sunulmadı ✅
- 334 (matris §10) ↔ 340 (disk 2026-09-24) → ⚠️ VERIFICATION REQUIRED (korundu)

### Log & senkron
- [x] log.md append (1207)
- [x] session-save.mjs → exit 0
- [x] vault-post-update.mjs → exit 0
- [x] log.md / MEMORY.md / project-state.md doğrulandı
```

*(Örnek satır/sayılar gösterim amaçlıdır — gerçek batch raporunda her değer disk ölçümünden gelir ve bu örnekteki değerler bağlayıcı değildir: ⚠️ VERIFICATION REQUIRED ilkesi.)*

---

## §8 Hata Yönetimi

| Durum | Belirti | Aksiyon | Escalation |
|-------|---------|---------|------------|
| Okuma listesi eksik | §4 satırı okunmadan yazım | DUR — okumayı tamamla | Gerekirse MO |
| Şablon yok | Guardrail #16 ihlali riski | DUR — `.templates/index.md`'den üret/sor | Kullanıcı |
| 31 sınırı aşıldı | Plan > 31 dosya | Batch'i böl, kuyruğa yaz | — |
| Silme isteği geldi | Herhangi bir | REDDET — onay + ADR gerekir | Kullanıcı |
| Regex ihlali | 5. seviye / K7.0.x / küçük-k | Satır edit ile düzelt + log HIGH | — |
| Kanıtsız 4. seviye | 3 kanıt da yok | Düğümü 3. seviyeye indir + log CRITICAL | — |
| Matriste olmayan ok | Yeni bağımlılık iddiası | Oku EKLEME — matris satırı + ADR iste | Üst karar |
| UTF-8 bozulması | `verify` hatası / mojibake | `vault-utf8-writer repair` (yedekli) | — |
| Link kırığı | Hedef dosya yok | Düzelt ya da `⚠️ VERIFICATION REQUIRED` + log HIGH | — |
| Sayım < 5000 | Betik çıktısı eşik altı | Kırmızı rapor — şişirme YASAK | Üst karar / ADR |
| Senkron başarısız | script ≠ 0 | Max 3 retry → `status: pending` | MO |

---

## §9 Yasaklar

1. **Dosya silmek** (her koşulda — bu akışta onay bile olsa yok; ayrı üst onay akışı gerekir).
2. **Dosya adını/yerini değiştirmek** (In-Place Refactoring kuralı #1; taşıma yalnız ADR-024 kapsamındaki 12 dosya).
3. **PowerShell yazım cmdlet'leri:** `Set-Content`, `Out-File`, `Add-Content`, `echo >`, `New-Item -Value`.
4. **Şablonsuz dosya üretimi** (Guardrail #16).
5. **Sayım birimini dosya/bileşen diye değiştirmek** (ADR-026: birim = düğüm).
6. **Sapmaları gizlemek** (K1 −17, K13 −30, 334↔340) — düzeltmek de yasak; ⚠️ korunur.
7. **Matriste olmayan bağımlılık oku eklemek** (önce matris satırı + gerekirse ADR).
8. **`log.md`'de append dışı yazım.**
9. **Frozen ADR-001…037'ye dokunmak.**
10. **İki ADR serisini birleştirmek** (ADR-026 §3.4 — REDDEDİLDİ).
11. **Kopya/boş düğüm ile hedefe ulaşmak** (metrik enflasyonu).
12. **Sır/`.env` değeri yazmak** (REDACTED).

---

## §10 Doğrulama Komutları (salt okunur)

```powershell
# Batch dosyalarının satır sayıları (AC-04)
Get-ChildItem .ai\architecture -Recurse -Filter *.md |
  ForEach-Object { "{0}`t{1}" -f (Get-Content $_.FullName).Count, $_.Name }

# Adlandırma regex ihlalleri (AC-06/07/09) — 0 beklenir
Get-ChildItem .ai\architecture -Recurse -Filter *.md |
  Select-String -Pattern 'K\d+(\.\d+){4,}|K7\.0|k[0-9]{1,2}\.'

# L-önek kalıntısı (AC-10) — 0 beklenir (mazeretli alıntılar hariç)
Get-ChildItem .ai\architecture -Recurse -Filter *.md |
  Select-String -Pattern 'L\d+(\.\d+){1,3}'

# Kırık wiki-link adayları (AC-12)
Get-ChildItem .ai\architecture -Recurse -Filter *.md |
  Select-String -Pattern '\[\[(k-surucu|L[0-9])'

# Dosya gerçekliği (AC-11)
(Get-ChildItem .ai\architecture\k-surucu  -Filter *.md -ErrorAction SilentlyContinue).Count
(Get-ChildItem .ai\architecture\k2-surucu -Filter *.md).Count
(Get-ChildItem .ai\architecture\firmware  -Filter *.md).Count
(Get-ChildItem .ai\architecture\adr       -Filter *.md).Count
```

### §10.1 Komut Çıktısı Yorumlama Tablosu

| Çıktı | Yorumlenme | AC etkisi |
|-------|------------|-----------|
| Regex komutu boş satır döndürür | İhlal yok | AC-06/07/09 ✅ |
| `k-surucu` = 0 · `k2-surucu` = 14 | ADR-024 taşıması yerinde | AC-11 ✅ |
| `firmware` = 8 · `adr` = 4 | K1.f + mimari seri sağlam | AC-11 ✅ |
| `L\d+(\.\d+){1,3}` eşleşmesi | Mazeretli alıntı mı? (`frontend-restructuring-plan` / CLAUDE §32) → değilse ❌ | AC-10 |
| `[[k-surucu/` veya `[[L[0-9]` eşleşmesi | Eski ad/şema linki → ❌ | AC-12 |
| Satır sayısı < 500 (şablon olmayan yeni md) | ❌ AC-04 — derinlik yetmedi, genişlet | AC-04 |
| `vault-utf8-writer verify` ≠ OK | Encoding bozulması → `repair` | AC-16 |

---

## §11 İlgili Dosyalar

| Dosya | İlişki |
|-------|--------|
| [[../WORKFLOW.md]] | Pointer + bağlayıcı özet (§8 bu akışın özeti) |
| [[../.ai/WORKFLOW.md]] | Süreç anayasası |
| [[../.ai/architecture/adlandirma-kurali]] | Adlandırma dili SSOT |
| [[../.ai/architecture/katman-baglilik-matrisi]] | Bağımlılık okları SSOT |
| [[../.ai/architecture/katman-sayim-rehberi]] | Sayım tablosu + sapmalar |
| [[../.ai/architecture/adr]] | Mimari ADR serisi (ADR-023…026) |
| [[../.workflows/vault-sync]] | Senkron akışı |
| [[../.workflows/adr-creation]] | ADR yazım akışı |
| [[../.workflows/hallucination-control]] | VERIFICATION protokolü |
| [[../.ai/.templates/index]] | Şablon registry (Guardrail #16) |
| [[../.ai/log.md]] | Audit trail (append-only) |

## §12 Değişiklik Geçmişi

| Tarih | Sürüm | Değişiklik | Kaynak |
|-------|-------|------------|--------|
| 2026-09-24 | 1.0.0 | İlk oluşturma — 31 dosya/0 silme batch kuralı, 20+ kabul kriteri, 8 adımlı yazım akışı, rapor formatı | Vault iş akışı genişletme görevi |

---

**REFACTOR REPORT:** FILE: architecture-write.md · PURPOSE: Katman md yazım batch'i — scope kilidi (≤31 dosya, 0 silme) + kabul kriterleri (AC-01…AC-26) + UTF-8/senkron zorunluluğu · VALIDATION: yeni dosya (0 → 500+ satır), mevcut dosya değiştirilmedi, 0 silme · RELATED: [[../WORKFLOW.md]] · [[../.ai/architecture/adlandirma-kurali]] · [[../.ai/.templates/index]]

*CoreMusic Mimari Yazım Akışı v1.0.0 — Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Human Mode · Truth Mode*
