---
node_id: context-anayasa
node_type: module
status: active
parent_id: context
children: []
depends_on: [context-index]
evidence:
  - ".ai/AGENTS.md §24+§13+§12 (boot/okuma sırası/lock, okundu 2026-10-17)"
  - "root CLAUDE.md + root AGENTS.md §14 (boot zinciri kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# context-anayasa - Ajan Bağlam Kuralları

> **Bu dosya ne değildir:** bir context window **uygulama kodu** (compaction/packer motoru) ve
> `.ai/AGENTS.md` §24/§13/§12 metinlerinin **tam kopyası** değildir; burada **bağlam anayasası** vardır —
> bir ajan oturumda hangi bilgiyi ne zaman, hangi sırayla, hangi bütçeyle bağlama alır, bağlam nerede
> kilitlenir, nerede temizlenir. SSOT çelişkisinde `.ai/AGENTS.md` kazanır.

## §1 Rol (ELI15)

Context alt-domaini, bir CoreMusic ajanının **çalışma hafızasına (context window) neyin gireceğini**
tanımlayan kuralların sahibidir: boot zinciri, okuma sırası, token bütçesi, lock, hijyen ve temizlik.
**eli15:**模型ın gözü ne kadar çok gereksiz şey görürse o kadar az görür — bu dosya "göze ne
gireceğini" sınırlayan perde kuralıdır. Bağlam bir **kaynak**tır, sonsuz değildir; kötü bağlam
dürüst olmayan çıktı üretir.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Bağlam kimin sorumluluğu? | Tüm ajanlar — MO koordine eder | `.ai/AGENTS.md:65` (Scope: context lock mekanizması) |
| Ne zaman bağlam kurulur? | Her görev başında (boot + §24.2 okuma) | `.ai/AGENTS.md:499` |
| Bağlam taşarsa ne olur? | Görev başarısız (Token overflow) | `.ai/AGENTS.md §18` satır 4 |
| Boot neyi bağlar? | CLAUDE.md → @.ai/AGENTS.md → yalnız ihtiyaç anında @.ai/ | root `AGENTS.md` §14 tablosu |
| Kilit neyi korur? | Eşzamanlı dosya erişimi (Context Lock) | `.ai/AGENTS.md:439` |

> **eli10:** Bu dosya, "hangi dosya ne zaman okunur" sorusunun cevabını ve "tüm vault'u oku" yasağını
> tek yerde tutar. > **eli15:** Yönlendirme/kural metni bağlamın kendisidir; ikisi karışınca hem token
> hem doğruluk kaybedilir (`Dosya: .ai/AGENTS.md · Satır: 485` §24 başlığı).

## §2 Kapsam (in / out) — context ≠ memory ≠ log

| Kavram | Nedir | Ömrü | Sahip | Kanıt |
|--------|-------|------|-------|-------|
| **Context** | Oturum anlık çalışma hafızası (boot + okunan dosyalar + mesajlar) | Tek görev/oturum | Tüm ajanlar | `.ai/AGENTS.md:485` §24 |
| **Memory** | Oturumlar arası kalıcı not (MEMORY.md session state) | Oturumlar arası | MO + vault-updater | `.ai/AGENTS.md §7.7` (MEMORY.md güncellenir) |
| **Log** | Append-only audit trail (log.md) | Kalıcı, yalnız ekleme | Tüm ajanlar (append-only) | `.ai/AGENTS.md:107` (§5 Domain Boundaries) |
| Context içine girer | Görevle ilgili `.ai/` dosyaları, kod dosyaları, mockup | — | — | `.ai/AGENTS.md:499` §24.2 |
| Context içine girmez | Secret, `.env`, credential, ilgisiz vault dosyaları | — | Security Engineer | `.ai/AGENTS.md §13.9` md 3 |

**Sınır kuralı:** memory ve log bağlamın **kaynakları** olabilir (okunarak çekilir) ama bağlamın
kendisi değildir; bağlam dolar, memory/log dolmaz — memory diskte, bağlam pencerede yaşar.

## §3 Boot Zinciri (root AGENTS.md §14 — gerçek tablo)

Boot = her session başında otomatik/şartlı okunan dosya paketi. Bağlayıcı sıra (birebir alıntı):

| # | Dosya | Ne verir | Ne zaman |
|---|-------|----------|----------|
| 1 | `AGENTS.md` (kök, bu dosya) | Ana kurallar: akış, zero-hallucination, execution loop, persona hiyerarşisi | Her zaman (harness boot'u) |
| 2 | `CLAUDE.md` (kök) | Skill Registry (SSOT) · vault bağlantı tablosu · mimari referans · web doğrulama | Her zaman — bu dosyadan linklenir, atlanmaz |
| 3 | `@.ai/AGENTS.md` | Tam agent registry (11 agent routing — profil indeksi `.ai/.agents/*.md` 11 dosya) | Her görevde |
| 4 | `@.ai/` diğer dosyalar (CLAUDE/WORKFLOW/RAG/...) | Anayasa, süreç, retrieval | **Yalnız ihtiyaç anında** (`@` ile) |

Ek kayıt: §24.2 (14 dosya) ile MEMORY.md §5 (20 adım) farkı bilinen durumdur; kanonik kök boot
listesi §24.2 + WORKFLOW §8.7A'dır (14 .ai kök dosya); FULL boot 17 öğedir
(`Dosya: .ai/AGENTS.md · Satır: 642` §25.4). Çift yönlü bağlantı: kök `AGENTS.md` → `CLAUDE.md`,
`CLAUDE.md` → `AGENTS.md`; ikisi tek boot paketidir, biri diğerinin yerine geçmez.

## §4 Okuma Sırası P0→P3 (§13 Reading Order)

§13, bağlam doldurma sırasını ve token aşımı önlemini tanımlar (birebir alıntı — `.ai/AGENTS.md:136`):

| # | Kural | Bağlam sonucu | Kanıt |
|---|-------|---------------|-------|
| 1 | P0 → P1 → P2 → P3 sırasıyla okunur | Öncelikli dosya önce bağlama girer | `.ai/AGENTS.md §13` madde 1 |
| 2 | Fallback: `index.md` | Eşleşme yoksa katalog taranır | `.ai/AGENTS.md §13` madde 2 |
| 3 | Token aşımı önlenir: gereksiz dosya okunmaz | Bağlam israfı yasak | `.ai/AGENTS.md §13` madde 3 |
| 4 | İstisna: görsel referanslar (`.ai/ui-design/screens/**`, `.ai/.png/**`) | Görsel bağlamı okuma sırasına tabi değildir | `.ai/AGENTS.md §13` madde 4 |
| 5 | Şablon zorunlu okuma (Guardrail #16) — Kalıp A-D | ui-design yazımı öncesi şablon bağlama girer | `.ai/AGENTS.md §13` madde 5 |
| 6 | Gate script'leri yalnız repo kökünden çalışır | Yanlış dizin = sahte sonuç = yanlış bağlam | `.ai/AGENTS.md §13` madde 6 |

P0 kök boot paketi (§3 tablosu 1-3), P1 görev-konu `.ai/` dosyaları (RAG §3 eşlemesi), P2 ilgili
kod/test dosyaları, P3 destekleyici referans (glossary, reports) olarak okunur — P-atanmamış dosya
okunursa §13 madde 3 ihlalidir.

## §5 Domain-Based Reading (§24.3 — gerçek tablo)

Bağlam, ajan domainine göre **daraltılır**; her ajan yalnız kendi zorunlu okuma setini bağlama alır:

| Agent | Zorunlu Okuma | Kanıt |
|-------|---------------|-------|
| Backend | `.ai/.decisions/index.md` (ADR-083), `shared/src/PageRouter/` | `.ai/AGENTS.md:520` §24.3 |
| Frontend | `ui-design/01-mockup-index.md`, `02-component-inventory.md`, Kalıp A-D şablonları (+ CSS görevinde `css-template.md` + kök `notes.md`) | `.ai/AGENTS.md:520` §24.3 |
| Security | `.ai/.decisions/index.md` (ADR-010), `shared/src/Middleware/` | `.ai/AGENTS.md:520` §24.3 |
| Data | `.ai/.sql/mysql/*.sql`, `shared/src/Database/` | `.ai/AGENTS.md:520` §24.3 |
| Embedded | `projects/NevaEngine/*.md` (38 md, 2026-10-15 ölçümü), `electronic/dsp/*.md` (10), `electronic/firmware/*.md` (7) | `.ai/AGENTS.md:520` §24.3 |
| QA | `ui-design/04-accessibility-gaps.md`, `ui-design/screens/**/*.md`, `.ai/reports/` | `.ai/AGENTS.md:520` §24.3 |
| DevOps | `.ai/ecosystem/*.md` | `.ai/AGENTS.md:520` §24.3 |

Kural: bir Backend görevinde Embedded dosyaları bağlama girmez (ve tersi) — domain dışı okuma,
§13 madde 3 (gereksiz dosya okunmaz) ihlalidir; dosya sayısı iddiaları yalnız disk ölçümüyle yazılır.

## §6 Ultra Thinking Protocol — 5 Adım (§24.1)

Kod yazmadan önce bağlam kurulumunun zorunlu protokolü (birebir tablo — `.ai/AGENTS.md:489`):

| Adım | Kontrol | Kaynak | Timeout |
|------|---------|--------|---------|
| 1. Vault Oku | CLAUDE.md → AGENTS.md → WORKFLOW.md → brain.md → ROLE.md → ilgili ADR'ler | `.ai/` vault | Max 25s |
| 2. Bağlamı Anla | Domain, katman, dosyalar, bağımlılıklar | Mevcut kod | Değişken |
| 3. Hata Kontrolü | Syntax, imports, types, style, security | LSP + Manuel | Anlık |
| 4. Sonuç Tahmini | Etki alanı, edge cases, performance | Düşünce | Değişken |
| 5. Doğrulama | LSP, typecheck, test, template uyumu | Build araçları | Anlık |

Adım 1 bağlam **kurulumu**, adım 2 bağlam **anlayışı**, adım 5 bağlamın **doğruluğu**dur; adım 1
25 saniyeyi aşarsa gereksiz dosya bağlama alınmış demektir → §8 bütçesine dönülür.

## §7 .ai Reference Tracking — 14 Dosya (§24.2)

"Her görev başında bu dosyaları OKU" listesi — canonical kök `.ai/` okuma seti (`.ai/AGENTS.md:499`):

| Sıra | Dosya | Amaç |
|------|-------|------|
| 1 | `.ai/CLAUDE.md` | AI anayasası, 16 Hard Guardrails |
| 2 | `.ai/AGENTS.md` | Agent sınırları, routing (SSOT) |
| 3 | `.ai/WORKFLOW.md` | Süreçler, fazlar |
| 4 | `.ai/brain.md` | Mimari kararlar |
| 5 | `.ai/ROLE.md` | Rol tanımı |
| 6 | `.ai/index.md` | Master katalog |
| 7 | `.ai/keys.md` | Keyword haritası |
| 8 | `.ai/MEMORY.md` | Session hafızası |
| 9 | `.ai/log.md` | Audit trail |
| 10 | `.ai/ULTRA-THINKING.md` | Ultra düşünme protokolü |
| 11 | `.ai/engine.md` | Orkestrasyon motoru — agent koordinasyonu |
| 12 | `.ai/glossary.md` | Terim sözlüğü — kod-referanslı |
| 13 | `.ai/VISION.md` | Vizyon ve yol haritası |
| 14 | `.ai/PROJECTS.md` | Proje envanteri |

Bunlar **boot** okumasıdır (§3 satır 3-4 bağlamı); görev-konu dosyaları bu listenin yerine geçmez,
bu liste görev-konu dosyalarının **üstüne** biner.

## §8 Token Bütçesi Kuralları

Bağlam bütçesi: boot paketi + görev dosyaları + kod + çıktı toplamı tek pencerede yaşar; taşma
görevi bitirir (§18 warning 4: "Token overflow → Görev başarısız").

| # | Bütçe kalemi | Kural | Kanıt |
|---|--------------|-------|-------|
| 1 | Boot (§3 + §7) | Şartsız — sabit maliyet, ilk bağlama girer | `.ai/AGENTS.md §14` (root) |
| 2 | Görev konu dosyaları | RAG §3 eşlemesine göre **en dar** hedef önce | `.ai/RAG.md:103` |
| 3 | Kod dosyası | Yalnız ilgili dosya/satır aralığı; tam klasör tarama yasak | `.ai/AGENTS.md:40` (16 maddelik inceleme ≠ toplu okuma) |
| 4 | Görsel (mockup/PNG) | İstisna kapsamı — okuma sırası dışı, ama görev şartı | `.ai/AGENTS.md §13` madde 4 |
| 5 | Çıktı/rapor | Özet odaklı; uzun alıntı yerine künye (`Dosya:`/`Satır:`) | bu anayasa §18 |
| 6 | Taşma | DUR → compact/özetle → devam; tahminle doldurma yasak | `.ai/AGENTS.md §18` madde 4 |

Ölçü (dış kaynak, doğrulandı 2026-10-17): Anthropic, bağlamı "sonlu kaynak" ve token artışını
"accuracy/recall degradation (context rot)" olarak tanımlar — bütçe kuralı bu gerçeğin vault karşılığıdır
(kaynak §28-1, §28-2).

## §9 Context Hijyeni — Toplu Okuma Yasağı

En sık bağlam ihlali: "başta tüm vault'u oku". Bu **açıkça yasaktır** (birebir alıntı, root `CLAUDE.md:80-82`):

> "Uzun referanslar `.ai/` vault'tadır ve **yalnızca ihtiyaç anında** `@` ile okunur. 'Başta tüm
> vault'u oku' YASAKTIR."

| # | Hijyen kuralı | Doğru davranış | Kanıt |
|---|---------------|----------------|-------|
| 1 | Toplu okuma yasağı | Yalnız görevin gerektirdiği `@.ai/` dosyaları | root `CLAUDE.md:82` |
| 2 | Boot ≠ keşif | Boot paketi (§3/§7) biter; konu dosyaları RAG §3 ile seçilir | `.ai/AGENTS.md:642` |
| 3 | Gereksiz dosya yasağı | P-atanmamış/ilgisiz dosya okunmaz | `.ai/AGENTS.md §13` madde 3 |
| 4 | Yinelenen okuma | Aynı dosya görev başına 1 kez okunur; ikinci okuma künyeyle | `.ai/RAG.md:63` |
| 5 | Özet yerine ham kopya | uzun metin alıntılmaz, künye + hedef satır yazılır | bu anayasa §18 |
| 6 | İhlal sonucu | Token overflow → görev başarısız (§18 madde 4) | `.ai/AGENTS.md §18` |

## §10 Context Lock (§12 — gerçek tablo/satır alıntıları)

"Eşzamanlı erişimi önlemek için dosya kilitleme mekanizması" (`Dosya: .ai/AGENTS.md · Satır: 439`).
Bağlamın **yazma** tarafını korur: iki ajan aynı dosyayı bağlarken kilit girer.

| Kurallar | Değer |
|---------|-------|
| Kilitleme süresi | Max 30 saniye |
| Deadlock prevention | MO en eski kilidi kırar |
| Öncelik | CRITICAL > HIGH > MEDIUM > LOW |
| Logging | Lock acquire/release `log.md`'ye yazılır |

Lock Flow (§12.2): `Ajan dosyaya erişmek ister → Lock acquire (max 30s bekleme) → Başarılı →
dosyayı düzenle · Başarısız → kuyruk → öncelik sırası → Timeout → escalation`. Deadlock önlemleri
(§12.3): timeout (30s sonrası serbest) · priority override (CRITICAL diğer kilidi kırar) · MO
intervention (en eski kilit kırılır) · queue reset (tüm kilitler sıfırlanır). Edge case 1 aynı
konuyu teyit eder: "Aynı dosyaya eşzamanlı erişim → Context Lock + Queue" (`.ai/AGENTS.md:199`).

## §11 Mockup Gate — Okunmadan Kod Yazılmaz

Bağlam kapısı: görsel bağlamı yoksa frontend kodu bağlamı eksiktir → DUR (birebir alıntı, §13):

> "CSS/HTML/JS/layout/bileşen görevlerinde `.ai/ui-design/` altındaki ilgili görsel okunmadan kod
> yazılamaz. Görsel okunamıyorsa DUR ve bildir."

| # | Gate | Eşik | Kanıt |
|---|------|------|-------|
| 1 | Mockup okuma | ui-design PNG/ekran spec'i bağlama girmediyse kod yazımı yasak | `.ai/AGENTS.md §13` (Mockup Before Frontend) |
| 2 | Referans sırası | PNG > ASCII art > Inventory > Tokens > Reference (01-10) | `.ai/AGENTS.md §7.2` |
| 3 | Şablon kapısı | Kalıp A-D okunmadıysa → DUR (Guardrail #16) | `.ai/AGENTS.md §7.2` |
| 4 | Gate script | Kökten çalıştırılmayan script sonucu geçersizdir | `.ai/AGENTS.md §13` madde 6 |
| 5 | Ölçüm (2026-09-29) | frontmatter 21 ekran/0 sorunlu · kalip-abc A:21·0 B:21·0 C:51·0 → GECTI · wiki-link 227/6 raporlanan = gerçek kırık 0 | `.ai/AGENTS.md §13` madde 6 |
| 6 | Bilinen 6 sahte kırık link | Düzeltme YASAK — şablon değişkeni/kaçış dizgesidir | `.ai/AGENTS.md §13` madde 7 |

## §12 Otomatik Temizlik (§24.4 — birebir tablo)

Bağlam kurulduktan sonra **çürüyen** bağlam öğeleri için zorunlu aksiyonlar (`.ai/AGENTS.md:546`):

| Durum | Aksiyon |
|-------|---------|
| LSP hata tespit | "FIX IMMEDIATELY" mesajı, devam yasak |
| Hallüsinasyon | "VERIFICATION REQUIRED" etiketi |
| Çelişki | DUR + kullanıcıya sor |
| Eksik dosya | Hemen tamamla veya sil |

Ek bağlam hijyeni (dış doğrulama, §28-3): eski tool çıktıları, ilgisiz mesaj geçmişi ve kullanılmayan
düşünce blokları bağlamı "diminishing returns" ile kirletir; vault karşılığı burada §9 (okuma yasağı)
+ §8 (bütçe) + bu §12 (etiketleme/DUR) üçlüsüdür. Temizlik = silmek değil; **etiketlemek ve
kaynakça ile yeniden çekebilmektir** (yeniden çekilebilir veri bağlamda ölü ağırlıktır).

## §13 Güvenlik — Secret Bağlama Girmez

| # | Kural | Doğru | Kanıt |
|---|-------|-------|-------|
| 1 | Credential bağlamı | `FIGMA_TOKEN`/`FIGMA_FILE_KEY` yalnız `.ai/.env.figma`'dan okunur (`.gitignore:69`) | `.ai/AGENTS.md §13.9` md 3 |
| 2 | Anahtar yazımı | Anahtar asla `.md`/`.json`/`.log` içine yazılmaz → bağlam dosyalarına da girmez | `.ai/AGENTS.md §13.9` md 3 |
| 3 | Log maskesi | Hassas veri log'da `[REDACTED]` ile maskelenir | `.ai/AGENTS.md §17` madde 3 |
| 4 | .env yüzeyi | `.env` yalnız Security Engineer alanında | `.ai/AGENTS.md:107` (§5) |
| 5 | Handover redaction | Çıktı doğrulamasında "Security: hassas veri redaction" kontrolü | `.ai/AGENTS.md §7.6` |
| 6 | Betik sabiti | `figma-extract.ps1`/`figma-tokens.ps1` gibi betiklerde sabit anahtar yoktur | `.ai/AGENTS.md §13.9` md 3 |

İhlal zinciri: secret bir `.md`'ye yazılır → o dosya boot/okuma listesine girer → tüm ajanların
baçlamına sızar → deşifre. Bu yüzden yasak üç katmanlıdır: yazma · loglama · bağlama.

## §14 Zero-Hallucination — Bağlam Kanıtsız İddia Üretmez

| # | Kural | Bağlam karşılığı | Kanıt |
|---|-------|------------------|-------|
| 1 | Repo/URL/API/dosya asla uydurulmaz | Bağlama giren her iddia disk künyesi taşır | `.ai/AGENTS.md:42` (Zero-Hallucination) |
| 2 | Bilinmeyen = `UNKNOWN` | Bağlam boşluğu uydurmayla doldurulmaz | `.ai/AGENTS.md:42` |
| 3 | Doğrulanamayan = `⚠️ VERIFICATION REQUIRED` | Eksik bağlam etiketlenir, tamamlanmış sayılmaz | `.ai/AGENTS.md:42` |
| 4 | Her iddia disk kanıtı olmadan yazılmaz | `Dosya:` + `Satır:` künyesi bağlamın fişiidir | `.ai/AGENTS.md` (root §3 madde 3) |
| 5 | Model hafızası ve web, vault'un altındadır | Bağlam hiyerarşisinde disk kazanır | `.ai/AGENTS.md` (root §3 madde 4) |
| 6 | Ölçüsüz sayı yasak | Dosya/satır sayıları yalnız ölçümle yazılır | `.ai/RAG.md:105` |

Bağlam kanıtsız iddia üretirse bunun adı hallüsinasyondur ve §12 otomatik temizlik aksiyonu
("VERIFICATION REQUIRED" etiketi) devreye girer — etiket zorunludur, sessiz düzeltme yasaktır.

## §15 Bağlam ↔ Hafıza ↔ Log — Derin Ayrım

| Soru | Context | Memory | Log |
|------|---------|--------|-----|
| Nerede yaşar? | Pencerede (RAM) | Disk (MEMORY.md) | Disk (log.md) |
| Ne taşır? | Görev anı kanıt + kurallar | Session state, todos | Append-only olay kaydı |
| Kim yazar? | Okuyan ajan | MO/vault-updater | Tüm ajanlar (yalnız ekleme) |
| Dolarsa ne olur? | Token overflow → görev başarısız | Dosya şişer → vault-sync | Kural ihlali yok (append-only) |
| Temizlik? | §9 hijyen + §12 etiketleme | vault-sync-post | Silinmez, maskeleme (§13) |
| Boot'a girer mi? | Evet (§3/§7) | Evet (sıra 8) | Evet (sıra 9) |

`MEMORY.md` §5 (20 adım) genişletilmiş okuma setidir; §24.2 (14 dosya) kanonik boot'tur — ikisi
çelişince §24.2 kazanır (`Dosya: .ai/AGENTS.md · Satır: 642`). Memory, bağlamın **dışında** durur
ve gerektiğinde bağlama **çekilir**; log asla bağlama toplu alınmaz (yalnız kilit/olay künyesi).

## §16 Dış Kaynak Doğrulaması — Context Engineering Literatürü

Vault kuralları 2026-10-17'de 3 resmi kaynakla çapraz doğrulandı (erişim tarihi 2026-10-17, §28):

| # | İlke (dış kaynak) | Vault karşılığı | Kaynak |
|---|-------------------|-----------------|--------|
| 1 | Context = "curating and maintaining the optimal set of tokens"; hedef "smallest possible set of high-signal tokens" | §8 bütçe + §9 hijyen | §28-1 |
| 2 | "Context rot": token arttıkça accuracy/recall düşer | §18 warning 4 (token overflow) + §8 | §28-2 |
| 3 | Just-in-time loading: her şeyi başta yükleme, referansla anlık çek | §4 P0→P3 + §7 on-demand `@` | §28-1 |
| 4 | Compaction / structured note-taking / sub-agent özetleme | §12 temizlik + §15 memory + subagent handover özeti | §28-1, §28-3 |
| 5 | "Irrelevant content degrades model focus" | §9 gereksiz okuma yasağı | §28-3 |

Çelişki kuralı: dış kaynak vault'u **değiştirmez** — yalnız doğrular; vault ile literatür çelişirse
`.ai/AGENTS.md` (SSOT) kazanır, literatür notu açık soruya (§27) taşınır.

## §17 Ölçüm Betikleri (PowerShell — gerçek kanıt betikleri)

Bağlam büyüklüğü **ölçülür**, tahmin edilmez. Betikler repo kökünden (`C:\www\coremusic.net`) çalışır:

```powershell
# Kanıt 1 — bu anayasanın üretim kapısı (>=500 satır, 2026-10-17)
$hedef = 'C:\www\coremusic.net\.ai\architecture\K015-AI\context\context-anayasa.md'
[IO.File]::ReadAllLines($hedef, [Text.Encoding]::UTF8).Count   # hedef: >= 500
```

```powershell
# Kanıt 2 — boot/okuma listesi envanteri (§7 14 dosyanın disk doğrulaması)
$boot = '.ai/CLAUDE.md','.ai/AGENTS.md','.ai/WORKFLOW.md','.ai/brain.md','.ai/ROLE.md',
        '.ai/index.md','.ai/keys.md','.ai/MEMORY.md','.ai/log.md','.ai/ULTRA-THINKING.md',
        '.ai/engine.md','.ai/glossary.md','.ai/VISION.md','.ai/PROJECTS.md'
$boot | ForEach-Object { '{0}  {1}' -f (Test-Path (Join-Path 'C:\www\coremusic.net' $_)), $_ }
```

| Betik (gerçek, §13.6) | Ne ölçer | Yer kuralı |
|-----------------------|----------|------------|
| `screens-frontmatter-check.ps1` | ekran spec frontmatter (21/0) | yalnız repo kökü |
| `kalip-abc-check` | Kalıp A/B/C uyumu (A:21·0 B:21·0 C:51·0 → GECTI) | yalnız repo kökü |
| `wiki-link-check.ps1` | wiki-link geçerliliği (227 link / 6 raporlanan = gerçek kırık 0) | yalnız repo kökü |
| `figma-tokens.ps1` | token üretimi kapısı (FAIL=0; Faz 9 hattı) | yalnız repo kökü |

`.ai/` içinden çalıştırılan gate **geçersizdir** — sahte "PNG yok" üretir (19 adet, §13.6) → yanlış
bağlam → yanlış karar. Ölçüm yeri de bağlam kuralıdır.

## §18 Repo Kanıtı — PHP Context Yüzeyi

**Repo'da bu alan kodu YOK:** bağlam packer/compactor/budgeter PHP sınıfı bulunmaz (2026-10-17
gözden geçirme). En yakın ilgili yüzey keyword-tabanlı retrieval iskeletidir, o da bağlam
**doldurma** motoru değil, yalnız arama iskeletidir:

| # | Yüzey | Durum | Künye |
|---|-------|-------|-------|
| 1 | Bağlam packer/compactor (PHP) | YOK — `VERIFICATION REQUIRED` sayılmaz, yokluğu yazılır | — |
| 2 | `KnowledgeBase` keyword matching | Yorum = niyet, kod iskeleti | `Dosya: shared/src/AI/KnowledgeBase.php · Satır: 48` |
| 3 | `KnowledgeBase::ragQuery` | Keyword-tabanlı iskelet (retrieval) | `Dosya: shared/src/AI/KnowledgeBase.php · Satır: 72-91` |
| 4 | Embedding/vektör kodu | 0 eşleşme — PLANNED (ADR-030) | `Dosya: .ai/.decisions/accepted/ADR-030-ai-strategy-core.md` (kanıt özeti: rag-anayasa §9) |

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```php
<?php
// Örnek: künyeli bağlam fişi şeması (illüstratif — repo kodu DEĞİL)
final class ContextEvidence
{
    public function __construct(
        private readonly string $file,   // Dosya:
        private readonly int $line,      // Satır:
        private readonly string $claim,  // iddia metni
    ) {}

    public function format(): string
    {
        return sprintf('Dosya: %s · Satır: %d — %s', $this->file, $this->line, $this->claim);
    }
}
```

## §19 İllüstratif — Python Context Window Budgeter

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# Boot + görev dosyalarını token bütçesine göre sıralar (illüstratif)
BUDGET = 8_000  # görev başına örneksel üst sınır (token)

def pack_context(boot: list[str], task_files: list[str], est) -> list[str]:
    picked, used = [], 0
    for path in boot + task_files:          # boot önce (§3), görev sonra (RAG §3)
        cost = est(path)
        if used + cost > BUDGET:
            break                            # taşma: §9 gereksiz okuma yasağı
        picked.append(path); used += cost
    return picked
```

| Kural | Karşılık | Kanıt |
|-------|----------|-------|
| Boot önce, görev sonra | §3 tablosu + RAG §3 en-dar-hedef | bu anayasa §8 |
| Bütçe aşımında dur | §18 warning 4 | `.ai/AGENTS.md §18` |
| Token tahmini | Tahmin etiketli olur (`UNKNOWN` kabul) | `.ai/AGENTS.md:42` |

## §20 İllüstratif — TypeScript Sliding-Window Summarizer (compaction)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// Uzun okuma geçmişini özetleyip pencereyi tazeler (illüstratif; upstream: Anthropic compaction)
type Turn = { role: "user" | "agent"; text: string };

export function compact(history: Turn[], keepRecent = 2): { summary: string; window: Turn[] } {
  const head = history.slice(0, -keepRecent);
  const window = history.slice(-keepRecent);
  const summary = head.map((t) => t.text).join("\n").slice(0, 1200); // kayıpsız değil — özet
  return { summary, window }; // kararlar/künyeler korunur, ham çıktılar düşer (§12)
}
```

| Kural | Karşılık | Kanıt |
|-------|----------|-------|
| Özet = karar + künye, ham çıktı değil | §12 otomatik temizlik | `.ai/AGENTS.md:546` |
| Son N tur korunur | lock/log künyeleri canlı kalsın | `.ai/AGENTS.md §12.1` |
| Compaction ≠ memory | özet oturum içi; kalıcı not MEMORY.md | bu anayasa §15 |

## §21 İllüstratif — Go RAG Context Packer

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// Paketleme: en dar hedef önce, token sınırı zorunlu (illüstratif)
type Target struct{ Path string; Tokens int }

func Pack(targets []Target, budget int) []Target { // targets: RAG §3 sırası
    out := []Target{}; used := 0
    for _, t := range targets {
        if used+t.Tokens > budget { break } // §9: gereksiz dosya bağlama girmez
        out = append(out, t); used += t.Tokens
    }
    return out
}
```

| Kural | Karşılık | Kanıt |
|-------|----------|-------|
| RAG §3 sırası korunur | en dar (domain-specific) hedef önce | `.ai/RAG.md:103` |
| Durum sütunu (`IMPLEMENTED`/`PLANNED`) | PLANNED dosya bağlamda "kod yok" etiketiyle taşınır | `.ai/RAG.md:104` |
| Hayalet satır (dosya yok) | pack edilmez; satır silinir | `.ai/RAG.md:102` |

## §22 İllüstratif — Rust Token Budget Tracker

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// Bütçe izleyici: taşma = Result hatası, sessiz taşma yok (illüstratif)
pub struct Budget { limit: usize, used: usize }

impl Budget {
    pub fn charge(&mut self, cost: usize) -> Result<(), &'static str> {
        if self.used + cost > self.limit { return Err("TOKEN_OVERFLOW"); } // §18 madde 4
        self.used += cost; Ok(())
    }
}
```

| Kural | Karşılık | Kanıt |
|-------|----------|-------|
| Taşma hata döner, sessiz değil | "Token overflow → Görev başarısız" | `.ai/AGENTS.md §18` |
| Bütçe=0 yanlış okuma | ölçüsüz okuma engellenir | `.ai/AGENTS.md §13` madde 3 |
| Fail-safe | fail-open yok; DUR prensibi | `.ai/AGENTS.md:44` (Final rule) |

## §23 İllüstratif — C++ Realtime Context Gate

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// Realtime callback: dosya/vault okuması yasak; bağlam önceden paketlenir (illüstratif)
struct ContextSnapshot { std::vector<std::byte> payload; bool ready = false; };

inline void audioCallback(ContextSnapshot& snap) noexcept {
    if (!snap.ready) return;              // hazır değilse iş yapma — callback'te I/O yok
    // payload önceden (non-realtime) hazırlanmıştır — §8 kuralı: bağlam önce kurulur
}
```

| Kural | Karşılık | Kanıt |
|-------|----------|-------|
| Callback'te I/O yok | bağlam önceden kurulur (§6 adım 1-2) | `.ai/AGENTS.md:489` |
| Zero-allocation/noexcept | Embedded standart %100 | `.ai/AGENTS.md §16` |
| Hazır değilse DUR | Final rule: riskli işlemede dur | `.ai/AGENTS.md:44` |

## §24 İllüstratif — C# Context Budget Service

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// Bağlam bütçe servisi: kaynak tüketimi raporlanır (illüstratif)
public sealed record ContextEntry(string Path, int Tokens);
public sealed class ContextBudget(int limit) {
    private int _used;
    public bool TryAdd(ContextEntry e) {
        if (_used + e.Tokens > limit) return false; // taşma: reddet, DUR
        _used += e.Tokens; return true;
    }
    public int Remaining => limit - _used;
}
```

| Kural | Karşılık | Kanıt |
|-------|----------|-------|
| Reddedilen giriş bağlama girmez | §9 hijyen | bu anayasa §9 |
| Kalan bütçe görünür | ölçüm şeffaflığı (Zero-Hallucination) | `.ai/AGENTS.md:42` |
| Record/immutability | C# standardı (#nullable, DTO) | global §5 dil standartları |

## §25 Doğrulama Kapıları

| # | Kapı | Kriter | Yöntem |
|---|------|--------|--------|
| 1 | Boot bütünlüğü | §3 tablosu 4 satır disk/boot ile çelişmiyor | root `AGENTS.md` §14 okuma |
| 2 | §7 envanter | 14 dosya diskte (Test-Path) | §17 Kanıt 2 betiği |
| 3 | Okuma sırası ihlali | Görev kaydı P0→P3 dışına okuma içermez | görev logu gözden geçirme |
| 4 | Lock davranışı | 30s/öncelik/kırma kuralları §12 ile aynı | `.ai/AGENTS.md §12` okuma |
| 5 | Secret sızıntısı | `.md` çıktılarda credential yok | grep + §13.9 kuralı |
| 6 | Künye disiplini | Her iddia `Dosya:`/`Satır:` ya da `⚠️ VERIFICATION REQUIRED` | §14 tablosu |
| 7 | Ölçüm kapısı | Bu dosya ReadAllLines >= 500 | §17 Kanıt 1 betiği |

## §26 Boot'a Neden Eklenmez (on-demand)

| # | Kural | Neden | Kanıt |
|---|-------|-------|-------|
| 1 | Boot listesine (§3/§7) eklenmez — on-demand referanstır | Boot sabit maliyettir; bu anayasa görev-konu dosyasıdır | `.ai/AGENTS.md:642` (§25.4 kanonik liste) |
| 2 | Boot zinciri bu dosyayla **değiştirilmez** | root `AGENTS.md` §14 sabittir | root `AGENTS.md` §14 |
| 3 | Okuma anı | Context kuralları soru sınıfı eşleşmesinde okunur | `.ai/RAG.md:63` (görev başına 1 kez) |
| 4 | Yerleşim | `.ai/architecture/K015-AI/context/` altında | bu FM (parent_id: context) |

Ek gerekçe: bağlam **kuralları** bağlam **içeriğinden** daha seyrek değişir ama boot'un parçası
değildir — boot zaten bu kuralları uygularken doğar; anayasa, boot'un **açıklamasıdır**, boot'un
**üyesi** değildir.

## §27 Açık Sorular

| # | Açık soru | Durum | Not |
|---|-----------|-------|-----|
| 1 | Token bütçe sayısal eşiği (ör. boot üst sınırı) vault'ta tanımlı mı? | `UNKNOWN` | Ölçümle belirlenecek; uydurulmaz |
| 2 | Context Lock bugün kodla mı, prosedürle mi uygulanıyor? | `VERIFICATION REQUIRED` | Lock mekanizması kodu bu gözden geçirmede aranmadı |
| 3 | Compaction/özetleme tetikleyicisi (otomatik/manuel) netleşmedi | açık | Literatür (§28-3) ile vault uygulaması hizalanacak |
| 4 | MEMORY.md §5 (20 adım) ile §24.2 (14 dosya) farkının bağlam etkisi ölçüldü mü? | `UNKNOWN` | §15'te fark kayıtlı; ölçüm ADR'siz yapılmaz |
| 5 | Subagent handover özeti token sınırı (ör. 1-2K) vault'a yazılacak mı? | açık | Karar: Vault Steward + MO |

## §28 Kaynaklar

| # | Kaynak | Erişim | Not |
|---|--------|--------|-----|
| 1 | Anthropic — "Effective context engineering for AI agents" · `https://www.anthropic.com/engineering/effective-context-engineering-for-ai-agents` | 2026-10-17 doğrulandı | smallest high-signal tokens · just-in-time · compaction · note-taking · subagents |
| 2 | Anthropic Docs — "Context windows" · `https://platform.claude.com/docs/en/build-with-claude/context-windows` | 2026-10-17 doğrulandı | context rot · working-memory tanımı |
| 3 | Anthropic Docs — "Context editing" · `https://platform.claude.com/docs/en/build-with-claude/context-editing` | 2026-10-17 doğrulandı | irrelevant content degrades focus · tool-result/thinking clearing |
| 4 | Vault SSOT — `.ai/AGENTS.md` §24 (Ultra Thinking) · §13 (Reading Order) · §12 (Context Lock) | okundu 2026-10-17 | bu anayasanın birincil kaynağı |
| 5 | Vault — root `AGENTS.md` §14 + root `CLAUDE.md` (boot zinciri) | okundu 2026-10-17 | §3/§9 kanıtı |
| 6 | Vault — `.ai/architecture/K015-AI/rag/rag-anayasa.md` | okundu 2026-10-17 | yapı referansı (çift katman/SSOT modeli) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
