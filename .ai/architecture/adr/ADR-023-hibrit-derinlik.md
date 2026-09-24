---
title: "ADR-023 — Hibrit Derinlik Stratejisi (Kanıt-Üstü 4. Seviye)"
type: adr
category: architecture
date: 2026-09-24
updated: 2026-09-24
tarih: 2026-09-24
durum: önerildi
status: proposed
version: 1.0.0
kaynak: "3 turlu agent tartışması 20 persona"
authority: "Karar metninin kendisi (Guardrail #16 adr-template iskeleti)"
governance: Red Team · Human Mode · Truth Mode
---

# ADR-023 — Hibrit Derinlik Stratejisi (Kanıt-Üstü 4. Seviye)

**Durum:** önerildi · **Tarih:** 2026-09-24
**Karar Veren:** 3 turlu agent tartışması 20 persona (uzlaşma)
**İlgili ADR'ler:** [[ADR-024-surucu-firmware-birlesme]] · [[ADR-025-k8-2-k15-siniri]] · [[ADR-026-sayim-birimi-5000]]
**Konum:** .ai/architecture/adr/ADR-023-hibrit-derinlik.md (ayrı seri — bkz. §6.2 numara notu)

---

## §1 Başlık

**Hibrit Derinlik Stratejisi:** CoreMusic katman ağacı 21 ana katmanda (K0-K20) sabit kalır; düğüm derinliği K{n} → K{n}.a → K{n}.a.b zorunlu üç seviye ile sınırlandırılır, 4. seviye (K{n}.a.b.c) ise yalnız üç kanıt türünden biriyle (diskte katman MD dosyası, README/index bileşen tablosu satırı, frontend-restructuring-plan.md §2.1-§2.2 satırı) açılır. Hedef: 5.152 (alt) / 5.400 (orta) / 6.200 (üst) düğüm aralığında, yapay şişirme olmadan 5000+ sayımına ulaşmak.

---

## §2 Durum

| Alan | Değer |
|------|-------|
| Durum | **önerildi** (proposed) |
| Tarih | 2026-09-24 |
| Kaynak | 3 turlu agent tartışması 20 persona |
| Bağlayıcılık | Evet — adlandırma ve sayım dosyaları bu karara bağlıdır |
| Onay akışı | Vault Steward ✅ → Tech Lead ⏳ → Arch Lead ⏳ (adr-template §4.2) |
| Revizyon koşulu | Derinlik kuralı değişirse YENİ ADR (ADR-023'ü supersede eder) |

---

## §3 Bağlam (Context)

### §3.1 Mevcut Durum

CoreMusic vault'unun .ai/architecture/ altında 21 katman klasörü (k0-isletim-sistemi … k20-bom) plus firmware/ ve k-surucu kalıntısı bulunur. Diskte ölçülen (2026-09-24) birinci seviye .md dosyası sayısı 332'dir; buna karşılık mimari tartışmanın hedefi 5000+ **düğüm**dür. Dosya sayısı ile hedef arasındaki fark, "nereye kadar derinleşileceği" sorusunu zorunlu kılar: 332 dosyayı 5000'e taşımak ya uydurma içerik ya da çok derin iç içe geçmiş bir hiyerarşi demektir.

Ayrıca vault'ta iki ayrı "derinlik" fikri birlikte yaşamaktadır:

| Kaynak | Derinlik Fikri | Durum |
|--------|----------------|-------|
| [[frontend-restructuring-plan]] §2.2 | 4. seviye (L11.1.4.13 = c-player.css) — 4 katmanlı kod dosyası planı | Mevcut, kanıtlı |
| [[CLAUDE.md]] §32 | 4/7 katmanlı basitleştirilmiş L0-L3 şeması | Mevcut (ikinci "L" uzayı) |
| [[adlandirma-kurali]] (bu tartışma) | K şeması: 3 seviye zorunlu + 4. seviye koşullu | Önerildi (bu ADR) |

### §3.2 Sorun Tanımı

**Soru:** 21 sabit ana katmanda 5000+ düğüme nasıl ulaşılır — derinlik hangi ilkeyle belirlenir?

Üç olası ilke tartışmaya girmiştir:

1. **Şişirme:** Var olmayan içeriği 4. seviyeyle üretmek (kanıt yoksa bile düğüm açmak).
2. **Sığlaştırma:** 4. seviyeyi tümüyle yasaklamak, hedefi düşürmek.
3. **Kanıt-üstü hibrit:** 4. seviyeyi yalnız fiziksel/plan kanıtıyla açmak.

### §3.3 Kısıtlar

| # | Kısıt | Açıklama | Kaynak |
|---|-------|----------|--------|
| 1 | 21 ana katman sabit | K0-K20; K16-K20 bağımsız, K16-K20'ye yeni katman eklenmez | 3 tur uzlaşma |
| 2 | Dependency Rule | Alt katman üst katmana bakmaz; derinlik bağımlılık yaratmaz | [[katman-baglilik-matrisi]] |
| 3 | Kanıt zorunluluğu | Kanıtsız düğüm uydurmadır; hallüsinasyon sayılır | [[CLAUDE.md]] guardrail |
| 4 | 5000+ hedefi | min kapasite ≥ 5000 (betikle sınanır) | [[ADR-026-sayim-birimi-5000]] |
| 5 | İkinci "L" uzayı | CLAUDE §32'nin 4 katmanlı L şeması K'ya otomatik çevrilmez | [[adlandirma-kurali]] §7.3 |
| 6 | Uygulama maliyeti | Derin klasör ağacı (>4 seviye) navigasyon ve link bakımı pahalı | Vault kuralı (In-Place #4) |
| 7 | Sayım denetlenebilirliği | Derinlik kuralı PowerShell betiğiyle taranabilir olmalı | [[katman-sayim-rehberi]] §9 |

### §3.4 Web Araştırması Raporu

| Alan | Değer |
|------|-------|
| Web Search Query | hierarchical taxonomy depth best practice 4 levels information architecture |
| Web Search Konusu | Bilgi mimarisinde hiyerarşi derinliği — kaç seviye sürdürülebilir? |
| Web Search Bağlamı | Katmanlı mimari + vault dokümantasyon derinliği |
| Web Search Kısa Açıklama | IA literatüründe 3-4 seviye genelde kullanıcı (ve yazar) için sürdürülebilir sınır sayılır |
| Web Search Uzun Açıklama | Derinlik arttıkça her seviyenin kanıt yükü ve link bakımı büyür; kanıtsız seviye "kondurma" bilgiye güvensizlik yaratır |
| Web Search Paragraf | CoreMusic için pratik sonuç: 3 seviye zorunlu (K{n}.a.b), 4. seviye yalnız fiziksel/plan kanıtıyla — hem derinlik hem doğruluk korunur |
| Web Search Sonucu | Kanıt-koşullu 4. seviye, siber-fiziksel vault için uygulanabilir sınır |
| Web Search Alınan Karar | Hibrit derinlik: 3 zorunlu + kanıtla 4. |
| Web Search Sonuç | 5.152/5.400/6.200 aralığına kanıt-üstü ulaşım |

*⚠️ VERIFICATION REQUIRED: Bu bölümdeki web araştırması, karar tartışmasının çıktılarına dayanır; canlı arama oturum kaydı .ai/log.md append ile güncellenmelidir.*

---

## §4 Karar (Decision)

**CoreMusic katman ağacı hibrit derinlik stratejisiyle düzenlenir:**

| # | Karar Maddesi | Ayırıcı Söz |
|---|---------------|-------------|
| 1 | 21 ana katman (K0-K20) sabittir; derinlik arttıkça ana katman sayısı artmaz | "Derinlik = seviye, katman değil" |
| 2 | Zorunlu şablon: K{n} → K{n}.a → K{n}.a.b (3 seviye her düğüm için yeterli başlangıç) | "Üç seviye zorunlu" |
| 3 | 4. seviye K{n}.a.b.c **yalnız** üç kanıt türünden biriyle açılır: (i) diskte katman MD dosyası, (ii) README/index bileşen tablosu satırı, (iii) frontend-restructuring-plan.md §2.1-§2.2 satırı | "Kanıt yoksa seviye yok" |
| 4 | 5. seviye (K{n}.a.b.c.d) YASAKTIR — yeni ADR olmadan açılamaz | "Dört seviye tavan" |
| 5 | Sayım hibrit derinlikten türetilir: alt 5.152 / orta 5.400 / üst 6.200; kabul min ≥ 5000 | "Hedef kanıttan gelir" |
| 6 | K7.0.x gibi ara numaralar REDDEDİLMİŞTİR (düğüm değil, yonga adıdır) | "Ara numara yok" |
| 7 | CLAUDE §32'nin 4 katmanlı L şeması bu derinlik uzayına dahil değildir; otomatik L→K dönüşümü yapılmaz | "İki uzay ayrı" |

### §4.1 Neden Bu Seçenek? (Rationale)

| Gerekçe | Açıklama |
|---------|----------|
| Kanıt bütünlüğü | Her 4. seviye düğüm geriye dönük olarak izlenebilir (dosya, satır, plan satırı) — uydurma yok |
| Hedefe ulaşım | 3 seviye tek başına yetersiz (21 + 193 + ~1.100); kanıtla açılan 4. seviye 5000+ bandını açar |
| Bakım maliyeti | 5. seviye yasak olduğu için link grafiği sınırlı kalır |
| İki "L" uzayı ayrımı | §3.1'deki çakışma (CLAUDE §32) otomatik dönüşüm yasağıyla kapatılır |

### §4.2 Teknik Detaylar

~~~text
K{n}                    → 1. seviye (21 ana katman, sabit)
K{n}.a                  → 2. seviye (a = 1..Alt[sütunu]; toplam 193)
K{n}.a.b                → 3. seviye (b = 1..Orta[sütunu]; zorunlu son seviye)
K{n}.a.b.c              → 4. seviye (yalnız kanıt (i)/(ii)/(iii); tavan: Tavan sütunu)
K{n}.a.b.c.d            → YASAK (§4 madde 4)
K{n}.0.x                → YASAK (ara numara reddi, §4 madde 6)
~~~

| Katman Sütunu | Ait Olduğu Tablo | Örnek Kullanım |
|---------------|------------------|----------------|
| Alt (a üst sınırı) | [[katman-sayim-rehberi]] §4 | K11 için a=11 → K11.1…K11.11 |
| Orta (b üst sınırı) | [[katman-sayim-rehberi]] §4 | K11 için b=7 → K11.1.1…K11.1.7 |
| Tavan (4. seviye üst sınırı) | [[katman-sayim-rehberi]] §4 | K11 için 330 — yalnız kanıtla |
| Gerçek 4. seviye örneği | [[frontend-restructuring-plan]] §2.2 | L11.1.4.13 → K11.1.4.13 (c-player.css) |

---

## §5 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| A | **Şişirilmiş derinlik:** K9'u 30 slot yerine 240 slot (a×b) yapay genişletmek; kanıt olmadan 4. seviye açmak | Sayı hedefi kolayca şişer; betik erken yeşil döner | Kanıtsız düğüm = hallüsinasyon; K9 (k9-api-routing, 14 dosya) 240 yuvayla anlamsızlaşır; SRP ve izlenebilirlik çöker; link bakımı patlar | **RED:** Sayım hedefi içerik doğruluğunun önüne geçer; kanıt şeffaflığı ilkesi (ADR-026) ihlal edilir; 3 tur tartışmada 20 persona'nın çoğunluğu kanıtsız derinliği reddetti |
| B | **Sığlaştırma:** 4. seviye tümüyle yasak; yalnız K{n}.a.b (3 seviye) | En basit kural; link grafiği minimal; denetim kolay | 3 seviyeyle tavan 1.500-3.500 bandına düşer → **5000 altı** kalır; kabul kriteri (min ≥ 5000) süresiz başarısız; [[frontend-restructuring-plan]] §2.2'deki kanıtlı L11.1.4.13 gibi 4. seviye satırları yetim kalır | **RED:** ADR-026'nın 5000+ bağlayıcı hedefi ile çelişir; mevcut kanıtlı 4. seviye planı çöpe gider |
| C | **Hibrit kanıt-üstü derinlik (SEÇİLEN)** | 3 seviye zorunlu + kanıtla 4. seviye; hedef bandı (5.152+) kanıtla ulaşılır; uydurma yok; 5. seviye tavanı sayesinde bakım sınırlı | Kanıt üretimi ek iş (README/plan satırı yazmak); betik ve tablo senkronu tutulmalı | — **SEÇİLDİ** — A ve B'nin tek başına kalıcı olmadığı tek formül; tüm kısıtları (§3.3) aynı anda karşılar |

### §5.1 Reddedilenlerin Karşılaştırma Matrisi

| Kriter | A Şişirme | B Sığlaştırma | C Hibrit |
|--------|-----------|---------------|----------|
| 5000+ hedefi | ✅ (sahte) | ❌ (1.500-3.500) | ✅ (5.152+) |
| Kanıt şeffaflığı | ❌ | ✅ | ✅ |
| Plan §2.2 uyumu | kısmi | ❌ | ✅ |
| Link bakım maliyeti | yüksek | düşük | orta |
| Hallüsinasyon riski | yüksek | düşük | düşük |
| 20 persona uzlaşması | red | red | **kabul** |

---

## §6 Sonuçlar (Consequences)

### §6.1 Olumlu Sonuçlar

- Sayım hedefi (≥5000) kanıt-üstü ulaşılır: 4.938 + 21 + 193 = 5.152 (alt senaryo).
- Her 4. seviye düğüm geriye dönük izlenebilir — hallucination-control ile hizalı.
- 5. seviye yasağı link grafiğini ve denetim maliyetini sınırlar.
- K7.0.x ara numara reddiyle yonga adları (Neve 7.0.x) ile mimari düğüm adları ayrışır.
- Betik (katman-sayim.ps1) derinliği doğrudan denetler; sapmalar (K1 −17, K13 −30) raporda görünür kalır.

### §6.2 Olumsuz Sonuçlar

- 4. seviye açmak README/plan satırı yazma disiplini gerektirir — ek tıklama maliyeti.
- İki "L" uzayı (plan §2.1 vs CLAUDE §32) arasındaki ayrım elle hatırlanmalı; otomatik dönüşüm yok.
- Tablo (sayım) ile betik senkronu bozulursa iki kaynak çelişir — birlikte revize zorunlu.
- ADR numarası uzayı notu: .ai/.decisions/index.md kayıtlarında ADR-023…026 farklı slug'larla (persona-driven-testing, ecosystem-modular-docs, professional-eq-system, download-service-architecture) mevcuttur; bu dosyalar .ai/architecture/adr/ altındaki **ayrı seridedir** — çakışma kasten kabul edilmiş, iki seri birleştirilmemiştir. ⚠️ VERIFICATION REQUIRED: birleştirme istenirse üst karar gerekir.

### §6.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|------------|
| 1 | 4. seviye kanıtı sonradan silinirse düğüm yetim kalır | Orta | Yüksek | Kanıt (i)/(ii)/(iii) taraması betiğe gömülü (§9.2 desen); link taraması zorunlu |
| 2 | Sayım betiği ile tablo senkronu bozulur | Orta | Orta | Tablo + betik birlikte revize (katman-sayim-rehberi §9.1 kural 1) |
| 3 | 5. seviye sessizce açılır (alışkanlık) | Düşük | Yüksek | regex denetimi: 4 noktadan fazla = RED; yeni ADR gerektirir |
| 4 | K1/K13 sapması gizlenerek "düzeltilir" | Düşük | Orta | ADR-026 şeffaflık kuralı; ⚠️ satırları silinemez |
| 5 | İki "L" uzayı karıştırılıp CLAUDE §32 düğümleri K'ya çevrilir | Orta | Orta | [[adlandirma-kurali]] §7.3 otomatik dönüşüm yasağı |
| 6 | Alternatif A'ya geri dönüş baskısı ("sayım düşük kaldı") | Düşük | Yüksek | Bu ADR §5.1 matrisi; geri dönüş = yeni ADR + supersede |

---

## §7 Uygulama (Implementation)

### §7.1 Adımlar

| # | Adım | Sorumlu | Durum |
|---|------|---------|-------|
| 1 | [[adlandirma-kurali]] yazımı (şablon, K7.0.x red, kanıt türleri, L→K) | Architect agent | ✅ 2026-09-24 |
| 2 | [[katman-sayim-rehberi]] yazımı (tablo, ayrıştırma, betik, kabul kriteri) | Architect agent | ✅ 2026-09-24 |
| 3 | Bu ADR (ADR-023) + ADR-024/025/026 yazımı | Architect agent | ✅ 2026-09-24 |
| 4 | scripts/katman-sayim.ps1 dosyasının diske yazılması ve ilk KABUL çıktısı | DevOps / Vault Steward | ⏳ |
| 5 | README/index bileşen tablosu (kanıt ii) satırlarının 4. seviyeyle eşlenmesi | Domain agent'lar | ⏳ |
| 6 | Onay akışı: Tech Lead → Arch Lead (§7.3) | Vault Steward | ⏳ |
| 7 | .decisions/index.md ve brain.md'ye kayıt satırı + log.md append | MO (vault-updater) | ⏳ |

### §7.2 Geri Dönüş Planı

Derinlik kuralı uygulanamaz veya sakat bulunursa: (1) betik exit 1'i durdurucu kabul edilir, (2) adlandırma ve sayım dosyaları eski sürüme alınır (git checkout), (3) yeni bir ADR ile alternatif B veya A yeniden değerlendirilir, (4) 4. seviye düğümler (kanıtlı olsalar bile) 3. seviyeye indirgenir — hiçbir dosya silinmez (In-Place #4).

### §7.3 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward (yazar) | Bayram Ali | 2026-09-24 | ✅ |
| Tech Lead | — | — | ⏳ |
| Arch Lead | — | — | ⏳ |

---

## §8 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[adlandirma-kurali]] | Bu kararı yürürlüğe koyan adlandırma kuralı |
| [[katman-sayim-rehberi]] | Sayım tablosu + betik + kabul kriteri |
| [[ADR-026-sayim-birimi-5000]] | Sayım birimi ve 5000 hedefi |
| [[ADR-024-surucu-firmware-birlesme]] | K1.f ve k-surucu derinlik kararları |
| [[ADR-025-k8-2-k15-siniri]] | K8.2/K15 servis-boru hattı sınırı |
| [[katman-baglilik-matrisi]] | Bağımlılık okları (derinlikten bağımsız) |
| [[frontend-restructuring-plan]] | Kanıt türü (iii) kaynağı (L11.1.4.13) |
| [[CLAUDE.md]] | §32 ikinci L uzayı (dönüşüm yasağı gerekçesi) |

---

## §9 Salt Okunur Doğrulama

| # | Kontrol | Komut (salt okunur) | Beklenen |
|---|---------|---------------------|----------|
| 1 | Zorunlu 3 seviye şablonu dosyada yazılı mı | Select-String -Pattern "K{n}.a.b" | Eşleşme var |
| 2 | 4. seviye üç kanıt türü sayıldı mı | Select-String -Pattern "diskte katman MD" | Eşleşme var |
| 3 | 5. seviye yasağı kayıtlı mı | Select-String -Pattern "K{n}.a.b.c.d" | "YASAK" ile |
| 4 | Alternatif sayısı ≥ 3 mü | §5 tablo sayımı | A, B, C = 3 satır |
| 5 | Risk tablosu dolu mu | §6.3 | 6 satır |
| 6 | Geri dönüş planı boş değil mi | §7.2 | Dolu |

---

## §10 Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-09-24 | İlk yazım — 3 tur / 20 persona uzlaşması: hibrit derinlik (A şişirme red, B sığlaştırma red, C kanıt-üstü kabul) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
