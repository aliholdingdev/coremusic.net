---
node_id: K18-testing-kararlar
node_type: subdomain
status: draft
parent_id: K18-testing
children: []
depends_on: [K17-devops, K009-security, K007-data, K18-testing]
evidence:
  - ".ai/.decisions/accepted/ADR-023-persona-driven-testing.md (title/satır 18 — 20 persona matrisi · %90 satır+şart · kritik yol %100 branş · PR coverage gate · IMPLEMENTED/PLANNED etiketleri — 2026-10-18 okundu)"
  - ".ai/.decisions/index.md satır 77 (ADR-023 · Testing · accepted) + index §5 (ADR-097 draft) — 2026-10-18"
  - ".ai/architecture/K18-testing/testing-master.md §6-§7 (doğrulama özeti + 8 açık soru = bekleyen kararlar — 2026-10-18)"
validation: "Yalnız diskte var olan gerçek ADR numaraları kullanılır; Nygard-format örnekleri numarasız 'şablon/taslak' olarak işaretlenir, gerçek ADR gibi sunulmaz."
open_questions:
  - "⚠️ ADR-097 (draft) K18'i K1-K7 kapılarıyla tanımlar — draft olduğu için bağlayıcı değil, durumu: bekliyor"
  - "⚠️ ADR-023 coverage gate 'PR'da düşerse merge yok' der; ci.yml'da coverage hâlâ soft (continue-on-error) — tescil ile uygulama arasında 1 uçurum var"
  - "⚠️ Yeni ADR GEREKLİ: soft→hard gate geçişi · 4 kanıtsız çocuğun kaderi · media.coremusic.net kapsam kararı"
group: kod
---

# Testing — Kararlar (ADR)

> K18-testing'i şekillendiren gerçek ADR'leri, uygulama durumlarını ve
> bekleyen karar kapısını listeler. Yalnız diskte var olan gerçek ADR
> numaraları kullanılır; Nygard-format örnekleri numarasız ve "şablon/taslak"
> etiketlidir.

## §1. ELI15 — "Kararlar" ne demek?

Yazılımda bazı sorular tekrar tekrar sorulur: "Coverage eşiği ne olmalı?",
"Soft gate yeterli mi?", "E2E kaç spec olmalı?" Bunlara **her seferinde yeniden**
cevap vermek yerine, bir kez cevaplayıp kayda geçirmek = **ADR (Architecture
Decision Record)**. Bu dosyadaki kural basit: **gerçek ADR numarası olmayan
hiçbir şeye "karar" denmez** — olsa olsa "taslak şablon" denir. K18-testing'te
durum: **1 kabul edilmiş ADR var** (ADR-023), **1 draft var** (ADR-097),
geri kalanı açık soru olarak kapıda bekliyor.

## §2. Gerçek ADR Kayıtları (disk kanıtlı)

### §2.1 ADR-023 — Persona-Driven Testing (accepted)

| Alan | Değer |
|---|---|
| Başlık | "CoreMusic — ADR-023: Persona-Driven Testing (20 Persona Test Matrisi · %90 Satır + Şart/Branch Coverage · Kritik Yollar %100 Branch · PR'da Coverage Gate)" |
| Durum | accepted (dizin: `index.md` satır 77 — Testing kategorisi) |
| Karar veren | Vault Steward (kullanıcı onaylı); debate 3 tur / 20 persona = 18/2/0 KABUL |
| Bağlayıcı maddeler | (a) 20 persona test matrisi (her persona: birim + entegrasyon; happy/error/boundary) · (b) coverage %90 satır + branch · (c) kritik yollar (auth/payment/ADR-020 API) %100 branch · (d) PR'da coverage gate — CI'da düşerse merge yok |
| IMPLEMENTED (ADR metni + bugünün disk verisi) | 32 test dosyası / ~243 test metodu (ADR yazımda) → **bugün 55 PHP dosya** (2026-10-12 sayımı); PHPUnit ^10.5 ×4; phpunit.xml ×4; CI lint+test+audit sert kapılar; PageRouter 4/21 test |
| PLANNED (kapanmamış) | coverage ölçümü + eşiği; PR merge gate; testsuite boşlukları; JS/E2E genişlemesi; persona matrisinin testlere bağlanması |

**Uygulama uçurumu (tescilli):** ADR-023 madde (d) "PR'da coverage gate —
CI'da düşerse merge yok" der; `ci.yml` L167'de coverage job'u hâlâ
`continue-on-error: true` (**soft**) — `testing-master.md` §4.2. Yani
**karar kabul edilmiş, kapı henüz sertleştirilmemiş**. Bu durum açık soru 1'dir
(`testing-master.md` §7); sessizce "uygulandı" denmez.

### §2.2 ADR-097 — v2 mimari geçiş (draft — bağlayıcı DEĞİL)

| Alan | Değer |
|---|---|
| Durum | **draft** (`.ai/.decisions/index.md` — 3 draft'tan biri: 097/102/103) |
| K18 ilgisi | §2 K1-K7 kapıları · §5 isimlendirme · §6 açık sorular (`testing-master.md` §8) |
| Bağlayıcılık | draft olduğu için karar sayılmaz; kapılar hedef olarak okunur |

## §3. Açık Sorular = Bekleyen Karar Kapıları (`testing-master.md` §7 + `testing-dogrulama.md` §5)

| # | Bekleyen karar | Etkilenen çocuk | Karar tipi |
|---|---|---|---|
| 1 | Coverage gate soft→hard geçişi; ≥80% bağlayıcı mı? | build-verification/ | ADR GEREKLİ |
| 2 | E2E spec ailesi büyütülecek mi? (1 spec kanıtlı — kapsam dar) | e2e/ | ADR GEREKLİ |
| 3 | Regression suite ayrı mı, mevcut unit/integration mı kapsıyor? | regression/ | ADR GEREKLİ |
| 4 | Performance hedef metrik/ekipman tanımlı mı? | performance/ | ADR GEREKLİ |
| 5 | Audio-quality hedefi (SNR/THD+N) laboratuvar mı, CI mı? | audio-quality/ | ADR GEREKLİ (K003-audio ile ortak) |
| 6 | hardware-validation hangi fiziksel rig üzerinde? (PCM3168A/AK4458/ASIO) | hardware-validation/ | ADR GEREKLİ (K004-dsp ile ortak) |
| 7 | media.coremusic.net neden phpunit.xml'siz? Kapsam dışı mı? | unit/ · integration/ | ADR GEREKLİ |
| 8 | phpstan yalnız shared'de — subdomainler static analysis dışında mı? | build-verification/ | ADR GEREKLİ |
| 9 | flaky karantina mekanizması ne zaman kurulacak (repo'da YOK)? | flaky/ | ADR GEREKLİ (`testing-dogrulama.md` §5/7) |

## §4. Nygard-Format Örnek Şablonlar (TASLAK — gerçek ADR DEĞİLDİR)

> **UYARI:** Aşağıdaki bloklar ADR şablonudur; **hiçbirinin ADR numarası
> yoktur** ve hiçbiri "karar" değildir — bekleyen kapıları yazarken kullanılacak
> iskelettir. (Zero-hallucination: numara uydurmak yasak.)

### Örnek A — Coverage Soft→Hard Gate (TASLAK · numarasız)

```markdown
Tarih: <tarih>              Durum: TASLAK (kabul edilmedi)
Bağlam: ci.yml L167 js-coverage continue-on-error:true; ADR-023 madde (d) ise
        "PR'da coverage gate, düşerse merge yok" der (accepted).
Karar: js-coverage job'u hard gate yapılır (continue-on-error kaldırılır);
       eşik .ai/AGENTS.md §16'daki ≥80% ile hizalanır.
Sonuç: (1) coverage düşen PR merge edilemez → geçici tıkanma riski;
       (2) ADR-023 ile kapı uygulaması hizalanır;
       (3) coverage raporu tutmayan alt alanlar (media.*) kapsam dışı tescillenmeli.
Reddedilen alternatifler: soft gate'te kal (ADR-023 bağlayıcılığı ihlal);
       eşik belirlemeden hard yap (bilinmeyen baseline ile üretime kilit).
```

### Örnek B — 4 Kanıtsız Çocuğun Kaderi (TASLAK · numarasız)

```markdown
Tarih: <tarih>              Durum: TASLAK (kabul edilmedi)
Bağlam: regression · performance · audio-quality · hardware-validation için
        disk glob = 0 isabet (testing-dogrulama.md §2/15-18); domain "verified"
        olamıyor (§6 kapanış kuralı).
Karar: Her çocuk için iki yol: (i) minimal kanıt seti üretilir (örn. regression
       = mevcut unit/integration'ın suite etiketlenmesi), ya da (ii) ADR ile
       "kapsam dışı — <faz>" tescillenir.
Sonuç: Domain doğrulama zinciri (§6) kapanır; 4 çocuk sahipsiz kalmaz.
Reddedilen alternatifler: kanıtsız "var saymak" (hallüsinasyon);
       children listesinden silmek (v2 spec §2.19 ile çelişir).
```

## §5. Karar Disiplini Kuralları (bu domain için)

| # | Kural | Kaynak |
|---|---|---|
| 1 | Kapsam kayması (K17-devops ↔ K18-testing) sessiz taşınmaz; tespit edilirse ADR ile düzeltilir | `testing-master.md` §2 (registry §6 tescilli ayrım) |
| 2 | S4 sınıfı iddia (kaynaksız/URL'siz) bu domainde karar girdisi OLAMAZ | `testing-dogrulama.md` §1 |
| 3 | Domain `verified` olmak için: S4 iddia kapanmalı · 4 negatif çocuk ya kanıtla ya ADR ile tescillenmeli | `testing-dogrulama.md` §6 |
| 4 | Numarasız taslak ADR, gerçek ADR gibi sunulamaz; "TASLAK" etiketi zorunlu | Bu dosya §4 (zero-hallucination) |

## §6. Kaynaklar

- `.ai/.decisions/accepted/ADR-023-persona-driven-testing.md` (başlık · satır 18-19 · satır 63 IMPLEMENTED/PLANNED) — 2026-10-18 okundu
- `.ai/.decisions/index.md` satır 77 (ADR-023 · accepted · Testing) — 2026-10-18
- `testing-master.md` §6-§8 · `testing-dogrulama.md` §4-§6 — 2026-10-18 okundu

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-18 (Stage-2-F)
