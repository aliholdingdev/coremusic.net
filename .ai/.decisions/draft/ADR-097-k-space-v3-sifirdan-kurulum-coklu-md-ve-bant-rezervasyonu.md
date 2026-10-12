---
reference_doc: .ai/.templates/adr/adr-nygard-template.md (v1.0.1 — Guardrail #16)
title: "CoreMusic — K-Space V3: Sıfırdan Kurulum, Çoklu-MD Düğüm Şeması, İsimlendirme Spesifikasyonu ve Band-2..7 Rezervasyonu"
type: adr
category: decisions
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: "v1 (coremusic-katmanlı-miamri-oluştur.ai.md, 697 satır) + v2 (coremusic-katmanli-mimari-agaci-v2.md, 1062 satır)"
---

# CoreMusic — K-Space V3: Sıfırdan Kurulum, Çoklu-MD Şema, İsimlendirme ve Band Rezervasyonu

**Durum:** draft — kullanıcı onayı bekliyor (onay → `accepted/`, redd → `rejected/`)
**Tarih:** 2026-10-12
**Karar Veren:** Bayram Ali / Vault Steward
**İlgili ADR'ler:** ADR-096 (önceki K-Space V2 rejimi — silinmiş) · ADR-091 (template engine) · v2 §3 (adlandırma kuralları) · v2 §4.5–4.8 (ID/bant şeması) · ADR-026 (iki ADR serisi ayrımı)

---

## §1 Bağlam (Context)

1. **2026-10-12 disk durumu:** `.ai/architecture/` çalışma ağacında **0 öğe**; `.git` **boş dizin** ("not a repository") — v2 §6.1'de kurtarma yolu olarak kayıtlı `9620016` commit'ine erişim **yok**. Eski 21 K klasörlü disk (§6 "DİSKTE" sütunu) ve band-1 üretimi (21 dosya + `00-kspace-anayasa.md` + ADR-096 + b1-K000…b1-K020 staging) **hiçbir yerde mevcut değil**. Kurtarma imkânsız → **sıfırdan yeniden kurulum tek yol**.
2. **Kaynak spec iki dosya:** v1 (pipeline + domain listesi + GitHub referans tabloları) ve v2 (25 K ağaçı + 8 kademe + §4.2 8 zorunlu alan + §4.7 bant şeması). Kullanıcı talimatı: **ikisi tek sürekli spec olarak birlikte** işlem görür.
3. **Çok-MD zorunluluğu:** Kullanıcı — her düğümün tek `index.md` ile sınırlı kalmayacağını, master/bağımlılık/doğrulama gibi **birden fazla MD** taşıyacağını, yapının "derya gibi" derinleşeceğini açıkça bildirdi.
4. **İsimlendirme direktifi:** Kullanıcı — *isimler Türkçe yazılacak, teknik terimler İngilizce kalacak, web araştırmasıyla doğrulanacak, anlaşılır ve tutarlı olacak* (saçma isim yok).
5. **v2 §4.7 çelişkisi:** "K021 uydurma ihlal sayılır, numara tek başına atanamaz" ↔ kullanıcının "Band-2..7 tamamı açılsın" kararı. Bu çelişkinin çözümü bu ADR'ye aittir.
6. **Web doğrulama turu-1 tamamlandı** (2026-10-12, exa/websearch): JUCE 8811★ · Koel 17175★ MIT/Laravel 12 · Ampache 3793★ AGPL-3.0 · TPA3255 ClassD PBTL (24–52 V PVDD, KiCad) · Signalsmith-basics MIT C++ · DSPark 2★ (v1'deki "90+" iddiası ≠ star — `⚠️ VERIFY REQUIRED`) · Patina (C++17 analog modeling) · OpenAudio 2599★ · **Steinberg ASIO SDK artık GPLv3 açık-kaynak** (steinberg.net/developers/asiosdk-open) · YouTube Music resmi public API yok (TOS riski) · Spotify Web API = OAuth2+PKCE · MusicKit developer token Worker'da imzalanır · hub-and-spoke evrensel Playlist/Track spec (open-playlist-engine) · Protobuf canonical model (portify).

---

## §2 Karar (Decision)

### K1 — Sıfırdan kurulum, SSOT = v1+v2 tek ağaç
`.ai/architecture/` v2 §1.1 ağacı esas alınarak sıfırdan kurulur. 25 K klasörü, v2'deki yazım/imla ile **birebir** üretilir.

### K2 — Çoklu-MD düğüm şeması (her kademe için minimum MD seti)

| Kademe | Zorunlu MD seti |
|---|---|
| 1 · Domain (K) | `index.md` (8 alan) + `{kapsam}-master.md` (v2'de adı geçen — birebir) + `{kapsam}-bagimliliklar.md` (yeni) + `{kapsam}-dogrulama.md` (yeni) |
| 2 · Subdomain (alt klasör) | `index.md` (8 alan, `node_type: subdomain`) — derin içerik üst kademenin `master`/`bagimliliklar` dosyalarında |
| 3 · Bounded Context | `index.md` + `{bc}-bilesenler.md` (kademe 5–8 planı) |
| 4 · Module | `index.md` (8 alan) |
| K020 yönetişim klasörleri | v2 §2.20'deki şekiller **birebir** (farklı setler) |
| Band-2..7 | klasör **açılmaz** — yalnız kayıt cetvelinde aralık (bkz. K5) |

> **İstisna:** v2'de master dosyası tanımlı olmayan tek domain `K001-donanım` → yeni dosya `donanım-anayasa.md` (yeni dosyalar Türkçe rol kelimesi taşır — bkz. §5).

### K3 — İsimlendirme spesifikasyonu (bağlayıcı)
v2 §3 KURAL 1 (mevcut isimler/imla dokunulmaz) ile kullanıcı direktifi (yeni isimler Türkçe + teknik terim İngilizce) **aynı anda** geçerlidir:

| Sınıf | Kural | Kaynak |
|---|---|---|
| v2'de adı geçen dosya/klasör | **birebir korunur** (`audio-master.md`, `donanaım-type/`, `identitiy`, `integraion`, `comilance`, `recomenataion` dahil) | v2 §3 KURAL 1 |
| Yeni dosya/klasör adı | kebab-case · ≤3 kelime · **Türkçe domain kavramı + İngilizce teknik terim** · anlaşılabilir (saçma/uydurma yok) · aynı kavram her yerde aynı ad | Kullanıcı direktifi 2026-10-12 + web araştırması (turkce-kod skill, DDD Ubiquitous Language, UBOS, Dev Playbook) |
| `index.md` | evrensel teknik convention — değişmez | v2 §1.1 + vault standardı |
| Kademe-1 ID biçimi | `K0xx-<ad>` (4 hane `K1000+` için onay bekliyor — v2 §4.8/1) | v2 §4.5 kural N1 |
| Kademe-2+ ID | yol adresi (path ID): üst ID + `/` + alt ad — numara uydurmaz | v2 §4.5 Yöntem A |

### K4 — 5000+ katman hedefi kademe 2–4'ten gelir
v2 §4.6 matematiği (21 × 11 × 4 × 6 → 4. kademede 5.544, kümülatif 6.720) esas alınır; **çarpanlar VARSAYIM** — gerçek sayı doldukça disk ölçümüyle yazılır. K slotu (5.001) ile katman (node) sayısı **ayrı sayılır**, karıştırılmaz.

### K5 — Band-2..7 = tescilli rezervasyon (uydurma domain yasağı korunarak)
Kullanıcının "Band-2..7 tamamı" kararı, v2 §4.7'nin *"dosya/klasör açılmaz, numara tek başına atanamaz"* kuralıyla şöyle uzlaştırılır:

1. **4.980 slot** (K021–K5000) `architecture/index.md` §Band Cetveli'nde **aralık olarak tescil edilir** (`RESERVED · ADR-097`) — yani bantların tamamı resmen açılmış/kaydedilmiştir.
2. **Klasör üretimi toplu yapılmaz**: bir slot, ancak o slotta **gerçek bir domain** tanımlandığında (ADR ile) isim alıp klasör açar → "K021 uydurma" ihlali oluşmaz.
3. İsimler doldukça §5 spesifikasyonundan ve web araştırmasından üretilir (saçma/uydurma isim yasağı).
4. v2 §4.8/2 (K021+ için A0–A5 etiketi yok) ve §4.8/1 (4 hane biçimi) açık sorular olarak kalır.

### K6 — Çakışma/GAP kayıtları aynen taşınır
`K002` ID GAP · 6× `K020` ID CONFLICT · `K020-comere` UNKNOWN (içerik boş) · v2 §6'nın 23 eski-disk çakışması (tarihsel kayıt — disk artık boş). **Hiçbiri onaysız düzeltilmez.**

### K7 — Üretim biçimi
Band-1 (25 K + alt kademeler) **paralel çok-ajanlı** üretimle (foreground subagent) yazılır; kök/ADR dosyalarını orkestratör yazar; subagent **git commit atmaz** (yasağın sahibi: `.ai/AGENTS.md` §6). Web doğrulama turları kanıt olarak `-dogrulama.md` dosyalarına işlenir.

---

## §3 Alternatifler

| # | Alternatif | Sonuç |
|---|---|---|
| A | `git checkout HEAD -- .ai/architecture/` (46 dosya kurtarma) | ❌ `.git` boş — teknik olarak imkânsız |
| B | Her düğüm tek `index.md` | ❌ Kullanıcı reddetti (çok-MD + derinlik direktifi) |
| C | Band-2..7'yi uydurma isimlerle 4.980 klasör olarak açmak | ❌ v2 §4.7 ihlali (uydurma domain yasağı) |
| D | **Rezervasyonu tescil et, klasörleri gerçek domain gelince aç** | ✅ SEÇİLDİ (K5) — kullanıcının "tamamı" kararı ve v2 kuralı birlikte karşılanır |
| E | Eski 21 K'lı disk adlarını (K003-ses-motoru…) SSOT almak | ❌ v2 §6 o tabloyu "son bilinen durum" (artık yok) ilan etti; SSOT v1+v2 ağacı |

---

## §4 Sonuçlar (Consequences)

**Olumlu:**
- Tek merkezden sayılabilir, denetlenebilir ağaç; 5.001 slot + 6.720+ katman hedefi kayıt altında.
- Çok-MD yapı her düğümü tek başına okunabilir kılar (master/bağımlılık/doğrulama ayrımı).
- İsimlendirme hem kaynağa sadık hem web-doğrulamalı ve tutarlı.
- Band-2..7 kapasitesi ADR ile güvence altında — ileride numara/klasör açmak yeniden tartışmayı gerektirmez.

**Olumsuz / risk:**
- 350+ MD üretimi (Band-1) + derin kademeler → bakım maliyeti; gate/validate şart.
- v2'nin İngilizce rol adları (`-master.md`) ile yeni Türkçe rol adları (`-bagimliliklar.md`) aynı ağaçta birlikte yaşar — kaynak-sadakat gereği bilinçli (§5.4 açık soru).
- ADR `draft` süresince Band-2..7 slot ataması yapılmaz.

---

## §5 İsimlendirme Spesifikasyonu detayı (üretim için bağlayıcı)

### 5.1 Desen
```text
yeni dosya  =  {kapsam}-{rol}.md          kapsam = o düğümün kendi adı (kaynaktan), rol = TÜRKÇE
yeni klasör =  {türkçe-domain}-{ingilizce-teknik-terim}  veya  {ingilizce-teknik-terim}   (kebab-case, ≤3 kelime)
```

### 5.2 Rol sözlüğü (yeni MD'ler — sabit, tutarlı)
| Rol | Türkçe dosya adı | Yerine getirdiği iş |
|---|---|---|
| Kayıt/registry | `index.md` | §4.2 8 zorunlu alan |
| Anayasa (v2'de `-master.md` yoksa) | `-anayasa.md` | rol, kapsam, sınır, çocuk tablosu |
| Bağımlılıklar | `-bagimliliklar.md` | depends_on / used_by / entegrasyon sınırı |
| Doğrulama | `-dogrulama.md` | ölçütler, web-kanıt kayıtları, open_questions |
| Kapsam sınırı | `-kapsam.md` | ne DEĞİL (K020-comere vb.) |
| Bileşen planı | `-bilesenler.md` | kademe 5–8 planı (kademe-3'te) |

### 5.3 Web araştırması dayanakları (2026-10-12)
- `turkce-kod` (github.com/aiskillstore): *"Türkçe kullan: iş mantığı (domain) — musteri, siparis, fatura · İngilizce bırak: framework/teknik terim — request, response, callback, index"*.
- DDD Ubiquitous Language (Sahin Yelkenci): *"Aynı kavram her yerde aynı terimle ifade edilir; isim işlevi anlatır; tutarsız isimlendirme anti-pattern"*.
- UBOS: *"İsim işlevi anlatır (soyut isim yasak) · 2-3 kelimeyi geçmez · tutarlı önek/sonek"*.
- Dev Playbook (Tansu Aşıcı): *dizinler kebab-case; descriptive naming; tek terim tutarlılığı*.
- `comere` kelimesi hiçbir yazılım sözlüğünde/geçerli terimde **yok** → UNKNOWN, anlam uydurulmaz.

### 5.4 Açık nokta (onay beklemede)
v2'nin `-master.md` (İngilizce) adları Türkçe `-anayasa.md` ile değiştirilsin mi? **Bu ADR'de DEĞİŞTİRİLMEZ** (KURAL 1); istenirse ayrı onayla revize edilir.

---

## §6 Açık Sorular (VERIFY REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| 1 | `K1000+` için 4 hane biçimi (`K1000-ad`) — v2 §4.8/1 | `⚠️ VERIFY REQUIRED` |
| 2 | `K021+` için A0–A5 alan etiketi tanımı yok — v2 §4.8/2 | `⚠️ VERIFY REQUIRED` |
| 3 | §4.6 `11 · 4 · 6` çarpanları VARSAYIM — doldukça ölçülür | `⚠️ VERIFY REQUIRED` |
| 4 | v1'deki DSPark "90+" iddiası vs GitHub 2★ — ne ifade ediyor? | `⚠️ VERIFY REQUIRED` |
| 5 | ASIO SDK GPLv3 açık-kaynak oluşu → K003/K005 lisans notları | `⚠️ VERIFY REQUIRED` |
| 6 | Eski 23 disk çakışması (v2 §6) — disk boş; kayıt tarihsel mi, yoksa yeni disk açılınca yeniden mi ölçülür? | `⚠️ VERIFY REQUIRED` |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-12
**Mode:** Red Team · Human Mode · Truth Mode
