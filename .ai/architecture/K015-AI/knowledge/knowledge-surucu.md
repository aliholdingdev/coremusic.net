---
node_id: knowledge-surucu
node_type: module
status: active
parent_id: knowledge
children: []
depends_on: [knowledge-anayasa]
evidence:
  - ".ai/RAG.md §3-§4 (retrieval indeksi + PLANNED pipeline, okundu 2026-10-17)"
  - ".ai/AGENTS.md §24.2 + §13 (okuma akışı kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# knowledge-surucu - Bilgi Kullanım Sürucusu

> **Kapsam:** Ajanın vault içindeki bilgiyle **nasıl buluşturulduğu** (arama), yeni bilginin
> **nereye yazılacağı** (yazma), iki kaynağın **çelişmesi durumunda** hangisinin kazandığı (SSOT
> çözümü), kararların **nasıl dondurulduğu** (ADR yazım akışı) ve bilginin **nasıl bayatlamadan
> tutulduğu** (stale knowledge). **Kural koyar değil, sürer:** bağlayıcı kurallar
> `Dosya: .ai/RAG.md · Satır: 129-138` (§5 Kurallar) ve `Dosya: .ai/CLAUDE.md · Satır: 628-648`
> (§16 Boot Protocol) içindedir. Bu dosya, o kuralların **bilgi-yönetimi kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (tüm satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count`
> ile bugün diskten ölçülmüştür — §17). **Erişim ilişkisi:** sürucü [[../rag/rag-surucu]] ile
> **kardeş modüldür** — rag-surucu *bulma'nın*, knowledge-surucu *yönetimin* (bulma + yazma +
> çelişki + tazelik) kılavuzudur; ikisi arasında çelişki çıkarsa **rag-surucu'nun tarama
> sırası** kazanır, bu dosyanın yazma/karar bölümleri geçerlidir.

## §1. ELI15 — Bu sürücü ne işe yarar

Ajan bir bilgiyle üç yerde karşılaşır: **soru gelince arar** (§3-§6), **yeni bilgi üretince
yazar** (§7-§10), **iki kayıt çelişince karar verir** (§11-§12). Üç davranışın da tek bir
otoritesi vardır: **SSOT hiyerarşisi**. Arama sırasında bulamamak "dosya yok" demek değildir —
`⚠️ VERIFICATION REQUIRED` / `UNKNOWN` demektir (§6). Yazma sırasında "nerede tutayım?"
sorusunun cevabı bilginin **türüdür**: karar → ADR, özet/katalog → index, keyword → keys,
süreç → WORKFLOW. Bayat bilgi (stale), tazeliği ölçülmeyen bilgidir; sürükleyici olan sayı
değil, **ölçüm tarihidir** (§13-§14).

> **eli10:** "Bulmak için sırayla bak, yazmak için türüne göre koy, çelişince en üstteki
> kazanır, taze olmayanı işaretle." — bu dört cümle sürücünün tamamıdır.
>
> **eli15:** Bugünkü vault'ta otomatik embedding/vektör boru hattı **yoktur**
> (`Dosya: .ai/RAG.md · Satır: 111` — ADR-030: "RAG / embedding / vektör — ❌ PLANNED (kod 0,
> tablo 0)`; `Dosya: .ai/.decisions/accepted/ADR-030-ai-strategy-core.md · Satır: 305` aynı
> sonucu taşır). Bu yüzden sürücünün tüm arama bölümleri **bugünün elle akışını** tarif eder;
> otomasyona geçiş, `PLANNED` satırlarının `IMPLEMENTED` olmasıyla mümkün olacak ve o gün
> §25'teki kapılar yeniden ölçülecektir. Yazma tarafında ise repo'da **kısmi** bir bilgi
> ömrü-kodlaması vardır: `KnowledgeBase.php` kategori kapısı + 365 günlük lifecycle temizliği
> (`Dosya: shared/src/AI/KnowledgeBase.php · Satır: 25-27, 134-156` — §19) — ama bu kod vault
> dosyalarını değil, bellek-içi diziyi yönetir; vault yazma akışı **elle ve kural tabanlıdır**.

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Bilgi arama akışı A-F + karar ağaçları (§3-§6) | Dosya içeriği özetleri (içerik hedef dosyanın işidir) |
| Bilgi yazma akışı: konum kararı + adım tablosu (§7-§8) | Boot listesinin değiştirilmesi (§24: **değiştirilemez**) |
| ADR yazım akışı + statü yaşam döngüsü (§9-§10) | ADR metinleri (frozen — yalnız okunur) |
| Çelişki çözümü: SSOT hiyerarşisi (§11-§12) | Embedding/vektör **kodunun** yazımı (ADR-030 ayrı görev) |
| Stale knowledge tespiti + tazeleme (§13-§14) | `.ai/` dışı uygulama kodunun bilgi tabanına taşınması |
| Token bütçesi + PowerShell ölçüm kalıpları (§15-§18) | `.env` / credential yüzeyi (hiçbir bölüme girmez) |
| Repo PHP kanıtı + illüstratif 3 blok (§19-§22) | `.ai/` dışı herhangi bir yazım (yasak) |

- **Kullananlar:** bilgi arayan/yaratan her ajan · MO (vault-updater) · Vault Steward (denetim).
- **Ön koşul:** `RAG.md §3` satırları diskte var olan dosyalara gider
  (`Dosya: .ai/RAG.md · Satır: 102`).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; künyesiz iddia yazılmaz (§23).

## §3. Bilgi Arama Akışı A-F (soru → keys → RAG §3 → hedef dosya)

> Sıra, maliyetin ucuzdan pahalıya gidişidir; rag-surucu §3'teki katman sırasıyla (K1→K4)
> **uyumludur** — bu tablo aynı akışın **bilgi-yönetimi gözünden** kaydıdır.

| Adım | Ne yapılır | Kanıt / durak | Hata davranışa düşünce |
|---|---|---|---|
| **A** | Soruyu 3-5 keyword'e böl (soru tipi + tahmin domain) | ajan belleği | 1 kelimeyle tarama yasak (§15/4) |
| **B** | Boot'ta zaten okunan dosyaları kontrol et (kök `AGENTS.md`, kök `CLAUDE.md`, `.ai/AGENTS.md`) | `Dosya: kök AGENTS.md · Satır: 282` ("yalnız ihtiyaç anında") | cevap oradaysa KAPA — tarama başlatma |
| **C** | `RAG.md §3.1` konu→dosya tablosu (20 satır, 2026-10-14 ölçümü) | `Dosya: .ai/RAG.md · Satır: 73-94` | eşleşme yok → D |
| **D** | `keys.md` keyword taraması (§2 Core · §4 Security · §11 karar ağacı · §12.3 P0-P3) | `Dosya: .ai/keys.md · Satır: 44-60, 64-86` | eşleşme yok → E |
| **E** | `index.md` master katalogda dosya/ADR adı ara | `Dosya: .ai/RAG.md · Satır: 80` | eşleşme yok → F-fallback (rag-surucu §7) |
| **F** | Hedef dosyayı `@` ile OKU + `Dosya:`/`Satır:` künyesiyle alıntıla | `Dosya: kök AGENTS.md · Satır: 282` | bulunamadı → `UNKNOWN`/`⚠️ VERIFICATION REQUIRED` + tek soru |

**Adım B kanıtı:** boot zinciri dört satırlık bağlayıcı tablodur (`Dosya: kök AGENTS.md ·
Satır: 273-282`); 4. satır "`@.ai/` diğer dosyalar — **yalnız ihtiyaç anında**" der — yani
**RAG.md zaten boot'ta değildir**, on-demand açılır ve **ikinci kez okunması israftır** (§15/3).

## §4. Karar Ağacı 1 — Soru Tipi → İlk Durak (bilgi arama)

> Tablo, rag-surucu §5 karar ağacının **bilgi-yönetimi eşleşmesidir**; ayrışma olursa
> `Dosya: .ai/RAG.md · Satır: 73-94` kazanır.

| # | Soru tipi | İlk durak | Eşleşmezse | Sonuç |
|---|---|---|---|---|
| 1 | "X kararı neden alındı?" | `.ai/.decisions/index.md` → ilgili ADR | K2 `adr, karar` | ADR no + frozen metin |
| 2 | "Bu özellik gerçekten var mı?" (durum) | `RAG.md §3.1` Durum sütunu | ADR-030 durum tablosu | `IMPLEMENTED`/`PLANNED`/`UNKNOWN` |
| 3 | "Süreç/faz/hard gate" | `.ai/WORKFLOW.md §8-§13` | K2 `faz, gate` | süreç adımı |
| 4 | "Kural/yasak/anayasa" | `.ai/CLAUDE.md §7` | K2 `guardrail, yasak` | kural metni + künye |
| 5 | "Agent routing/handover" | `.ai/AGENTS.md §6 · §6.1` | K2 `agent, routing` | routing tablosu |
| 6 | "Konu hangi dosyada?" | `RAG.md §3.1` (20 satır) | K2 keyword | hedef dosya |
| 7 | "Dosya/ADR listesi" | `.ai/index.md` | K2 `katalog` | katalog satırı |
| 8 | "Terim anlamı" | `.ai/glossary.md` | K2 `terim, sözlük` | tanım |
| 9 | "Session geçmişi/audit" | `.ai/MEMORY.md` · `.ai/log.md` | K2 `session, log` | session kaydı |
| 10 | "Şablon var mı?" | `.ai/.templates/index.md` | K2 `şablon, template` | kalıp A-D |
| 11 | "Son karar güncel mi?" (tazelik) | hedef dosya `updated:` alanı | §14 tazeleme akışı | taze / bayat işareti |
| 12 | "İki kaynak çelişiyor" | §11 SSOT hiyerarşisi | §12 çelişki ağacı | üst sıradaki + çelişki kaydı |
| 13 | "Bu konu vault'ta hiç yok" | — | fallback F0-F5 (rag-surucu §7) | `UNKNOWN` + tek soru |

## §5. Karar Ağacı 2 — Bilgi Türü → Yazma Konumu (yazma öncesi)

| Bilgi türü | Konum | Kalıp | Kanıt |
|---|---|---|---|
| Mimari karar (neden böyle?) | `.ai/.decisions/draft/` → `accepted/` | ADR (§9) | `Dosya: .ai/decisions/index.md` (ADR envanteri) |
| Karar özeti / çapraz referans | `.ai/brain.md` | §'bazlı kaydetme | `Dosya: .ai/RAG.md · Satır: 82` |
| Yeni konu→dosya eşlemesi | `RAG.md §3.1` satırı | `# · Konu · Hedef · Keyword · Durum` | `Dosya: .ai/RAG.md · Satır: 73` |
| Yeni keyword | `keys.md` ilgili § tablosu | `Anahtar Kelime → Hedef Dosya` | `Dosya: .ai/keys.md · Satır: 44` |
| Katalog kaydı | `index.md` | katalog satırı | `Dosya: .ai/RAG.md · Satır: 80` |
| Süreç adımı / faz | `.ai/WORKFLOW.md` | faz + hard gate tablosu | `Dosya: .ai/WORKFLOW.md · Satır: 55-68` |
| Kural / yasak | `.ai/CLAUDE.md` (§7 guardrail) | guardrail satırı | `.ai/CLAUDE.md` (SSOT — §11) |
| Session bilgisi | `.ai/MEMORY.md` | session kaydı | `Dosya: .ai/RAG.md · Satır: 89` |
| Denetim izi (ne yapıldı) | `.ai/log.md` (**append-only**) | tek satır giriş | `Dosya: .ai/AGENTS.md · §5 (append-only)` |
| Geçici analiz / rapor | `.ai/reports/` | rapor dosyası | `Dosya: .ai/AGENTS.md · §24.3 (QA → .ai/reports/)` |
| Uygulama kodu içi bilgi | `shared/src/**` ilgili sınıf + test | PHP şablonu | `shared/AGENTS.md §4.1 (php-template)` |

**Ağacın kuralı:** bilgi **önce türüne göre** konumlanır; tür belirsizse en dar hedefe yazılır
(`Dosya: .ai/RAG.md · Satır: 103` — "en dar (domain-specific) dosya önce"). **Boot listesine
(13 kanonik) yeni bilgi satırı EKLENMEZ** — boot yalnız dosya listesidir, içerik değildir
(`Dosya: .ai/CLAUDE.md · Satır: 632-646`).

## §6. Retrieval Pipeline `PLANNED` vs Günlük Pratik

| # | Gelecekte (pipeline) | Bugün (elle pratik) | Durum kanıtı |
|---|---|---|---|
| 1 | index sorgusu | `RAG.md §3` tablosu gözle tarama | `Dosya: .ai/RAG.md · Satır: 116` (tek `IMPLEMENTED` satır) |
| 2 | BM25 katmanı | `keys.md` keyword exact/substring arama | `Dosya: .ai/RAG.md · Satır: 120` |
| 3 | vektör / semantik | — (yok; sezgisel eşleşme) | `Dosya: .ai/RAG.md · Satır: 118-119` (`PLANNED`) |
| 4 | chunk (~500 token, §-bazlı) | bölüm başlığı (`§`) bazlı okuma | `Dosya: .ai/RAG.md · Satır: 117` |
| 5 | retrieve + augment | `@` okuma + künyeli alıntı | `Dosya: .ai/RAG.md · Satır: 122` |
| 6 | otomatik güncelleme tetikleyicisi | `vault-sync-post` skill'i ile elle ölçüm | `Dosya: .ai/RAG.md · Satır: 121, 135` |
| 7 | metadata senkronu | Durum sütunu elle güncelleme | `Dosya: .ai/RAG.md · Satır: 104` |

**Günlük yasak:** "Sistem var" sanmak — `Dosya: .ai/RAG.md · Satır: 124` (eli10): *"Otomatik
arama sistemi henüz kurulmadı; bugün arama, bu dosyadaki liste ve keyword tablosuyla elle
yapılıyor."* · **Geçiş kapısı:** bir bileşen `IMPLEMENTED` olursa §4 başlığındaki "ÇOĞU
PLANNED" ibaresi de yeniden değerlendirilir; başlık-satır çelişkisi → **DUR** (rag-surucu §11).

## §7. Bilgi Yazma Akışı — Yeni Bilgi Nereye?

| # | Soru | Evet → konum | Hayır → |
|---|---|---|---|
| A | Bir mimari kararı değiştiriyor mu? | ADR (`draft/` — §9) | B'ye |
| B | Kural/yasak/izin içeriyor mu? | `.ai/CLAUDE.md` §7 guardrail satırı | C'ye |
| C | Yeni bir "soru→dosya" eşlemesi mi? | `RAG.md §3.1` yeni satır (§8 D) | D'ye |
| D | Yeni bir keyword mü açığa çıkıyor? | `keys.md` ilgili § tablosu | E'ye |
| E | Katalogda kaydı yok mu? | `index.md` satırı | F'ye |
| F | Süreç/faz adımını etkiliyor mu? | `WORKFLOW.md` faz tablosu | G'ye |
| G | Session/audit bilgisi mi? | `MEMORY.md` / `log.md` (append-only) | H'ye |
| H | Geçici/tek-seferlik mi? | `.ai/reports/` — kalıcı vault'a girmez | DUR + MO'ya sor |

**Kesin karar (A-H dışına taşma):** hiçbir kutuya sığmayan bilgi **üretilmez**; `UNKNOWN`
işaretle ve MO'ya sor. Sürükleyici kural: *yazma, okumadan pahalıdır* — yazmadan önce §3
arama akışını çalıştır (çünkü bilgi **zaten var olabilir**; duplicate = §12 çelişki tohumu).

## §8. Bilgi Yazma Akışı — Adım Tablosu (yazma öncesi kapı)

| Adım | Ne yapılır | İhlal sonucu | Kanıt |
|---|---|---|---|
| **A** | §3 arama akışını çalıştır — bilgi zaten var mı? | duplicate bilgi = iki SSOT | §7 bu dosya |
| **B** | Türü belirle (§7 A-H) | yanlış konum = çelişki tohumu | §5 bu dosya |
| **C** | Hedef dosyanın şablonunu oku (Guardrail #16) | şablonsuz dosya üretilmez | `.ai/.templates/index.md` |
| **D** | İçeriği künyeli kanıtla yaz (`Dosya:`/`Satır:`; sayılar ölçümle) | Zero-Hallucination ihlali | `Dosya: .ai/RAG.md · Satır: 105` |
| **E** | `RAG.md §3.1` satırı ekle/güncelle + `Test-Path` doğrula | hayalet satır | `Dosya: .ai/RAG.md · Satır: 102` |
| **F** | `keys.md`/`index.md` bağlantılarını güncelle | kopuk referans | §24 kapı 7 |
| **G** | `updated:` alanını bugüne çek (bayatlık kayıt zinciri) | stale bilgi | §13-§14 |
| **H** | `vault-sync-post` ile §3 yeniden ölç · boot listesine EKLEME | boot bozulur | `Dosya: .ai/RAG.md · Satır: 135-136` |

## §9. ADR Yazım Akışı (kalıp: context / decision / alternatives / consequences)

> ADR, **tek bir kararı** dondurur; karar değil, **kararın gerekçesi** saklanır. Metin
> `accepted/` altına girdikten sonra **frozen'dır: okunur, referans edilir, değiştirilmez**
> (`Dosya: .ai/AGENTS.md · §25.3 kural 2`).

| # | Bölüm | Ne yazılır | Yazılmayan | Kalıp soru |
|---|---|---|---|---|
| 1 | **Context** | Sorun, kısıtlar, tetikleyici olay | çözüm fikri | "Neyi çözmeye çalışıyoruz?" |
| 2 | **Decision** | Alınan karar — tek cümle, aktif dil | koşullu ifade ("belki") | "Ne karar verdik?" |
| 3 | **Alternatives** | Reddedilen seçenekler + neden reddedildiği | yalnız kazanan | "Başka ne düşünüldü, neden elendi?" |
| 4 | **Consequences** | Kazançlar + bedeller + yeni borçlar | tek taraflı övgü | "Buna katlanacağız mı?" |
| 5 | **Status** | `draft` → `accepted` → (`rejected`) | atlanmış statü | "Nerede duruyor?" |
| 6 | **Evidence** | `Dosya:`/`Satır:` künyeleri, ölçüm tarihi | bellekten iddia | "Kanıt nerede?" |

**Yaşam döngüsü akışı:**

```text
[A] Karar olgunlaşır → .ai/.decisions/draft/ADR-NNN-*.md (§9 kalıbı 1-6)
[B] İnceleme (Expert+Senior+Junior — AGENTS.md §6.1, kanıt > seviye)
[C] Onay → accepted/ taşınır · red → rejected/ (R-NNN adlandırması korunur)
[D] Frozen: bundan sonra metin DEĞİŞMEZ — düzeltme = YENİ ADR (superseded kaydıyla)
[E] İlgili dosyalara wiki-link + RAG.md §3.1 satırı + index.md kaydı (§8 E-F)
```

| Statü | Disk kanıtı (2026-10-17) | Anlamı |
|---|---|---|
| `draft/` | `ADR-102-tur-taksonomi-genislemesi-5-dal.md` (glob isabeti) | incelemede — bağlayıcı değil |
| `accepted/` | ADR-001…ADR-089 serisi + `ADR-030-ai-strategy-core.md` | bağlayıcı karar |
| `rejected/` | `R-001`…`R-010` + `index.md` (glob isabeti) | nedenlenerek elenen |
| `index.md` | `.ai/.decisions/index.md` | tüm serinin kataloğu |

## §10. ADR Karar Ağaçları — "ADR gerekli mi?" ve "dondurabilir miyim?"

| # | Soru | Evet | Hayır |
|---|---|---|---|
| 1 | Geri alınması pahalı bir seçim mi? (bağımlılık/şema/süreç) | **ADR yaz** (§9) | normal görev — `log.md` yeter |
| 2 | İki veya daha fazla ciddi alternatif vardı mı? | **ADR yaz** (alternatives zorunlu) | tek yol vardı — ADR israfı |
| 3 | Aynı karar ikinci kez tartışılıyor mu? | **ADR yaz +** eski tartışmayı linkle | — |
| 4 | İnceleme tamamlandı mı (min. 1 Expert + 1 Senior)? | `accepted/`'a taşı | `draft/`'ta bekle |
| 5 | Düzeltme mi isteniyor (frozen metinde)? | **Yeni ADR** (superseded) | frozen'a dokunma (§24/5) |
| 6 | Karar hâlâ `PLANNED` mi (kod yok)? | Durum etiketi `PLANNED` kalsın | kanıtsız `IMPLEMENTED` yasak (§23/2) |

## §11. Bilgi Çelişkisi — SSOT Hiyerarşisi Çözümü

**Bağlayıcı sıra (çelişkide önceki kazanır):**

```text
1) .ai/CLAUDE.md      → anayasa, guardrails, boot protokolü   (vault-içi otorite §1)
2) .ai/AGENTS.md      → agent registry, routing, handover
3) .ai/WORKFLOW.md    → süreç, faz, hard gate
4) .ai/brain.md       → mimari karar özeti
5) .ai/index.md       → katalog
6) .ai/.templates/    → üretim kalıpları
```

| # | Çelişki senaryosu | Kazanan | Davranış |
|---|---|---|---|
| 1 | `CLAUDE.md` ↔ `AGENTS.md` | **CLAUDE** | `AGENTS.md` satırı revize edilir (onay: MO) |
| 2 | `AGENTS.md` ↔ `WORKFLOW.md` | **AGENTS** | WORKFLOW fazı hizalanır |
| 3 | `brain.md` ↔ `index.md` | **brain** | katalog satırı düzeltilir |
| 4 | ADR (accepted) ↔ düz metin dosya | **ADR** | metin düzeltilir; ADR frozen (§9/D) |
| 5 | Repo kodu ↔ vault iddiası | **kod kazanır +** vault güncellenir | iki kanıt da künyeli yazılır; etiket `⚠️ VERIFICATION REQUIRED` (rag-surucu §21) |
| 6 | Boot sayım zinciri ↔ yeni bulgu | **boot** (`13 = 14 − 1`) | **DUR** + üst karar (`Dosya: .ai/CLAUDE.md · Satır: 648`) |
| 7 | `keys.md` ↔ `RAG.md §3.1` | **RAG §3.1** | keys satırı güncellenir (`RAG.md` = konu→dosya otoritesi) |
| 8 | İki `accepted/` ADR birbirini yalanlıyor | — | **DUR** + yeni ADR (superseded) + MO kararı |

> **Çelişki gizlenmez:** her çelişki, hangi ikisinin nerede çeliştiğini gösteren
> `Dosya:`/`Satır:` çift künyesiyle **kaydedilir**; hangisinin bağlayıcı olduğu §11 sırasıyla
> söylenir; kapatılamayan (ör. §21'teki ADR-030 ↔ enjeksiyon çelişkisi) `⚠️ VERIFICATION
> REQUIRED` olarak **açık** bırakılır.

## §12. Çelişki Karar Ağacı (adım adım)

```text
[C0] İki bilgi çelişiyor mu? → ölç: künyeli alıntı, göz kararı DEĞİL
      · hayır → normal okuma, kapan
[C1] Hangi katmanda çelişiyor? (kural ↔ süreç ↔ karar ↔ katalog ↔ kod)
[C2] SSOT sırasına göre üst katmanı bul (§11) → üst katman YAZILMAZ
[C3] Alt katman düzeltilebilir mi (onay gerektirmez mi)?
      · evet → düzelt + log.md'ye tek satır + updated: tazele
      · hayır (frozen / boot / çekirdek) → DUR + MO'ya tek soru
[C4] Çelişki kaydı: iki künye + kazanan + tarih → ilgili dosyanın açık-sorular bölümü
[C5] 3 başarısız düzeltme turu → DUR — şüpheli varsayımı yaz, üst karar iste
```

| Adım | İhlal edilirse | Sonuç |
|---|---|---|
| C0 | ölçmeden "çelişki" iddiası | sahte çelişki → boşa revizyon |
| C2 | alt katman yazılır | SSOT ihlali — otorite dağılır |
| C3 | frozen'a dokunulma | tarihçe kaybolur (ADR-042) |
| C4 | kayıtsız düzeltme | denetim izi yok (`log.md` append-only ihlali) |

## §13. Stale Knowledge (Bayat Bilgi) — Tespit

**Tazelik üç sinyalde taşınır:** `updated:` alanı (frontmatter) · `validation`/`evidence`
alanları · sayım tarihleri (`2026-10-14 disk ölçümü` gibi). **Bayat = sinyal yok veya eski.**

| # | Bayatlık türü | Semptom | Tespit yolu |
|---|---|---|---|
| 1 | `updated:` bayat | dosya 30+ gün güncellenmemiş | §18 betiği (tarih taraması) |
| 2 | Ölçümsüz sayı | "96 md" gibi tarihiz iddia | sayı+`ölçüm` eşleşmesi yok → bayat |
| 3 | Hayalet indeks satırı | hedef dosya diskte yok | `Test-Path` = false (§18/D) |
| 4 | Kopuk wiki-link | `[[...]]` hedefi yok | `.ai/scripts/wiki-link-check.ps1` (kökten) |
| 5 | Statü bayatlığı | `PLANNED` iken kod yazılmış | ADR-030 + kod grep'i (§20) |
| 6 | Çelişkide kalmış bilgi | §12 kaydı açıksa bayat | açık-soru tablosu |
| 7 | Boot dışı referans | boot'da olmayan dosyaya "zorunlu okuma" denmesi | boot 13 kanonik (§24) |

**Bugünün ölçülebilir örnekleri (2026-10-17 disk taraması):**

| Dosya | `updated:` | Yorum |
|---|---|---|
| `.ai/RAG.md` | 2026-10-12 | taze (5 gün) |
| `.ai/AGENTS.md` | 2026-10-12 | taze (5 gün) |
| `.ai/WORKFLOW.md` | 2026-10-12 | taze (5 gün) |
| `.ai/keys.md` | 2026-10-06 | **sınırda (11 gün)** — §3.1 sayıları 2026-10-14 ölçümüyle değişti; keys'te karşılığı ölçülmedi → tazeleme adayı |
| `.ai/CLAUDE.md` | 2026-10-07 | **takip gerekli** (boot dosyası — değişiklik onay ister) |

## §14. Stale Knowledge — Tazeleme Akışı

| # | Tetikleyici | Tazeleme adımı | Sahibi |
|---|---|---|---|
| 1 | Vault'a yeni dosya eklendi | `RAG.md §3.1` satırı + `keys.md` keyword + `index.md` kaydı | üreten ajan + MO |
| 2 | Dosya taşındı/silindi | §3.1 hücresi + wiki-link hedefleri (`Test-Path` yeniden) | MO |
| 3 | Ölçüm tarihi geçti (>30 gün) | yeniden ölç + tarihle (§18) | ölçümü yapan ajan |
| 4 | Kod yazıldı, ADR `PLANNED` duruyor | ADR durum etiketi + `RAG.md §4` satırı | Backend/Embedded + MO |
| 5 | Çelişki kapatıldı | açık-soru kaydı kapanır + `log.md` append | MO |
| 6 | `updated:` alanı bayat | dosya gözden geçirilir; içerik tazelenir ya da **arşiv notu** düşülür | dosya sahibi |
| 7 | Boot dosyası etkilendi | **DUR** — 13 kanonik sayım zinciri onayı (§24) | Vault Steward |

**Tazelik ekonomisi:** her şey **sürekli** tazelenmez — tazeleme **tetikleyici bazlıdır**
(1-7). Sürekli tarama israftır; tetikleyicisiz kalan bilgi §18 betiğiyle **periyodik**
(yayın öncesi) denetlenir.

## §15. Token Ekonomisi (arama bütçesi)

| # | Kural | Sonuç | Kanıt |
|---|---|---|---|
| 1 | Toplu okuma yasak — boot'ta tüm vault okunmaz | bütçe korunur | `Dosya: kök AGENTS.md · Satır: 282` |
| 2 | A-F sırası ucuzdan pahalıya (§3) | ortalama okuma sayısı düşer | §3 bu dosya |
| 3 | Boot'ta olan dosya yeniden okunmaz | çift israf yok | §6 / rag-surucu §14 |
| 4 | Rastgele okuma yasak — keys kullan | asimetrik tarama yok | `Dosya: .ai/keys.md · Satır: 579` |
| 5 | Öncelik matrisi P0→P3 | sıralı bütçe | `Dosya: .ai/keys.md · Satır: 566-571` |
| 6 | §-bazlı okuma (~500 token chunk) | gereksiz bölüm okuma yok | `Dosya: .ai/RAG.md · Satır: 117` |
| 7 | Yazma öncesi arama (§8/A) | duplicate önlenir → revizyon israfı yok | §8 bu dosya |
| 8 | ADR frozen → yeniden tartışma yok | karar tekrarı israfı biter | §9/D |

**Bütçe şablonu (görev başına):** boot (verilmiş) + K1 tablosu (~20 satır) + 1-2 hedef dosya
§-okuma = tipik retrieval **~1-3 dosya**; 5+ dosya okunduysa §3 sırası bozulmuş demektir
(rag-surucu §15/2 hata modu).

## §16. PowerShell Ölçüm Kalıbı A — Arama (grep kalıpları, kökten)

> ✅ **Çalıştırılabilir** — repo kökünden (`C:\www\coremusic.net`) çalıştırılır; `.ai/` gizli
> dizindir, `-Force`/`hidden: true` **zorunludur** (`Dosya: kök AGENTS.md · §7 adım 2`).

```powershell
# A1) Bir keyword hangi vault dosyalarında geçiyor? (gizli dizin dahil)
$kw = 'knowledge|bilgi bankası|lifecycle'
$hits = Get-ChildItem -Path '.ai' -Recurse -Filter '*.md' -Force |
        Select-String -Pattern $kw -List
$hits | ForEach-Object { "{0} => {1}" -f $_.Path, $_.LineNumber }
"isabet => {0}" -f $hits.Count

# A2) Frontmatter 'updated:' değerlerini topla (stale taraması — §13/1)
$docs = '.ai\CLAUDE.md','.ai\AGENTS.md','.ai\WORKFLOW.md','.ai\keys.md','.ai\RAG.md'
foreach ($d in $docs) {
    $u = (Select-String -Path $d -Pattern '^updated:\s*(.+)$').Matches.Groups[1].Value.Trim()
    $age = (New-TimeSpan -Start ([datetime]$u) -End (Get-Date)).Days
    "{0,-20} updated={1} age={2}d  stale={3}" -f $d, $u, $age, ($age -gt 30)
}
```

## §17. PowerShell Ölçüm Kalıbı B — Doğrulama Kapısı (satır / FM / Authority)

```powershell
# B1) Bu dosyanın üretim kapısı (>=500 + FM 10 alan + Authority son satır — tek seferde)
$p = '.ai\architecture\K015-AI\knowledge\knowledge-surucu.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir     => {0}   (kapı >=500: {1})" -f $lines.Count, ($lines.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority => {0}" -f ($lines[$lines.Count - 1] -eq $auth)
$pat = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10     => {0}" -f ((Select-String -Path $p -Pattern $pat).Count -eq 10)

# B2) Placeholder + mojibake taraması (kapı 6)
"placeholder => {0} (kapı: 0)" -f (Select-String -Path $p -Pattern '\{\{').Count
"mojibake    => {0} (kapı: 0)" -f (Select-String -Path $p -Pattern 'Ã.|â€.|Â.').Count
```

## §18. PowerShell Ölçüm Kalıbı C — Hayalet Satır + Wiki-Link + Tazelik

```powershell
# C1) Yazılan satırlardaki hedef dosyalar diskte mi? (hayalet satır taraması)
$targets = @('.ai\CLAUDE.md','.ai\AGENTS.md','.ai\WORKFLOW.md','.ai\brain.md',
             '.ai\index.md','.ai\keys.md','.ai\RAG.md','.ai\glossary.md',
             '.ai\MEMORY.md','.ai\log.md','.ai\.decisions\index.md',
             '.ai\.templates\index.md','shared\src\AI\KnowledgeBase.php')
$ghost = 0
foreach ($t in $targets) { if (-not (Test-Path $t)) { $ghost++; "GHOST => $t" } }
"sonuc => ghost={0} (kapı: 0)" -f $ghost

# C2) Wiki-link hedefleri (kökten çalıştır — .ai/ içinden YASAK)
#   .ai/scripts/wiki-link-check.ps1  → 227 link / 6 raporlanan = GERÇEK KIRIK 0
#   (6'sı kök AGENTS.md §13.7 sahte kırık — düzeltme YASAK, sayım 6'da kalır)

# C3) §-bazlı okuma ölçümü: bir dosyanın satır bütçesi (chunk ~500 token ≈ ~80-100 satır)
$n = [IO.File]::ReadAllLines('.ai\RAG.md', [Text.Encoding]::UTF8).Count
"RAG.md => {0} satir ≈ {1} chunk (500 token hedef)" -f $n, [math]::Ceiling($n / 90)
```

## §19. Repo Kanıtı — PHP Bilgi Ömrü (lifecycle + skor)

> ✅ **Repo kanıtı** — aşağıdaki bloklar `shared/src/AI/KnowledgeBase.php` içinden alınmış
> birebir alıntılardır (uydurma yok; satır sonundaki yorumlar künyedir). *Görev notu:
> "illüstratif" uyarısı yalnızca gerçekten illüstratif olan Python/TS/Go bloklarına (§20-§22)
> konmuştur; repo kanıtına "repo kanıtı DEĞİL" etiketi YANLIŞ etiket olurdu.*

```php
private const CATEGORIES = ['adr','architecture','security','audio',
                            'hardware','workflow','template'];            // Satır 25
private const LIFECYCLE_MAX_AGE_DAYS = 365;                             // Satır 27

public function addKnowledge(string $title, string $content, string $category,
                             array $metadata = []): string               // Satır 96
{
    if (!in_array($category, self::CATEGORIES, true)) {                  // Satır 98
        throw new \InvalidArgumentException("Invalid category: {$category}...");
    }                                                                     // Satır 99
    ...
    'created_at' => $now, 'updated_at' => $now,                          // Satır 111-112
}

public function cleanupLifecycle(): array                                // Satır 134
{
    $cutoffDate = date('Y-m-d', strtotime('-' . self::LIFECYCLE_MAX_AGE_DAYS . ' days'));
                                                                        // Satır 139
    foreach ($this->knowledge as $id => $item) {
        if ($item['created_at'] < $cutoffDate) {                         // Satır 142
            unset($this->knowledge[$id]); $removed++;                    // Satır 144-145
        }
    }
}
```

**Sürücü açısından anlamı (üç satır):**

| Repo davranışı | Sürücüdeki karşılığı | Fark |
|---|---|---|
| `addKnowledge` kategori kapısı (7 kategori) | §7 tür→konum ağacı | repo: bellek-içi dizi · vault: dosya sistemi |
| `created_at` 365 gün → `cleanupLifecycle` siler | §13/1 bayatlık + §14 tazeleme | repo **siler**; vault **işaretler/tazeler** (frozen/append-only) |
| `updated_at` yazımı (`Satır: 112`) | §13 `updated:` sinyali | repo'da alan var, **okuma/tarama kodu yok** (§20) |

## §20. Repo Kanıtı — "Bu Alan Kodu YOK" Cümleleri (negatif kanıt)

```php
    // search: kategori filtresi + skor + sıralama — keyword matching
    // "Basit keyword matching (gerçek implementasyonda vector cosine similarity)"  // Satır 48
    $score = $this->calculateRelevanceScore($queryLower, $item);         // Satır 49
    if (empty($retrieved)) { return 0.0; }                               // Satır 225-226
```

| İddia | Kanıt | Durum |
|---|---|---|
| "Repo'da bu alan kodu YOK: **vault dosyalarını okuyan bilgi-yazma katmanı**" | `.ai/` · `RAG.md` · `keys.md` → `shared/src/AI/*.php` | **1 isabet, o da yorum** (`shared/src/Log/Redactor.php:8` — rag-surucu §21 ölçümü) |
| "Repo'da bu alan kodu YOK: **`updated_at` bayatlık okuması / stale taraması**" | `cleanupLifecycle` yalnız `created_at` kullanır (`Satır: 142`) | **0 isabet** — lifecycle **yazıyor, bayat kontrol etmiyor** |
| "Repo'da bu alan kodu YOK: **ADR/çelişki çözümü otomasyonu**" | `conflict|ssot|contradiction` → `shared/src/AI/*.php` | **0 isabet** (§21 betiği kökten çalıştırılabilir) |
| "Vault pipeline (chunk/embedding/store) kodu" | `RAG.md §4` durum sütunu | **PLANNED** (`Dosya: .ai/RAG.md · Satır: 117-122`) |

**Gerçek çelişki kaydı (kapatılmaz, işaretlenir):** ADR-030 "`shared/src/AI/` instantiate
**0**" der (`Dosya: .../ADR-030-ai-strategy-core.md · Satır: 38`), `KnowledgeBase` ise
`AIWorkflow` constructor'ına enjekte edilir (`Dosya: shared/src/AI/AIWorkflow.php · Satır: 22,
30`). İkisi birden doğru olabilir — hangisi bağlayıcı `⚠️ VERIFICATION REQUIRED`
(rag-surucu §21 · §30/Q3).

## §21. İllüstratif — Python: Stale Knowledge Skorlayıcı

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# Vault §-bazlı bayatlık skoru: updated: alanı + doğrulama yaşı + ölçümsüzlük
from datetime import date
from pathlib import Path
import re

FRESH_DAYS = 30  # §13/1 eşiği (vault kuralı: tetikleyici bazlı tazeleme)

def freshness(path: Path, today: date) -> tuple[str, int]:
    m = re.search(r"^updated:\s*(\d{4}-\d{2}-\d{2})", path.read_text(encoding="utf-8"), re.M)
    if not m:                       # updated alanı yok → sinyalsiz = bayat
        return "STALE", 9999
    age = (today - date.fromisoformat(m.group(1))).days
    return ("FRESH" if age <= FRESH_DAYS else "STALE", age)
```

*İskelet kaynak: `Dosya: .ai/RAG.md · Satır: 135` (tazeleme kuralı) · `Dosya:
shared/src/AI/KnowledgeBase.php · Satır: 27, 139` (eşik + cutoff mantığının ruhu — repo siler,
bu iskelet işaretler).*

## §22. İllüstratif — TypeScript: SSOT Çelişki Çözücü

> ⚠️ İllüstratif örnek - repo kanıti DEĞİL - upstream/docs referansı

```typescript
// §11 SSOT hiyerarşisi: sıra=otorite; ilk kazanır, kayıp satır raporlanır
const SSOT_ORDER = ['CLAUDE.md', 'AGENTS.md', 'WORKFLOW.md',
                    'brain.md', 'index.md', '.templates/'] as const;

type Claim = { doc: string; line: number; text: string };

export function resolve(a: Claim, b: Claim): { winner: Claim; loser: Claim | null } {
  const ia = SSOT_ORDER.findIndex((d) => a.doc.includes(d));
  const ib = SSOT_ORDER.findIndex((d) => b.doc.includes(d));
  if (ia === -1 || ib === -1)
    throw new Error('⚠️ VERIFICATION REQUIRED — kaynak SSOT sırasında değil');
  if (ia === ib) return { winner: a, loser: null };       // aynı dosya: satır-yönlü inceleme
  return ia < ib ? { winner: a, loser: b } : { winner: b, loser: a };
}
// frozen (ADR accepted) veya boot sayım zinciri → C3: DUR + MO kararı (§12)
```

## §23. Hata Modları

| # | Hata | Semptom | Tespit | Tepki |
|---|---|---|---|---|
| 1 | Duplicate bilgi | iki dosya "iki gerçeklik" anlatıyor | §8/A arama atlandı | birleştir + `log.md` |
| 2 | Yanlış konum yazımı | ADR metni normal dosyada | §7 ağacı atlandı | §9 akışına taşı |
| 3 | Hayalet indeks satırı | hedef açılamıyor | `Test-Path` false (§18/C1) | satır sil/raporla |
| 4 | Frozen'a dokunma | ADR metni değişmiş | git diff (orkestratör) | geri al + yeni ADR |
| 5 | SSOT sırası ihlali | alt dosya kazanmış | §11 karşılaştırması | üst katmana göre düzelt |
| 6 | Mojibake | `Ç`, `â€"` gibi bozuk karakterler | §17/B2 taraması | düzelt (vault geneli 745 düzeltme geçmişi — `.ai/AGENTS.md §26.3`) |
| 7 | Kırık wiki-link | `[[...]]` hedefi yok | `wiki-link-check.ps1` (kökten) | 6 sahte kırık **onarılmaz** (§24/7) |
| 8 | Ölçümsüz sayı | tarihiz istatistik | sayı yanında `ölçüm` yok | ölç + tarihle ya da `UNKNOWN` |
| 9 | `updated:` unutulması | tazelik zinciri kopuk | §18/A2 bayat | G adımını tekrarla |
| 10 | `.ai/` içinden gate betiği | 19 sahte "PNG yok" gibi | kök kuralı ihlali | **Kökten** çalıştır |
| 11 | 3 başarısız düzeltme turu | aynı hata tekrarı | tur sayacı | DUR — şüpheli varsayımı yaz, üst karar iste |

## §24. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem / komut |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz (birebir, `title`/`parent` anahtarı YOK) | §17/B1 |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` (§17/B1) |
| 3 | Authority son satır | birebir | §17/B1 eşitlik kontrolü |
| 4 | §5/§7 hedefleri | her satırda dosya diskte | `Test-Path` döngüsü (§18/C1) |
| 5 | Durum/tazelik | sayılar ölçümlü, `updated:` sinyalli | §18/A2 · §13 |
| 6 | Placeholder + mojibake | `\{\{` → 0 · mojibake → 0 | §17/B2 |
| 7 | Wiki-link | hedefler diskte (6 sahte kırık istisna) | `wiki-link-check.ps1` (kökten) |
| 8 | Çelişki kaydı | açık çelişkiler `⚠️ VERIFICATION REQUIRED` | §11-§12 · §27 |
| 9 | Boot listesi | 13 kanonik değişmedi | `Dosya: .ai/CLAUDE.md · Satır: 632-646` |
| 10 | Kapsam dışı yazım | 0 dosya (yalnız bu dosya) | `git status --porcelain` sayacı |
| 11 | `git commit` | **0** (subagent yasak — orkestratöre ait) | §26 kapanış |

## §25. Sık Yapılan Hatalar (anti-pattern)

| # | Yanlış | Doğru | Kaynak |
|---|---|---|---|
| 1 | Aramadan yazma (duplicate) | önce §3 A-F, sonra yaz | §8/A |
| 2 | Bilgiyi "hoş görünen" yere yazma | türüne göre §7 A-H | §5 |
| 3 | Çelişkiyi sessizce "düzeltme" | kaydet + §11 sırasıyla karar ver | §12/C3-C4 |
| 4 | Frozen ADR'yi düzeltmek | yeni ADR (superseded) | §9/D |
| 5 | Bayat bilgiyi tazelemeden kullanmak | `updated:`/ölçüm tarihi kontrolü | §13 |
| 6 | Sürekli "her şeyi" tazelemek | tetikleyici bazlı tazeleme | §14 |
| 7 | Bellekten sayı/istatistik | `[IO.File]::ReadAllLines` ile ölç | `RAG.md §3.2/4` |
| 8 | Boot listesine içerik eklemek | boot yalnız dosya listesi | `CLAUDE.md §16` |
| 9 | `.ai/` gizli olduğu için arama | `-Force` / `hidden: true` | kök `AGENTS.md §7/2` |
| 10 | Kanıtsız "bitti" | kapı tablosu + ölçüm (§24) | bu dosya |

## §26. Kapanış Kontrol Listesi (her bilgi-yönetimi görevi sonunda)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | Arama akışı A-F izlendi mi (boot'da olan atlandı mı)? | evet / gerekçesi |
| 2 | Yazma konumu türüne göre mi seçildi (§7)? | evet |
| 3 | Çelişki varsa §11 sırası uygulandı mı? | evet / çelişki yok |
| 4 | ADR yazıldıysa §9 kalıbı 1-6 dolu mu? | evet / ADR yok |
| 5 | `updated:` alanları tazelendi mi? | evet |
| 6 | Her iddia `Dosya:`+`Satır:` künyeli mi? | evet |
| 7 | Bulunamayan için `UNKNOWN`/`⚠️ VERIFICATION REQUIRED` yazıldı mı? | evet |
| 8 | Boot listesi değişti mi? | **hayır (13 sabit)** |
| 9 | Kapsam dışı dosyaya yazıldı mı? | **hayır (0)** |
| 10 | `git commit` atıldı mı? | **hayır (orkestratöre ait)** |

## §27. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [knowledge-anayasa]` — `Test-Path` 2026-10-17 = **True** (diskte var, `git status: ??` = commit edilmemiş paralel üretim). İçerik bağımlılığı (sürucünün anayasa ile çelişmemesi) denetlendi mi? | dosya kanıtlı · içerik denetimi `⚠️ VERIFICATION REQUIRED` |
| Q2 | Ebeveyn `knowledge/index.md` `children` listesi bu modül eklendikten sonra güncellenecek mi? | orkestratör kararı |
| Q3 | ADR-030 "instantiate 0" (`Satır: 38`) ↔ `AIWorkflow` constructor enjeksiyonu (`Satır: 22, 30`) — hangisi bağlayıcı? | `⚠️ VERIFICATION REQUIRED` (§20) |
| Q4 | `shared/AGENTS.md` notu ".ai/architecture/** K-space katman mimarisi **kaldırıldı** (2026-10-12)" der — oysa bu dosya `.ai/architecture/K015-AI/knowledge/` altında üretilmektedir. Kaldırma kapsamı kademe-1 miydi, K015 dahil mi? | `⚠️ VERIFICATION REQUIRED` — **dosya yolu değiştirilmez**; kapsam kararı üstte |
| Q5 | `cleanupLifecycle` (`Satır: 142`) `updated_at` yerine `created_at`'e bakar — bilinçli mi? (bayat ama sık güncellenen kayıt silinmez, taze ama eski-kökenli silinir) | bilgi: §19, düzeltme yetkisi Backend Architect + MO |
| Q6 | `keys.md` `updated: 2026-10-06` iken `RAG.md §3.1` sayıları 2026-10-14 ölçümüyle değişti — keys tazelemesi planlandı mı? | MO kararı (§13 örnek satırı) |
| Q7 | Stale eşiği (30 gün — §13) bağlayıcı mı, yoksa tetikleyici-bazlı model (§14) tek geçerli model mi? | Vault Steward kararı |

## §28. Web Kaynakları (2026-10-17 doğrulama — websearch)

| # | Kaynak | Konu | Kullanım |
|---|---|---|---|
| 1 | Microsoft Cloud Blog — "Common retrieval augmented generation (RAG) techniques explained" (2025-02-04) · `microsoft.com/microsoft-cloud/blog/2025/02/04/common-retrieval-augmented-generation-rag-techniques-explained` | hybrid search = keyword + vektör + RRF; chunking; full-text search | §6 pipeline tasarımıyla uyum: bugünkü "exact keyword → BM25 ruhu" sırası endüstri pratiğiyle tutarlı |
| 2 | Microsoft Learn — Copilot Studio "Knowledge sources summary" · `learn.microsoft.com/microsoft-copilot-studio/knowledge-copilot-studio` | "official source" işareti; kaynak tazeliği (gerçek zamanlı vs. 4-6 saat senkron); kullanıcıyı doğrulamaya çağırma | §13-§14 tazelik sinyali + §24/8 doğrulama kapısı: bayat kaynak **işaretlenir**, kullanılmaz |
| 3 | Slite — "Single source of truth: what it means and why teams need one" · `slite.com/learn/single-source-of-truth` | SSOT 4 özellik: authority · uniqueness · accessibility · ownership; tazelik/sahiplik ajan-okunur sinyal olmalı | §11-§12: otorite sıra + `updated:`/`owner` sinyali — bayat sayfa alıntı yapılmaz |

> Doğrulama biçimi: 2026-10-17 `websearch` — iki Microsoft kaynağı (resmi) + bir sektör makalesi.
> URL'ler arama çıktısından alınmıştır; **uydurma URL yok**. Erişim kesintisi yaşanırsa bu
> bölüm `⚠️ VERIFICATION REQUIRED` olarak işaretlenir ve §11-§14 vault içi kanıtlarla
> (RAG.md §5, CLAUDE.md otorite sırası) **tek başına** dayanır.

## §29. Kaynaklar

| Kaynak | Yol | Kullanım amacı | Ölçüm (2026-10-17) |
|---|---|---|---|
| Retrieval indeksi + pipeline | `.ai/RAG.md` | §3-§6 tarama sırası, §5-§6 kurallar | 177 satır |
| Keyword haritası | `.ai/keys.md` | §3/D keyword taraması, P0-P3 bütçe | 763 satır |
| Agent registry (SSOT #2) | `.ai/AGENTS.md` | §9 frozen kuralı (§25.3/2), §20.3 | 2026-10-12 |
| Süreç/faz | `.ai/WORKFLOW.md` | §5/WORKFLOW satırı, Faz 7 hard gate | 2026-10-12 |
| Boot protokolü (13 kanonik) | `.ai/CLAUDE.md §16` | §24 kapı 9 (Satır: 628-648) | 987 satır (rag-surucu §17 ölçümü) |
| Boot zinciri | `AGENTS.md` (kök) §14 | §3 adım B (Satır: 273-282) | 285 satır |
| AI strateji ADR'si | `.ai/.decisions/accepted/ADR-030-ai-strategy-core.md` | §1/§6/§20 `PLANNED (kod 0)` | Satır: 43, 38, 305 |
| Decisions envanteri | `.ai/.decisions/` (draft/accepted/rejected + index.md) | §9 statü kanıtı | glob isabeti (draft ADR-102, rejected R-001…R-010) |
| Kardeş modül | `.ai/architecture/K015-AI/rag/rag-surucu.md` | §3 katman sırası, §20-§21 çapraz ölçümler | 779 satır |
| Repo bilgi-ömrü kodu | `shared/src/AI/KnowledgeBase.php` | §19-§20 PHP kanıtı | 232 satır |
| Repo AGENTS (düzeltme yetkisi) | `shared/AGENTS.md §2-§4` | §27/Q4 architecture kapsam notu | 2026-10-12 |
| Doğrulama betikleri | `.ai/scripts/*.ps1` (kökten) | §18/C2 wiki-link kapısı | §24 |
| Web (2026-10-17) | §28 — Microsoft ×2 + Slite ×1 | pipeline/tazelik/SSOT karşılaştırması | websearch |

## §30. Gate (özet)

§1-§30 tamam: ELI15 · kapsam · 6 adımlı arama akışı A-F · 13 satırlık soru-tipi karar ağacı ·
11 satırlık tür→konum yazma ağacı · PLANNED vs günlük 7 davranış · 8 adımlı yazma akışı ·
6 bölümlük ADR kalıbı + 5 adımlı yaşam döngüsü · 6 soruluk ADR karar ağacı · SSOT 6 kademe +
8 senaryoluk çelişki tablosu · 6 adımlı çelişki ağacı · 7 tür stale tespiti + ölçülebilir
bugün örnekleri · 7 tetikleyicili tazeleme · 8 token kuralı · PowerShell 6 betik (A1-A2 ·
B1-B2 · C1-C3) · PHP 2 blok + 3 satırlık bağlama tablosu · negatif kanıt 4 satır ·
illüstratif 2 blok (Python · TypeScript) · 11 hata modu · 11 kapı · 10 anti-pattern ·
10 soruluk kapanış · 7 açık soru · 3 web kaynağı · 13 kaynak.

**Kapılar:** satır `>= 500` · FM **10 alan** (title/parent anahtarı YOK) · Authority son satır
birebir · kapsam dışı yazım **0** · `git commit` **0**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
