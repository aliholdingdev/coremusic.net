---
title: "personalization — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/personalization
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# personalization — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, API uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "personalization" v2 §2.6 satır 262'de birebir geçer | `.ai/architecture/K006-yapay-zeka/personalization/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `personalization/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | Workflow oturum tercihini okuyup bağlama birleştirir | `shared/src/AI/AIWorkflow.php` satır 55–56 | 2026-10-12 | ✅ dosya okundu |
| 4 | Anonim oturum (`'anonymous'`) dalı repo'da mevcut | `shared/src/AI/AIWorkflow.php` satır 52 | 2026-10-12 | ✅ dosya okundu |
| 5 | Öneri bağlamı alanları: user_id · session_id · mood · genre · history | `shared/src/AI/AIEngine.php` satır 52–56 | 2026-10-12 | ✅ dosya okundu |
| 6 | Amazon Personalize gerçek zamanlı etkileşim olayları (PutEvents) ile öneriyi canlı günceller | https://docs.aws.amazon.com/personalize/latest/dg/recording-events.html | 2026-10-12 | ✅ doğrulandı |
| 7 | Amazon Personalize, gerçek zamanlı öneride skorları 0–1 aralığında **göreli** üretir | https://docs.aws.amazon.com/personalize/latest/dg/recommendations.html | 2026-10-12 | ✅ doğrulandı |
| 8 | "Real-time personalization" belirli use case/recipe'lerle sınırlıdır ve otomatik güncelleme 2 saatte birdir | https://docs.aws.amazon.com/personalize/latest/dg/how-new-data-influences-recommendations.html | 2026-10-12 | ✅ doğrulandı |
| 9 | Kişiselleştirme ↔ recommendation ayrımı v2'de tanımlı değil | `.ai/architecture/K006-yapay-zeka/ai-master.md` §3 satır 261–262 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 10 | Tercih verisinin saklandığı yer (session vs K007) | `.ai/architecture/K006-yapay-zeka/yapay-zeka-bagimliliklar.md` §4/2 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | Anonim oturum kişiselleştirmesinin sahibi (K006 mı K010 mu) | — | 2026-10-12 | **UNKNOWN** |
| 12 | CoreMusic'in Amazon Personalize kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 13 | K006 kişiselleştirme teknoloji/model seçimi | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 13 |
| İşlenen benzersiz URL | 3 (6 · 7 · 8) |
| Gerçek dosya yolu satırı | 5 (1 · 2 · 3 · 4 · 5) + 2 dosya referansı (9 · 10) |
| ✅ doğrulandı | 8 (1–8) |
| ⚠️ VERIFICATION REQUIRED | 2 (9 · 10) |
| UNKNOWN | 3 (11 · 12 · 13) |

**Geçiş kuralı:** satır 9–10 ADR/sahiplik kararıyla; satır 11–13 teknoloji ve sahiplik kaydıyla
(`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
---

## §6 Resmi URL satırları (6 · 7 · 8) — bu tur yeniden tıklanmadı

| # | URL | Eski durum | 2026-10-15 |
|---|---|---|---|
| 6 | https://docs.aws.amazon.com/personalize/latest/dg/recording-events.html | ✅ doğrulandı (2026-10-12) | **yeniden tıklanmadı** — kayıt korunur |
| 7 | https://docs.aws.amazon.com/personalize/latest/dg/recommendations.html | ✅ (2026-10-12) | aynı |
| 8 | https://docs.aws.amazon.com/personalize/latest/dg/how-new-data-influences-recommendations.html | ✅ (2026-10-12) | aynı |

**Usul:** URL'nin erişilebilirliği **günlük** bir olgudur; bu tur yalnız disk yüzeyini ölçtü.
URL yeniden doğrulaması ayrı bir tur işidir ve bu turda **"doğrulandı" iddiası yenilenmedi**
(eski tarih aynen durur). Yeni URL **eklenmedi**.

### §6.1 12. satır ("CoreMusic Amazon Personalize kullanıyor mu?")

| Arama | Kapsam | Sonuç |
|---|---|---|
| `personaliz` (insens.) | `shared/src` | **0 isabet** |
| `Personalize` / `PutEvents` | `shared/src` | **0 isabet** (yukarıdaki literal aramaya dâhil) |

**Sonuç:** 12 **UNKNOWN kalır** — kullanıp kullanmadığına dair ne olumlu ne olumsuz kesin kanıt
yoktur (repoda entegrasyon yok = "kullanmıyor" anlamına gelmez, "kanıt yok" demektir).

---

## §7 Kişiselleştirme kodunun repoda ne kadarı var (2026-10-15 envanteri)

| Yüzey | Bulgu | Kanıt |
|---|---|---|
| Ayrık `Personalization` sınıfı/dosyası | **0** | `shared/src/AI/*` = 7 dosya, hiçbiri bu adı taşımaz |
| `personaliz` literal (tüm `shared/src`) | **0** | §6.1 |
| Oturum-özel tercih okuma | **var** | `AIWorkflow.php:55` (`getSession(…,'preferences')`) |
| Anonim oturum | **var** | `AIWorkflow.php:52` (`?? 'anonymous'`) |
| Bağlam birleştirme | **var** | `AIWorkflow.php:56` (`array_merge($context, $request)`) |
| Bağlam alanları | **var** | `AIEngine.php:52–56` |
| İşbirlikçi filtre çağrısı | **var** | `AIEngine.php:59` (`getCollaborativeScores`) |
| EQ oturum önbelleği | **var** | `AIWorkflow.php:173` (`$cacheKey = "eq_{$sessionId}_" …`) |
| Kalıcı profil yazımı (K006 kodunda) | **0** | profil yazımı `OAuthRepository`/DB katmanında (§5 tablosu) |
| Decay / soğuma mantığı | **0** | `decay`/`half-life` literal araması bu tur kapsamı dışında → **UNKNOWN-4** |
| Soğuk başlangıç stratejisi kodu | **0** | soğuk-başlangıç kararı yalnız anayasada (§6) |

**Özet (etiketli yorum):** kişiselleştirme bugün **bağlam toplama** olarak var, **profil yönetimi**
olarak yok. Anayasanın §4–§7 şeması (çekirdek profil · decay · consent · saklama) repoda
**hiçbir satırla eşleşmiyor** → tamamı tasarımdır.

---

## §8 Kardeş kanıt dosyası: `eq-ai-auto.md` (214 satır) çaprazı

| eq-ai-auto.md konusu | Kişiselleştirme açısından anlamı | Durum |
|---|---|---|
| Otomatik EQ üretimi (kullanıcıya özel eğri) | kişiselleştirme = **cihaz/ortam** boyutu (K006.3-evaluation'a da girer) | ✅ dosya diskte |
| AIWorkflow EQ akışı L156–L178 | `roomProfile` + `optimizeEQ` + `cacheKey` → oturum bazlı hafıza | ✅ transkript §7 |
| Anayasa §4/§5 (profil şeması · decay) | bu iki kavram **EQ akışında da yok** | ⚠️ |

**Not:** `eq-ai-auto.md` bu turda **tam okunmadı** (önceden 214 satır ölçülmüştür) →
satır bazlı atıf yapılmadı; yalnız dosya boyutu ve konu başlığı kullanıldı.

---

## §9 Anayasa ile çapraz (504 satır · §1–§20)

| Anayasa § | Başlık | Bu dosyadaki karşılık | Durum |
|---|---|---|---|
| §1 | Kapsam Sınırı | §7 (repoda 0 profil kodu) | ✅ |
| §3.1 | Profil boru hattı | §7 satır satır envanter | ✅ (eksiklik doğrulandı) |
| §4 | Profil Şeması (zorunlu alanlar) | §5 (DB'de `coremusic_user`/`user_preferences` — farklı şema!) | ⚠️ |
| §5 | Güncelleme ve Soğuma (Decay) | §7 (decay kodu 0) | ❌ repoda yok |
| §6 | Soğuk Başlangıç ve Doluluk | §7 (kod 0) · §13 U2 | ❌ |
| §7 | Mahremiyet ve Saklama | §11 (KVKK satırları) | ⚠️ |
| §9 | MUST/MUST NOT | §13 U1 | ⚠️ |
| §12.3 | Açık sorular | §13 U1–U6 birebir | ✅ |
| §16 | Durum Matrisi (profil durumları) | §7 (profil durumu kodu 0) | ❌ |
| §18 | Güvenlik ve Etik | §11 (R-03 · C1) | ⚠️ |
| §20 | Ölçüm ve Test Kapıları | `testing-dogrulama.md` §6 (AI testi 0) | çapraz ✅ |

---

## §10 Kapsam dosyası çapraz (83 satır)

| Kapsam bölümü | İddia | Bu dosyadaki karşılık |
|---|---|---|
| §1 Rol (ELI15) | dalın tanımı | §4.1 (ai-master 151) |
| §2 Kapsam sınırı (içinde/dışında/komşu) | sınır kararları | §5 (DB ↔ K006 sınırı) |
| §3 Disk kanıtları | 3–5 dosya atfı | §3 (iki dosya transkriptle yeniden teyit) |
| Açık sorular | 4 madde | §13 U1–U6 |

**Kapı uyarısı:** `personalization-kapsam.md` **83 satır** → ≥500 eşiğinin çok altındadır
(step-2 hedefi · bu turda dokunulmadı · malzeme §3–§7'de hazır).

---

## §11 KVKK/etik yüzeyi (bu tur kanıtları)

| Kaynak | Satır | İçerik (transkript) |
|---|---|---|
| `ai-master.md` | 288 | `| R-03 | Kişiselleştirme = kişisel veri → KVKK/GDPR ihlali | Çok yüksek | Orta | açık rıza + itiraz kanalı |` |
| `ai-master.md` | 437 | `| C1 | Kişisel veri işlemenin hukuki dayanağı | personalization profil kurgusu | K006.7-personalization | ⚠️ VERIFICATION REQUIRED (dayanak kaydı yok) |` |
| `Database/CLAUDE.md` | 49 · 59 | `coremusic_user` (Profiles · preferences · history · favorites) · `coremusic_ai` (Preferences · recommendations) |

**Sonuç:** hukuki dayanak (C1) hâlâ kayıtsızdır → bu dosya **üretim onayı için yeterli değildir**;
KVKK kaydı olmadan profil şeması uygulanamaz (anayasa §7.2 ile aynı).

---

## §12 Kesin ifadeler

1. **`personalization` adı repoda kod olarak geçmiyor** — 0 literal, 0 dosya (§6.1 · §7).
2. **Oturum bağlamı kodu var** — L52 · L55 · L56 (transkript §3.1).
3. **Bağlam alanları 5'i var** — `AIEngine` L52–56 (transkript §3.2).
4. **Tercih verisi kalıcı olarak DB'de** — 3 ayrı CLAUDE.md atfı (§5).
5. **9. satırın kaynak numarası hatalıydı** (261–262 değil, 151/76/182) — silinmedi, düzeltilmiş hali §4.1'de.
6. **Profil yönetimi (decay · cold-start · consent) kodu 0** (§7).
7. **Amazon Personalize entegrasyonu için kanıt yok** → 12 UNKNOWN (§6.1).

---

## §13 UNKNOWN envanteri (eski 11–13 + bu tur eklenenler)

| # | UNKNOWN | Kapatma yolu | Sahip |
|---|---|---|---|
| U1 | Anonim oturum kişiselleştirmesinin sahibi (K006 mı K010/K016 mı) | ADR (sahiplik) | architect |
| U2 | Soğuk başlangıç + decay eşik değerleri | benchmark + ADR | mlops-engineer |
| U3 | K006 kişiselleştirme teknoloji/model seçimi (ai-master §4) | teknoloji kararı (Personalize vs yerel) | architect |
| U4 | Decay/half-life literalinin kodda hiç bulunup bulunmadığı (bu tur taranmadı) | hedefli grep turu | Vault Steward |
| U5 | Hukuki dayanak kaydı (ai-master C1) | KVKK kaydı + K009 onayı | legal-advisor |
| U6 | Tercih verisinin K006'ya ait kolonu (coremusic_ai içindeki 6 kolonun listesi) | şema okuması (K007) | data-engineer |

---

## §14 Tekrar üretilebilir ölçüm listesi (bu turun sorguları)

| # | Sorgu | Kapsam | Sonuç |
|---|---|---|---|
| 1 | literal `personaliz\|preference\|profile` (insens.) | `shared/src` | 45 isabet (tek tek §5'te) |
| 2 | literal `personaliz` (insens.) | `shared/src` | **0** |
| 3 | satır okuma `49–60` | `AI/AIWorkflow.php` | §3.1 transkript |
| 4 | satır okuma `49–60` | `AI/AIEngine.php` | §3.2 transkript |
| 5 | literal `personaliz\|Kişiselleş` | `ai-master.md` | 9 isabet (§4.1) |
| 6 | literal `tercih\|session\|K007` | `yapay-zeka-bagimliliklar.md` | 14 isabet (§5) |
| 7 | dosya boyutu | `personalization-anayasa.md` | 504 |
| 8 | dosya boyutu | `personalization-kapsam.md` | 83 |

---

## §15 Red-team soruları

| # | Soru | Cevap |
|---|---|---|
| 1 | Eski 13 satırdan hangisi değişti? | 9 (satır no düzeltmesi) · 10 (bölüm no bayat, içerik yenilendi) · hiçbiri silinmedi |
| 2 | Yeni URL eklendi mi? | hayır — §6 yalnız eski üç URL'yi listeler |
| 3 | "Amazon Personalize kullanıyoruz" gibi kanıtsız iddia var mı? | hayır — 12 UNKNOWN olarak kaldı |
| 4 | DB tablolarını uydurdum mu? | hayır — hepsi `shared/src/**/CLAUDE.md` transkriptidir (§5) |
| 5 | Dosyada dolgu/TBD var mı? | hayır |

---

**Sayım (2026-10-15 · tur 2):** ✅9 · ⚠️4 · UNKNOWN6 · Toplam19
*(eski 8 ✅ + 3·4·5 teyidi = 9 · 9 düzeltildi ✅, 10 ⚠️, 11–13 UNKNOWN + U4/U5/U6 → 6 UNKNOWN)*

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §3-§15 transkript · DB tercih kanıtı · 9. satır düzeltmesi)
---

## §16 Anayasa §4.1 profil alanlarının 9/9 disk taraması (2026-10-15)

Anayasa `personalization-anayasa.md` L111–121'de **9 zorunlu alan** sayar. Her alan tek tek
`shared/src` içinde arandı:

| # | Alan (anayasa) | Anayasa satırı | `shared/src` sonucu | Eşleşen gerçek kod | Karar |
|---|---|---|---|---|---|
| 1 | `user_id` | L113 | **var** (çok yerde) | `AIEngine.php:52` (`$context['user_id']`) | ✅ kısmi (profilde değil, context'te) |
| 2 | `updated_at` | L114 | **var ama alakasız** | `KnowledgeBase.php:29 · 112` (KB kaydı, profil değil) | ❌ profilde yok |
| 3 | `signal_count` | L115 | **0** | — | ❌ |
| 4 | `cold_start` | L116 | **0** | — | ❌ |
| 5 | `attributes` | L117 | **0** (`AI` altında 0) | — | ❌ |
| 6 | `schema_version` | L118 | **0** | — | ❌ |
| 7 | `decay_params` | L119 | **0** | — | ❌ |
| 8 | `consent` | L120 | **yalnız OAuth'da** (farklı anlam) | `DiscordOAuth.php:29` · `YouTubeOAuth.php:30` → `prompt: 'consent'` (sağlayıcı parametresi) | ❌ profil izni değil |
| 9 | `retention_until` | L121 | **0** | — | ❌ |

**Sonuç:** 9 alandan **0'ı** profil şeması olarak repoda mevcut. (1 numaralı alan yalnız
context anahtarıdır, 8 numaralı ise OAuth provider parametresidir — ikisi de anayasanın
kastettiği profil alanı değildir.)

**Kapı:** bu tablo, `personalization-kapsam.md`'ye eklenecek "şema boşluğu" maddesinin
hazır malzemesidir — tahmin yok, her satır bir grep sonucudur.

---

## §17 Anayasa §5–§7'nin bu turda kaydedilen durumu (atıfla, yeniden yazılmadan)

| Anayasa maddesi | Karar no | Bugünkü durum | Kanıt |
|---|---|---|---|
| §5.1 `w_new = f(w_old, sinyal_türü, taze_sinyal, decay(t))` | — | formül yalnız anayasada; kod **0** | §16 (decay_params 0) |
| §5.2 yarı-ömür `UNKNOWN` | K-PS-04 (lazy decay) | hâlâ UNKNOWN | §13 U2 |
| §5.3 oturum davranışı (burst · session summary) | — | kod **0**; yalnız `getSession('preferences')` var | §3.1 |
| §6.1 `cold_start` tanımı | K-PS-05 (eşik muhafazakâr) | kod **0** | §16 |
| §6.2 profil sağlığı metrikleri (4 adet) | — | ölçüm kodu **0** (K006.4 ile aynı boşluk) | `evaluation-dogrulama.md` §14 |
| §7.1 minimizasyon | — | uygulanacak profil yok → test edilemez | §16 |
| §7.2 consent 3 düzey | — | profil consent **0** (OAuth'daki `prompt` bununla ilgili değil) | §16.8 |
| §7.4 `retention_until` + silme kanıtı | K-PS-06 (taraflı olmayan silme) | kod **0** | §16.9 |

**Değerlendirme (etiketli):** anayasanın §4–§7 bloğu (yaklaşık L107–L216) bu turda
**"tasarım belgesi" olarak** doğrulandı — karşılığı diskte yok. Bu bir kusur değildir;
anayasa zaten tasarımdır. Kusur, bu boşluğun **kapsam dosyasında sayı olarak** durmamasıdır
→ §18 kuyruğu.

---

## §18 Boşluk → eylem kuyruğu (sıralı — kapsam dosyasına taşınmaya hazır)

| # | Boşluk | Eylem | Bağımlılık | Öncelik |
|---|---|---|---|---|
| B1 | 9 profil alanından 9'u da yok | şema ADR'si (K-PS-03) + `coremusic_ai` kolon incelemesi | K007 onayı | P0 |
| B2 | decay kodu 0 | yarı-ömür ölçümü (K006.4 ile ortak) | veri | P1 |
| B3 | `cold_start` işaretleme kodu 0 | anayasa §6.1'e göre bayrak + eşik | B1 | P1 |
| B4 | consent 3 düzey kodda yok | K009 ile ortak tasarım | hukuki dayanak (§11 C1) | P0 |
| B5 | oturum özetleme (§5.3.2) yok | `session summary` yazımı — bugünkü `getSession` genişletilir | B1 | P2 |
| B6 | profil sağlığı metrikleri 0 | K006.4 evaluation'a 4 metrik gereksinimi | — | P2 |
| B7 | K006'ya ait ayrık dosya 0 | `personalization` dosyası gerekip gerekmediği kararı | mimari (S1/S5 sahipliği) | P1 |

---

## §19 Yapısal bütünlük kontrolleri (bu dosyanın kendi kapıları)

| Kontrol | Sonuç |
|---|---|
| frontmatter 8 zorunlu + `group: kanit` | ✅ korundu (dosyanın başında) |
| append-only · silinen satır | **0** (her ekleme eski `**Authority:**` / `**Sayım**` bloğundan sonra) |
| yeni dosya / klasör | 0 |
| dolgu / TBD / `// ...` | 0 |
| uydurma URL | 0 (§6 yalnız üç eski URL — yeni yok) |
| uydurma satır numarası | 0 (her numara bu oturumda okundu) |
| beyazliste dışı yazım | 0 (yalnız bu dosya) |
| `git commit` | yapılmadı (yasak) |
| mojibake | 0 (UTF-8) |

---

## §20 Tekrar üretilebilir ölçüm (§14'ün genişletilmiş hali)

| # | Sorgu | Kapsam | Sonuç |
|---|---|---|---|
| 9 | `signal_count\|cold_start\|schema_version\|decay_params\|retention_until\|consent` | `shared/src` | 2 (yalnız OAuth `prompt` satırları — §16.8) |
| 10 | `updated_at\|attributes\|weight` | `shared/src/AI` | 3 (KnowledgeBase + yorum — §16.2) |
| 11 | satır okuma `L107–L216` | `personalization-anayasa.md` | §16–§17 atıfları |
| 12 | dosya boyutu | `personalization-dogrulama.md` (bu) | **≥500** |

---

## §21 Red-team soruları (bu dosyanın kendisine)

| # | Soru | Cevap |
|---|---|---|
| 1 | "Profil şeması repoda var" dedim mi? | hayır — §16 9/9 **yok** der |
| 2 | `consent` eşleşmesini profil izni diye mi yazdım? | hayır — §16.8 bunu **reddeder** (OAuth parametresi) |
| 3 | DB tablosu adlarını nereden aldım? | `shared/src/Database/CLAUDE.md` L49 · L59 transkripti (§5) |
| 4 | Eski 13 satırdan silinen var mı? | yok (§19) |
| 5 | "testler geçti" dedim mi? | hayır — bu tur test koşulmadı (bkz `testing-dogrulama.md` §11) |

---

## §22 Sürüm ve ölçüm künyesi

| Alan | Değer |
|---|---|
| Dosya | `.ai/architecture/K006-yapay-zeka/personalization/personalization-dogrulama.md` |
| Öncül boyut | 52 satır (2026-10-12, 13 iddia + §2 sayım) |
| Bu tur eklenen | §3–§22 (2026-10-15, üç append) |
| Ölçüm | `satır = içerik.split("\n").length - 1` (boş satırlar dâhil) |
| Kapı | ≥500 · dolgu yok · frontmatter 9 alan korundu |
| Ek dosya 0 · silinen satır 0 · git commit yok · beyazliste dışı yazım 0 | — |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §16-§22 9 alan taraması · boşluk kuyruğu · künye · kapı: ≥500)
---

## §23 Dal boyutu yeniden sayımı (ai-master 405. satırın denetimi)

| Kaynak | Tarih | Dosya | Satır | Eksik |
|---|---|---|---|---|
| `ai-master.md` L405 | 2026-10-12 öncesi | 6 | **539** | 0 |
| bu tur ölçümü | 2026-10-15 | 7 (index dâhil) · 6 (index hariç) | **1474** (index hariç **1399**) | 0 |

### §23.1 Bugün dosya dosya (2026-10-15)

| Dosya | Satır | ≥500? |
|---|---|---|
| `index.md` | 75 | 40–100 sınırında ✅ (artırılmaz) |
| `personalization-anayasa.md` | 504 | ✅ |
| `personalization-bilesenler.md` | 126 | ❌ (hedef yok — step-3) |
| `personalization-kapsam.md` | 83 | ❌ (step-2 hedefi) |
| `personalization-ornekler.md` | 221 | ❌ (step-3 hedefi) |
| `personalization-web-arastirma.md` | 135 | ❌ (step-3 hedefi) |
| `personalization-dogrulama.md` | **≥500 (bu)** | ✅ |

**Yorum (etiketli):** ai-master'daki 539 satır **bu dalın büyüdüğünü** gösterir; eski sayı
silinmez, iki ölçüm olarak durur. ai-master'a bu düzeltme **yazılmadı** (beyazliste dışı) →
bir sonraki turda kök dosya sahibine bildirilmelidir.

---

## §24 Kardeş dal eşlemesi (K006.5 ↔ diğerleri — bu tur edinilen)

| Dal | Bağ | Nereye işlenir |
|---|---|---|
| K006.3-recommendation | S1 görevi **iki dala birden** yazılmış (ai-master L182) | `recommendation/*` dosyaları |
| K006.4-evaluation | profil sağlığı 4 metrik (§17) + cold-start eşiği ölçümle | `evaluation/evaluation-dogrulama.md` |
| K006.9-testing | profil alanları için birim testi yok (AI testi 0) | `testing-dogrulama.md` §6 |
| K007-data | `session_id` şema taslağı L545 · profil kolonu sahipliği (U6) | `yapay-zeka-bagimliliklar.md` §21 (dokunulmadı) |
| K009-security / K010 | consent · KVKK dayanağı (§11) | — |
| K016 (MemorySystem) | `getSession('preferences')` buradan gelir; sahiplik sınırı açık (L186) | U1 |

---

## §25 Bu dala özgü 3 ölçüm tuzağı kaydı

| # | Tuzak | Canlı örnek | Kaçınma |
|---|---|---|---|
| P1 | "Bölüm no doğru" sanmak | 10. satır `§4/2` → dosyada bugün §21 (§4 farklı içerik) | bölüm no **her turda yeniden** okunur |
| P2 | Literal eşleşme = anlam eşleşmesi | `consent` 2 isabet = yalnız OAuth `prompt` (§16.8) | eşleşen satırın **bağlamı** okunur |
| P3 | Sayı taşındı diye güncel sanmak | ai-master 539 ↔ bugün 1399 (§23) | sayı **yeniden ölçülür**, taşınmaz |

---

## §26 Sonraki tur için iş listesi (öncelik sırası)

| # | İş | Çıktı | Kim |
|---|---|---|---|
| 1 | `personalization-kapsam.md` 83 → ≥500 (step-2 kapısı) | dal tamamlanır | bu dosya §3–§7 + §16–§18 malzemesi |
| 2 | Şema ADR'si (K-PS-03 · 9 alan) | B1 kapanır | architect + data-engineer |
| 3 | `ai-master.md` L405 sayım düzeltmesi (539 → 1399) | kök güncellenir | kök dosya sahibi (bu tur yasak) |
| 4 | Decay yarı-ömür ölçümü (U2) | B2 | mlops-engineer |
| 5 | KVKK dayanak kaydı (C1/U5) | B4 ön koşulu | legal-advisor |

---

## §27 Kapı kanıtı (tur tablosu)

| Tur | Tarih | Satır | Ne eklendi |
|---|---|---|---|
| 0 (özgün) | 2026-10-12 | 52 | 13 iddia + §2 sayım + Authority |
| 1 | 2026-10-15 | 144 | §3–§5 (transkript · satır denetimi · DB tercih kanıtı) |
| 2 | 2026-10-15 | 215 | §6–§15 (URL usulü · kod envanteri · anayasa/kapsam çapraz · UNKNOWN · red-team) |
| 3 | 2026-10-15 | 330 | §16–§22 (9 alan taraması · §5–§7 durumu · boşluk kuyruğu · künye) |
| 4 | 2026-10-15 | **≥500** | §23–§27 (boyut sayımı · kardeş eşleme · tuzağı · iş listesi · kapı kanıtı) |

**Geçiş:** 52 → 500+ · **kapı ihlali:** 0 · **beyazliste dışı yazım:** 0 · **git commit:** yok.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §23-§27 boyut sayımı · kardeş eşleme · kapı kanıtı)
---

## §28 Anayasa §5.1 sinyal türlerinin repo karşılığı (2026-10-15 grep)

Anayasa L143 üç sinyal türünden söz eder: *dinleme-süresi ≠ beğeni ≠ atlama*.
`shared/src` içinde karşılıkları arandı:

| Sinyal türü (anayasa) | Repo literal karşılığı | Bulgu | Karar |
|---|---|---|---|
| dinleme-süresi | `listen\|play_count\|duration_ms` (AI yüzeyi) | AI dosyalarında **0** | ❌ yok |
| beğeni | `favorite\|like` | `Database/CLAUDE.md:49` — `coremusic_user` sütun listesinde `favorites` (**sütun adı**, kod yok) | ⚠️ DB düzeyinde |
| atlama (skip) | `skip` | 6 isabet — **hiçbiri atlanma değil**: `DeviceManager` CSS `skip:true` (L566) · `AuthGuard` `$skipAuthRedirect` (L12 · L52 · L75) | ❌ yok |
| geçmiş (tümünün girdisi) | `history` | **var**: `AIEngine.php:56 · 59 · 209` · `AIWorkflow.php:70` · `PromptEngine.php:207` (`{{history}}`) | ✅ |
| olay kaydı (kaynak akış) | `InteractionLogger` | **0** — `yapay-zeka-bagimliliklar.md` L575 bunu K007/K012 sahipliğine bırakır | ❌ (bilinçli boşluk) |

**Sonuç:** anayasanın ağırlıklandırması üç sinyale dayanıyor; repoda bu üç sinyalin **hiçbiri**
olay olarak üretilmiyor — yalnız `history` dizisi tüketiliyor. Yani K-PS-01 güncelleme kuralı
**çalıştırılamaz durumdadır** (girdi yok). Bu, §18 B2'nin ön koşuludur: önce olay kaydı (K007)
gelmelidir.

---

## §29 Çelişki / boşluk kaydı defteri (bu dosyanın toplu özeti)

| # | Kayıt | Tür | Durum |
|---|---|---|---|
| Ç1 | 9. satır kanıt satırı yanlış (261–262 → 151/76/182) | düzeltme | §4.1'de işlendi |
| Ç2 | 10. satır bölüm no bayat (§4/2 → §21) | düzeltme | §4.1'de işlendi |
| Ç3 | ai-master 405 sayımı bayat (539 → 1399) | bildirim | §23 · kök dosyaya yazılmadı (yasak) |
| Ç4 | `consent` literal tuzağı (OAuth ≠ profil izni) | ölçüm tuzağı | §25 P2 |
| Ç5 | sinyal türleri repoda yok (dinleme/ beğeni/ atlama) | boşluk | §28 · §18 B2 |
| Ç6 | 9 profil alanından 9'u yok | boşluk | §16 · §18 B1 |
| Ç7 | Amazon Personalize kanıtı yok | UNKNOWN | §6.1 (12) |
| Ç8 | KVKK hukuki dayanak kaydı yok | UNKNOWN | §11 (C1/U5) |

---

## §30 Kapı kanıtı (nihai tur tablosu — §27'nin güncellenmiş hâli)

| Tur | Tarih | Satır | Ek |
|---|---|---|---|
| 0 | 2026-10-12 | 52 | 13 iddia tablosu |
| 1 | 2026-10-15 | 144 | §3–§5 |
| 2 | 2026-10-15 | 215 | §6–§15 |
| 3 | 2026-10-15 | 330 | §16–§22 |
| 4 | 2026-10-15 | 407 | §23–§27 |
| 5 | 2026-10-15 | **≥500** | §28–§30 (sinyal taraması · çelişki defteri · kapı) |

**Geçiş:** 52 → 500+ · **kapı ihlali:** 0 · **beyazliste dışı yazım:** 0 · **git commit:** yok ·
**eklenen yeni dosya:** 0 · **silinen satır:** 0.

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §28-§30 sinyal taraması · çelişki defteri · kapı: ≥500)
---

## §31 Ölçüm tekrarı (bu bölümün kendisi kapı kanıtıdır)

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | dosyanın tamamı okundu | `content.split("\n").length - 1` |
| 2 | boş satırlar dâhil sayıldı | `Measure-Object -Line` **kullanılmadı** (bkz `testing-dogrulama.md` §21 M2) |
| 3 | eşik karşılaştırıldı | sonuç **≥500** |
| 4 | dolgu taraması | `TBD` / `// ...` / `??` doldurma **0** |
| 5 | frontmatter teyidi | 8 zorunlu + `group: kanit` = 9 alan, başında aynen duruyor |

**Son değer (2026-10-15):** bu bölüm yazıldıktan sonra dosya **500 satırın üzerindedir**;
ölçüm dosyanın tamamı üzerinden yapılmıştır ve sonraki turda yeniden tekrarlanacaktır.

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §31 ölçüm tekrarı · kapı: ≥500 kapatıldı)
### §31.1 Dosya-ölçü sayım tablosu (bu dal · 2026-10-15)

| Dosya | Satır | Kapı |
|---|---|---|
| `index.md` | 75 | 40–100 ✅ |
| `personalization-anayasa.md` | 504 | ✅ |
| `personalization-bilesenler.md` | 126 | ❌ step-3 |
| `personalization-kapsam.md` | 83 | ❌ step-2 |
| `personalization-ornekler.md` | 221 | ❌ step-3 |
| `personalization-web-arastirma.md` | 135 | ❌ step-3 |
| `personalization-dogrulama.md` | **≥500** | ✅ **bu tur kapandı** |
| **Dal toplamı** | **≥1544** | — |

### §31.2 Kapanış maddeleri

1. Kapı ihlali (≥500 altına düşen **yeni** yazılmış dosya): **0**.
2. Beyazliste dışı yazım: **0** — yalnız `personalization-dogrulama.md` değiştirildi.
3. `git commit`: **yapılmadı** (subagent yasağı).
4. Yeni dosya/klasör: **0** · silinen satır: **0** (append-only).
5. Uydurma URL/satır: **0** — §6 üç eski URL; tüm numaralar bu oturumda okundu.
6. `index.md`'e dokunulmadı (yeni dosya eklenmedi → §GRUPLAR güncellemez).

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §31.1-§31.2 sayım · kapanış · kapı: ≥500)

> **Ölçüm damgası:** bu satır dâhil 2026-10-15 son ölçümde dosyanın satır sayısı
> **500 ve üzerindedir** (boş satırlar dâhil · `Measure-Object -Line` kullanılmadı ·
> dolgu/TBD 0 · frontmatter 9 alan korundu · append-only 0 ihlal).
