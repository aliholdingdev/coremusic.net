---
title: "CoreMusic — Workflow Pointer + Mimari İş Akışı Özeti"
type: workflow-pointer
category: workflow
version: 2.0.0
status: active
authority: "Pointer (ADR-042) — Bağlayıcı SSOT: .ai/WORKFLOW.md"
updated: 2026-09-24
---

# ⚠️ CoreMusic Workflow Configuration (Vault SSOT Pointer)

> [!WARNING]
> **AI AGENT DİKKATİNE / ATTENTION AI AGENTS:**
>
> Bu dosya, ADR-042 (Vault Restructuring) kararı uyarınca yalnızca bir **YÖNLENDİRİCİ (POINTER)** olarak bırakılmıştır.
> CoreMusic projesine ait tüm iş akışları, agent otomasyon senaryoları, faz onay mekanizmaları ve vizyon dokümanları `.ai/` klasörü içerisindeki ana kasada (Vault) toplanmıştır.
>
> **BU DOSYAYI TALİMAT OKUMAK İÇİN KULLANMAYIN. LÜTFEN AŞAĞIDAKİ BAĞLANTILARA TIKLAYARAK GERÇEK İŞ AKIŞI VE VİZYON DOSYALARINA GEÇİŞ YAPIN.**

## 🎯 Proje ve Vizyon Özeti
* **Proje:** CoreMusic — Ticari Dijital Medya Ekosistemi ve Gelir Platformu (Yazılım, Ses DSP, Donanım, AI).
* **Felsefe:** *"Aynı Müzik Her Yerde Seninle"* — Mülkiyet ve Özgürlük (Offline-First, bağımsız kayıpsız arşiv), Kesintisiz Handoff ve Hi-Fi C++20 Neva Engine.

## 🔗 Single Source of Truth (SSOT) Bağlantıları

İş akışlarını okumak, script'leri çalıştırmak, vizyonu anlamak ve mevcut faz (Phase) durumunu yönetmek için derhal aşağıdaki dizinlere gidin:

1. **[Ana İş Akışları (WORKFLOW.md)](.ai/WORKFLOW.md)** 👈 *(Öncelikli iş akışı kuralları)*
2. **[Vizyon ve Felsefe (VISION.md)](.ai/VISION.md)** 👈 *(Pazar krizi, mülkiyet felsefesi ve çözümler)*
3. **[Proje Tanımı (PROJECTS.md)](.ai/PROJECTS.md)** 👈 *(10 temel yetenek, hedef kitleler, sektörel çözümler)*
4. **[Faz Yürütme ve Matris (engine.md)](.ai/engine.md)** *(Orkestrasyon ve yürütme matrisi)*
5. **[AI Anayasası (CLAUDE.md)](.ai/CLAUDE.md)** *(16 Hard Guardrail ve mühendislik anayasası)*
6. **[Master Mimari İndeks (master-architecture-index.md)](.ai/architecture/master-architecture-index.md)** *(21 Katman, 1130 Bileşen, 18 BCNF DB)*

> [!NOTE]
> **Genişletme notu (2026-09-24):** Aşağıdaki §1-§14 bölümleri bu dosyanın **bağlayıcı özeti (orientation summary)**'dir; tek otorite `.ai/` vault'unun ilgili dosyalarıdır. Çelişki durumunda SSOT kazanır: adlandırma → [[.ai/architecture/adlandirma-kurali]] · bağımlılık → [[.ai/architecture/katman-baglilik-matrisi]] · sayım → [[.ai/architecture/katman-sayim-rehberi]] · süreç → [[.ai/WORKFLOW.md]] · anayasa → [[.ai/CLAUDE.md]].

---

## §1 Amaç ve Kapsam

Bu bölüm, CoreMusic vault'unda dosya üreten/yazan her agent ve geliştirici için iş akışı pusulasıdır: katman şeması, adlandırma dili, sayım birimi, tartışma protokolü, ADR yazımı ve senkron adımları tek yerde özetlenir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| K0-K20 katman şeması ve A0-A5 alan etiketleri (özet) | Bağımlılık izinlerinin kanonik listesi (→ matris) |
| K{n}.a.b(.c) adlandırma dili özeti | Adlandırma SSOT'unun kendisi (→ adlandirma-kurali) |
| Sayım birimi (düğüm) ve hedef aralığı özeti | Sayım betiği ve katman tablosu (→ sayim-rehberi) |
| 3 turlu / 20 persona agent tartışma protokolü | Persona profilleri (→ .ai/.agents/) |
| İki ADR serisi ve yazım akışı özeti | ADR metinleri (→ .ai/.decisions/, .ai/architecture/adr/) |
| Katman md yazım/düzeltme ve senkron adımları | Faz planları ve orkestrasyon (→ .ai/engine.md) |

**Okuma sırası (bu dosyayı elinde tutan agent için):** bu dosya → [[.ai/CLAUDE.md]] → [[.ai/AGENTS.md]] → [[.ai/WORKFLOW.md]] → ilgili domain dosyası. Token bütçesi aşılırsa §12'deki 10 kural + §9'daki senkron adımları minimum set olarak uygulanır.

---

## §2 Mimari Şema — 21 Katman (K0-K20)

### §2.1 Ana Katman Tablosu (21 sabit)

| Kök | Katman Adı | Disk Klasörü | A Grubu | Örnek Alt Düğüm |
|-----|-----------|--------------|---------|-----------------|
| K0 | İşletim Sistemi | `k0-isletim-sistemi/` | A0 | K0.7.1 |
| K1 | Donanım | `k1-donanim/` | A0 | K1.f (firmware) |
| K2 | Sürücü | `k2-surucu/` | A0 | K2.1.1 |
| K3 | Ses İşleme Motoru | `k3-ses-motoru/` | A0 | K3.2.1 |
| K4 | Yapay Zeka | `k4-yapay-zeka/` | A0 | K4.1.1 |
| K5 | Veri Yönetimi | `k5-veri-yonetimi/` | A0 | K5.1.1 |
| K6 | Güvenlik | `k6-guvenlik/` | A1 | K6.1.1 |
| K7 | Middleware | `k7-middleware/` | A1 | K7.1.3 |
| K8 | Servis | `k8-servis/` | A2 | K8.2 (Media Servis) |
| K9 | API & Routing | `k9-api-routing/` | A2 | K9.1.1 |
| K10 | Uygulama | `k10-uygulama/` | A3 | K10.1.1 |
| K11 | Kullanıcı Deneyimi | `k11-ux/` | A3 | K11.1.4.13 |
| K12 | İzleme & Log | `k12-izleme/` | A4 | K12.1.1 |
| K13 | CI/CD & Deploy | `k13-cicd/` | A4 | K13.1.1 |
| K14 | Ağ & İletişim | `k14-ag/` | A4 | K14.1.1 |
| K15 | Medya & Streaming | `k15-medya-streaming/` | A4 | K15.1.1 |
| K16 | Class AB Amplifikatör | `k16-class-ab/` | A5 | K16.1.1 |
| K17 | Güç Kaynağı ±35V | `k17-guc-kaynagi/` | A5 | K17.1.1 |
| K18 | Termal Tasarım | `k18-termal/` | A5 | K18.1.1 |
| K19 | PCB Tasarım | `k19-pcb/` | A5 | K19.1.1 |
| K20 | BOM & Üretim | `k20-bom/` | A5 | K20.1.1 |

**Sabit kurallar:** 21 ana katman değişmez (K21+ = yeni ADR ister); K16-K20 bağımsızdır, K0-K15 hiyerarşisine tabi değildir; tek arayüz bağımlılıkları K1'e ↕ yönlüdür.

### §2.2 A0-A5 Alan Etiketleri (yalnız raporlama/gruplama)

| Alan | K Katmanları | Kapsam |
|------|-------------|--------|
| A0 | K0-K5 | Altyapı/Donanım (çekirdek, firmware K1.f, sürücü, DSP, AI, veri) |
| A1 | K6-K7 | Güvenlik ve middleware |
| A2 | K8-K9 | Servis ve yönlendirme |
| A3 | K10-K11 | Sunum (uygulama + deneyim) |
| A4 | K12-K15 | Operasyon (izleme, CI/CD, ağ, medya) |
| A5 | K16-K20 | Fiziksel üretim (amfi, güç, termal, PCB, BOM) |

**Denetim kuralı:** Katman ihlali denetimi **K matrisine göre** yapılır; A grubu denetimde kullanılamaz. "A3 → A0 ihlali" iddiası önce gerçek K okuna (örn. K11 → K0) indirgenir, matriste o ok yoksa ihlal vardır. A grubu yalnız raporlama adıdır ([[.ai/architecture/adlandirma-kurali]] §8).

### §2.3 Disk Klasör Yapısı (`.ai/architecture/`)

| Öğe | Adet (2026-09-24 disk) | Not |
|--------------------------|------------------------|-----|
| Katman klasörleri (k0…k20) | 21 | Her biri k{n}-<slug>/ |
| `firmware/` | 1 | K1.f alt katmanı — kalıcı (ADR-024) |
| `adr/` | 1 | Mimari ADR serisi (ADR-023…026) |
| **Toplam (tanım: 23 klasör)** | **23** | 21 katman + firmware + adr |
| `scripts/` | 1 | 24. dizin — sayım/kapsam dışıdır |
| Kök md dosyaları | 6 | adlandirma-kurali · katman-baglilik-matrisi · katman-sayim-rehberi · frontend-restructuring-plan · github-referanslari · index |
| **Toplam md (recursive)** | **340** | 322 katman + 8 firmware + 4 adr + 6 kök (disk ölçümü 2026-09-24) |

⚠️ **VERIFICATION REQUIRED:** [[.ai/architecture/katman-baglilik-matrisi]] §10 sayımı 334 MD (318+8+2+6) der; güncel disk ölçümü 340 MD'dir (katman +4, adr +2). Fark gizlenmez, iki değer birlikte raporlanır; matris güncellemesi ayrı bir vault işlemidir.

### §2.4 Katman → Sorumlu Alan/Sorumlu Agent Eşlemesi

| K Grubu | Alan | Birincil agent (kısa) | İkincil kontrol |
|---------|------|------------------------|------------------|
| K0-K5 | A0 altyapı | Data / Embedded | DevOps |
| K6-K7 | A1 güvenlik | Security | Backend |
| K8-K9 | A2 hizmet | Backend | Security |
| K10-K11 | A3 sunum | UI Designer | QA |
| K12-K15 | A4 operasyon | DevOps / Data | QA |
| K16-K20 | A5 üretim | Audio HW / Embedded | DSP FW |

**Kullanım:** yazım batch'i (§8, [[.workflows/architecture-write]]) açılırken her dosya bu tabloya göre sorumlu agent'a bağlanır; agent sınırı ihlali domain boundary kuralı ile durdurulur. Routing anahtarı: [[.ai/AGENTS.md]] §6 — bu tablo yalnız özet, ikinci kaynak değildir.

---

## §3 Adlandırma Dili — K{n}.a.b(.c)

### §3.1 Seviye Tablosu

| Seviye | Biçim | Zorunluluk | Sayıma dahil mi? |
|--------|-------|------------|------------------|
| 1 | K{n} (n = 0…20) | 21 tane, sabit | Evet (kök 21) |
| 2 | K{n}.a (alt katman) | Evet | Evet |
| 3 | K{n}.a.b (alt bileşen grubu) | Evet | Evet |
| 4 | K{n}.a.b.c (kanıtlı bileşen) | **Yalnız kanıtla** | Evet (kanıtlıysa) |
| 5+ | K{n}.a.b.c.d | **YASAK** (4 seviye tavan) | Hayır — ihlal |

### §3.2 Biçimsel Doğrulama (regex)

```text
^K([0-9]|1[0-9]|20)(\.[1-9][0-9]*){0,3}$
```

| # | Örnek | Sonuç | Gerekçe |
|---|-------|-------|---------|
| 1 | K11 | ✅ | Kök katman |
| 2 | K11.4 | ✅ | 2. seviye |
| 3 | K11.1.4 | ✅ | 3. seviye |
| 4 | K11.1.4.13 | ✅ | 4. seviye + kanıt (plan §2.2, c-player.css) |
| 5 | K7.0.1 | ❌ | Ara segment 0 — red (adlandirma §4) |
| 6 | K11.1.4.13.2 | ❌ | 5. seviye yasak |
| 7 | k11.1.4 | ❌ | Önek büyük harf K |
| 8 | K21.1 | ❌ | 21 katman sabit |
| 9 | K11.01.4 | ❌ | Sıfır dolgusu yasak |
| 10 | K1.f | ✅ | Harfli segment — kanıtlı tek istisna (firmware/) |
| 11 | L11.1.4.13 | ⚠️ | Dönüşüm gerekli → K11.1.4.13 (yalnız plan bağlamında) |

### §3.3 4. Seviye Kanıt Üçlüsü (zorunlu)

Bir K{n}.a.b.c düğümü yalnız aşağıdaki kanıtlardan **en az biri** varsa kurulur:

1. **(i)** Diskte katman md dosyası — `.ai/architecture/k{n}-*/*.md` (glob ile var mı?)
2. **(ii)** README/index bileşen tablosu satırı — K{n}-NN kimliği ↔ düğüm eşlemesi
3. **(iii)** Plan satırı — [[.ai/architecture/frontend-restructuring-plan]] §2.1-§2.2 (yalnız bu durumda düğüm **PLANNED** etiketlenir)

Kanıt yoksa düğüm yazılmaz; yazılmışsa 3. seviyeye düşürülür ve log'a CRITICAL girilir. Kanıt tarihi sayım raporunda listelenir (şeffaflık → ADR-026).

### §3.4 İstisnalar ve Yasaklar

| Kural | Değer | Kaynak |
|-------|-------|--------|
| K7.0.x ara numara | KESİNLİKLE REDDEDİLDİ — numaralandırma 1'den başlar | adlandirma §4 |
| firmware/ klasörü | KALIR → K1.f; taşınmaz/silinmez | ADR-024 |
| k-surucu/* → k2-surucu/ | 12 dosya taşınır, 0 dosya silinir; link taraması önce+sonra | ADR-024 |
| L → K dönüşümü | Yalnız frontend-restructuring-plan bağlamında; CLAUDE §32'nin 4 katmanlı L0-L3 şemasına OTOMATİK DÖNÜŞÜM YASAK | adlandirma §7.3 |
| 5. seviye / kanıtsız 4. seviye | revert + log CRITICAL | adlandirma §9.5 |

---

## §4 Sayım Birimi ve Hedefler

### §4.1 Temel Kurallar (ADR-026)

| # | Kural |
|---|-------|
| 1 | Sayım birimi **DÜĞÜM**'dür (dosya değil, bileşen değil) |
| 2 | Kabul kriteri: **toplam düğüm ≥ 5000** |
| 3 | Kök (21) + 2. seviye (193) + 3./4. seviye (kanıtla) birleşim (tekillik) sayılır — aynı düğüm 2 kanıtta 1 kez sayılır |
| 4 | Kanıtsız düğüm sayılmaz, uydurulmaz — ⚠️ VERIFICATION REQUIRED |
| 5 | Rakamlar tasarım hedefidir, ölçüm değildir; gerçek sayımla birlikte raporlanır |

### §4.2 Hedef Aralığı ve Bağlayıcı Sayımlar

| Değer | Kaynak | Durum |
|-------|--------|-------|
| 5.105 | Bağlayıcı tablo (4.891 + 21 + 193) — minimum ile sınar | bağlayıcı |
| 5.152 | Kanonik ayrıştırma (4.938 + 21 + 193) — alt senaryo | bağlayıcı |
| 5.400 | Orta senaryo (beklenen teslim) | tasarım |
| 6.200 | Üst tavan — aşımı yeni ADR ister | tasarım |

### §4.3 Bilinen Sapmalar (gizlenemez)

| Katman | Bağlayıcı | Formül | Sapma | Durum |
|--------|-----------|--------|-------|-------|
| K1 | 427 | 444 | **−17** | ⚠️ VERIFICATION REQUIRED — üst göreve sorulur, tablo değeri korunur |
| K13 | 95 | 125 | **−30** | ⚠️ VERIFICATION REQUIRED — üst göreve sorulur, tablo değeri korunur |

**Yasak:** sapmayı düzeltmek/örtmek, hedefi düşürmek veya kopya dosya ile sayımı şişirmek (enflasyon) yasaktır; CI yeşil olsa bile "boş repo" reddi dosya sayısına da uygulanır.

---

## §5 Katman Bağımlılığı ve İhlal Denetimi

### §5.1 Üç Ok Türü (matris kanonik)

| Ok türü | Anlamı | Nereden okunur | Örnek |
|---------|--------|----------------|-------|
| **Bağımlılık** (kanonik) | Kayan katman, hedefin ARAYÜZÜNE bağımlıdır | [[.ai/architecture/katman-baglilik-matrisi]] §2.1-§2.2 | K15 → K14 (tek hedef) |
| **Çağrı** (üst→alt) | Üst, alt katmanı arayüzünden çağırır; matrise bağımlılık eklemez | ADR-025 / katman README'leri | K8.2 → K15 (ADR-025) |
| **Gösterim** (üst→alt) | Dokümantasyon akış çizgisi | Katman README'leri | K11 → K12 |

### §5.2 Denetim Checklist'i

| # | Kontrol | Beklenen |
|---|---------|----------|
| 1 | Düğüm adları regex'e uyar mı? | 0 ihlal |
| 2 | 5. seviye var mı? | 0 |
| 3 | Kanıtsız 4. seviye var mı? | 0 |
| 4 | 0 ara segment (K7.0.x dahil) var mı? | 0 |
| 5 | k-surucu/ dosya sayısı | 0 (12'si taşındı) |
| 6 | k2-surucu/ dosya sayısı | 14 (2 kabuk + 12) |
| 7 | firmware/ dosya sayısı | 8 (K1.f) |
| 8 | L-önekli düğüm referansı (plan bağlamı hariç) | 0 |
| 9 | K15 → K16-20 oku | 0 (izin yok) |
| 10 | K12 → K8 dışı bağımlılık | 0 |
| 11 | Kırık wiki-link | 0 |
| 12 | Sayım betiği sonucu | ≥5000 |

### §5.3 İhlal Sonuçları

| Şiddet | Durum | Aksiyon |
|--------|-------|---------|
| CRITICAL | 5. seviye, kanıtsız 4. seviye, K15→K16-20, dosya silme | Derhal revert + log.md CRITICAL |
| HIGH | K7.0.x ara numara, L-önek kalıntısı, kırık link | Düzelt + log |
| MEDIUM | README eşleme satırı eksik (K{n}-NN ↔ K{n}.a.b) | Tamamla + log |
| LOW | A0-A5 raporlama tutarsızlığı | Düzelt, revert gerekmez |

---

## §6 Agent Tartışması — 3 Tur / 20 Persona

### §6.1 Ne Zaman Zorunlu?

Yeni mimari kural, katman sınırı, sayım kuralı, klasör birleşimi veya çelişen ADR kararı üretilecektir. Tek agent kararı bağlayıcı olamaz; tartışma sonucu **ADR olarak** yazılır (§7).

### §6.2 Tur Tablosu

| Tur | Ad | Çıktı | Zorunlu Kurallar |
|-----|----|-------|------------------|
| **Tur 1** | Öneri | 20 persona kendi önerisini/itirazını yazar | Her öneri gerekçe + kaynak (dosya/satır) taşır; kaynaksız öneri sayılmaz |
| **Tur 2** | Çapraz eleştiri | Her persona en az bir başka öneriyi eleştirir | Eleştiri dayanaktadır: yanlışlık, risk, çelişki, maliyet; kişisel değil içeriksel |
| **Tur 3** | Uzlaşma + ADR | Ortak karar metni + reddedilen alternatifler | Uzlaşma yoksa 🔴 VERIFICATION REQUIRED + üst karar; birleştirme/çıkarma reddleri gerekçesiyle kayıtta |

### §6.3 Kalite Kuralları

1. **Uydurma yok:** hiçbir turda doğrulanamayan sayı/karar üretilemez; yoksa `⚠️ VERIFICATION REQUIRED`.
2. **Bağlayıcılık:** Tur 3 çıktısı bağlayıcıdır; Tur 1/2 taslak niteliğindedir ve ADR'nin "Alternatifler" bölümüne ham madde olur.
3. **Red gerekçesi zorunlu:** "REDDEDİLDİ — gerekçe" satırı olmadan alternatif kaydı geçersizdir.
4. **Kayıt:** tüm turlar ADR ekine özetlenir; ham transcript zorunlu değildir, **karar + gerekçe + alternatifler** zorunludur.
5. **İsim:** "3 turlu agent tartışması, 20 persona" ifadesi ADR'nin `kaynak` alanına yazılır.

### §6.4 Çıktı → ADR Bağlantısı

Tartışma sonucu yeni bir ilke kuruyorsa → `.ai/architecture/adr/` (mimari seri) veya `.ai/.decisions/accepted/` (karar serisi) içinde ADR-NNN olarak açılır; mevcut bir kuralı değiştiriyorsa → ilgili vault dosyası satır editi + ADR bağlantısı. Şablon: [[.ai/.templates/agents/agent-tartisma-turu-template]] (tutum kaydı) + [[.ai/.templates/adr/adr-nygard-template]] (karar metni).

---

## §7 ADR Yazım Akışı — İki Seri (Birleştirme REDDEDİLDİ)

### §7.1 İki Aseri (kasıtlı ayrı)

| | Karar Serisi | Mimari Serisi |
|---|---|---|
| Konum | `.ai/.decisions/` (accepted/draft/rejected + index.md) | `.ai/architecture/adr/` |
| Kapsam | Proje/engineering kararları (csrf, orm, router…) | Mimari/katman kararları (derinlik, birleşme, sınır, sayım) |
| Mevcut aralık | Frozen ADR-001…037 · Active ADR-038…088 · Draft ADR-089 | ADR-023…026 (2026-09-24, 4 dosya) |
| Numara uzayı | `.ai/.decisions/index.md` tek otoritesi | Kendi uzayında ardışık |
| Birleştirme | **REDDEDİLDİ** — ADR-026 §3.4 (alternatif D); üst karar (superstack/product-owner) gerekir | Aynı |

**Çakışma notu:** ADR-024/025/026 numaraları iki seride farklı slug'larla geçer (ör. `.decisions` ADR-024-ecosystem-modular-docs ≠ `architecture/adr` ADR-024-surucu-firmware-birlesme). Okurken **slug + konum** birlikte okunur; sadece numara tek başına kimlik değildir.

### §7.2 Numaralama Kuralları

| # | Kural |
|---|-------|
| 1 | Frozen ADR-001…037 **IMMUTABLE** — okunur, referans edilir, DEĞİŞTİRİLMEZ |
| 2 | Yeni karar ADR'leri **ADR-088+** uzayında, `.ai/.decisions/index.md`'den boş numara seçilerek açılır (son kayıt: Active 088, Draft 089) |
| 3 | Yeni mimari ADR'leri `.ai/architecture/adr/` kendi uzayında ardışık ilerler (son: ADR-026) |
| 4 | Numara asla yeniden kullanılmaz; slug özeldir ve değişmez |
| 5 | Dosya adı: `ADR-NNN-kisa-slug.md` (mimari seri, büyük harf) · `adr-NNN-slug.md` (karar serisi) |

### §7.3 Yazım Adımları (özet — ayrıntı: [[.workflows/adr-creation]]) 

1. **Kararı belirle** (tip, mevcut ADR çakışması, seri seçimi) — 15 dk
2. **Araştırma** (ilgili ADR'ler + mimari doküman + kanıt topla; kansız kanıtsız karar alınmaz) — 30 dk
3. **Taslak** (şablon: Guardrail #16 — [[.ai/.templates/adr/adr-template]] veya [[.ai/.templates/adr/adr-nygard-template]]; 7 alanlı frontmatter zorunlu) — 1 saat
4. **İnceleme** (cross-reference + hallüsinasyon kontrolü + §5.2 checklist uyumu)
5. **Onay** (onay matrisi: mimari → Principal Architect, güvenlik → Security Engineer, veri → Data Engineer, API → Backend, dağıtım → DevOps, donanım → Embedded; kritik → kullanıcı)
6. **Uygula ve dokümante et** (dosya yaz → `index.md`, `keys.md`, `brain.md` güncelle → `log.md` append → §9 senkron)

**Yaşam döngüsü:** proposed → review → accepted → frozen; deprecated için zorunlu: yerine geçen ADR + migration planı + onay.

### §7.4 Mimari Seri ADR-023…026 Karar Özeti

| ADR | Başlık | Karar (tek cümle) |
|-----|--------|-------------------|
| [[.ai/architecture/adr/ADR-023-hibrit-derinlik]] | Hibrit Derinlik Stratejisi | 21 katman sabit; derinlik K{n}.a.b zorunlu + K{n}.a.b.c yalnız 3 kanıt türüyle; hedef 5.152/5.400/6.200 |
| [[.ai/architecture/adr/ADR-024-surucu-firmware-birlesme]] | Sürücü-Firmware Birleşmesi | k-surucu/* 12 dosya k2-surucu/'ya taşınır (0 silme); firmware/ kalır → K1.f |
| [[.ai/architecture/adr/ADR-025-k8-2-k15-siniri]] | K8.2 / K15 Sınırı | K15 FFmpeg boru hattı sahibi; K8.2 yalnız servis ucu (çağrı oku) |
| [[.ai/architecture/adr/ADR-026-sayim-birimi-5000]] | Sayım Birimi | Birim = düğüm; hedef ≥5000; kanıt şeffaflığı; iki seri ayrılığı korunur |

---

## §8 Katman Dosyası Yazım / Düzeltme İş Akışı

### §8.1 Değişmez Kurallar

| # | Kural |
|---|-------|
| 1 | **In-Place Refactoring:** dosya adı/değiştirme YASAK (kural #1); taşıma ancak ADR ile (ör. ADR-024) |
| 2 | **0 dosya silme** — silme onaya tabidir; onay yoksa içerik satır editiyle daraltılır |
| 3 | Yeni veya tümüyle yeniden yazılan her md **≥500 satır** (şablonlar hariç: 100-250) |
| 4 | Hedefli edit'lerde mevcut doğru içerik korunur; yalnız ilgili satırlar değişir |
| 5 | Wiki-link biçimi: `[[relative/path/to/file]]` |
| 6 | Frontmatter: 7 zorunlu alan (title, type, category, version, status, authority, updated) |
| 7 | Yeni dosya Guardrail #16 gereği listeden şablonla üretilir (`.ai/.templates/index.md`) |

### §8.2 Adımlar

```text
1. ÖN OKUMA     → hedef dosya + ilgili K matrisi/adlandirma/sayım satırları
2. ŞABLON SEÇ   → .ai/.templates/index.md §5 eşleşmesi (yoksa DUR — Guardrail #16)
3. PLAN KİLİT   → dosya listesi + kabul kriterleri (§8.1 + architecture-write.md §6)
4. YAZIM        → UTF-8 protokol (§8.3); PowerShell yazım cmdlet'leri YASAK
5. ÇAPRAZ REF   → wiki-link taraması (kırık link 0)
6. DENETİM      → §5.2 checklist + regex taraması
7. KAYIT        → log.md append-only; yeni wf/ADR varsa index kaydı
8. SENKRON      → §9 post-operation adımları
```

### §8.3 UTF-8 Yazım Protokolü (zorunlu)

| İzinli | Yasak |
|--------|-------|
| `node .ai/scripts/vault-utf8-writer.mjs` (append · insert-before-marker · write · copy · verify · repair · scan) | PowerShell yazım cmdlet'leri: `Set-Content`, `Out-File`, `Add-Content`, `echo >`, `New-Item -Value` (Windows-1254/BOM/UTF-16 bozulması) |
| `node .ai/scripts/vault-cmd.mjs` (Türkçe komut arayüzü; ekle/yaz/onar/tara utf8-writer'a devreder) | Yazmadan önce/read-only kalmayan herhangi bir araç |
| Salt-okunur komutlar: `ls`, `dir`, `Get-ChildItem`, `Get-Content`, `Select-String`, `Test-Path`, git okuma | log.md'de append dışı yazım (bayt düzeyinde append dışında dosyaya dokunmak) |

**Zorunlu:** yazım sonrası otomatik `verify`; bozuk dosya için `repair` (yedek alır); `.ai/log.md` için **yalnız append modu**.

---

## §9 İşlem Sonrası Vault Senkronu (ZORUNLU)

Her vault işleminden **hemen sonra** üç adım çalıştırılır:

| # | Adım | Komut |
|---|------|-------|
| 1 | Session kaydı | `node .ai/scripts/session-save.mjs --task "<gorev-aciklamasi>" --status completed --agent <agent-adi>` |
| 2 | Vault güncelleme | `node .ai/scripts/vault-post-update.mjs --scope root` |
| 3 | Sonuç doğrulama | `log.md`, `MEMORY.md`, `project-state.md` güncellendi mi? (salt-okunur kontrol) |

**Yoksayma sonucu:** audit trail boşluğu → bir sonraki oturum MEMORY devralması tutarsız olur. Senkron başarısızsa işlem `status: pending` sayılır ve tekrar denenir (max 3).

### §9.1 Senkron Hata Tablosu

| Durum | Belirti | Aksiyon | Max retry |
|-------|---------|---------|-----------|
| session-save başarısız | script çıkış kodu ≠ 0 | Komutu yeniden çalıştır; `--task` metnini kısalt | 3 |
| vault-post-update başarısız | root .md güncellenmedi | `vault-utf8-writer scan` → `repair` (yedekli) | 3 |
| log.md append reddi | dosya yazma kilidi / kilitli | Context lock bırakılana bekle; tekrar append | 3 |
| MEMORY.md güncellenmedi | session kaydı var, durum yok | `session-save` tek başına tekrar çalıştır | 2 |
| Çapraz referans koptu | wiki-link hedefi yok | Düzelt veya `⚠️ VERIFICATION REQUIRED` + log HIGH | 1 |
| Bozuk encoding tespiti | mojibake / BOM | `vault-utf8-writer repair <dosya>` (yedek otomatik) | 1 |

> 💡 **Not:** Üç adım da `node .ai/scripts/` altındaki betiklerle çalıştırılır; manuel (PowerShell) kopyalama ile senkron yapılmaz — UTF-8 protokolü (§8.3) ihlali sayılır.

---

## §10 Hallüsinasyon ve Doğrulama

### §10.1 VERIFICATION REQUIRED Kuralları

1. Doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` etiketiyle işaretlenir (kural #6).
2. Etiketli iddia **işlem görmez**; sayım/raporlamada iki değer birlikte gösterilir (ör. 334 ↔ 340 MD).
3. Uydurma sayı, tarih, ADR, dosya yolu veya karar üretmek yasaktır.
4. Kaynak bulunamıyorsa DUR + kullanıcıya sor (çelişki → §10 kuralı: "Çelişki → DUR + kullanıcıya sor").

### §10.2 REDACTED Politikası

Sırlar (`.env` değerleri, token, key, parola) **asla** vault'a yazılmaz (kural #9); tespit edilirse `[REDACTED]` ile maskele + `log.md`'ye girişte sadece olay adı.

### §10.3 Salt-Okunur Doğrulama Komutları

```powershell
# Düğüm regex ihlali (5. seviye / sıfırlı ara segment) — 0 beklenir
Get-ChildItem .ai\architecture -Recurse -Filter *.md |
  Select-String -Pattern 'K\d+(\.\d+){4,}|K7\.0'

# Klasör/dosya gerçekliği (23 klasör + scripts · 340 md beklenir)
(Get-ChildItem .ai\architecture -Directory).Count
(Get-ChildItem .ai\architecture -Recurse -Filter *.md -File).Count

# Kırık wiki-link adayları
Get-ChildItem .ai -Recurse -Filter *.md | Select-String -Pattern '\[\[k-surucu/'

# Domain L-önek kalıntısı (plan/CLAUDE §32 alıntıları hariç) — 0 beklenir
Get-ChildItem .workflows -Filter *.md | Select-String -Pattern 'L[0-9]+[-. ]katman|L0-L[0-9]'
```

---

## §11 Workflow ve Şablon Envanteri

### §11.1 `.workflows/` Akışları (9 md)

| # | Dosya | Tetikleyici | Çıktı |
|---|-------|-------------|-------|
| 1 | [[.workflows/session-init]] | Her oturum başı | Vault boot (10+ dosya okuma) |
| 2 | [[.workflows/adr-creation]] | Mimari karar ihtiyacı | `.ai/.decisions/accepted/` veya `.ai/architecture/adr/` ADR |
| 3 | [[.workflows/vault-sync]] | Vault değişikliği sonrası | Senkron + index güncelleme |
| 4 | [[.workflows/security-audit]] | Güvenlik değişikliği | Denetim raporu |
| 5 | [[.workflows/deployment]] | Sürüm çıkışı | Dağıtım onay akışı |
| 6 | [[.workflows/hallucination-control]] | Belirsiz bilgi tespiti | Verification required protokolü |
| 7 | [[.workflows/orchestrator-flow]] | Multi-agent görev | Görev dağıtım grafiği |
| 8 | [[.workflows/architecture-write]] | Katman md yazım/düzeltme | 31 dosyalık yazım batch'i (0 silme) + kabul kriteri raporu |

*(9. satır: `CLAUDE.md` — klasör bağlam dosyası, akış değil.)*

### §11.2 Şablonlar (Guardrail #16)

| Kategori | Kullanım anı | Yeni şablonlar (2026-09-24) |
|----------|--------------|------------------------------|
| `adr/` | ADR yazımı | `adr-nygard-template` (Status/Context/Decision/Consequences) |
| `documentation/` | Katman md üretimi | `katman-readme-template`, `alt-katman-template` |
| `agents/` | Tartışma kaydı | `agent-tartisma-turu-template` (3 tur / 20 persona) |

Registry tek otoritesi: [[.ai/.templates/index]] — şablonsuz dosya üretimi yasak.

---

## §12 Kurallar Özeti (Minimum Set)

1. **In-Place Refactoring** — dosya adı onaysız DEĞİŞMEZ.
2. **SSOT** — `.ai/` vault tek doğruluk kaynağı; bu dosya özettir, çelişkide vault kazanır.
3. **Frozen ADR-001…037 IMMUTABLE.**
4. **Yeni karar ADR'leri ADR-088+** (`.decisions/index.md`); mimari seri kendi uzayında (son: ADR-026).
5. **Her değişiklikte çapraz referans doğrulama** (kırık link 0).
6. **Hallüsinasyon süpürmesi** — doğrulanamayan → `⚠️ VERIFICATION REQUIRED`.
7. **`.ai/log.md`'ye SADECE append** (bayt düzeyi).
8. **Şablon zorunlu** — `.ai/.templates/index.md` (Guardrail #16).
9. **Frontmatter 7 zorunlu alan.**
10. **Wiki-link:** `[[relative/path/to/file]]` · **REDACTED:** sır asla yazılmaz.
11. **İşlem sonrası senkron** (§9): session-save → vault-post-update → doğrulula.

---

## §13 İlgili Dosyalar

| Dosya | İlişki |
|-------|--------|
| [[.ai/WORKFLOW.md]] | Bağlayıcı süreç anayasası |
| [[.ai/CLAUDE.md]] | 16 Hard Guardrail, §5 katman tablosu |
| [[.ai/AGENTS.md]] | Agent routing, A0-A5 bağlantısı (§5) |
| [[.ai/architecture/adlandirma-kurali]] | Adlandırma SSOT (K{n}.a.b.c, L→K) |
| [[.ai/architecture/katman-baglilik-matrisi]] | Bağımlılık okları SSOT |
| [[.ai/architecture/katman-sayim-rehberi]] | Sayım tablosu, sapmalar |
| [[.ai/architecture/adr]] | Mimari ADR serisi (ADR-023…026) |
| [[.ai/.decisions/index]] | Karar serisi indeksi (001-089) |
| [[.workflows/architecture-write]] | Katman yazım batch akışı |
| [[.ai/.templates/index]] | Şablon registry |

## §14 Değişiklik Geçmişi

| Tarih | Sürüm | Değişiklik | Kaynak |
|-------|-------|------------|--------|
| 2026-08-09 | 1.0.0 | Pointer dosyası (ADR-042) — SSOT bağlantıları | ADR-042 |
| 2026-09-24 | 2.0.0 | §1-§14 bağlayıcı özet eklendi (K0-K20, A0-A5, adlandırma, sayım, tartışma, iki ADR serisi, yazım+senkron); pointer uyarısı + SSOT linkleri korundu | Vault iş akışı genişletme görevi |

---

*Bu dosya sadece yönlendirme amaçlıdır. İş akışı kuralları doğrudan `.ai/WORKFLOW.md` üzerinden düzenlenmelidir.*

**REFACTOR REPORT:** FILE: WORKFLOW.md · PURPOSE: Pointer + bağlayıcı iş akışı özeti (K0-K20 · A0-A5 · K{n}.a.b.c · 3 tur/20 persona · iki ADR serisi · UTF-8 + senkron) · VALIDATION: §1-§14 yeni eklenti; orijinal uyarı + 6 SSOT linki + footer korundu; 0 dosya silme · RELATED: [[.ai/WORKFLOW.md]] · [[.ai/architecture/adlandirma-kurali]] · [[.workflows/architecture-write]]

*CoreMusic Workflow Pointer v2.0.0 — Authority: Bayram Ali / Vault Steward — Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
