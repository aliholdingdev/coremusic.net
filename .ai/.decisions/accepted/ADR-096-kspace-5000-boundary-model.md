---
reference_doc: .ai/.templates/adr/adr-template.md (v2.0.2 — Guardrail #16)
title: "CoreMusic — K-Space 5000+ Boundary Model & V2 Mimari Rejimi"
type: adr
category: decisions
date: 2026-10-08
updated: 2026-10-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/architecture/rules.md"
---

# CoreMusic — K-Space 5000+ Boundary Model & V2 Mimari Rejimi

**Durum:** active (Draft → Review → Active · Vault Steward onayı 2026-10-08)
**Tarih:** 2026-10-08
**Karar Veren:** Bayram Ali / Vault Steward (tek onay mercii — F1 §15/I3)
**İlgili ADR'ler:** [[accepted/ADR-001-vanilla-js-itcss]] · [[accepted/ADR-002-pdo-mandatory-no-orm]] · [[accepted/ADR-005-ultrathink-protocol]] · [[accepted/ADR-042-vault-restructuring-2026-08-03]] · [[accepted/ADR-084-api-gateway-architecture]]

---

## 1. Bağlam (Context)

CoreMusic mimarisi 2026-10-08'de **iki yeni master prompt** ile yeniden tanımlandı: **F1** (`coremusic-katmanli-mimari-master-prompt-v1.md` — v2.2.0, 7.560 satır, PICCO, EK A/B/C) ve **F2** (`coremusic-katmanlı-miamri-oluştur.ai.md` — 9.912 satır, 50 bölüm). Kullanıcı talimatı: *"MİMARİ KARAR DEĞİŞTİ — BU YENİ PROMPTA GÖRE REVİZE ET, BAŞLA, SORULAR SOR."* Bu ADR, yeni rejimin **kurucu kararını** kaydeder: K-space uzayı, bant şeması, kayıt formatı, dizin yapısı, ölçek-felsefesi ve kapı disiplini.

Bu karar olmadan `.ai/architecture/` sıfırdan üretimi hiçbir kapıdan geçemez (F1 H08: ADR'siz mimari karar YASAK).

### 1.1 Mevcut Durum

| Öğe | Durum | Kanıt |
|-----|-------|-------|
| Eski `.ai/architecture/` (95 dosya: 500'lik K000-K499 + 10 domain + k0-k20 set) | **Kasıtlı silindi (D=95)** — kullanıcı kararı: sıfır say | `git status --short -- .ai/architecture/` (2026-10-08) |
| v1 plan ürünleri (k1 set, k0 envanter, rules v1.3.0, master-index v1.1.0) | **Terk edildi** — staging kopyaları yalnız çalışma-alanı arşivi | `.superpowers/sdd/…/staging/` (vault'a taşınmadı) |
| Yeni kaynaklar vault'ta | F1 + F2 arşivleri + EK A anayasa yazıldı → `validate --check` exit 0 (34 dosya · 11 check) | `.ai/prompts/2026-10-08-*.md` · `.ai/architecture/00-kspace-anayasa.md` (5.857 satır) |
| Eski modelin yapısal sorunları (GÖREV 02 · 7F analizi) | Rezerve satırlar = yapay dolum (F2 §11 ihlali) · RUNTIME/Failure boundary yok · k0 tek dizinde mixed responsibility · domain dosyaları SSOT ikilemi | `.ai/SESSIONS/2026-10-08-kspace-kesif.md` §4 |
| Mevcut kurallar | Eski R1-R20 (v1.3.0) terk edildi; yeni `rules.md` bu ADR'den türetilecek | plan R1 |

### 1.2 Sorun Tanımı

Yeni rejim şu altı soruyu tek bir bağlayıcı kararda yanıtlamak zorundadır:

1. **K-space uzayı:** K000-K499 (eski sabit 500) mi, K000-K5999 (F1 EK A 9 bant) mi?
2. **Ölçek felsefesi:** "1000 hedef değil / sayı değil boundary" (F2 §11-12) ile "5000+ hedef" (kullanıcı) nasıl birleştirilir?
3. **Kayıt formatı:** EK C 20 alan zorunlu (F1 M10) mu, özet satır mı, hibrit mi?
4. **Yapı:** multi-MD hangi eksende (F2 §42 domain 00-20 ↔ F1 9 bant ↔ katman-bazlı dizin)?
5. **Boyut:** F1 §10.3 min-500 ↔ F2 §43 doğal boyut çelişkisi.
6. **Kapılar:** On Kapı + §11 ≥85 + §9 ≥90 eşikleri bağlayıcı mı?

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | K-space v2.2.0 araştırma turları (F1 EK B defteri devralındı) |
| Web Search **Konusu** | Boundary-based layered architecture · CQRS/Bounded Context · agent/orchestration patterns · audio/runtime kanıtları |
| Web Search **Bağlam** | F1 v2.2.0 üretim süreci — 4 araştırma turu, **37 doğrulanmış URL** (2026-10-08) |
| Web Search **Kısa Açıklama** | Bu ADR YENİ tur çalıştırmaz; F1'in EK B research kapısını (§7.3/§7.1 KAPI 4) referans alır |
| Web Search **Uzun Açıklama** | Örnek kayıtlar (EK B defterinden, F1 metni içi kanıt satırları): #6 `research.google/blog` music recommendation (2024-08-16) · #10 Microsoft Learn Low Latency Audio + FlexASIO BACKENDS.md · #20-21 Fowler — CQRS ve Bounded Context · #24 MS Learn orkestrasyon (5 kalıp) · #25 OpenAI ajan rehberi · #35-37 4. tur doğrulamaları |
| Web Search **Paragraf Veri Uzun** | F1 §7.3 kaynak kalite redleri + §7.4 ile süzgeçten geçmiş; ikincil kaynaklar çapraz doğrulanmış (F1 v2.1.0 changelog: "4. tur kanıt turu: 3 sorgu → EK B toplam 37") |
| Web Search **Sonucu** | Boundary/BC/CQRS literatürü F2 §12 9 kademeli hiyerarşiyle uyumlu; audio runtime kanıtları F1 §8 domain kurallarını besliyor |
| Web Search **Alınan Karar** | EK B bu ADR'nin research kanıtıdır; **ADR-096'ya özgü yeni gap'ler** R3 (GÖREV 03) araştırmasında doldurulur ve EK B vault kopyasına eklenir |
| Web Search **Sonuç** | `⚠️ VERIFICATION REQUIRED` — ADR-096'ya özgü ek tur (varsa) R3 Step 1'de; bu tablo o tarihte güncellenir |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Anayasa üstünlüğü | `.ai/CLAUDE.md`/`.claude/CLAUDE.md` (Zero-Hallucination, §5.1 katman yönü, 16 guardrail) bu ADR'nin üstünde; çelişkide anayasa kazanır |
| Tek onay mercii | F1 §15/I3: agent APPROVE yetkisiz; onay = Bayram Ali (bu ADR, plan v2.0 onayı ile 2026-10-08) |
| F2 §11 gerçek-boundary | Yapay katman / sayı oyunu yasak — 5000+ hedefi bu yasağın İÇİNDE geçerli (dolum yalnız gerekçeli boundary ile) |
| Vault prosedürü | H5 yalnız `vault-utf8-writer.mjs` · H7 `validate.mjs --check` exit 0 · H6 commit = ayrı insan onayı |
| Operasyonel risk | 402 kredi kırığı (ajan ölümü → inline fallback) · peer tekrar-silme (her gate `git status`) |

---

## 2. Karar (Decision)

**CoreMusic mimari rejimi V2 = K-Space Boundary Model:**

1. **K-space uzayı = K000 → K5999**, 9 bant (F1 EK A §A şeması — `.ai/architecture/00-kspace-anayasa.md` SSOT):
   `K000-K020 FOUNDATION` · `K021-K120 ENTERPRISE EXPANSION` · `K121-K500 SPECIALIZED DOMAINS` · `K501-K1020 EXTENDED/DEEP PLATFORM` · `K1021-K2020 DEEP DOMAIN/RUNTIME/CROSS-CUT` · `K2021-K3020 HARDWARE/MEDIA/UI DEEP` · `K3021-K4020 AGENT/SIM/RESEARCH/GOV` · `K4021-K5020 COMMERCE/RIGHTS/AI/DATA` · `K5021-K5999 RESILIENCE/FUTURE/RESERVED`.
2. **Ölçek = 5000+ HEDEF, boundary-GEREKÇELİ dolum:** hedef tabi (kullanıcı kararı 3); ancak her katman gerçek bir boundary (Domain/Subdomain/BC/Runtime/Security/Deployment/Hardware/Driver/Protocol/Data/Concurrency/Ownership/Failure/Integration — F2 §11 listesi) üretmelidir. Sayı için yapay katman, "Rezerve" dolumu, sahte soyutlama **YASAK**. Hedef ≠ kanıt ayrı raporlanır (H10).
3. **İsimlendirme:** dizin = `K{NNN}-{türkçe-ad}` (teknik terim özgün kalır — karar 9); kanonik K-ID **asla değişmez**; teatral epitet (`«TEMEL TAŞI»`) yalnız sıfat — ID'yi ezmez (EK A kuralı).
4. **Kayıt formatı = hibrit (karar 6):** her katman için **özet satır** (F1 §10.2 · 16 alan: K-ID | KANONİK AD | TEATRAL EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ BAĞIMLILIK | YASAK BAĞIMLILIK | DATA BOUNDARY | SECURITY BOUNDARY | FAILURE MODE | OBSERVABILITY | TEST | KANIT) **+** tam **EK C 20 alan kartı** kritik/risk:high/derin katmanlarda zorunlu. F1'in kendi EK A deseni ("özet satırı burada, tam kart gerektiğinde") esastır; M10 bu yorumla bağlanır.
5. **Yapı = hibrit (karar 5):** EK A = K-space anayasa SSOT (`00-kspace-anayasa.md`) + **multi-MD** ağ (`F2 §42` — tek dev md yasak) + **katman-bazlı fiziksel dizinler** (karar 9). Eski k0-k20/10-domain yapısıyla çakışan klasör üretilmez.
6. **Boyut = min-500 her markdown** (karar 10 · F1 §10.3; rapor ≥5000); >2000 satır → `partNN`. **Şişirme yasak** — 500 gerçek derinlikten gelir, dolgu değil.
7. **Dosya boyu/felsefe çelişkisi F1 lehine çözüldü** (F2 §43 reddedildi — karar 10).
8. **Kapılar bağlayıcı:** F1 §7 On Kapı + §7.1 çıkış kriterleri + §11 sekiz kapı **≥85/100** + §9 güvenlik **≥90** + KAPI 10 insan onayı. Eşik altı üretim teslim edilmez (F1 GÖREV 12).
9. **Eski rejim resmen terk:** 500'lük K000-K499 sabiti, 10 domain × 50 tablo, Rezerve dolum, eski R1-R20, eski set şablonu — yenisi yalnız bu ADR + türetilen `rules.md` ile kalkar. Eski içerik yalnız GÖREV 02 analiz girdisidir (salt-okunur `git show HEAD:`).
10. **Hiyerarşi = 9 kademe** (F2 §12): K-Layer → Domain → Subdomain → Bounded Context → Module → Component → Service → Adapter → Implementation.

### 2.1 Neden Bu Seçenek?

- **F1 + F2 birlikte** kullanıcı kararı 2 ile esas; ikisi arasında kalan her çelişki bu ADR'nin §2 maddeleriyle çözüldü (boyut F1, sınır-gerçekliği F2, ikisi de D1/§5.1 klasik yönle uyumlu).
- **Hibrit kayıt**, F1'in KENDİ doküman içi deseniyle (EK A ↔ EK C) uyumlu — 5000+ katmanda tam 20 alan kart ekonomisi ile özet satır gezinilebilirliği dengeler.
- **Boundary-gerekçeli 5000+**, F2 §11'in "1000 fiziksel klasör/yapay abstraction yasak" hükmünü korurken kullanıcının hedefini (karar 3) tamamlar: hedef = kapasite, dolum = gerekçe.

### 2.2 Teknik Detaylar

**9 bant şeması (EK A §A — anayasa):**

```text
Bant 1  K000-K020    FOUNDATION (OS, HARDWARE, DRIVERS, … MANUFACTURING — 21 kart, A.0 anahtar tablosu)
Bant 2  K021-K120    ENTERPRISE EXPANSION
Bant 3  K121-K500    SPECIALIZED DOMAINS
Bant 4  K501-K1020   EXTENDED / DEEP PLATFORM
Bant 5  K1021-K2020  DEEP DOMAIN / RUNTIME / CROSS-CUT
Bant 6  K2021-K3020  HARDWARE / MEDIA / UI DEEP
Bant 7  K3021-K4020  AGENT / SIM / RESEARCH / GOV
Bant 8  K4021-K5020  COMMERCE / RIGHTS / AI / DATA
Bant 9  K5021-K5999  RESILIENCE / FUTURE / RESERVED
```

**Uçak ayrımı (F1 GÖREV 07):** SOFTWARE PLANE = K000→K15+ (K001 HARDWARE dahil — donanım-yazılım kesişimi, yalnız driver/API sınırı) · PHYSICAL PLANE = K016→K020 (+ bant 6 derin donanım).

**Dependency yönü (uyumlu):** izinli = alt katmanlar (K-ID aralığı daha düşük) + port/adapter · yasak = üst katmana doğrudan erişim, geri çağrı (F1 H20), veri paylaşımı (H19). Olay (event) yayını yukarı serbest — anayasa §5.1 + F1 §8 ile üçlü uyum.

**Validator (11 check):** `fm · link · ssot · dep · tier · orphan · id · budget · depdir · fanout · idrange` — kapsam: `.ai/*.md` + `.agents/.rules/.decisions/index` + `architecture/**`; `prompts/` tarama dışı (R0 keşfi). Genişletme yalnız `validate.mjs` içinden.

**Dizin deseni (karar 9):**

```text
.ai/architecture/
├── 00-kspace-anayasa.md      ← EK A (SSOT, 9 bant + 21 foundation kartı)
├── 00-master-index.md        ← giriş kataloğu (R2)
├── rules.md · context.md     ← kural + hot-memory
├── K000-isletim-sistemi/     ← katman dizini (index.md zorunlu; derinleşme = gerçek karmaşıklık)
├── K001-donanim/ … K020-uretim/
└── (bant 2-9 dizinleri — üretim R3, bant bant)
```

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | Eski 500'lik modeli sürdür (K000-K499 + 10 domain) | Kanıtlı altyapı, 95 dosya geri gelebilirdi | Rezerve satırlar = yapay dolum (F2 §11 ihlali); RUNTIME/Failure boundary yok; kullanıcı "sıfır say" dedi | Karar 1 + 4 (kullanıcı) · F2 §11 |
| 2 | F2 §42 domain-based klasör ekseni (00-overview…20-ci-cd) | Konu bazlı gezinme | Katman-ID ile klasör uyuşmaz; 5000+ katmanda katman-bazlı sorgular (K-ID → dosya) kırılır | Karar 9 (kullanıcı): katman-bazlı KID dizin + Türkçe ad |
| 3 | Tam 20 alanlı kart zorunlu (F1 M10 literal) | Tek biçim | 5000+ katmanda üretim maliyeti ~20 alan × 5000; gezinme ağır; EK A kendi deseni özet diyor | Karar 6 (hibrit) — M10, EK A deseniyle yorumlandı; tam kartlar kritik/risk:high katmanda zorunlu |
| 4 | Boundary-only, hedefsiz (F2 §11-12 literal) | Safest mimari | Kullanıcının açık 5000+ hedefi (karar 3) bağlayıcı | Kullanıcı kararı 3 — hedef korundu; YALNIZ dolum gerekçeli |
| 5 | F2 §43 doğal boyut (min-500 yok) | Daha hızlı üretim | F1 §10.3 bağlayıcı + kullanıcı karar 10 | Karar 10: F1 min-500 her yerde |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Tek K-space SSOT (EK A) + tek kural SSOT (yeni rules.md) + tek kapı rejimi (On Kapı) — üçlü hizalı.
- 5000+ kapasite, boundary disipliniyle: büyüme esnek, uydurma katman yok.
- Hibrit kayıt: özet satır navigasyonu (hızlı) + 20 alan kart derinliği (güvenli) — F1 EK A'nın kendi modeli.
- Eski modelin 7F sorunları (mixed responsibility, eksik boundary'ler, SSOT ikilemi) yeni yapıda önceden çözülmüş tanımlanıyor.

### 4.2 Olumsuz Sonuçlar

- Geçiş maliyeti: eski 95 dosya + v1 ürünleri gitti; GÖREV 02 analizi `git show` ile yapılmalı (kayıp yok, ama üretim sıfırdan).
- min-500 (karar 10) üretim süresini uzatır; dolgu yasağıyla birleşince her dosya gerçek derinlik ister.
- Kritik/risk:high katmanlarda tam 20 alan kart zorunluluğu (M10) üretim derinliğini artırır.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| 5000+ hedefi baskı yapıp gerçek boundary'siz dolum üretir | 3 (olasi) | 4 (yüksek) — mimari sahtecilik | KANIT/gerekçe alanı zorunlu; §11 rubrik + Kapı 5 denetimi; hedef≠kanıt ayrı rapor (H10) |
| min-500 + dolgu yasağı gerilimi → şişirme denemesi | 3 | 3 (orta) — kalite | Zero-Hallucination üstte; ⚠️ damgası + research kapısı; KAPI 9 hallucination kontrolü |
| 402 kredi ajan üretimini keser | 4 (çok olası) | 3 | Kademeli multi-agent + inline fallback (karar 8, plan Risk-1) |
| Peer oturum tekrar siler | 2 (mümkün) | 4 | Her gate `git status` (plan Risk-2); commit R5 ayrı onay |
| Validator eski model varsayımları (idrange/orphan desenleri) yeni yapıyı işaretler | 2 | 2 | Genişletme validate.mjs içinden + ADR-096 kaynağı (R12) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | R0 Kapı 1-3: F1/F2/EK A arşivleri + keşif notu (TAMAM 2026-10-08) | Claude / Vault Steward | ✅ |
| 2 | R1: bu ADR + yeni `rules.md` + şablon envanteri | Claude → Vault Steward onayı | 1 gün |
| 3 | R2: iskelet ağaç (master-index · context · Foundation 21 dizin) | Claude (multi-agent) | 1 gün |
| 4 | R3: GÖREV 03-10 üretimi — bant bant, 5000+ kayıt, kapı 5-9 | Claude (multi-agent, 402 fallback) | çoklu faz |
| 5 | R4: §10 28-başlık FINAL rapor (≥5000) + §11/§9 puanları → Kapı 10 👤 | Claude → insan | faz sonu |
| 6 | R5: git commit (D=95 + yeni dosyalar) — ayrı insan onayı | İnsan | R4 sonrası |

### 5.2 Geri Dönüş Planı

- **ADR henüz Active; vault'ta commit YOK (H6).** Geri dönüş = `git checkout -- .ai/architecture/` (HEAD'deki 95 dosya zaten silinmiş working-tree'de — geri getirme tek komut) + yeni eklenen 4 dosyanın (`00-kspace-anayasa`, `prompts/2026-10-08-×2`, `SESSIONS/…`) silinmesi; staging arşivi + G: kaynakları hiç bozulmaz.
- Commit sonrası geri dönüş = **yeni ADR ("revert of ADR-096")** (şablon §6.3) — bu ADR asla sonradan düzenlenmez (Active iken yalnız `superseded by`).
- `log.md` append-only geçmiş iptal edilemez; geri dönüş de append ile kaydedilir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Anayasa (üst otorite — bu ADR'yi geçersiz kılamaz, üstünde) |
| `G:\…\coremusic-katmanli-mimari-master-prompt-v1.md` → `.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md` | F1 spec (EK A/B/C kaynağı) |
| `G:\…\coremusic-katmanlı-miamri-oluştur.ai.md` → `.ai/prompts/2026-10-08-enterprise-arch-f2.md` | F2 spec (§10-14, §41-44) |
| `.ai/architecture/00-kspace-anayasa.md` | EK A kopyası — K-space SSOT |
| `.ai/architecture/rules.md` | Bu ADR'den türetilen kural SSOT (R1) |
| `.ai/SESSIONS/2026-10-08-kspace-kesif.md` | Kapı 1-3 keşif + çelişki defteri (OPEN-1..3) |
| [[brain.md]] | ADR özetine eklenecek (senkron: index + brain + log — şablon §4.4) |
| `.ai/.decisions/index.md` | Kayıt satırı (§4 listesi + FM totals) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-08 | ✅ (plan v2.0 onayı = 11 karar bu ADR'nin özüdür) |
| Tech Lead | Bayram Ali (tek onay mercii — F1 §15/I3) | 2026-10-08 | ✅ (aynı onay) |
| Arch Lead | Bayram Ali (tek onay mercii — F1 §15/I3) | 2026-10-08 | ✅ (aynı onay) |

**Not:** Üç kademe aynı insandır (proje gerçekliği: I3 "tek onay mercii"); üç ✅ tek bir ExitPlanMode onayından türedi — sahte imza DEĞİL, aynı olayın üç kaydı.

---

*1.0.0 | 2026-10-08 | Created*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Human Mode · Truth Mode*

**REFACTOR REPORT:** FILE: ADR-096-kspace-5000-boundary-model.md · PURPOSE: K-Space V2 rejimi kurucu kararı (F1 H08) · VALIDATION: şablon §1-§7 iskeleti korundu · placeholder kalmadı (grep köşeli-parantez = 0) · §1.3 dolu (EK B referansı) · §5.2 geri dönüş planı dolu · RELATED: [[.templates/adr/adr-template]]