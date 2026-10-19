---
node_id: context-surucu
node_type: module
status: active
parent_id: context
children: []
depends_on: [context-anayasa]
evidence:
  - ".ai/AGENTS.md §24.2 (14 dosya okuma sırası, okundu 2026-10-17)"
  - ".ai/MEMORY.md §5 (20 adımlık boot listesi)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# context-surucu - Bağlam Kullanım Sürucusu

> **Kapsam:** Bir görev başladığında ajanın bağlamı **nasıl hazırladığı** — boot'tan ilgili vault
> dosyasına, oradan hedef dosyaya ve koda geçiş; bağlam genişletme/daraltma karar ağaçları;
> token taşması davranışı; lock edinim akışı; bağlam yanlışlığı (stale context) durumunda davranış.
> **Kural koyar değil, tarif eder:** bağlayıcı kurallar `Dosya: .ai/AGENTS.md · Satır: 499-517`
> (§24.2 okuma tablosu), `Dosya: .ai/MEMORY.md · Satır: 311-336` (§5 boot listesi) ve
> `Dosya: kök AGENTS.md · Satır: 177-205` (§7 Execution Loop) içindedir. Bu dosya bu kuralların
> **kullanım kılavuzudur**.
>
> **Ölçüm tarihi:** 2026-10-17 (tüm satır sayıları `[IO.File]::ReadAllLines(...UTF8).Count`
> ile bugün diskten ölçülmüştür — §14).

## §1. ELI15 — Bu sürücü ne işe yarar

Bir ajan (veya insan) yeni bir göreve başladığında bağlamı **ansızın doldurmaz**. Sürücü, boot
paketini (kök `AGENTS.md` + kök `CLAUDE.md` + `.ai/AGENTS.md`) zaten okunmuş sayar; görevin
konusuna göre `.ai/` içinden **yalnız ilgili** dosyaları sırayla çeker (§4 A-8 akışı), her adımda
token bütçesini ölçer (§9), bağlam bayatlarsa yeniler (§10), dosya yazımında lock edinir (§11) ve
kapıları geçmeden "bitti" demez (§23).

> **eli10:** "Göreve başlarken ne okuyayım?" sorusunun cevabı üç tabloda yazılıdır: kök `AGENTS.md`
> §14 (boot zinciri), `.ai/AGENTS.md` §24.2 (14 dosya) ve `.ai/MEMORY.md` §5 (20 adım). Sürücü bu
> tabloları **sırayla**, **ihtiyaç anında** kullanmayı ve token taşınca **geriye doğru kesmeyi**
> tarif eder.
>
> **eli15:** Bağlam bir **bütçedir**, depo değil. Her satır okuma bütçeden harcar; sürücünün işi
> harcamayı **en yüksek sinyal/_token oranına** optimize etmek ve bütçe dolunca ya **daraltmak**
> ya **devretmektir** (subagent — §8). Dış literatür de aynı sonuca varır: "context engineering,
> sınırlı pencereye **en küçük** yüksek-sinyal token kümesini koymaktır"
> (Anthropic 2025-09-29 — §21/K1).

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Görev başı bağlam hazırlama akışı A-8 (§4-§5) | Boot listesinin kendisinin **değiştirilmesi** (§7: değiştirilemez) |
| Konu → vault dosyası eşleme (§6) | Vault dosyalarının **içerik** özeti (içerik hedef dosyanın işidir) |
| Genişletme/daraltma kararı + token taşması (§8-§9) | Model tarafında otomatik context editing (upstream — §21) |
| Stale context tespiti/yenileme (§10) | `.ai/` dışı uygulama kodunun bağlama taşınması |
| Lock edinim akışı (§11) | ADR metinleri (frozen — yalnız okunur) |
| Ölçüm betikleri + PHP repo kanıtı (§13-§17) | `git commit` (orkestratöre ait — §24) |

- **Kullananlar:** görev başına bağlam kuran her ajan · MO (boot listesi sahibi) · Vault Steward (denetim).
- **Ön koşul:** boot zinciri kök `AGENTS.md` §14'de bağlayıcıdır (`Satır: 273-282`).
- **Kanıt standardı:** her iddia `Dosya:` + `Satır:` künyelidir; sayılar yalnız ölçükle yazılır.

## §3. Boot Kaynakları — Üç Bağlayıcı Tablo

| Kaynak | Ne verir | Satır (2026-10-17) | Ölçüm |
|---|---|---|---|
| Kök `AGENTS.md` §14 Boot Zinciri | 4 satırlık bağlayıcı boot sırası: 1 `AGENTS.md` → 2 `CLAUDE.md` → 3 `@.ai/AGENTS.md` → 4 diğer `.ai/` **"yalnız ihtiyaç anında"** | `Satır: 273-282` | 285 satır |
| `.ai/AGENTS.md` §24.2 | Her görevde okunacak **14 dosya** sırası (`.ai/CLAUDE.md` → … → `.ai/PROJECTS.md`) | `Satır: 499-517` | 775 satır |
| `.ai/MEMORY.md` §5 | **20 adımlık** genişletilmiş boot (14 kök + `.claude/rules/*` + prompt0-3 + mockup indeksi) | `Satır: 311-336` | 728 satır |

**Sürücü açısından anlamı:** boot **tek liste değil, üç katmandır** — kök §14 *sıra*kuralı,
`.ai/AGENTS.md` §24.2 *kanonik 14 dosya*, `MEMORY.md` §5 *genişletilmiş 20 adım*. Sürücü hangi
görevde hangi katmanın geçerli olduğunu §7'de çözer; ikisini birbirine **çevirmez**.

## §4. Görev Başı Bağlam Akışı — A-8 Adım

```text
[A-1] Boot paketi kontrolü → kök AGENTS.md + kök CLAUDE.md + .ai/AGENTS.md zaten yüklü mü?
[A-2] Görev tipi çıkarımı (keyword → routing tablosu) → bağlam paketi seç (§5 karar ağacı)
[A-3] Konu → dosya eşlemesi (§6) → okunacak dosya listesi (sıralı, en ucuz önce)
[A-4] Boot'ta olmayan ilgili .ai/ dosyalarını oku (P0 → P1 → P2 sırası, §22 bütçe)
[A-5] Hedef dosya/kod okuma (kod yüzeyi) → künyeli alıntı hazırlığı
[A-6] Bağlam bütçesi ölç → taşma varsa daralt (§9: ölç → kes → yeniden dene)
[A-7] Stale context tespiti → bayat iddia varsa yenile (§10)
[A-8] Kapılar (§23) → kapanış listesi → rapor → DUR
```

| Adım | Durum | Dal | Sonuç |
|---|---|---|---|
| A-1 | boot eksik | boot tamamlanmadan devam **yasak** | boot'u tamamla |
| A-1 | boot tam | A-2'ye geç | — |
| A-2 | keyword routing'de eşleşme | `.ai/AGENTS.md §6` tablosu → birincil agent | bağlam paketi |
| A-2 | eşleşme yok | `RAG.md §3` (retrieval) → §6 bu dosya | dürüst durum |
| A-3 | konu sınıfı bulundu | dosya diskte `Test-Path` ile doğrula | okuma listesi |
| A-3 | konu sınıfı yok | `.ai/keys.md` keyword taraması | aday listesi |
| A-4 | dosya diskte yok | hayalet satır → raporla, okuma yapma | DUR/ilerle |
| A-5 | kod yüzeyi | `shared/src/` vb. — künyeli alıntı | kanıt |
| A-6 | bütçe taşma | §9 daraltma | yeniden dene |
| A-7 | bayatlık | §10 yenileme | taze okuma |
| A-8 | kapı geçti | raporla | kapanış |

**Adım A-1 kanıtı:** boot zinciri dört satırlık bağlayıcı bir tablodur
(`Dosya: kök AGENTS.md · Satır: 273-282`); 4. satır "`@.ai/` diğer dosyalar — **yalnız ihtiyaç
anında**" der (`Satır: 282`). Yani `.ai/CLAUDE.md` dahil 14 dosya **boot'ta otomatik değildir**;
A-4'te on-demand açılır.

## §5. Karar Ağacı — Görev Tipi → Bağlam Paketi

| # | Görev tipi (girdi) | Birincil bağlam | Ek bağlam (§24.3) | Kapanınca |
|---|---|---|---|---|
| 1 | PHP endpoint / middleware / controller | `.ai/AGENTS.md §6` routing → Backend | `.ai/.decisions/index.md` (ADR-083) · `shared/src/PageRouter/` | kod + künye |
| 2 | CSS / UI / mockup / bileşen | `.ai/AGENTS.md §6` → UI Designer | `ui-design/01-mockup-index.md` · Kalıp A-D · `css-template.md` | **mockup gate** |
| 3 | Güvenlik (CSRF/CSP/JWT/auth) | `.ai/CLAUDE.md §7` guardrails | ADR-010 · `shared/src/Middleware/` | ADR metni |
| 4 | DB şema / BCNF / migration | `.ai/.sql/mysql/*.sql` | ADR-040 · `shared/src/Database/` | şema |
| 5 | Ses motoru / DSP / Neva | `.ai/projects/NevaEngine/*.md` (38 md) | `.ai/electronic/dsp/*.md` (10) · firmware (7) | modül dokümanı |
| 6 | Test / coverage / E2E | `shared/tests/` · QA standartları | `ui-design/04-accessibility-gaps.md` | test raporu |
| 7 | CI/CD / deploy | `.ai/ecosystem/*.md` · `.github/workflows/` | DevOps kuralları | pipeline |
| 8 | Vault / ADR / indeks güncellemesi | `.ai/RAG.md §3` · `.ai/keys.md` | `.ai/index.md` · `vault-sync-post` skill | §12 akışı |
| 9 | Skill var mı? | kök `CLAUDE.md §Skill Registry` | `.claude/skills/` + global | **skill yüklemek ilk işlem** |
| 10 | Bağlam/boot sırası sorusu (bu konu) | `.ai/AGENTS.md §24.2` · `MEMORY.md §5` | kök `AGENTS.md §14` | bu dosya |
| 11 | Bu sınıfların hiçbiri | — | `.ai/keys.md` → `.ai/index.md` | `UNKNOWN` / tek soru |

**Kaynak:** `Dosya: .ai/AGENTS.md · Satır: 520-533` (§24.3 domain-bazlı okuma) +
`Dosya: .ai/AGENTS.md · Satır: 536-549` (§6 keyword routing — 9 grup).

## §6. Konu → Vault Dosyası Eşleme Tablosu (§24.2 taşıması)

> §24.2 tablosunun **hızlı kopyasıdır**; ayrışma olursa `Dosya: .ai/AGENTS.md · Satır: 499-517`
> kazanır. 14 satır, bugün okunarak alındı — uydurma satır yok.

| Sıra | Dosya | Amaç (bağlam rolü) |
|---|---|---|
| 1 | `.ai/CLAUDE.md` | AI anayasası, 16 Hard Guardrails (en yüksek otorite) |
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing, lock, health |
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar, hard gate |
| 4 | `.ai/brain.md` | Mimari kararlar (ADR 001-089) |
| 5 | `.ai/ROLE.md` | Rol tanımı (Senior Software Architect) |
| 6 | `.ai/index.md` | Master katalog |
| 7 | `.ai/keys.md` | Keyword haritası |
| 8 | `.ai/MEMORY.md` | Session hafızası + §5 boot listesi |
| 9 | `.ai/log.md` | Audit trail (append-only) |
| 10 | `.ai/ULTRA-THINKING.md` | Ultra düşünme protokolü |
| 11 | `.ai/engine.md` | Orkestrasyon motoru |
| 12 | `.ai/glossary.md` | Terim sözlüğü |
| 13 | `.ai/VISION.md` | Vizyon, 36 aylık yol haritası |
| 14 | `.ai/PROJECTS.md` | Proje envanteri |

**Sürücü kuralı:** 14 dosyanın **tamamı her görevde okunmaz** — §24.2 "her görev başında oku"
der (`Satır: 501`), kök `AGENTS.md` §14 ise "`@.ai/` diğer dosyaları **yalnız ihtiyaç anında**"
der (`Satır: 282`). Bu iki talimat arasındaki gerilim §7'de çözülür.

## §7. Boot Listesi 14 vs 20 — §25.4 Çelişki Çözümü

| Liste | Kaynak | Adım | Kapsam |
|---|---|---|---|
| Kanonik boot | `Dosya: .ai/AGENTS.md · Satır: 499-517` (§24.2) | **14** | 14 `.ai/` kök dosya |
| Genişletilmiş | `Dosya: .ai/MEMORY.md · Satır: 311-336` (§5) | **20** | 14 + `.claude/rules/*` + prompt0-3 arşivleri + mockup indeksi |
| FULL boot | `Dosya: .ai/AGENTS.md · Satır: 640-642` (§25.4) | **17 öğe** | root CLAUDE + root WORKFLOW + root README + 14 `.ai/` kök + 3 dizin |

**Çözüm (bağlayıcı, §25.4):** "§24.2 (14 dosya) ile MEMORY §5 (20 adım) arasındaki adım sayısı
farkı **bilinen durumdur**; §5 genişletilmiş okuma setidir. Kanonik kök boot listesi
`AGENTS.md §24.2` ve `WORKFLOW.md §8.7A`'dır"
(`Dosya: .ai/AGENTS.md · Satır: 640-642`; aynı kayıt `Dosya: .ai/MEMORY.md · Satır: 605`'te).

| Durum | Sürücü davranışı |
|---|---|
| Sıra/sayı soruluyor | **14** (kanonik) der; 20'yi "genişletilmiş set" olarak ekler |
| Çelişki raporu | DUR yok — bu **bilinen ve kapatılmış** bir farktır, yeniden açılmaz |
| Yeni dosya boot'a ekleme | **Yasak** — boot listesi orkestratöre aittir (§24) |
| Prompt0 | §5'te 16. adım; `.ai/AGENTS.md §14.1` "prompt0 her zaman okunur" der |

## §8. Bağlam Genişletme / Daraltma Kararı

| Tetikleyici | Karar | Aksiyon | Kanıt |
|---|---|---|---|
| Görev tek konu, boot yeterli | **Daralt** | A-4'te fazladan dosya okuma | kök `AGENTS.md` "gereksiz dosya okunmaz" |
| Görev çok-alanlı (ör. UI+Security) | **Genişlet** | §5'ten 2+ paket, sırayla | §24.3 domain tablosu |
| Bütçe %80'e ulaştı | **Devret** | subagent'a böl — ayrı pencere | §9 |
| Tek dosya >1000 satır | **§-bazlı okuma** | bölüm başlığı + satır aralığı | `MEMORY.md §16` (max 1000 satır) |
| Keşif belirsiz | **Genişlet → sonra kes** | aday listesi çıkar, en olası 1-3'ü oku | RAG fallback zinciri |
| Tekrarlanan okuma | **Kes** | boot'ta olan yeniden okunmaz | §22 kural 3 |

**Karar ağaçları özeti (üç eksende):**

```text
[Genişlik]  Tek-domain ──► dar paket (1-2 dosya)
            Çok-domain  ──► paket başına sıralı okuma (asla toplu)
[Uzunluk]   Dosya <=1000 satır ──► tam okuma
            Dosya >1000 satır  ──► §-bazlı bölüm okuma
[Derinlik]  Boot'ta var ──► yeniden okuma YASAK (künye yeter)
            Boot'ta yok ──► ihtiyaç anında @ ile oku
```

## §9. Token Taşması Davranışı — Ölç → Kes → Yeniden Dene

> Bağlayıcı uyarı: "Token overflow → Görev başarısız"
> (`Dosya: .ai/AGENTS.md · Satır: 571` — §18 Warnings/4).

| # | Adım | Ne ölçülür | Ne kesilir | Sıra |
|---|---|---|---|---|
| 1 | **Ölç** | her okunan dosyanın satır sayısı (`ReadAllLines`) + tahmini token (satır × ~13) | — | §13/A |
| 2 | **Kes** | — | (a) boot'ta olmayan "merak" dosyaları · (b) uzun dosyada ilgisiz § · (c) tekrar eden okuma | §8 |
| 3 | **Yeniden dene** | bütçe geçti mi? | — | A-6 |
| 4 | **Devret** | hâlâ taşma | görev parçası subagent'a (ayrı pencere) | §8/3 |
| 5 | **DUR** | 3 kesme sonrası hâlâ taşma | şüpheli varsayımı yaz, üst karar iste | §12/11 |

| Kesme önceliği (ilk kesilen → son kesilen) | Gerekçe |
|---|---|
| 1. Konu dışı okuma ("acaba bakayım") | sinyal 0 |
| 2. Boot'ta zaten var olan dosyanın yeniden okuması | çift israf |
| 3. Uzun dosyanın ilgisiz bölümleri | §-bazlı okuma |
| 4. Ham veri/JSON kopyaları | `.ai/**` 79.7 MB raw — özet yeter |
| 5. Boot paketi kendisi | **asla kesilmez** (bütçe bozulur) |

## §10. Stale Context (Bağlam Yanlışı) — Tespit ve Yenileme

> Dış doğrulama: bağlam bayatlaması ("stale indexing") bilinen bir başarısızlık modudur; Claude
> Code bunu "just-in-time glob/grep ile bayat indeksi **atlatma**" olarak tarif eder (Anthropic
> 2025-09-29 — §21/K1). JetBrains anlatısı da "bağlam ögelerini zaman damgasıla ve durum
> değişince geçersiz kıl" der (§21/K4).

| # | Tespit sinyali | Kök neden | Tepki (yenileme) |
|---|---|---|---|
| 1 | İddia ↔ disk uyuşmazlığı (dosya taşınmış/silinmiş) | boot'tan sonra dosya değişti | `Test-Path` + yeniden okuma |
| 2 | Sayı/istatistik "eski tarihli" | ölçüm yapılmadan kopyalanmış | §13 betiği ile yeniden ölç |
| 3 | Mojibake (`Ã¢â‚¬â€"` gibi) | yanlış kodlama/çift encode | kaynak UTF-8 yeniden oku; dosyayı **düzeltme**, raporla |
| 4 | Kırık wiki-link | hedef taşındı | `wiki-link-check.ps1` **kökten** |
| 5 | Boot listesi sürüm farkı | §7 listeleri karıştırılmış | 14/20/17 tablosuna dön (§7) |
| 6 | ADR etiketi ↔ repo kodu çelişkisi | ADR frozen, kod ilerlemiş | iki kanıt + `⚠️ VERIFICATION REQUIRED` |
| 7 | Uzun oturumda unutulan karar | pencere dolu, özet kaybı | §9/4 devretme + künye |

**Yenileme akışı:**

```text
[1] Şüphe belir → [2] disk kanıtı çek (Test-Path / ReadAllLines / grep)
  → [3] iddia ile karşılaştır → [4] uyuşmazlık: eski bilgiyi KULLANMA
  → [5] taze okuma + ölçümlü sayı → [6] sonuç künyeli yaz
```

**Tabu:** bayat bağlamı "güvenle" kullanmak. Durum: `⚠️ VERIFICATION REQUIRED` — sessiz düzeltme
**yok** (§23 kapı 6).

## §11. Lock Edinim Akışı (§12.2)

> Kaynak: `Dosya: .ai/AGENTS.md · Satır: 452-470` (§12.2 Lock Flow + §12.3 deadlock önleme).

```text
Ajan dosyaya erişmek ister
  → Lock acquire (max 30s bekleme)
    → Başarılı → dosyayı düzenle
    → Başarısız → kuyruk → öncelik sırası (CRITICAL > HIGH > MEDIUM > LOW)
      → Timeout → escalation
```

| Kural | Değer | Kanıt |
|---|---|---|
| Kilitleme süresi | Max 30 sn | `Dosya: .ai/AGENTS.md · Satır: 446` (§12.1) |
| Deadlock prevention | Timeout 30s · CRITICAL diğer kilidi kırar · MO en eskiyi kırar · queue reset | `Satır: 462-470` (§12.3) |
| Logging | Lock acquire/release `log.md`'ye yazılır | `Satır: 449` |
| Bellek modu | `Locked` (max 30s) | `Dosya: .ai/MEMORY.md · Satır: 95` |
| Eşzamanlı erişim senaryosu | Context Lock + Queue | `Dosya: .ai/AGENTS.md · Satır: 408` (§17/1) |

**Repo durumu (negatif kanıt — dürüst yazım):** `shared/src/**` içinde `Lock` kelimesi
grep taraması **0 isabet** (2026-10-17) → lock mekanizmasının PHP uygulaması **repo'da YOK**;
akış vault kuralı olarak **PLANLI/manuel** uygulanır. `⚠️ VERIFICATION REQUIRED`: hangi fazda
gerçekleşeceği bilinmiyor (§27/Q2).

## §12. Hata Modları

| # | Hata | Semptom | Tespit | Tepki |
|---|---|---|---|---|
| 1 | Yanlış dosya okuma (konu ≠ dosya) | cevap vault'ta var ama boş sonuç | §6 eşlemesi atlanmış | A-3'e dön |
| 2 | Kırık wiki-link | hedef açılamıyor | `wiki-link-check.ps1` (kökten) | raporla; 6 bilinen sahte kırık **onarılmaz** |
| 3 | Mojibake | `Ã¢â‚¬â€"` gibi bozuk dizge | UTF-8 ReadAllLines gözlemi | kaynağı yeniden oku; dosyayı **düzeltme** (subagent yasak) |
| 4 | Hayalet boot satırı | listedeki dosya diskte yok | `Test-Path` false | satırı sil/raporla; boot'a dokunma |
| 5 | Boot↔§24.2 karışıklığı | 14 mü 20 mi tartışması | §7 tablosu | 14 kanonik der, kapat |
| 6 | Token şişmesi | görev başarısız | §9 ölçüm | daralt/devret |
| 7 | Stale bağlam | eski iddia ile üretim | §10 | yenile |
| 8 | Lock çakışması | iki ajan aynı dosyada | §11 kuyruk | öncelik + timeout |
| 9 | `.ai/` içinden gate betiği | 19 sahte "PNG yok" gibi | kök kuralı ihlali | **kökten** çalıştır |
| 10 | Gereksiz vault üretimi | boot'a/ADR'ye yeni dosya | manifest dışı yazım | DUR; "manifest dışı üretim yasak" |
| 11 | 3 başarısız düzeltme | aynı hata tekrarı | tur sayacı | DUR — şüpheli varsayımı yaz, üst karar |

## §13. PowerShell Ölçüm Kalıpları (A — boot bütçesi)

> ✅ **Çalıştırılabilir PowerShell betiği** — repo kökünden (`C:\www\coremusic.net`) çalışır;
> sayılar 2026-10-17 disk ölçümüdür (§14 tablosu).

```powershell
# A) Boot bütçesi: satır sayısı + tahmini token (satir x 13) — §9 adım 1
$boot = @('AGENTS.md','.ai\CLAUDE.md','.ai\AGENTS.md','.ai\WORKFLOW.md','.ai\brain.md',
          '.ai\ROLE.md','.ai\index.md','.ai\keys.md','.ai\MEMORY.md','.ai\log.md',
          '.ai\ULTRA-THINKING.md','.ai\engine.md','.ai\glossary.md','.ai\VISION.md',
          '.ai\PROJECTS.md')
$total = 0
foreach ($f in $boot) {
    $p = Join-Path (Get-Location) $f
    if (Test-Path $p) {
        $n = [IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8).Count
        $t = [int]($n * 13)
        $total += $t
        "{0,-28} satir={1,-5} token~{2}" -f $f, $n, $t
    } else { "{0,-28} MISSING" -f $f }
}
"BOOT toplam token~{0}  (kapı: raporla, kesme degil)" -f $total
```

## §14. PowerShell Ölçüm Kalıpları (B — üretim kapısı)

```powershell
# B) Bu dosyanin kapisi: >=500 + Authority + FM 10 alan tek seferde
$p = '.ai\architecture\K015-AI\context\context-surucu.md'
$lines = [IO.File]::ReadAllLines((Join-Path (Get-Location) $p), [Text.Encoding]::UTF8)
"satir    => {0}   (kapı >=500: {1})" -f $lines.Count, ($lines.Count -ge 500)
$auth = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
"authority=> {0}" -f ($lines[$lines.Count - 1] -eq $auth)
$pat = '^(node_id|node_type|status|parent_id|children|depends_on|evidence|validation|open_questions|group):'
"fm 10    => {0}" -f ((Select-String -Path $p -Pattern $pat).Count -eq 10)
```

## §15. PowerShell Ölçüm Kalıpları (C — stale/hayalet/mojibake)

```powershell
# C) Boot hedefleri hayalet taraması + mojibake gozlemi (§10/1, §10/3)
$targets = @('.ai\CLAUDE.md','.ai\AGENTS.md','.ai\WORKFLOW.md','.ai\brain.md','.ai\ROLE.md',
             '.ai\index.md','.ai\keys.md','.ai\MEMORY.md','.ai\log.md','.ai\ULTRA-THINKING.md',
             '.ai\engine.md','.ai\glossary.md','.ai\VISION.md','.ai\PROJECTS.md')
$ghost = 0
foreach ($t in $targets) { if (-not (Test-Path $t)) { $ghost++; "GHOST => $t" } }
"ghost => {0} (kapı: 0)" -f $ghost

# Mojibake gozlemi: UTF-8 çift kodlama dizgeleri (dosya DÜZELTILMAZ, sadece rapor)
$mm = '.ai\MEMORY.md'
"mojibake hit (MEMORY.md) => {0}  (rapor-only; duzeltme yetkisi MO)" -f `
     (Select-String -Path $mm -Pattern 'Ã‚Â|Ã¢â‚¬â€"').Count
```

**2026-10-17 gözlem (gerçek):** `.ai/MEMORY.md` okumasında `Ã‚§` / `Ã¢â‚¬â€"` biçiminde mojibake
dizgeleri gözlendi (ör. `Satır: 3, 26, 311`) — bu **rapor-only** bir bulgudur; subagent dosyayı
düzeltmez (§24 yasak).

## §16. Repo Kanıtı — PHP Bağlam Yüzeyi (`shared/src/AI/`)

> ✅ **Repo kanıtı** — aşağıdaki bloklar `shared/src/AI/AIOrchestrator.php` ve `MemorySystem.php`
> içinden alınmış birebir alıntılardır (uydurma yok; satır sonundaki yorumlar künyedir).

```php
    // AIOrchestrator — oturum baglami (gorev basi bağlam girdisi)
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
```

```php
    // MemorySystem — session/persistent/hiyerarsi + suresi gecen kayit temizligi
    public function setSession(string $sessionId, string $key, mixed $value, int $ttl = 0): bool; // Satır 45
    public function getPersistent(string $key): mixed;                    // Satır 104
    public function remember(string $key, callable $loader, string $level = 'l2',
                             int $ttl = 3600): mixed;                     // Satır 123
    public function getHierarchy(): array;                                // Satır 152
    public function pruneExpired(): int;                                  // Satır 183
```

**Sürücü ile bağlantısı (üç satır):**

| Repo davranışı | Sürücüdeki karşılığı | Künye |
|---|---|---|
| `getContext/updateContext` — session-bazlı bağlam birleştirme | §10 stale yenileme (taze veri ile güncelleme) | `AIOrchestrator.php · Satır: 154-169` |
| `remember(..., $ttl)` — TTL'li önbellek | §10/2 bayatlık sinyali (TTL = zaman damgası) | `MemorySystem.php · Satır: 123` |
| `pruneExpired()` — süresi geçen temizliği | §10 yenileme akışı (eskiyi at) | `MemorySystem.php · Satır: 183` |

**Negatif kanıt cümleleri (gerçek grep 0 isabet):**

| İddia | Tarama | Sonuç |
|---|---|---|
| "Repo'da bu alan kodu YOK: **lock mekanizması (Context Lock)**" | `Lock` → `shared/src/**` | **0 isabet** (2026-10-17) |
| "Repo'da bu alan kodu YOK: **token bütçe hesabı / context budget**" | `token.*budget\|contextBudget` → `shared/src/**` | **0 isabet** |
| Vault boot'unu otomatik okuyan PHP katmanı | `.ai/` okuyan kod | ADR-030: vault pipeline **PLANNED** |

## §17. İllüstratif — Python Context Budget Calculator

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# §9 ariginin ruhu: satir basina ~13 token, butce asiminda daraltma adayi uret
from pathlib import Path

LINE_TOKENS = 13  # yaklasik ortalama (olcum: ReadAllLines x 13)

def file_tokens(path: Path) -> int:
    return len(path.read_text(encoding="utf-8").splitlines()) * LINE_TOKENS

def plan(boot: list[Path], extra: list[Path], budget: int = 20_000):
    used = sum(file_tokens(p) for p in boot)          # boot asla kesilmez (§9/5)
    spill, cut = [], []
    for p in extra:                                    # ek okumalar kesilir
        t = file_tokens(p)
        if used + t <= budget:
            used += t
        else:
            cut.append(p.name)                         # daraltma adayı
    spill = [p.name for p in extra if p.name not in cut]
    return {"used": used, "keep": spill, "cut": cut}
```

## §18. İllüstratif — Go Boot Chain Loader

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package boot

// Chain, §6 tablosunun 14 dosyalık okuma sırasıdır (kanonik: AGENTS.md §24.2).
type Chain struct{ Files []string }

// P0 → P1 → P2 sırası korunur; paralel okuma YASAK (MEMORY §5: sirali bagimlilik).
func (c Chain) Load(exists func(string) bool) []string {
    var loaded []string
    for _, f := range c.Files {
        if exists(f) { // hayalet satır korunmaz (§12/4)
            loaded = append(loaded, f)
        }
    }
    return loaded // eksik dosya listesi raporlanır, boot'a dokunulmaz
}
```

## §19. İllüstratif — TypeScript Stale Context Detector

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
interface Cited { file: string; line: number; measuredAt: string; }

/** §10/1: iddia ↔ disk uyuşmazlığı — disk kanıtı yoksa bayat. */
export async function isStale(c: Cited, diskLines: number): Promise<boolean> {
  // Künye "olcum" iddiası taşıyorsa tarih zorunlu; tarihsiz = bayat sayılır.
  if (!c.measuredAt) return true;
  // Ölçüm yenilendiyse eski künye geçersizdir (stale indexing — §21/K1).
  return c.line > diskLines || c.line < 1;
}

/** Devretme kararı (§8/3): bütçe taşınca görev parçası subagent penceresine. */
export const shouldDelegate = (usedTokens: number, budget = 20_000) =>
  usedTokens > budget * 0.8;
```

## §20. Dış Kaynak Doğrulaması (web — 2026-10-17)

| # | İddia (sürücü ile ilişkisi) | Kaynak | Durum |
|---|---|---|---|
| K1 | "Context engineering = sınırlı pencereye **en küçük** yüksek-sinyal token kümesi"; CLAUDE.md boot'a girer, glob/grep **just-in-time** bayat indeksi atlatır (§1, §4 A-4) | Anthropic — *Effective context engineering for AI agents*, 2025-09-29 · `anthropic.com/engineering/effective-context-engineering-for-ai-agents` | ✅ doğrulandı |
| K2 | Context editing bayat araç çağrılarını temizler; memory tool pencere dışı dosya sistemi sağlar; combined **+%39** (§9, §10) | Anthropic — *Managing context on the Claude Developer Platform*, 2025-09-29 · `anthropic.com/news/context-management` | ✅ doğrulandı |
| K3 | Dört strateji: **write / select / compress / isolate**; auto-compact ~%95 dolulukta (§8, §9) | LangChain — *Context Engineering*, 2025-07-02 · `langchain.com/blog/context-engineering-for-agents` | ✅ doğrulandı |
| K4 | Token budgeting = çağrı öncesi bütçe kararı; bağlam ögelerini **zaman damgasıla, durum değişince geçersiz kıl** (stale context — §10) | JetBrains — *How to Manage AI Agent Context Windows* · `jetbrains.com/pages/ai-agents/architecture/context-management` | ✅ doğrulandı |

> Uydurma URL yasak; dört kaynak da 2026-10-17 aramasında erişilip özetlenmiştir. Vault içi
> otorite (§7 boot, §11 lock) vault dışı kaynağın **altındadır** — çelişkide vault kazanır.

## §21. Token Ekonomisi

| # | Kural | Sonuç | Kanıt |
|---|---|---|---|
| 1 | Toplu okuma yasak — boot'ta tüm vault okunmaz | bütçe korunur | `Dosya: kök AGENTS.md · Satır: 282` |
| 2 | Boot'ta olan dosya yeniden okunmaz | çift israf yok | §4 A-1 |
| 3 | P0 → P1 → P2 sırası; paralel okuma desteklenmez | sıralı bağımlılık | `Dosya: .ai/MEMORY.md · Satır: 336` |
| 4 | Uzun dosyada §-bazlı okuma | ilgisiz bölüm girmez | `MEMORY.md §16` (max 1000 satır) |
| 5 | Boot bütçesi ~15 dosya, ölçülebilir | §13/A betiği | ölçüm §14 |
| 6 | 80% eşiğinde devretme | pencere dolmadan subagent | §19 `shouldDelegate` |
| 7 | Boot paketi asla kesilmez | zincir bozulmaz | §9/5 |

## §22. Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem / komut |
|---|---|---|---|
| 1 | Frontmatter | 10 alan eksiksiz | §14/B `fm 10` |
| 2 | Satır sayısı | `>= 500` | `[IO.File]::ReadAllLines(...).Count` |
| 3 | Authority son satır | birebir | §14/B `authority` |
| 4 | Boot listesi değişmedi | 14 kanonik korundu | `Test-Path` döngüsü (§15/C) |
| 5 | Hayalet satır | boot hedeflerinin tümü diskte | §15/C `ghost => 0` |
| 6 | Stale işaret | bayat iddia `⚠️`/`UNKNOWN` ile işaretli | §10 gözden geçirme |
| 7 | Mojibake | gözlem rapor-only, dosya düzeltmesi yok | §15/C |
| 8 | Kapsam dışı yazım | 0 dosya | `git status --porcelain` sayacı |
| 9 | Placeholder | şablon değişkeni (üçlü sürgü ile `\{`+`{`) yok | `Select-String` → 0 |
| 10 | `git commit` | **0** (orkestratöre ait) | yasak (§24) |

## §23. Yasaklar ve Sınırlar

| # | Yasak | Neden |
|---|---|---|
| 1 | Boot listesini değiştirmek/eklemek | 14 kanonik sabit; sayım zinciri bozulur |
| 2 | Boot'ta olan dosyayı yeniden okumak | token israfı (§21/2) |
| 3 | Toplu/tahmini okuma ("tüm .ai'yi oku") | bütçe taşması (§9) |
| 4 | Bayat bağlamı güvenle kullanmak | üretim hatası → §10 zorunlu |
| 5 | Mojibake/bozuk dosyayı onarmak (subagent) | yetki MO; düzeltme → üst karar |
| 6 | `.env` / credential'ı bağlama almak | sızıntı |
| 7 | `.ai/` içinden gate betiği çalıştırmak | sahte başarısızlıklar (kök §13.6) |
| 8 | `git commit` atmak | orkestratöre aittir |
| 9 | Bu dosya dışına yazmak / adı değiştirmek | kapsam dışı yazım = ihlal |
| 10 | Boot'a ihtiyaç dışı dosya eklemek | boot süresi/örtüsü bozulur |

## §24. Sık Yapılan Hatalar (anti-pattern)

| # | Yanlış | Doğru | Kaynak |
|---|---|---|---|
| 1 | "14 mü 20 mi?" diye tartışmaya açmak | 14 kanonik; 20 genişletilmiş — kapatılmış fark | §7 (§25.4) |
| 2 | Göreve başlar her şeyi okumak | A-2 paket seç, A-4 yalnız ilgili | §4 |
| 3 | Bütçeyi ölçmeden kesmek | önce ölç, sonra kes, sonra yeniden dene | §9 |
| 4 | Bayat künyeyi tekrarlamak | disk kanıtı + ölçümlü sayı | §10, §13 |
| 5 | `.ai/` gizli diye arama "0 sonuç" | `hidden: true` / doğrudan path | kök `AGENTS.md §7/2` |
| 6 | Uzun dosyayı baştan sona okumak | §-bazlı bölüm okuma | §21/4 |
| 7 | Lock akışını "var" sanmak | repo'da kod **YOK** — vault kuralı | §11 negatif kanıt |
| 8 | Kanıtsız "bitti" | kapı tablosu + kapanış listesi | §22 |

## §25. Kapanış Kontrol Listesi (her bağlam görevi sonunda)

| # | Soru | Zorunlu cevap |
|---|---|---|
| 1 | A-1…A-8 sırası izlendi mi? | evet / gerekçesi |
| 2 | Okuma §6 eşlemesinden mi geldi (rastgele mi)? | eşleme |
| 3 | Bütçe ölçüldü mü (§13/A)? | evet |
| 4 | Taşma varsa kesme/devretme uygulandı mı? | evet / N-A |
| 5 | Stale kontrolü yapıldı mı (bayat iddia 0)? | evet |
| 6 | Lock gerekiyorsa edinildi mi (§11)? | evet / N-A |
| 7 | Her iddia `Dosya:` + `Satır:` künyeli mi? | evet |
| 8 | Bulunamayan için `UNKNOWN`/`⚠️` yazıldı mı? | evet |
| 9 | Boot listesi değişti mi? | **hayır** |
| 10 | Kapsam dışı yazım / `git commit`? | **0 / 0** |

## §26. Açık Sorular

| # | Soru | Durum |
|---|---|---|
| Q1 | `depends_on: [context-anayasa]` — bu turda `context/` altında yalnız `index.md` diskteydi (glob: 1 dosya). `context-anayasa` paralel üretiliyor mu? | `⚠️ VERIFICATION REQUIRED` |
| Q2 | Context Lock repo kodu **YOK** (§11) — hangi fazda PHP'ye taşınacak? ADR kararı var mı? | `⚠️ VERIFICATION REQUIRED` |
| Q3 | §24.2 "her görevde oku" ↔ kök §14 "yalnız ihtiyaç anında" gerilimi: hangi görevlerde 14 dosyanın **tamamı** zorunlu? | MO kararı (§7 kapatmaz) |
| Q4 | `MEMORY.md §5` 20. adım mockup indeksini "frontend görevlerinde zorunlu" der; §24.3 aynı kapıyı **domain istisnası** olarak verir — ikisi aynı mı? | muhtemelen evet; `⚠️ VERIFICATION REQUIRED` |
| Q5 | Token başına satır katsayısı (~13) ölçülmüş bir sabit mi, yoksa dosya-tipine göre ayrılmalı mı? | bilinmiyor — **uydurulmaz** |
| Q6 | Oturum ortasında boot listesi değişirse (orkestratör edit'i) bağlam bayatlar mı, session restart zorunlu mu? | WORKFLOW kararı |

## §27. Kaynaklar

| Kaynak | Yol | Kullanım amacı | Ölçüm (2026-10-17) |
|---|---|---|---|
| Agent registry SSOT | `.ai/AGENTS.md` | §24.2 (Satır: 499-517) · §24.3 (520) · §12.2-12.3 (452-470) · §25.4 (640) · §18 (571) | 775 satır |
| Memory/boot | `.ai/MEMORY.md` | §4 lifecycle (295) · §5 20 adım (311-336) · §16 limits (64-73) · §25.4 kaydı (605) | 728 satır |
| Boot zinciri | `AGENTS.md` (kök) | §7 Execution Loop (177) · §14 Boot Zinciri (273-282) | 285 satır |
| Anayasa | `.ai/CLAUDE.md` | guardrails, boot protokolü (§16) | 987 satır |
| Süreçler | `.ai/WORKFLOW.md` | §8.7A boot (referans) | 900 satır |
| Retrieval indeksi | `.ai/RAG.md` | §3 konu→dosya (fallback) | 177 satır |
| Keyword haritası | `.ai/keys.md` | keyword taraması (fallback) | 763 satır |
| Ebeveyn düğüm | `.ai/architecture/K015-AI/context/index.md` | kapsam sınırı + `children` durumu (Satır: 6, 30) | 50 satır |
| Yapı referansı | `.ai/architecture/K015-AI/rag/rag-surucu.md` | H1 + § + karar ağacı + betik iskeleti | 779 satır |
| Repo bağlam kodu | `shared/src/AI/AIOrchestrator.php` (210) · `MemorySystem.php` (242) | §16 PHP kanıtı | 210 / 242 |
| Dış kaynaklar | §20 K1-K4 | context engineering / stale / bütçe doğrulaması | 4 kaynak |

## §28. Gate (özet)

§1-§28 tamam: ELI15 · kapsam · 3 boot kaynağı · 8 adımlı A-akışı + karar ağacı · 11 satır görev→bağlam ·
14 satır konu→dosya · 14/20/17 çelişki çözümü · genişletme/daraltma 6 tetikleyici + 3 eksenden ağaç ·
5 adımlı token taşması · 7 sinyalli stale context + yenileme akışı · lock akışı 5 kural ·
11 hata modu · PowerShell 3 betik (A-C) · PHP 2 blok + 3 negatif kanıt · illüstratif 3 blok
(Python/Go/TS) · 4 dış kaynak doğrulaması · 7 token kuralı · 10 kapı · 10 yasak · 8 anti-pattern ·
10 soruluk kapanış · 6 açık soru · 11 kaynak.

**Kapılar:** satır `>= 500` · FM **10 alan** · Authority son satır birebir · kapsam dışı yazım **0** ·
`git commit` **0**.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
