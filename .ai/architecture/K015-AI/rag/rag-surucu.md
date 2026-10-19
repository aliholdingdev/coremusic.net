---
node_id: rag-surucu
node_type: module
status: active
parent_id: rag
children: []
depends_on: [rag-anayasa]
evidence:
  - ".ai/RAG.md §3 retrieval indeksi (177 satır)"
  - ".ai/keys.md keyword haritası (763 satır)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# rag-surucu - Retrieval Kullanım Sürucusu

> **Kapsam:** Bir soru geldiğinde sürücünün gerçekten çalıştığı adımlar — indeks tarama sırası,
> karar ağacı, fallback zinciri, eksik kanıt davranışı, güncelleme akışı, hata modları ve
> doğrulama kapıları. **Kural koyar değil, tarif eder:** bağlayıcı kurallar
> `Dosya: .ai/RAG.md · Satır: 129-137` (§5 Kurallar) ve `Dosya: .ai/CLAUDE.md · Satır: 628-648`
> (§16 Boot Protocol) içindedir. Bu dosya o kuralların **kullanım kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (tüm satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count`
> ile bugün diskten ölçülmüştür — §17).

## §1. ELI15 — Bu sürücü ne işe yarar

Bir ajan (veya insan) "şu konuda ne var?" diye sorduğunda vault **tümüyle taranmaz**. Sürücü,
soruyu önce en ucuz indekse (`RAG.md §3` — 20 satırlık konu→dosya tablosu) vurur; eşleşme yoksa
keyword haritasına (`keys.md`), oradan da katalogda (`index.md`) arar; hâlâ yoksa hedef dosyayı
**doğrudan `@` ile** okur. Hiçbir katman yanıt vermezse fallback zinciri devreye girer
(§7) ve sonuç `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` olarak işaretlenir — **uydurulmaz**.

> **eli10:** "Hangi dosyayı okuyayım?" sorusunun cevabı zaten iki küçük tabloda yazılıdır
> (`RAG.md §3` + `keys.md`); sürücü bu tabloları **sırayla** kullanmayı ve **boş kalırsa
> dürüstçe 'bilmiyorum' demeyi** tarif eder.
>
> **eli15:** Retrieval bugün **elle** yapılıyor — embedding/vektör boru hattı `PLANNED`'tır
> (`Dosya: .ai/RAG.md · Satır: 111` → ADR-030 "RAG / embedding / vektör — ❌ PLANNED (kod 0, tablo 0)`;
> `Dosya: .ai/.decisions/accepted/ADR-030-ai-strategy-core.md · Satır: 305` aynı sonucu taşır).
> Bu yüzden sürücünün "günlük davranış" bölümü (§10) otomasyonu değil, **bugünün elle akışını**
> anlatır; otomasyona geçiş kapısı ayrı bölümde (§11) durur.

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Soru → ilk okunacak dosya sırası (§3-§6) | Dosya içeriği özetleri (içerik hedef dosyanın işidir) |
| Fallback zinciri ve eksik kanıt davranışı (§7-§9) | Embedding/vektör **kodunun** yazımı (ADR-030 §5.1/6 ayrı görev) |
| Yeni/taşınan dosyada indeks güncelleme akışı (§12-§13) | Boot listesinin değiştirilmesi (§14: **değiştirilemez**) |
| Ölçüm/betik örnekleri + repo PHP kanıtı (§17-§21) | `.ai/` dışı uygulama kodunun retrieval'a taşınması |
| Hata modları, kapılar, kapanış listesi (§15-§16, §29) | ADR metinleri (frozen — yalnız okunur) |

- **Kullananlar:** görev başına retrieval yapan her ajan · MO (indeks güncellemesi) · Vault Steward (denetim).
- **Ön koşul:** `RAG.md §3` satırları diskte var olan dosyalara gider (`Dosya: .ai/RAG.md · Satır: 102`).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; künyesiz iddia yazılmaz (§9).

## §3. Kaynak Katmanları (tarama sırasının kaynağı)

| Kademe | Kaynak | Ne verir | Ölçüm (2026-10-17) | Kanıt |
|---|---|---|---|---|
| **K1** | `.ai/RAG.md` §3.1 | Konu sınıfı → hedef dosya → keyword → durum (ilk okunacak tablo) | **177 satır** (tablo başlığı Satır: 69-73) | `Dosya: .ai/RAG.md · Satır: 69-95` |
| **K2** | `.ai/keys.md` | Keyword → dosya geniş harita + karar ağacı + öncelik matrisi | **763 satır** | `Dosya: .ai/keys.md · Satır: 36, 44-60, 600-613` |
| **K3** | `.ai/index.md` | Master katalog (dosya/ADR listesi) | **246 satır** | `Dosya: .ai/RAG.md · Satır: 80` (hedef `index`), ölçüm §17 |
| **K4** | Doğrudan `@` okuma | Hedef dosyanın kendisi (son söz her zaman dosyanındır) | değişken | `Dosya: kök AGENTS.md · Satır: 282` ("yalnız ihtiyaç anında `@` ile") |

**Kural (sıra değişmez):** K1 → K2 → K3 → K4. Sıralamanın gerekçesi **maliyet**tir:
K1 yirmi satırdır ve tek başına çoğu soruyu çözer (`Dosya: .ai/RAG.md · Satır: 71` — "20 satır
— 2026-10-14 disk ölçümü"); K2 yüzlerce satırda keyword taraması ister; K3 dosya adı biliniyorsa
işe yarar, konu biliniyorsa zayıftır; K4 en pahalısıdır (token) ve en sona bırakılır
(`Dosya: .ai/keys.md · Satır: 579` — "Rastgele okuma yasak … Token asimina yol acar").

**Karışıklık uyarısı:** `RAG.md §3` ile `keys.md §3` (L0-L6 Layer Keywords) **aynı şey değildir**
(`Dosya: .ai/keys.md · Satır: 340-342`). Karar ağaçlarındaki "§3" daima **hangi dosyanın §3'ü**
olduğu yazılarak kullanılır.

## §4. Soru Gelir → Tarama Sırası (adım adım)

```text
[0] Soruyu cümlelere ayır → 3-5 keyword çıkar (soru tipi + tahmin edilen domain)
[1] Boot'ta zaten okunan dosyaları kontrol et (kök AGENTS.md · kök CLAUDE.md · .ai/AGENTS.md)
      → cevap oradaysa KAPA: künye yaz, taramayı başlatma (token israfı yasak)
[2] K1: .ai/RAG.md §3.1 tablosunu tara (20 satır)
      → konu sınıfı eşleşti → hedef dosya → [4]
[3] K2: .ai/keys.md keyword taraması (§2-§10) + §11 Decision Tree + §12.3 P0-P3
      → hedef çıktı → [4]    · çıkmadı → [3b]
[3b] K3: .ai/index.md master katalogda dosya/ADR adı ara
      → çıktı → [4]          · çıkmadı → [5]
[4] K4: hedef dosyayı doğrudan `@` ile OKU (özetleme yok, künyeli alıntı)
      → cevap + `Dosya:`/`Satır:` künyesi → [7]
[5] Fallback zinciri (§7): CLAUDE.md §16 boot listesi → genel glob/grep → durum işaretle
[6] Hiçbir şey bulunmadıysa: `UNKNOWN` veya `⚠️ VERIFICATION REQUIRED` (§8) + kullanıcıya tek soru
[7] Kapanış listesi (§29) → raporla → DUR
```

**Adım [1] kanıtı:** boot zinciri dört satırlık bağlayıcı bir tablodur
(`Dosya: kök AGENTS.md · Satır: 273-282`); 4. satır "`@.ai/` diğer dosyalar (CLAUDE/WORKFLOW/RAG/…)
— **Yalnız ihtiyaç anında**" der (`Dosya: kök AGENTS.md · Satır: 282`). Yani **RAG.md zaten
boot'ta değildir**; on-demand açılır.

## §5. Karar Ağacı (soru tipi → ilk durak → fallback → sonuç)

| # | Soru tipi (girdi) | İlk durak | Eşleşmezse | Sonuç |
|---|---|---|---|---|
| 1 | "Boot sırası / kanonik okuma listesi" | K1 → `CLAUDE.md §16` (Satır: 628-646) | K4 doğrudan okuma | 13 kanonik liste |
| 2 | "Guardrail / yasak / anayasa" | K1 → `.ai/CLAUDE.md §7` | K2 keyword `guardrail` | kural metni + künye |
| 3 | "Agent routing / handover / persona" | K1 → `.ai/AGENTS.md §6 §6.1` | K2 `agent, routing` | routing tablosu |
| 4 | "Süreç / faz / hard gate" | K1 → `.ai/WORKFLOW.md §8-§13` | K2 `faz, gate` | süreç adımı |
| 5 | "Vault envanteri / dizin yapısı" | K1 → `.ai/CONTEXT.md` | K2 `envanter, dizin` | envanter satırı |
| 6 | "Dosya/ADR listesi, katalog" | K1 → `.ai/index.md` | K2 `katalog, indeks` | katalog satırı |
| 7 | "Keyword eşlemesi aranıyor" | K1 → `.ai/keys.md` | K3 `index.md` | keyword satırı |
| 8 | "Mimari karar (ADR)" | K1 → `.ai/.decisions/index.md` | K2 `adr, karar` | ADR no + frozen okuma |
| 9 | "Şablon seçimi (Guardrail #16)" | K1 → `.ai/.templates/index.md` | K2 `şablon, template` | kalıp A-D |
| 10 | "UI mockup / bileşen / token" | K1 → `ui-design/01-mockup-index.md` | K2 `mockup, token, c01-c16` | mockup gate (okunmadan kod yok) |
| 11 | "DB şema / BCNF / migration" | K1 → `.ai/.sql/` + ADR-040 | K2 `bcnf, şema` | şema dosyası |
| 12 | "Skill var mı?" | Kök `CLAUDE.md §Skill Registry` | K2 `skill, mandate` | skill yüklemek **ilk işlem** |
| 13 | "RAG / embedding / retrieval tasarımı" | K1 → `.ai/RAG.md` §3 + §4 | K2 `rag, retrieval, vektor` (Satır: 58) | §4 = **PLANNED** durumu |
| 14 | "Güvenlik (CSRF/CSP/JWT)" | K1 → ADR-010/011/012/013/022 | K2 §4 Security (Satır: 64-86) | ADR metni |
| 15 | "Ses motoru / DSP / Neva" | K1 → `projects/NevaEngine/` | K2 §7 Audio (Satır: 122) | modül dokümanı |
| 16 | "PHP/backend kod yüzeyi" | K4 `shared/src/` (kod okuma) | K2 `php, pdo, middleware` | kod + künye |
| 17 | "Session hafızası / audit" | K1 → `.ai/MEMORY.md` · `.ai/log.md` | K2 `session, log, audit` | session kaydı |
| 18 | "Terim anlamı" | K1 → `.ai/glossary.md` | K2 `terim, sözlük` | tanım |
| 19 | "Soru bu sınıfların hiçbirinde değil" | — | **K2 → K3 → fallback (§7)** | dürüst durum işareti (§8) |
| 20 | "Sistem/özellik gerçekten var mı?" (durum sorusu) | `RAG.md §3` Durum sütunu | ADR-030 durum tablosu | `IMPLEMENTED` / `PLANNED` / `UNKNOWN` |

**Kaynak tablo:** Dosya: `.ai/RAG.md · Satır: 73-94` (20 satır — başlık `Satır: 71`).
**Boşluk kaydı:** §3.1 tablosunda `#9` numarası **yoktur** (`Satır: 82` → `#8`, `Satır: 83` → `#10`);
bu bilinen numara boşluğudur, **9 numaralı satır uydurulmaz**.

## §6. Her Katmanda Ne Aranır (derinlik tablosu)

| Katman | Arama biçimi | Sütunlar / anahtar | Ortalama maliyet | Dur |
|---|---|---|---|---|
| K1 `RAG.md §3.1` | konu sınıfı eşleşmesi (satır gözlü okuma) | `# · Konu/Soru Sınıfı · Hedef Dosya(lar) · Keyword'ler · Durum` | ~20 satır | en ucuz |
| K1b `RAG.md §3.2` | eşleme kuralı (4 madde) | en dar hedef önce · durum zorunlu · sayı yalnız ölçümle | 4 satır | kural katmanı |
| K2 `keys.md §2-§10` | keyword exact/substring | `Anahtar Kelime → Hedef Dosya` | 763 satır taranır | orta |
| K2b `keys.md §11` | karar ağacı (adalara göre) | `İstenen Bilgi → İlk Kontrol` | 14 satır (`Satır: 602-613`) | hızlı |
| K2c `keys.md §12.3` | öncelik matrisi | P0/P1/P2/P3 okuma sırası | 6 satır (`Satır: 566-571`) | bütçe |
| K3 `index.md` | dosya/ADR adı | katalog satırı | 246 satır | orta |
| K4 hedef dosya | bölüm başlığı (`§`) | başlık + satır aralığı | değişken | en pahalı |

**§ numarası tuzağı (gerçek gözlem):** `keys.md` içinde `§7` iki kez geçer
(`Satır: 168` "Hardware" · `Satır: 207` "AI Architecture"), `§3A`/`§3C`/`§3D` de ikişer kez
(`Satır: 414/496`, `430/460`, `444/479`). Bu yüzden sürücüde **§ numarası tek başına anahtar
değildir** — arama yapılırken **dosya adı + bölüm başlığı** birlikte kullanılır; yalnız § numarasıyla
giden yönlendirme `⚠️ VERIFICATION REQUIRED` sayılır.

## §7. Fallback Zinciri (indeks yanıt vermezse)

| Kademe | Ne denenir | Ne zaman | Çıkış |
|---|---|---|---|
| **F0** | Boot'ta zaten okunan dosyalar (kök `AGENTS.md`, kök `CLAUDE.md`, `.ai/AGENTS.md`) | ilk deneme | yanıt varsa kapa |
| **F1** | `RAG.md §3` + `keys.md` yeniden (daha geniş keyword ile) | K1/K2 tek kelimeyle isabet etmedi | yanıt veya F2 |
| **F2** | **`.ai/CLAUDE.md §16 Boot Protocol` — 13 kanonik dosya taraması** | indekslerde iz yok | dosya listesi (`Satır: 632-646`) |
| **F3** | Genel tarama: `glob` / `grep` (`.ai/` gizli dizin → `hidden: true`) | F2 de boş | aday dosya listesi |
| **F4** | Doğrudan `@` ile en olası 1-3 dosyayı oku | aday var | künyeli alıntı |
| **F5** | **DUR** — `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` + kullanıcıya tek soru | hepsi tükendi | dürüst durum |

**F2 kanıtı:** `.ai/CLAUDE.md §16 Boot Protocol (Canonical Reading List)` başlığı `Satır: 628`,
"**13 kanonik `.ai/` dosyası**" zorunluluğu `Satır: 630`, liste `Satır: 632-646`,
"13 = 14 − 1" sayım zinciri `Satır: 648` (`Dosya: .ai/CLAUDE.md`).
**F3 kanıtı (kritik):** `.ai/` nokta (gizli) dizindir — `hidden: false` ile yapılan glob/grep
`.ai/**` dosyalarını **görmez** ve sahte "0 sonuç" üretir (`Dosya: kök AGENTS.md · §7 adım 2` —
"`.ai/` gizli (nokta) dizin — glob/grep aramasında `hidden: true` kullanılır"). Sürücüde bu,
"sonuç yok" ile "arama yanlıştı" ayrımı olarak **zorunlu** bir adımdır.

**F5 davranışı:** fallback de sonuç vermediyse yanıt **"bulamadım" değil**, şu üç biçimden biridir
(§8): `UNKNOWN` (bilinmeyen değer) · `⚠️ VERIFICATION REQUIRED` (doğrulanamayan iddia) ·
tek kısa soru (belirsiz istek).

## §8. Eksik Kanıt Durumu — `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` Davranışı

| # | Senaryo | Sürücü davranışı | Yasak |
|---|---|---|---|
| 1 | İndekste satır var, hedef dosya diskte yok | Hayalet satır → satır **silinir/raporlanır** (`RAG.md §3.2` kural 1 · Satır: 102) | Hedefi "varmış gibi" yazmak |
| 2 | Dosya var ama içerik iddiası doğrulanamadı | `⚠️ VERIFICATION REQUIRED` + `Dosya:` künyesi eksik bırakılır | Kanıtsız iddia cümlesi |
| 3 | Değer bilinmiyor (ör. sayım yapılmadı) | `UNKNOWN` | Tahmini sayı yazmak |
| 4 | Durum etiketi belirsiz (`IMPLEMENTED` mı?) | `Durum` sütunu `UNKNOWN` (`RAG.md §3.2` kural 3 · Satır: 104) | Kanıtsız `IMPLEMENTED` (Satır: 134) |
| 5 | Dış URL/ API doğrulanamadı | `⚠️ VERIFICATION REQUIRED` | "çalışıyor" demek |
| 6 | Sayı/istatistik iddiası | Yalnız ölçümle yazılır (Satır: 105) | Bellekten sayı |
| 7 | Boot listesiyle çelişen bulgu | Çelişki → **DUR** + üst karar (boot `Satır: 648` sayım zinciri) | Sayım zincirini bozmak |
| 8 | Repo kodu ile ADR etiketi çelişiyor | İki kanıt da yazılır + `⚠️ VERIFICATION REQUIRED` (§21, §30/Q3) | Birini sessizce yok saymak |

**Şablon cümleler (kullanım hazır):**

```text
İddia doğrulanamadı — ⚠️ VERIFICATION REQUIRED (Dosya/Satır künyesi yok).
Bu değer ölçülmedi — UNKNOWN (2026-10-17 ölçümü çalıştırılmadı).
İndeks satırı hedefi diskte bulunamadı — hayalet satır raporlandı (RAG.md §3.2/1).
Durum sütunu PLANNED — kod kanıtı yok (RAG.md §5/2).
```

## §9. Durum Etiketleri Sözlüğü

| Etiket | Anlamı | Kanıtı | Kaynak künye |
|---|---|---|---|
| `IMPLEMENTED` | Dosya diskte ve içerik kanıtlı | Test-Path / glob / ReadAllLines | `Dosya: .ai/RAG.md · Satır: 104` |
| `PLANNED` | ADR/plan var, kod/ödeme yok | ADR metni | `Dosya: .ai/RAG.md · Satır: 104, 111` |
| `UNKNOWN` | Değer bilinmiyor | — (kanıt yok) | `Dosya: .ai/RAG.md · Satır: 104` |
| `⚠️ VERIFICATION REQUIRED` | İddia var, doğrulama yok | doğrulanana kadar açık kalır | `Dosya: .ai/.decisions/.../ADR-030 · Satır: 107` |
| `DUR` | Yasak yüzey/çelişki tespit edildi | iş durur, üst karar | `Dosya: .ai/AGENTS.md · §18 Warnings` |

> **Not (kanıt disiplini):** `Dosya: .ai/.decisions/.../ADR-030-ai-strategy-core.md · Satır: 107`
> "Kod kanıtı olmayan her iddia etiketli … `⚠️ VERIFICATION REQUIRED` / `PLANNED`" der — bu
> sürucünün §8 tablosunun ADR tarafındaki karşılığıdır.

## §10. Pipeline `PLANNED` İken Günlük Davranış (bugünkü manuel retrieval)

`RAG.md §4` bileşen tablosunda **tek `IMPLEMENTED` satırı manuel keyword indeksidir**
(`Dosya: .ai/RAG.md · Satır: 116`); chunk `PLANNED` (Satır: 117), embedding adayı `PLANNED` (Satır: 118),
vektör store `PLANNED` (Satır: 119), arama sırası `PLANNED` (Satır: 120), güncelleme tetikleyicisi
`PLANNED` (Satır: 121), kod kapsamı `.ai/**/*.md` (Satır: 122).

Günlük altı davranış:

| # | Bugün yapılan | Karşılığı (gelecekte) | Kanıt |
|---|---|---|---|
| 1 | `RAG.md §3` tablosu gözle tarama | index sorgusu | Satır: 116 |
| 2 | `keys.md` keyword arama (exact/BM25 ruhu) | BM25 katmanı | Satır: 120 (ilk ikisi "bugün el ile yapılır") |
| 3 | Hedefi `@` ile okuma + künye | retrieve + augment | Satır: 122 (kod kapsam dışı) |
| 4 | § bazlı bölüm okuma | chunk (`§`-bazlı, ~500 token) | Satır: 117 |
| 5 | İndeks güncelleme (elle ekleme) | tetikleyici otomasyonu | Satır: 121 |
| 6 | Durum sütunu elle güncelleme | metadata senkronu | Satır: 104 |

**Günlük yasak:** "Sistem var" sanmak. `Dosya: .ai/RAG.md · Satır: 124` (eli10): *"Otomatik arama
sistemi henüz kurulmadı; bugün arama, bu dosyadaki liste ve keyword tablosuyla elle yapılıyor."*

## §11. `PLANNED` → `IMPLEMENTED` Geçiş Kapısı (otomasyona hazırlık)

| # | Geçiş koşulu | Kanıt sütununa ne yazılır | Bugün |
|---|---|---|---|
| 1 | Chunk kodu yazıldı | dosya yolu + satır aralığı | `PLANNED` |
| 2 | Embedding modeli **seçildi** (ADR kararı) | ADR no + tablo | `PLANNED` (karar yok — Satır: 118) |
| 3 | Vektör store seçildi | şema + tablo kanıtı | `PLANNED` (adaylar değerlendirilmedi — Satır: 119) |
| 4 | Arama sırası kodlandı (exact → BM25 → vektör) | fonksiyon künyesi | `PLANNED` (Satır: 120) |
| 5 | Güncelleme tetikleyicisi otomatikleşti | tetikleyici betiği | `PLANNED` (Satır: 121) |
| 6 | `RAG.md §4` satırı yeniden ölçüldü | ölçüm tarihi | kapı: `§6` kontrol 4 (Satır: 149) |

**Kural:** tek bir bileşen bile `IMPLEMENTED` olmaya başlarsa `RAG.md §4` başlığındaki
"ÇOĞU PLANNED" ibaresi de yeniden değerlendirilir (`Dosya: .ai/RAG.md · Satır: 109`);
başlık ile satır çelişirse **DUR** (§8/7).

## §12. Güncelleme Akışı — Yeni Vault Dosyası → §3 Satırı

```text
[A] Yeni dosya üretilir (.ai/** altına .md — Guardrail #16 şablon zorunlu)
[B] Konu sınıfı belirlenir (hangi soru bu dosyayı çözer?)
[C] RAG.md §3.1'e YENİ SATIR eklenir: # · Konu · Hedef · Keyword · Durum
[D] Satır doğrulanır: Test-Path / glob → diskte yoksa satır yazılmaz (§3.2/1)
[E] Durum sütunu yazılır: IMPLEMENTED (kanıt var) / PLANNED / UNKNOWN (§3.2/3)
[F] Sayılar ölçülür ve ölçüm tarihiyle yazılır (§3.2/4) — bellekten sayı YASAK
[G] keys.md'e keyword satırı var mı bakılır (varsa güncelle, yoksa ekle)
[H] Kök boot listesine EKLENMEZ (§14) · vault-sync-post ile §3 yeniden ölçülür
```

| Adım | İhlal sonucu | Kaynak künye |
|---|---|---|
| D | Hayalet satır silinir | `Dosya: .ai/RAG.md · Satır: 102` |
| E | Durumsuz satır geçersiz | `Dosya: .ai/RAG.md · Satır: 104` |
| F | Zero-Hallucination ihlali | `Dosya: .ai/RAG.md · Satır: 105` |
| H | İndeks bayatlar / boot bozulur | `Dosya: .ai/RAG.md · Satır: 135-136` |

**Tetikleyici:** `vault-sync-post` skill'i — "Vault dosya ekleme/taşıma işleminden sonra §3 yeniden
ölçülür" (`Dosya: .ai/RAG.md · Satır: 135`, §5 kural 3).

## §13. Taşıma / Silme / Yeniden Adlandırma Akışı

| Olay | Sürücü adımı | Kontrol |
|---|---|---|
| Dosya taşındı | §3.1'deki `Hedef Dosya(lar)` hücresi güncellenir | `Test-Path` yeniden |
| Dosya silindi | Satır silinir (hayalet satır kalmaz) | glob = 0 isabet |
| Dosya yeniden adlandırıldı | Wiki-link hedefleri + `keys.md` keyword satırı birlikte | `.ai/scripts/wiki-link-check.ps1` **kökten** |
| Bölüm (`§`) yeniden numaralandı | Bu dosyadaki `Dosya:/Satır:` künyeleri tazelenir | yeniden ölçüm |
| Klasör kapatıldı (ör. kademe-3) | `parent`/`children` FM alanları güncellenir | FM 10 alan |

**Dikkat (bu üretim turunda gözlenen):** `.ai/architecture/K015-AI/rag/index.md` `children: []`
taşır ve "Kademe-3 bu fazda AÇILMADI" yazar (`Dosya: .../rag/index.md · Satır: 6`); bu dosya
kademe-3 (`node_type: module`) olarak eklendiği için **ebeveyn `children` listesi orkestratör
tarafından güncellenmelidir** (§30/Q2).

## §14. Boot İlişkisi — Boot'a Eklenmez Kuralı

| # | Kural | Kanıt |
|---|---|---|
| 1 | `RAG.md` boot listesine eklenmez — **on-demand referanstır** | `Dosya: .ai/RAG.md · Satır: 47` |
| 2 | Boot `§16` listesi bu dosyayla **değiştirilemez** (13 kanonik sabit) | `Dosya: .ai/RAG.md · Satır: 136` |
| 3 | Boot zinciri bağlayıcı 4 satır; `@.ai/` dosyaları "yalnız ihtiyaç anında" | `Dosya: kök AGENTS.md · Satır: 273-282` |
| 4 | 13 kanonik liste `.ai/CLAUDE.md` içinde yaşar (13 = 14 − 1) | `Dosya: .ai/CLAUDE.md · Satır: 630, 648` |
| 5 | Boot listesi değiştiyse kapı: `RAG.md §6` kontrol 8 | `Dosya: .ai/RAG.md · Satır: 153` |

**Sürücü ile boot ilişkisi:** sürucü **boot'a girmez**; sürucü, boot'tan **sonra** bir soru
geldiğinde açılır. Boot'ta olan dosyalar (kök `AGENTS.md`, kök `CLAUDE.md`, `.ai/AGENTS.md`) zaten
hafızada olduğu için §4 adım [1] orada **kapanır** — ayrı tarama yapılmaz.

## §15. Hata Modları

| # | Hata | Semptom | Tespit | Tepki |
|---|---|---|---|---|
| 1 | Hayalet indeks satırı | Hedef dosya açılamıyor | `Test-Path` false | Satırı sil/raporla (§12/D) |
| 2 | Yanlış kademede arama | Boş sonuç ama cevap vault'ta var | K1'e dönmeden K4'e atlanmış | Sırayı sıfırla (§3) |
| 3 | Gizli dizin unutulmuş | `.ai/**` için sahte "0 sonuç" | `hidden: true` kullanılmadı | Taramayı `hidden: true` ile tekrar |
| 4 | § numarası yanlış eşleşti | `keys.md §3` ↔ `RAG.md §3` karışması | Dosya adı yazılmamış | Dosya+başlık birlikte (§6) |
| 5 | Boot'tan kaçan gereksiz okuma | Token şişmesi | §4 adım [1] atlanmış | Adıma geri dön |
| 6 | Kanıtsız `IMPLEMENTED` | "Var" sanılan sistem yok | Durum sütunu boş/karışık | `UNKNOWN`/`PLANNED` (§9) |
| 7 | Bellekten sayı/istatistik | Ölçüm tarihi yok | `RAG.md §3.2/4` ihlali | Ölç ve tarihle (§17) |
| 8 | `.ai/` içinden gate betiği | 19 sahte "PNG yok" gibi yanlışlık | Kök kuralı ihlali | **Kökten** çalıştır |
| 9 | Boot sayım zinciri bozulma | 13 ≠ 14 − 1 | `CLAUDE.md §16` kontrolü | DUR + geri al |
| 10 | Çelişen iki kanıt (ADR ↔ disk) | Etiket farkı | ikisi de künyeli | İki kanıt + `⚠️ VERIFICATION REQUIRED` (§21) |
| 11 | 3 başarısız düzeltme turu | aynı hata tekrarı | tur sayacı | DUR — şüpheli varsayımı yaz, üst karar iste |

## §16. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem / komut |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz (aşağıda) | `Select-String` sayacı (§19) |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` (§17) |
| 3 | Authority son satır | birebir | `§19` eşitlik kontrolü |
| 4 | `§3` hedefleri | her satırda dosya diskte | `Test-Path` döngüsü (§18) |
| 5 | Durum sütunu | her satır dolu | gözden geçirme (`RAG.md §6/3`) |
| 6 | Placeholder | şablon değişkeni (üçlü sürgü ile `\{`+`{`) yok | `Select-String -Pattern '\{\{'` → 0 |
| 7 | Wiki-link | hedefler diskte | `.ai/scripts/wiki-link-check.ps1` (kökten) |
| 8 | ADR tutarlılığı | `PLANNED` satırları ADR-030 ile çelişmiyor | ADR-030 okuma (`RAG.md §6/4`) |
| 9 | Boot listesi | 13 kanonik değişmedi | `Dosya: .ai/CLAUDE.md · Satır: 632-646` |
| 10 | Kapsam dışı yazım | 0 dosya | whitelist sayacı (yalnız bu dosya) |

> Kapı 1-3 bu dosyanın kendi üretim kapısıdır (§32); kapı 4-10 `RAG.md §6 Doğrulama`
> ile uyumludur (`Dosya: .ai/RAG.md · Satır: 142-155`).

## §17. Ölçüm Komutları — PowerShell (satır sayısı kapısı)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden (`C:\www\coremusic.net`) çalışır;
> çıkan sayılar 2026-10-17 ölçümüdür.

```powershell
# A) Temel kapı: satır sayısı (UTF8, BOM farkından bağımsız)
$files = @('.ai\RAG.md','.ai\keys.md','.ai\index.md','.ai\CLAUDE.md','AGENTS.md',
           '.ai\architecture\K015-AI\rag\rag-surucu.md')
foreach ($f in $files) {
    $p = Join-Path (Get-Location) $f
    if (Test-Path $p) {
        $n = [IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8).Count
        "{0} => {1}" -f $f, $n
    } else {
        "{0} => MISSING" -f $f
    }
}
```

**2026-10-17 ölçüm çıktısı (gerçek):**

| Dosya | Satır |
|---|---|
| `.ai\RAG.md` | 177 |
| `.ai\keys.md` | 763 |
| `.ai\index.md` | 246 |
| `.ai\CLAUDE.md` | 987 |
| `AGENTS.md` (kök) | 285 |
| `...agents\surucu.md` (yapı referansı) | 507 |
| `shared\src\AI\KnowledgeBase.php` | 232 |

```powershell
# B) Bu dosyanın üretim kapısı (>=500 + Authority + FM 10 alan tek seferde)
$p = '.ai\architecture\K015-AI\rag\rag-surucu.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir    => {0}   (kapı >=500: {1})" -f $lines.Count, ($lines.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority=> {0}" -f ($lines[$lines.Count - 1] -eq $auth)
$pat = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10    => {0}" -f ((Select-String -Path $p -Pattern $pat).Count -eq 10)
```

## §18. Ölçüm — İndeks Satırı Doğrulama Betiği (§3 hayalet satır taraması)

> ✅ **Çalıştırılabilir PowerShell betiği** — `RAG.md §3.1` hedeflerinin diskte olduğunu kanıtlar
> (kapı 4).

```powershell
# C) RAG.md §3.1 hedefleri (2026-10-17 tablosundaki disk hedefleri) - hayalet satır taraması
$targets = @(
  '.ai\CLAUDE.md', '.ai\WORKFLOW.md', '.ai\AGENTS.md', '.ai\CONTEXT.md',
  '.ai\index.md', '.ai\keys.md', '.ai\brain.md', '.ai\glossary.md',
  '.ai\MEMORY.md', '.ai\log.md', '.ai\VISION.md', '.ai\PROJECTS.md',
  '.ai\.decisions\index.md', '.ai\.templates\index.md', '.ai\RAG.md'
)
$ghost = 0
foreach ($t in $targets) {
    $ok = Test-Path $t
    if (-not $ok) { $ghost++ }
    "{0,-45} => {1}" -f $t, $(if ($ok) { 'OK' } else { 'GHOST' })
}
"sonuc => ghost={0} (kapı: 0)" -f $ghost

# D) Gizli dizin kuralı: .ai/** araması hidden:true ile yapılır
$hit = Get-ChildItem -Path '.ai' -Recurse -Filter 'RAG.md' -Force |
       Select-Object -ExpandProperty FullName
"hidden arama isabet => {0}" -f $hit.Count
```

```powershell
# E) Durum sütunu + placeholder denetimi (kapı 5 ve 6)
$r = '.ai\RAG.md'
"PLANNED sayisi  => {0}" -f (Select-String -Path $r -Pattern 'PLANNED').Count
"UNKNOWN sayisi   => {0}" -f (Select-String -Path $r -Pattern '\bUNKNOWN\b').Count
"placeholder hit  => {0} (kapı: 0)" -f (Select-String -Path $r -Pattern '\{\{').Count
```

## §19. Ölçüm — FM / Authority / Kapsam Sayacı (kapanış sayacı)

> ✅ **Çalıştırılabilir PowerShell betiği** — §16 kapı 1-3 ve §22 whitelist sayacı.

```powershell
# F) Kapsam: bu üretimde yazılan dosya tek olmalı (kapsam dışı yazım = 0)
$changed = git status --porcelain -- '.ai/architecture/K015-AI/rag/'
"degisen => {0}" -f $changed.Count
$changed | ForEach-Object { "  {0}" -f $_ }

# G) FM alan adları tek tek (yanlış yazım / fazla alan tespiti)
$fields = 'node_id','node_type','status','parent_id','children',
          'depends_on','evidence','validation','open_questions','group'
$fmEnd = 0
$lines  = [IO.File]::ReadAllLines('.ai\architecture\K015-AI\rag\rag-surucu.md',
                                  [Text.Encoding]::UTF8)
for ($i = 0; $i -lt $lines.Count; $i++) { if ($lines[$i] -eq '---' -and $i -gt 0) { $fmEnd = $i; break } }
$present = 0
foreach ($f in $fields) { if ($lines[0..$fmEnd] -match ("^" + $f + ":")) { $present++ } }
"fm alan => {0}/10   fm satir => 0..{1}" -f $present, $fmEnd
```

## §20. Repo Kanıtı A — PHP Retrieval Çekirdeği (`shared/src/AI/`)

> **Envanter (2026-10-17 ReadAllLines):** `KnowledgeBase.php` 232 · `AIEngine.php` 382 ·
> `AIWorkflow.php` 271 · `ToolCalling.php` 326 · `PromptEngine.php` 325 · `MemorySystem.php` 242 ·
> `AIOrchestrator.php` 210 — **7 dosya** (`Dosya: shared/src/AI/` · dizin listesi).

> ✅ **Repo kanıtı** — aşağıdaki bloklar `shared/src/AI/KnowledgeBase.php` içinden alınmış
> birebir alıntılardır (uydurma yok; satır sonundaki yorumlar künyedir).
> *Görev notu: "illüstratif" uyarısı yalnızca gerçekten illüstratif olan Python/Go/TS bloklarına
> (§22-§24) konmuştur; repo kanıtına "repo kanıtı DEĞİL" etiketi koymak **yanlış etiket** olurdu.*

```php
<?php declare(strict_types=1);                                        // Satır 1
namespace CoreMusic\AI;                                               // Satır 11
class KnowledgeBase implements KnowledgeBaseInterface                  // Satır 23
{
    // search: kategori filtresi + skor + sıralama
    public function search(string $query, int $maxResults = 10,
                           array $filters = []): array                 // Satır 36
    {
        // Basit keyword matching (gerçek implementasyonda vector cosine similarity)
                                                          // Satır 48 (yorum = niyet)
        $score = $this->calculateRelevanceScore($queryLower, $item);   // Satır 49
        usort($results, fn(array $a, array $b) => $b['score'] <=> $a['score']); // Satır 64
        return array_slice($results, 0, $maxResults);                  // Satır 66
    }
```

**Sürücü açısından anlamı:** bugün repo'nun retrieval çekirdeği **keyword matching + skor sıralamasıdır**;
vektör/cosine **yorum satırında** yazılıdır, kodda değildir. ADR-030 aynı bulguyu taşır:
"`KnowledgeBase.php:48` … → **yorum = niyet, kod 0**"
(`Dosya: .ai/.decisions/accepted/ADR-030-ai-strategy-core.md · Satır: 43`).

```php
    // RAG adımı: retrieval -> augmentation -> generation (üç adım, gözle görünür)
    public function ragQuery(string $query, array $context = []): array // Satır 72
    {
        // 1. Retrieval — ilgili belgeleri bul                           // Satır 74
        $retrieved = $this->search($query, 5, $context['filters'] ?? []); // Satır 75
        // 2. Augmentation — prompt'a bağlam ekle                         // Satır 77
        $augmentedContext = $this->augmentContext($query, $retrieved, $context); // Satır 78
        // 3. Generation — LLM ile yanıt üret (şimdilik placeholder)      // Satır 80
        $answer = $this->generateAnswer($query, $augmentedContext);      // Satır 81
        return [
            'answer'     => $answer,                                     // Satır 84
            'sources'    => array_map(fn(array $r) => [...] , $retrieved), // Satır 85-88
            'confidence' => $this->calculateConfidence($retrieved),      // Satır 89
        ];
    }
```

```php
    // Skor ağırlıkları (retrieval kalitesinin bugünkü tüm "modeli")
    if (str_contains($titleLower, $query))  { $score += 0.6; }           // Satır 171-173
    if (str_contains($contentLower, $query)) { $score += 0.3; }          // Satır 176-178
    $score += ($matchCount / count($queryWords)) * 0.1;                  // Satır 191
    // Eksik kanıt / boş sonuç davranışı:
    if (empty($retrieved)) { return 0.0; }                               // Satır 225-226
    return round(min($avgScore * 1.2, 1.0), 2);                          // Satır 230
```

**Sürücü ile bağlantısı (üç satır):**

| Repo davranışı | Sürücüdeki karşılığı | Künye |
|---|---|---|
| `search()` — kategori filtresi + skor + kesme | §6 katman taraması (dar hedef önce) | `shared/src/AI/KnowledgeBase.php · Satır: 44, 66` |
| `ragQuery()` — retrieval→augment→generate 3 adım | §4 adım [4]-[7] (okuma → künye → kapanış) | `.../KnowledgeBase.php · Satır: 74-81` |
| `calculateConfidence()` — boşsa `0.0` | §8 eksik kanıt → `UNKNOWN` (sessiz başarı yok) | `.../KnowledgeBase.php · Satır: 225-226` |

## §21. Repo Kanıtı B — Bağlam/Kayıt Katmanı ve "Bu Alan Kodu YOK" Cümleleri

```php
    // AIOrchestrator — oturum bağlamı (retrieval'ın bağlam girdisi)
    public function getContext(string $sessionId): array                 // Satır 154
    {
        return $this->contexts[$sessionId] ?? [];                       // Satır 156
    }
    public function updateContext(string $sessionId, array $data): bool  // Satır 162
    {
        $this->contexts[$sessionId] = array_merge(
            $this->contexts[$sessionId] ?? [], $data);                   // Satır 164-167
        return true;                                                     // Satır 169
    }
    // Bilinmeyen ajan → açık hata (sessiz yok sayma yok)
    return ['success' => false, 'result' => null,
            'error' => "Unknown agent: {$agentId}"];                     // Satır 128-132
```

```php
    // AIWorkflow — KnowledgeBase bağımlılık enjeksiyonu (iskelet bağlı, kullanım PLANNED)
    private KnowledgeBase $knowledge;                                    // Satır 22
    public function __construct(..., KnowledgeBase $knowledge, ...)      // Satır 27-30
```

**`Dosya: shared/src/AI/AIWorkflow.php · Satır: 22, 30`** · **`Dosya: shared/src/AI/AIOrchestrator.php · Satır: 154-170, 125-133`**
(`MemorySystem` tarafı — `getSession` Satır: 60 · `getPersistent` Satır: 104 · `remember` Satır: 123 ·
`getHierarchy` Satır: 152 · `pruneExpired` Satır: 183 — **imza satırı kanıtı**, gövde okunmadı →
gövde iddiaları `⚠️ VERIFICATION REQUIRED`).

**Negatif kanıt cümleleri (gerçek grep 0/1 isabet — dürüst yazım):**

| İddia | Tarama | Sonuç |
|---|---|---|
| "Repo'da bu alan kodu YOK: **embedding/vektör/cosine uygulaması**" | `cosine\|embedding\|vector` → `shared/src/AI/*.php` | **3 isabet, 3'ü de yorum satırı** (`KnowledgeBase.php:19,48` · `AIEngine.php:233` — `AIEngine.php:234` hemen ardından `return [];`) |
| "Repo'da bu alan kodu YOK: **vault dosyalarını okuyan retrieval katmanı**" | `\.ai/\|RAG\.md\|keys\.md` → `shared/src/*.php` | **1 isabet, o da yorum** (`shared/src/Log/Redactor.php:8`) |
| "Repo'da bu alan kodu YOK: **LLM sağlayıcı çağrısı**" | ikincil kanıt (ADR-030 taraması) | `Dosya: .../ADR-030-ai-strategy-core.md · Satır: 27` — "0 eşleşmedir" |
| "Vault pipeline (chunk/embedding/store) kodu" | `RAG.md §4` durum sütunu | **PLANNED** (`Dosya: .ai/RAG.md · Satır: 117-122`) |

**Çelişki kaydı (kapatılmaz, işaretlenir):** ADR-030 "`shared/src/AI/` instantiate **0**" der
(`Dosya: .../ADR-030 · Satır: 38`), `KnowledgeBase` ise `AIWorkflow` constructor'ına enjekte edilir
(`Satır: 22, 30`). İkisi birden **doğru** olabilir (sınıf tanımlı ama uygulama genelinde
instantiate edilmemiş olabilir) — hangisinin bağlayıcı olduğu **`⚠️ VERIFICATION REQUIRED`**
(`§30/Q3`).

## §22. İllüstratif — Python Retrieval Örneği

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# Vault §-bazlı chunk okuma + ilk-eşleşen bölüm stratejisi (tasarım: RAG.md §4/2 ~500 token)
from pathlib import Path

def load_sections(path: Path) -> dict[str, str]:
    sections, current, buf = {}, "PREAMBLE", []
    for line in path.read_text(encoding="utf-8").splitlines():
        if line.startswith("## "):
            sections[current] = "\n".join(buf).strip()
            current, buf = line.strip(), []
        else:
            buf.append(line)
    sections[current] = "\n".join(buf).strip()
    return sections

def retrieve(path: Path, keywords: list[str], k: int = 3) -> list[tuple[str, float]]:
    secs = load_sections(path)
    scored = []
    for name, body in secs.items():
        hay = (name + " " + body).lower()
        score = sum(0.6 if kw in name.lower() else 0.3 for kw in keywords if kw in hay)
        if score:
            scored.append((name, score))
    return sorted(scored, key=lambda x: -x[1])[:k]
```

*İskelet kaynak: `Dosya: .ai/RAG.md · Satır: 117` (§-bazlı chunk, ~500 token) ·
`Dosya: shared/src/AI/KnowledgeBase.php · Satır: 171-191` (ağırlık şemasının ruhu).*

## §23. İllüstratif — Go Retrieval Örneği

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package retrieval

import (
    "os"
    "sort"
    "strings"
)

// IndexRow, RAG.md §3.1 tablosunun tek bir satırıdır.
type IndexRow struct {
    Topic    string
    Target   string
    Keywords []string
    Status   string // IMPLEMENTED | PLANNED | UNKNOWN
}

// Match: en dar (domain-specific) eşleşme önce kazanır — §3.2/2.
func Match(rows []IndexRow, query string) (IndexRow, bool) {
    q := strings.ToLower(query)
    var best IndexRow
    bestScore := 0
    for _, r := range rows {
        if r.Status == "UNKNOWN" { // durumsuz/kanıtsız satır eşleşmez
            continue
        }
        score := 0
        for _, kw := range r.Keywords {
            if strings.Contains(q, strings.ToLower(kw)) {
                score++
            }
        }
        if score > bestScore {
            best, bestScore = r, score
        }
    }
    sort.SliceStable([]IndexRow{best}, func(i, j int) bool { return bestScore > 0 })
    return best, bestScore > 0
}

func Exists(path string) bool {
    _, err := os.Stat(path)
    return err == nil // hayalet satır kontrolü: Test-Path karşılığı
}
```

## §24. İllüstratif — TypeScript Retrieval Örneği

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type Status = 'IMPLEMENTED' | 'PLANNED' | 'UNKNOWN';

interface IndexRow {
  topic: string;
  target: string[];      // hedef dosya(lar)
  keywords: string[];
  status: Status;
}

/** K1 → K2 → K3 → K4 sırasını zorlayan sürücü çekirdeği. */
export async function retrieve(query: string, rows: IndexRow[]): Promise<string[]> {
  const terms = query.toLowerCase().split(/\s+/).filter((t) => t.length > 3);

  // K1: en dar eşleşme; UNKNOWN satırlar yanıt sayılmaz (eksik kanıt davranışı §8)
  const hits = rows
    .filter((r) => r.status !== 'UNKNOWN')
    .map((r) => ({ r, score: r.keywords.filter((k) => terms.includes(k.toLowerCase())).length }))
    .filter((x) => x.score > 0)
    .sort((a, b) => b.score - a.score);

  if (hits.length > 0) return hits[0].r.target;

  // Fallback zinciri §7: F1 (geniş keyword) → F2 (boot listesi) → F5 ( dürüst durum )
  return ['UNKNOWN: ilgili indeks satırı bulunamadı — ⚠️ VERIFICATION REQUIRED'];
}
```

## §25. Hızlı Eşleme — Soru Sınıfı → İlk Dosya (RAG §3.1 taşıması)

> Bu tablo §5 karar ağacının **hızlı kopyasıdır**; ayrışma olursa `Dosya: .ai/RAG.md · Satır: 73-94`
> kazanır. (Satırlar bugün okunarak alınmıştır; uydurma satır yoktur.)

| Konu sınıfı | Hedef dosya | Durum |
|---|---|---|
| Boot sırası, kanonik okuma listesi | `.ai/CLAUDE.md §16` · kök `WORKFLOW` boot | IMPLEMENTED |
| Guardrail, yasak, anayasa | `.ai/CLAUDE.md §7 · §21 · §23` | IMPLEMENTED |
| Agent routing, handover, persona | `.ai/AGENTS.md §4 §6 §15` · kök `AGENTS.md` | IMPLEMENTED |
| Süreç, faz, hard gate, ADR lifecycle | `.ai/WORKFLOW.md §8-§13` | IMPLEMENTED |
| Vault envanteri, dizin yapısı | `.ai/CONTEXT.md` | IMPLEMENTED |
| Master katalog, dosya/ADR listesi | `.ai/index.md` | IMPLEMENTED |
| Keyword routing eşlemesi | `.ai/keys.md` | IMPLEMENTED |
| Mimari kararlar (ADR) | `.ai/.decisions/index.md` · `.ai/brain.md` | IMPLEMENTED |
| Şablon seçimi (Guardrail #16) | `.ai/.templates/index.md` | IMPLEMENTED |
| UI mockup, bileşen, token, ekran spec | `ui-design/01-mockup-index.md` · `02-component-inventory.md` | IMPLEMENTED |
| DB şema, BCNF, migration | `.ai/.sql/` · brain ADR-040 | IMPLEMENTED |
| Skill registry ve eşleşme kuralı | kök `CLAUDE.md §Skill Registry` | IMPLEMENTED |
| Vizyon, kapsam, yetenekler | `.ai/VISION.md` · `.ai/PROJECTS.md` | IMPLEMENTED |
| Terimler | `.ai/glossary.md` | IMPLEMENTED |
| Session hafızası, audit trail | `.ai/MEMORY.md` · `.ai/log.md` | IMPLEMENTED |
| PHP/backend kod yüzeyi | `shared/src` · alt alan adı `*/include/` | IMPLEMENTED |
| AI stratejisi, RAG/embedding kararı | `ADR-030-ai-strategy-core` | **PLANNED** |
| Güvenlik: CSRF/CSP/JWT/session | ADR-010 · 011 · 022 · skill `security-hardening` | IMPLEMENTED |
| Class AB donanım, PCB, güç, termal | ADR-089 | IMPLEMENTED |
| Ses motoru, mixer, EQ/DSP | `projects/NevaEngine/` · `brain §7.4` | IMPLEMENTED |

**Olmayanlar (uydurulmaz):** `.ai/wiki/` (kök `AGENTS.md §7`'de anılır, diskte **0 dosya**) ·
`.ai/.rules/error-recovery.md` (diskte YOK) — `Dosya: .ai/RAG.md · Satır: 96`.

## §26. Token / Okuma Ekonomisi

| # | Kural | Sonuç | Kanıt |
|---|---|---|---|
| 1 | Toplu okuma yasak — boot'ta tüm vault okunmaz | token bütçesi korunur | `Dosya: kök AGENTS.md · Satır: 282` |
| 2 | K1 → K4 sırası (ucuzdan pahalıya) | ortalama okuma sayısı düşer | §3 bu dosya |
| 3 | Boot'ta olan dosya yeniden okunmaz (§4 adım [1]) | çift israf yok | §14 |
| 4 | Rastgele okuma yasak — keys kullan | asimetrik tarama yok | `Dosya: .ai/keys.md · Satır: 579` |
| 5 | Öncelik matrisi P0→P3 (`Satır: 566-571`) | sıralı bütçe | `Dosya: .ai/keys.md` |
| 6 | §-bazlı okuma (hedef ~500 token chunk) | gereksiz bölüm okuma yok | `Dosya: .ai/RAG.md · Satır: 117` |

## §27. Yasaklar ve Sınırlar

| # | Yasak | Neden |
|---|---|---|
| 1 | İndeks satırına **olmayan dosya** yazmak | Yanlış hedef → isabetsiz analiz (`RAG.md §3.2/1`) |
| 2 | Kanıtsız `IMPLEMENTED` etiketi | "Var" sanılan sistem aslında yok (`RAG.md §5/2` · Satır: 134) |
| 3 | Boot `§16` listesini bu dosyayla değiştirmek | 13 = 14 − 1 sayım zinciri bozulur (`RAG.md §5/4` · Satır: 136) |
| 4 | `.env` / credential anahtarını satıra almak | Sızıntı (`RAG.md §5/5` · Satır: 137) |
| 5 | Markdown path linki kullanmak (wiki-link yerine) | Link denetimi çalışmaz (`RAG.md §5/6` · Satır: 138) |
| 6 | `.ai/` içinden gate betiği çalıştırmak | Sahte başarısızlıklar (kök `AGENTS.md §13.6`) |
| 7 | `git commit` atmak | **Orkestratöre aittir** (subagent yasak) |
| 8 | Bu dosya dışına yazmak / dosya adını değiştirmek | Kapsam dışı yazım = ihlal (§19/F) |
| 9 | `RAG.md §3` tablosunu **taşımadan** dosya taşımak | İndeks bayatlar → hayalet satır |

## §28. Sık Yapılan Hatalar (anti-pattern)

| # | Yanlış | Doğru | Kaynak |
|---|---|---|---|
| 1 | "Sistem var" (embedding kurulu sanmak) | `PLANNED` de, bugün elle | `RAG.md Satır: 111, 124` |
| 2 | `.ai/` gizli olmadığı için arama | `hidden: true` / doğrudan path | kök `AGENTS.md §7/2` |
| 3 | `keys.md §3` ile `RAG.md §3`'ü karıştırmak | dosya adıyla an (`Satır: 342`) | `Dosya: .ai/keys.md` |
| 4 | § numarasıyla tek başına yönlenmek | dosya + bölüm başlığı (§6 tuzağı) | §6 bu dosya |
| 5 | Bellekten satır/sayı yazmak | `[IO.File]::ReadAllLines` ile ölç | `RAG.md §3.2/4` |
| 6 | Boş sonucu "dosya yok" saymak | Önce aramanın `hidden`/yolunu sorgula | §15/3 |
| 7 | Boot listesine `RAG.md` eklemek | On-demand kalır | `RAG.md Satır: 47, 136` |
| 8 | Kanıtsız "bitti" | Ölçüm + kapı tablosu (§16) | §29 |

## §29. Kapanış Kontrol Listesi (her retrieval görevi sonunda)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | Sıra K1→K2→K3→K4 izlendi mi (boot'a gömülü olan atlandı mı)? | evet / gerekçesi |
| 2 | Her iddia `Dosya:` + `Satır:` künyeli mi? | evet |
| 3 | Bulunamayan için `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` yazıldı mı? | evet |
| 4 | Ölçüm yapıldı mı (§17-§19 betikleri)? | evet |
| 5 | Durum sütunu dolu mu (`IMPLEMENTED`/`PLANNED`/`UNKNOWN`)? | evet |
| 6 | Kapsam dışı dosyaya yazıldı mı? | **hayır (0)** |
| 7 | `git commit` atıldı mı? | **hayır (orkestratöre ait)** |
| 8 | Boot listesi değişti mi? | **hayır (13 sabit)** |

## §30. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [rag-anayasa]` — bu üretim turunda `rag/` altında yalnız `index.md` diskteydi (glob: 1 dosya). `rag-anayasa` paralel üretiliyor mu? | `⚠️ VERIFICATION REQUIRED` |
| Q2 | Ebeveyn `rag/index.md` `children: []` ve "Kademe-3 bu fazda AÇILMADI" diyor (`Satır: 6`). Bu modül eklendikten sonra `children` güncellenecek mi? | orkestratör kararı |
| Q3 | ADR-030 "instantiate 0" (`Satır: 38`) ↔ `AIWorkflow` constructor enjeksiyonu (`Satır: 22, 30`) — hangisi bağlayıcı? | `⚠️ VERIFICATION REQUIRED` (§21) |
| Q4 | `RAG.md §3.1` tablosunda `#9` numarası yok (`Satır: 82-83`) — bilinen boşluk mu, eksik satır mı? | bilinmiyor — **uydurulmaz** |
| Q5 | `keys.md` § numaraları tekrarlı (§7, §3A, §3C, §3D) — normalize edilecek mi? | bilgi: §6, düzeltme yetkisi MO |
| Q6 | Pipeline `PLANNED` iken bu sürücü otomasyon (script) olacak mı, yoksa kılavuz mu kalacak? | ADR-030 §5.1/6 karar adımı |

## §31. Kaynaklar

| Kaynak | Yol | Kullanım amacı | Ölçüm |
|---|---|---|---|
| Retrieval indeksi + pipeline | `.ai/RAG.md` | §3 tarama sırası, §4 durum, §5-§6 kurallar/kapılar | 177 satır |
| Keyword haritası | `.ai/keys.md` | §2 keyword tablosu, §11 karar ağacı, §12.3 P0-P3, §15 uyarılar | 763 satır |
| Boot zinciri | `AGENTS.md` (kök) §14 | Boot sırası + "yalnız ihtiyaç anında" (Satır: 273-282) | 285 satır |
| Boot protokolü (13 kanonik) | `.ai/CLAUDE.md` §16 | Fallback F2 (Satır: 628-648) | 987 satır |
| AI strateji ADR'si | `.ai/.decisions/accepted/ADR-030-ai-strategy-core.md` | `PLANNED (kod 0)` kanıtı (Satır: 43, 305) | — |
| Master katalog | `.ai/index.md` | K3 katmanı | 246 satır |
| Ebeveyn düğüm | `.ai/architecture/K015-AI/rag/index.md` | Kapsam sınırı + `children` durumu (Satır: 6, 14, 27) | 48 satır |
| Yapı referansı | `.ai/architecture/K015-AI/agents/surucu.md` | H1 + § + karar ağacı + tablo iskeleti | 507 satır |
| Repo retrieval kodu | `shared/src/AI/KnowledgeBase.php` (7 dosya) | §20-§21 PHP kanıtı | 232 / 382 / 271 / 326 / 325 / 242 / 210 |
| Doğrulama betikleri | `.ai/scripts/*.ps1` (kökten çalışır) | Wiki-link / frontmatter kapıları | §16 |

## §32. Gate (özet)

§1-§32 tamam: ELI15 · kapsam · 4 katman · 8 adımlı akış · 20 satırlık karar ağacı · derinlik
tablosu · 6 kademeli fallback · 8 maddelik eksik kanıt · 5 etiket sözlüğü · günlük 6 davranış ·
6 maddelik geçiş kapısı · 8 adımlı güncelleme · taşıma/silme · boot 5 kural · 11 hata modu ·
10 kapı · PowerShell 5 betik (A-G) · PHP 4 blok · illüstratif 3 blok (Python/Go/TS) ·
20 satır hızlı eşleme · 6 token kuralı · 9 yasak · 8 anti-pattern · 8 soruluk kapanış ·
6 açık soru · 10 kaynak.

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · kapsam dışı yazım **0** ·
`git commit` **0**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
