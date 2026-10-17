---
node_id: memory-anayasa
node_type: module
status: active
parent_id: memory
children: []
depends_on: [memory-index]
evidence:
  - ".ai/MEMORY.md (728 satır, okundu 2026-10-17)"
  - "shared/src/AI/MemorySystem.php (kod kanıtı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# memory-anayasa - Ajan Hafıza Kuralları

> **Bu dosya ne değildir:** `.ai/MEMORY.md` içeriğinin **kopyasını** ve `MemorySystem.php` kodunun
> **tamamını** taşımaz; burada **hafıza anayasası** vardır — oturumlar arası hatırlama nasıl disipline
> edilir, kalıcı kayıt nereye yazılır, hafıza katmanları hangi öncelikle okunur, hafıza-context-log
> sınırı nerede çizilir.

## §1 Rol (ELI15)

Memory alt-domaini, ajanların **oturumlar kapandıktan sonra da hatırladığı** bilginin (session state,
persistent cache, audit trail) sahibidir. Hafıza bir "akıllı sistem" değil, **disiplinli bir kayıt
defteridir**: her oturum sonunda ne değiştiyse dosyaya yazılır; yeni oturum o dosyayı okuyarak başlar.
Hafıza unutursa sistem baştan keşfeder; hafıza uydurursa sistem yanlış üzerine inşa eder — ikisi de
bu anayasa ile engellenir.

| Soru | Cevap | Kanıt |
|------|-------|-------|
| Hafıza nerede durur? | `.ai/MEMORY.md` (session), `.ai/log.md` (audit), kod `MemorySystem` (cache) | `.ai/MEMORY.md:88-89` |
| Kim yazar? | Oturumu yürüten ajan + MO (vault-updater) | `.ai/MEMORY.md:106` |
| Kim okur? | Her ajan, boot sırasında (8. sırada) | `.ai/AGENTS.md` §24.2 (satır 8) |
| Ne zaman güncellenir? | Her oturum sonunda (Session Hafızası satırı) | `.ai/MEMORY.md:106` |

> **eli10:** Ajan "hatırlayan" değil, "not tutan" sistemdir — not tutulmayan bilgi yok sayılır.
> **eli15:** Hafıza = disk; model hafızası oturum sonunda sıfırlanır (`Dosya: .ai/MEMORY.md · Satır: 36`).

## §2 Kapsam (in / out) — memory ≠ context ≠ log

| Kapsam (bu anayasa) | Kapsam Dışı | Kanıt |
|---------------------|-------------|-------|
| Oturumlar arası persistent state (`MEMORY.md`) | Oturum içi context window (geçici, oturum sonunda yok olur) | `.ai/MEMORY.md:48` |
| Audit trail (`log.md`, append-only) | Uygulama logları (`src/Log/` — LoggerFactory, ayrı sistem) | `.ai/MEMORY.md:89` · `shared/AGENTS.md` §2 |
| Kod tarafı hafıza katmanları (`MemorySystem` L1/L2/L3) | Otomatik LLM hafızası (embedding/semantic — PLANNED, ADR-030) | `MemorySystem.php:18-21` · `.ai/RAG.md:111` |
| Hafıza kuralları (append-only, redaction, çelişki) | Hafıza içeriğinin kendisi (özet/karar metinleri) | `.ai/MEMORY.md:113-118` |
| Session lifecycle hafıza adımları (§4/§5/§6/§7) | Görev içi geçici değişkenler (kod scope'u) | `.ai/MEMORY.md:295-307` |
| Secret yasağı (hafızaya REDACTED) | `.env` credential yüzeyi (Security Engineer) | `.ai/MEMORY.md:255-262` |

**Sınır cümlesi:** *Context oturuma aittir, hafıza ekosisteme aittir, log denetime aittir.* Üçü
karıştırılırsa: context taşar (token overflow), hafıza şişer (1000 satır limit), log kanıtsız kalır
(audit trail eksik).

## §3 MEMORY.md Yapısı ve Boot Rolü

`.ai/MEMORY.md` (v25.2.0, 728 satır) 7 H2 iskeleti + 24 § taşır ve boot listesinin **8. sırasında**
okunur (P1, 3s timeout):

| # | Boot sırası | Dosya | Amaç | Kanıt |
|---|-------------|-------|------|-------|
| 1 | 8. sıra (P1) | `.ai/MEMORY.md` | Oturum hafızası | `.ai/MEMORY.md:322` |
| 2 | Genişletilmiş | `.ai/MEMORY.md` §5 | 20 adımlık geniş okuma seti | `.ai/MEMORY.md:311` |
| 3 | Referans takip | `.ai/AGENTS.md` §24.2 satır 8 | "Session hafızası" etiketi | `.ai/AGENTS.md` §24.2 |

**Yapı iskeleti (8 H2):** Purpose (§1) · Scope (§2/§16) · Architecture (§3/§9/§10) · Rules (§8/§12/§13)
· Workflow (§4/§5/§6/§7/§11/§17/§20) · Validation (§14/§15/§21-§24) · References (§18/§19) — iskelet
uyumu §24.1 tablosunda kayıtlıdır (`Dosya: .ai/MEMORY.md · Satır: 591-600`).

**Boot kuralı:** MEMORY.md boot'da okunur ama **boot listesi bu dosya ile değiştirilemez** —
kanonik liste `.ai/AGENTS.md` §24.2 ve `WORKFLOW.md` §8.7A'dır (`Dosya: .ai/MEMORY.md · Satır: 605`).

## §4 Hafıza Hiyerarşisi — Kod Kanıtlı (L1/L2/L3 + brain>log)

İki hiyerarşi vardır ve ikisi de kodda/sayımda sabittir: (a) **dosya öncelik sırası** (brain→index→
agents→memory→log), (b) **cache katmanları** (L1 Hot / L2 Warm / L3 Cool). Kod kanıtı — gerçek alıntı:

```php
// Dosya: shared/src/AI/MemorySystem.php · Satır: 25-31
private const HIERARCHY = [
    'brain'  => 5, // En yüksek
    'index'  => 4,
    'agents' => 4,
    'memory' => 3,
    'log'    => 1, // En düşük
];
```

```php
// Dosya: shared/src/AI/MemorySystem.php · Satır: 18-21 (docblock)
// Cache Seviyeleri:
// - L1 (Hot): SSOT dosyaları (CLAUDE, AGENTS, WORKFLOW) — Oturum sonu
// - L2 (Warm): Görev dosyalari (ADR, architecture) — Görev sonu
// - L3 (Cool): Referans dosyalari (testing, ui-design) — İsteğe bağlı
```

| Katman | Kapsam | Ömür | Gecersizleştirme | Kanıt |
|--------|--------|------|------------------|-------|
| L1 (Hot) | SSOT: CLAUDE, AGENTS, WORKFLOW | Oturum sonu | Otomatik | `.ai/MEMORY.md:128` |
| L2 (Warm) | Görev: ADR, architecture | Görev sonu | Dosya değişikliği | `.ai/MEMORY.md:129` |
| L3 (Cool) | Referans: testing, ui-design | İsteğe bağlı | Manuel | `.ai/MEMORY.md:130` |

**Politika:** Read-Through, Write-Through, LRU eviction; P0 dosyalar eviction'a uğramaz
(`Dosya: .ai/MEMORY.md · Satır: 132`). Hiyerarşi okuması `getHierarchy()` ile koddan da alınır
(`Dosya: shared/src/AI/MemorySystem.php · Satır: 152-155`).

## §5 Append-Only Kuralı

`log.md` ve oturum geçmişi **yalnızca ekleme** ile genişler; geçmiş satırlar silinmez, değiştirilmez.
Kural üç yerde tekrarlanır ve üçü de bağlayıcıdır:

| # | Kural | İhlal Sonucu | Kanıt |
|---|-------|--------------|-------|
| 1 | `log.md` Append-Only, geçmiş silinemez | Butunluk ihlali, ADR-004 | `.ai/MEMORY.md:444` (Warning #2) |
| 2 | Session timestamp'leri UTC immutable | İzlenebilirlik kaybı, ADR-004 | `.ai/MEMORY.md:445` (Warning #3) |
| 3 | Append-only dosyalara Edit değil ekleme (Add-Content) | Revizyon kaybı | `.ai/MEMORY.md:563` (§23.5 Ders #6) |
| 4 | `log.md` tüm ajanlara açık, "sadece ekleme" | Domain boundary ihlali | `.ai/AGENTS.md` §5 |

**Uygulama notu:** MEMORY.md Read-Write'tir ama **session geçmiş bölümleri** (§18 Session History,
§20-§23 revizyon kayıtları) append-only muamelesi görür — eski satır düzeltmez, yeni satır eklersin.
Silme yalnızca **hayalet veri** (var olmayan dosyaya işaret eden link) için geçerlidir.

## §6 Session Kayıt Formatı

Oturum sonu kaydı, `.ai/MEMORY.md` §20 `Current State` + §18 `Session History` tablolarına yazılır.
Zorunlu alanlar:

| Alan | Değer kuralı | Örnek (§20'den) | Kanıt |
|------|--------------|------------------|-------|
| Session Date | UTC tarih | `2026-10-15` | `.ai/MEMORY.md:407` |
| Domain | Kısa konu özeti | "Neva Engine OSS sanal surucu arastirmasi + MEMORY.md kurtarma" | `.ai/MEMORY.md:408` |
| Last Action | Ne yapıldığı (dosya + sürüm) | "doc 11 + 11-examples/: 10 dil × 6 örnek ~235KB" | `.ai/MEMORY.md:409` |
| Changed Files | Etkilenen dosya yolları | ".ai/projects/NevaEngine/09-...md (v1.1.0) + keys.md + log.md" | `.ai/MEMORY.md:410` |
| Known Issue | Açık riskler, `⚠️ VERIFICATION REQUIRED` | "session-save.mjs ... YOK — post-op sync manuel" | `.ai/MEMORY.md:411` |
| History satırı | Tarih · Konu · Durum · ADR · Agent | "2026-09-05 · Device-Aware Rendering Vault Update · ✓ · — · vault-updater" | `.ai/MEMORY.md:687` |

**Yasak:** Known Issue alanına tahmin yazmak; "her şey tamam" varsayımı; kanıtsız sayı iddiası
(Sayım Ders #2: "her revizyonda yeniden ölçülmeli" — `Dosya: .ai/MEMORY.md · Satır: 559`).

## §7 Oturum Sonu Kayıt Akışı (Sync End — 5 adım)

`§7 Session Vault Sync — Bitis` kapanıştır; adımlar **sırayla** ve süre bütçesi içinde yapılır:

| # | Adım | Kontrol | Süre | Kanıt |
|---|------|---------|------|-------|
| 1 | Değişiklikleri vault'a yaz (in-place) | Dosya boyutu | 5s | `.ai/MEMORY.md:360` |
| 2 | `log.md`'ye timestamp ekle | Format doğrulama | 2s | `.ai/MEMORY.md:361` |
| 3 | MEMORY.md session state güncelle | Session indeks | 3s | `.ai/MEMORY.md:362` |
| 4 | Wiki-link'leri doğrula | Regex `\[\[([^\]]+)\]\]` | 5s | `.ai/MEMORY.md:363` · `:366` |
| 5 | Hallusinasyon sweep | `VERIFICATION REQUIRED` taraması | 3s | `.ai/MEMORY.md:364` |

**Toplam kapanış bütçesi: max 20 saniye** (`Dosya: .ai/MEMORY.md · Satır: 366`). Adım 3 atlanırsa
sonraki oturum eski durumu okur → çift iş; adım 5 atlanırsa uydurma etiketsiz kalır → CRITICAL
(`Dosya: .ai/MEMORY.md · Satır: 424`). Session lifecycle'in tamamı 7 aşamadır (Initialize → Close,
55s sabit + görev süresi) — `Dosya: .ai/MEMORY.md · Satır: 297-307`.

## §8 Hafıza Temizliği / Güncelleme Kuralı

Hafıza güncellenir ama **şişirilmez**. Limit ve arşiv disiplini:

| Kural | Limit | Aşım Çözümü | Kanıt |
|-------|-------|--------------|-------|
| Dosya boyutu | Max 1000 satır/dosya | Modüler yapı, arşivleme | `.ai/MEMORY.md:68` |
| Vault boyutu | Max 100MB | Gereksiz kopyaları arşivle | `.ai/MEMORY.md:70` |
| Boot süresi | Max 25s (genişletilmiş 36s) | Paralel değil — sıralı okuma | `.ai/MEMORY.md:71` · `:336` |
| Memory boyutu | Max 10MB toplam | Arsivleme, sıkıştırma | `.ai/MEMORY.md:72` |
| Eski bilgi | `VERIFICATION REQUIRED` varsa | Doğrula veya sil | `.ai/MEMORY.md:427` |

**Güncellenme akisi (tek yönlü):** Değişiklik tespit → ilgili dosya belirle → in-place uygula →
cross-reference güncelle → `log.md`'ye yaz → MEMORY.md session state güncelle → wiki-link doğrula
(`Dosya: .ai/MEMORY.md · Satır: 120`). Prune mekanizması kodda da vardır: `pruneExpired()` süresi
dolmuş cache + persistent kayıtları temizler (`Dosya: shared/src/AI/MemorySystem.php · Satır: 183-199`).

## §9 Çelişki Durumunda Hafıza Önceliği (Disk > Hafıza)

Model hafızası, web ve agent iddiası **diskin altındadır**; çelişkide disk kazanır:

| # | Çelişki | Kazanan | Gerekçe | Kanıt |
|---|---------|---------|---------|-------|
| 1 | Vault dosyası ↔ agent hatırlaması | Disk (dosya) | Zero-Hallucination: iddia disk kanıtlıdır | `.ai/AGENTS.md` §3 (kök boot) |
| 2 | Hafıza dosyaları arası öncelik | brain > index > AGENTS > MEMORY > log | Üst seviye kazanır | `.ai/MEMORY.md:97` · `MemorySystem.php:25-31` |
| 3 | Write-Write (iki ajan aynı dosya) | Context Lock + Queue (MO) | Max 30s lock | `.ai/MEMORY.md:279` · `:286` |
| 4 | Version Conflict | MO müdahalesi | Master kaynak | `.ai/MEMORY.md:281` |
| 5 | ADR Conflict | Escalation L1→L2→L3→İnsan | Frozen karar | `.ai/MEMORY.md:283` |
| 6 | Ölçüm ↔ eski sayı | Yeni ölçüm (tarih damgalı) | "Sayım iddiaları yeniden ölçülmeli" | `.ai/MEMORY.md:559` |

**İstisna yoktur:** "hafızamda böyleydi" gerekçe sayılmaz; `UNKNOWN` yazılır, ölçüm istenir.

## §10 Hafıza → Vault Senkronu

Hafıza tek başına durmaz — değişiklikler ilgili vault dosyalarına **in-place** yazılır, sonra
`log.md` + `MEMORY.md` güncellenir. Senkron zinciri:

| # | Tetikleyici | Aksiyon | Hedef | Kanıt |
|---|-------------|---------|-------|-------|
| 1 | Oturum başı (Sync Start) | 5 soru ile vault durumu analiz | `log.md`'ye INFO | `.ai/MEMORY.md:352` |
| 2 | Görev sırasında | Anlık audit trail | `log.md` (append) | `.ai/MEMORY.md:301` |
| 3 | Oturum sonu (Vault-Sync) | Vault'u güncelle | ilgili `.ai/` dosyaları | `.ai/MEMORY.md:302` |
| 4 | Oturum sonu (Sync End) | MEMORY.md session state | `MEMORY.md` §20 | `.ai/MEMORY.md:303` |
| 5 | Session Close | Final state kaydı | `MEMORY.md` | `.ai/MEMORY.md:304` |

Güncelleme akışının zorunlu son halkası: "MEMORY.md session state guncelle" (`Dosya: .ai/MEMORY.md ·
Satır: 120`) — vault-sync bu akışın ta kendisidir; skill karşılığı `vault-sync-post`'tur.

## §11 Güvenlik — Hafızaya Secret Yazılmaz

Hafıza dosyaları git arşivindedir; **herhangi bir hafıza satırına secret yazılmaz**. Sınıflandırma:

| Veri Türü | Sınıf | Hafızaya Yazılabilir mi? | Loglanırken | Kanıt |
|-----------|-------|---------------------------|-------------|-------|
| API Key | SECRET | ❌ ASLA | `[REDACTED]` | `.ai/MEMORY.md:257` |
| DB Password | SECRET | ❌ ASLA | `[REDACTED]` | `.ai/MEMORY.md:258` |
| JWT Secret | SECRET | ❌ ASLA | `[REDACTED]` | `.ai/MEMORY.md:259` |
| Session Token | SECRET | ❌ ASLA | `[REDACTED]` | `.ai/MEMORY.md:260` |
| User Email | PII | Kısmi (maskeleme) | Kısmi maskeleme | `.ai/MEMORY.md:263` |
| ADR Kararı / Dosya Yolu | PUBLIC | Evet | Yok | `.ai/MEMORY.md:266-267` |

**Doğru/Yanlış:** `API Key: [REDACTED] (service: deezer)` ✅ · `API Key: abc123` ❌ ASLA
(`Dosya: .ai/MEMORY.md · Satır: 269`). **Oturum sonu redaction taraması zorunlu:**
`Select-String -Path .ai/log.md -Pattern "password|api[_-]?key|secret|token"`
(`Dosya: .ai/MEMORY.md · Satır: 271`). Kural #1 uyarısı: "Hassas veri ASLA `.ai/` dizinine yazılmaz"
(ADR-022 — `Dosya: .ai/MEMORY.md · Satır: 443`).

## §12 Zero-Hallucination — Hafıza Kanıtsız İddia Üretmez

Hafıza dosyasına yazılan **her iddia disk kanıtlıdır**; kanıtlanamayan `⚠️ VERIFICATION REQUIRED`
etiketi alır. Hafıza kaynaklı hallusinasyon en tehlikeli türdür çünkü sonraki tüm oturumlar onu
"gerçek" okur:

| # | Kural | Uygulama | Kanıt |
|---|-------|----------|-------|
| 1 | Sayı iddiaları yalnız ölçümle yazılır | 728 satır, 19 PNG, 787 dosya — hepsi `ReadAllLines`/`Get-ChildItem` | `.ai/MEMORY.md:559` |
| 2 | Kanıtsız iddia = `⚠️ VERIFICATION REQUIRED` | CRITICAL öncelik | `.ai/MEMORY.md:424` |
| 3 | Bilinmeyen değer = `UNKNOWN` | Tahmin yazılmaz | `.ai/AGENTS.md` (kök boot) |
| 4 | Oturum sonu hallusinasyon sweep | `VERIFICATION REQUIRED` taraması | `.ai/MEMORY.md:364` |
| 5 | İddia 3 kaynakla desteklenir | Test-Path + kod okuma + satır sayımı | `.ai/MEMORY.md:550` |

**Ders kaydı:** "500 satır hedefi doldurmayla değil kanıtla ulaşılır — her ekleme izlenebilirlik
satırı taşır" (`Dosya: .ai/MEMORY.md · Satır: 562`). Bu anayasanın kendisi de bu kurala uyar: her §
künyeli kanıt taşır.

## §13 Doğrulama Kapıları (hafıza yazımı öncesi/sonrası)

| # | Kapı | Kriter | Yöntem |
|---|------|--------|--------|
| 1 | Satır ölçümü | Dosya hedef bandında (500-520 / 1000 max) | `[IO.File]::ReadAllLines(..., UTF8).Count` |
| 2 | Frontmatter | Zorunlu alanlar tam, `title`/`parent` anahtarı YOK | gözden geçirme |
| 3 | Timestamp | Her yeni kayıt UTC tarih damgalı | gözden geçirme |
| 4 | Wiki-link | Hedefler diskte | repo kökünden `wiki-link-check.ps1` |
| 5 | Redaction | `password\|api[_-]?key\|secret\|token` 0 isabet | `Select-String` |
| 6 | Hall sweep | `VERIFICATION REQUIRED` etiketli kalem kalmadı/etiketlendi | grep |
| 7 | Authority | Son satır birebir | gözden geçirme |

**Betik kuralı:** `.ai/scripts/*.ps1` yalnız repo kökünden çalışır — `.ai/` içinden çalıştırılan betik sahte "dosya yok" üretir (`Dosya: .ai/AGENTS.md` §13.6). Kapı 1 bu üretimde zorunlu ölçümdür (FM `validation`).

## §14 Hafıza-vs-Context Sınırı (uzun/kısa vadeli ayrımı)

Harici literatürle (LangChain) doğrulanan ayrım, CoreMusic karşılığıyla eşlenir:

| Kavram (literatür) | Tanım | CoreMusic karşılığı | Kanıt |
|--------------------|-------|---------------------|-------|
| Short-term (thread-scoped) | Oturum içi mesaj durumu, checkpointer ile | Context window + `setSession`/`getSession` | LangChain docs §14 · `MemorySystem.php:45-75` |
| Long-term (cross-session) | Oturumlar arası, namespace'li kalıcı kayıt | `.ai/MEMORY.md` + `setPersistent` | LangChain docs §14 · `MemorySystem.php:89-118` |
| Context window riski | Uzun tarihçe taşar/dağınıklık yaratır | Boot token aşımı önlenir (gereksiz okuma yasak) | LangChain docs · `.ai/MEMORY.md:249` |
| Semantic (fact) | Geçmişten çıkarılan olgular | Session kaydı, ADR özetleri | LangChain blog §14 |
| Episodic (action) | Geçmiş eylem dizileri | §18 Session History, `log.md` | LangChain blog §14 |
| Procedural (rules) | Görev kuralları | `.ai/CLAUDE.md` Guardrail, WORKFLOW | LangChain blog §14 |

**Çekirdek kural:** model hafızası **kalıcı değildir**; oturum kapanınca tek geri çağırma yolu
boot'tur (MEMORY.md 8. sırada). Context'te olup MEMORY.md'de olmayan bilgi, oturum sonunda **yok
sayılır**.

## §15 Memory Update Stratejileri (hot-path vs background)

Literatürde iki güncelleme yolu; CoreMusic ikisini birleştirir (görev içi log = hot-path, oturum
sonu sync = background):

| # | Strateji (literatür) | Artı | Eksı | CoreMusic Karşılığı | Kanıt |
|---|----------------------|------|------|---------------------|-------|
| 1 | Hot-path (çevrim içi karar) | Anında güncelleme | Gecikme, karmaşık sorumluluk | Anlık `log.md` append (aşama 4) | LangChain blog §15 · `.ai/MEMORY.md:301` |
| 2 | Background (arka plan) | Düşük gecikme, ayrık mantık | Tetikleme sıklığı kritik | Oturum sonu Vault-Sync (aşama 5-7) | LangChain blog §15 · `.ai/MEMORY.md:302-304` |
| 3 | User feedback | Doğrulanmış hafıza | İnsan bağımlı | Onay kapıları (ASKING QUESTIONS) | LangChain blog §15 · kök `AGENTS.md` §1 |
| 4 | Özetleme (summary) | Context taşması önlenir | Bilgi kaybı riski | §20 Current State özeti | LangChain docs §15 · `.ai/MEMORY.md:403-411` |

**Çift yazım kuralı:** Kritik olay iki yerde iz bırakır — anlık (`log.md`) ve özet (`MEMORY.md`).
Tek kanallı kayıt yasaktır: yalnız log = bağlam yok, yalnız MEMORY = denetim izi yok.

## §16 İllüstratif Örnek — Python (periyodik hafıza özeti)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# periyodik özetleme — upstream docs (LangChain summarization deseni) referansı; repo'da karşılığı YOK
MAX_TURNS = 12

def maybe_summarize(state: dict, model) -> dict:
    messages = state["messages"]
    if len(messages) < MAX_TURNS:
        return state
    prior = state.get("summary", "")
    prompt = (f"Önceki özet:\n{prior}\n\nYeni mesajları özetle:" if prior
              else "Sohbeti özetle:")
    summary = model.invoke(messages + [{"role": "user", "content": prompt}])
    # eski mesajları sil, son 2 turn'u bırak (kayip minimized)
    keep = messages[-2:]
    return {"summary": summary.content, "messages": keep}
```

Kural karşılığı: oturum sonu "Last Action" özeti aynı mantıkla çalışır — tüm turn taşınmaz,
özet + son adımlar kalır (`Dosya: .ai/MEMORY.md · Satır: 409`).

## §17 İllüstratif Örnek — TypeScript (LRU session cache)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```ts
// LRU session cache — upstream docs (LRU deseni) referansı; repo'da karşılığı YOK
// Politika: Read-Through, Write-Through, LRU eviction (Dosya: .ai/MEMORY.md · Satır: 132)
export class LruSessionCache<T> {
  private map = new Map<string, { value: T; expiresAt: number | null }>();
  constructor(private max: number, private ttlMs = 0) {}

  get(key: string): T | null {
    const e = this.map.get(key);
    if (!e) return null;
    if (e.expiresAt !== null && e.expiresAt < Date.now()) { this.map.delete(key); return null; }
    this.map.delete(key); this.map.set(key, e);          // LRU: en son kullanılan sona
    return e.value;
  }
  set(key: string, value: T): void {
    if (this.map.size >= this.max) {
      const oldest = this.map.keys().next().value as string;
      this.map.delete(oldest);                            // eviction — P0 anahtarlar bu listeye girmez
    }
    this.map.set(key, { value, expiresAt: this.ttlMs ? Date.now() + this.ttlMs : null });
  }
}
```

P0 muafiyeti kodda değil **politikadır**: "P0 dosyaları eviction'a uğramaz"
(`Dosya: .ai/MEMORY.md · Satır: 132`).

## §18 İllüstratif Örnek — Go (kalıcı kayıt satırı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// append-only audit satırı — upstream docs (Go os/io referansı); repo'da karşılığı YOK
// Kural: log.md geçmiş satırları silinemez (Dosya: .ai/MEMORY.md · Satır: 444)
package audit

type Entry struct {
    Timestamp string // UTC, immutable (Dosya: .ai/MEMORY.md · Satır: 445)
    Level     string // INFO | WARN | ERROR
    Actor     string // agent kod adı
    Message   string
}

func Append(path string, e Entry) error {
    f, err := os.OpenFile(path, os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
    if err != nil { return err }
    defer f.Close()
    _, err = fmt.Fprintf(f, "%s [%s] %s: %s\n", e.Timestamp, e.Level, e.Actor, e.Message)
    return err // O_TRUNC asla kullanılmaz — append-only
}
```

## §19 İllüstratif Örnek — Rust (triplet hafıza kaydı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// (subject, predicate, object) triplet hafıza — upstream docs referansı; repo'da karşılığı YOK
// Semantic memory karşılığı: fact repository (Dosya: .ai/MEMORY.md · Satır: 48 "Persistent State")
pub struct MemoryEntry<S, P, O> {
    pub subject: S,
    pub predicate: P,
    pub object: O,
    pub recorded_at: String, // UTC damga — immutable
    pub source: String,      // kanıt künyesi (Dosya: ... · Satır: N)
}

pub fn upsert<S, P, O>(store: &mut Vec<MemoryEntry<S, P, O>>, e: MemoryEntry<S, P, O>)
where S: PartialEq, P: PartialEq
{
    // append-only: mevcut kaydın object'ini GÜNCELLE, satırı silme
    match store.iter_mut().find(|m| m.subject == e.subject && m.predicate == e.predicate) {
        Some(existing) => { existing.object = e.object; } // düzeltme = yeni değer, eski satır geçersiz
        None => store.push(e),                            // ekleme
    }
}
```

## §20 İllüstratif Örnek — C++ (önbellek katmanı seçimi)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
// L1/L2/L3 katman seçimi — upstream docs referansı; repo'da karşılığı YOK
// Çekirdek sabit: L1 Hot / L2 Warm / L3 Cool (Dosya: shared/src/AI/MemorySystem.php · Satır: 18-21)
#include <string>
#include <unordered_map>

enum class CacheLevel { L1_Hot, L2_Warm, L3_Cool };

CacheLevel level_for(const std::string& file) {
    // SSOT kök dosyalar → L1: oturum sonuna kadar sıcak kalır
    if (file == "CLAUDE.md" || file == "AGENTS.md" || file == "WORKFLOW.md")
        return CacheLevel::L1_Hot;
    // görev dosyaları → L2: görev sonu gecersizlesir
    if (file.rfind(".ai/.decisions/", 0) == 0 || file.rfind(".ai/architecture/", 0) == 0)
        return CacheLevel::L2_Warm;
    // referans dosyalar → L3: isteğe bağlı, manuel gecersizlestirme
    return CacheLevel::L3_Cool;
}
```

Eşleme PHP docblock'ının birebir türevidir (`Dosya: shared/src/AI/MemorySystem.php · Satır: 18-21`).

## §21 İllüstratif Örnek — C# (hafıza özet + kapı sayacı)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// oturum sonu özet kaydı — upstream docs referansı; repo'da karşılığı YOK
// Zorunlu alanlar: Session Date, Domain, Last Action, Changed Files, Known Issue
// (Dosya: .ai/MEMORY.md · Satır: 407-411)
public sealed record SessionSnapshot(
    DateOnly SessionDate,
    string Domain,
    string LastAction,
    IReadOnlyList<string> ChangedFiles,
    IReadOnlyList<string> KnownIssues);   // "⚠️ VERIFICATION REQUIRED" işaretli kalemler dahil

public static bool Validate(SessionSnapshot s)
{
    var ok = s.SessionDate != default
             && !string.IsNullOrWhiteSpace(s.Domain)
             && !string.IsNullOrWhiteSpace(s.LastAction)
             && s.ChangedFiles.Count > 0;
    // Kanitsiz iddia yok: KnownIssue disinda hicbir alan tahmin tasiyamaz
    // (Dosya: .ai/MEMORY.md · Satır: 562 "500 satir hedefi kanitla ulasilir")
    return ok;
}
```

## §22 Hafıza Bakım Roller Matrix

| Durum | Aksiyon | Sorumlu | Kanıt |
|-------|---------|---------|-------|
| LSP hata tespit | "FIX IMMEDIATELY", devam yasak | ilgili ajan | `.ai/AGENTS.md` §24.4 |
| Hallüsinasyon | `VERIFICATION REQUIRED` etiketi | tüm ajanlar | `.ai/AGENTS.md` §24.4 |
| Çelişki | DUR + kullanıcıya sor | tüm ajanlar | `.ai/AGENTS.md` §24.4 |
| Eksik dosya | Hemen tamamla veya sil | vault-updater | `.ai/AGENTS.md` §24.4 |
| Vault bozulması | `git checkout` + son commit | MO | `.ai/MEMORY.md:381` |
| Session kaybı | `log.md`'den resume | MO | `.ai/MEMORY.md:384` |
| Cache bozulması | L1 flush, yeniden yükle | MO | `.ai/MEMORY.md:387` |

## §23 Hafıza Yasağı Özeti (toplu)

| # | Yasak | Kategori | ADR | Kanıt |
|---|-------|----------|-----|-------|
| 1 | Hassas veri ASLA `.ai/`'ye yazılır | Güvenlik | ADR-022 | `.ai/MEMORY.md:443` |
| 2 | `log.md` geçmiş silinemez | Bütünlük | ADR-004 | `.ai/MEMORY.md:444` |
| 3 | Timestamp değiştirilemez | İzlenebilirlik | ADR-004 | `.ai/MEMORY.md:445` |
| 4 | FROZEN ADR metni düzenlenmez | İmmutability | ADR-042 | `.ai/MEMORY.md:446` |
| 5 | Boot listesi hafıza dosyasıyla değiştirilemez | Boot bütünlüğü | — | `.ai/MEMORY.md:605` |
| 6 | Kanıtsız sayı/iddia yazılmaz | Zero-Hall | — | `.ai/MEMORY.md:559` |
| 7 | `SELECT *` / ORM hafıza sorgusuna taşınamaz | Veri | ADR-002 | `.ai/MEMORY.md:449` |

## §24 Açık Sorular

| # | Soru | Öncelik | Durum / Kanıt |
|---|------|---------|----------------|
| 1 | Semantic/vector hafıza (v19.0) ne zaman PLANNED→IMPLEMENTED olur? | HIGH | ⏳ Roadmap 2026 Q4 (`Dosya: .ai/MEMORY.md · Satır: 395`) |
| 2 | Otomatik bellek yönetimi (v21.0 Auto-Memory/Smart Cache) kapsamı? | MEDIUM | ⏳ Roadmap 2027 Q2 (`Dosya: .ai/MEMORY.md · Satır: 397`) |
| 3 | Cross-project memory (v20.0, WirelessConnect) namespace tasarımı? | MEDIUM | ⏳ Roadmap 2027 Q1 (`Dosya: .ai/MEMORY.md · Satır: 396`) |
| 4 | `MemorySystem` kodu üretimde hangi servis tarafından çağrılıyor? | HIGH | `⚠️ VERIFICATION REQUIRED` — çağrısayan yüzey ölçülmedi |
| 5 | 10MB toplam memory limiti gerçek yükte tutuyor mu? | LOW | `⚠️ VERIFICATION REQUIRED` — ölçüm yok (`Dosya: .ai/MEMORY.md · Satır: 72`) |
| 6 | Session-save betikleri (`session-save.mjs` vb.) YOK — post-op sync manuel mi kalacak? | HIGH | `⚠️ VERIFICATION REQUIRED` (`Dosya: .ai/MEMORY.md · Satır: 411`) |

## §25 Kaynaklar

| # | Kaynak | Yol / URL | Erişim / Ölçüm | Kullanım |
|---|--------|-----------|----------------|----------|
| 1 | Session hafızası SSOT | `.ai/MEMORY.md` (728 satır, v25.2.0) | okundu 2026-10-17 | §3-§13, §22-§24 |
| 2 | Hafıza kodu | `shared/src/AI/MemorySystem.php` | okundu 2026-10-17 | §4, §8, §14, §20 |
| 3 | Referans takip listesi | `.ai/AGENTS.md` §24.2 (MEMORY.md 8. sırada) | okundu 2026-10-17 | §1, §3 |
| 4 | Anayasa şablonu (yapı referansı) | `.ai/architecture/K015-AI/rag/rag-anayasa.md` | okundu 2026-10-17 | yapı: H1 + § + tablo |
| 5 | LangChain — Memory overview | https://docs.langchain.com/oss/python/concepts/memory | erişim 2026-10-17 | §14, §15 |
| 6 | LangChain — Short-term memory | https://docs.langchain.com/oss/python/langchain/short-term-memory | erişim 2026-10-17 | §14 |
| 7 | LangChain blog — Memory for agents (CoALA tipleri) | https://www.langchain.com/blog/memory-for-agents | erişim 2026-10-17 | §14, §15 |

## §26 Hızlı Referans (tek ekran)

| # | Soru | Cevap |
|---|------|-------|
| 1 | Oturum sonunda ne yazılır? | `log.md` append + MEMORY.md §20 state + §18 history |
| 2 | Hafıza ile context farkı? | Context oturuma, hafıza ekosisteme ait; oturum sonunda yalnız hafıza kalır |
| 3 | Hangi dosya önce okunur? | brain → index → AGENTS → MEMORY → log (`MemorySystem.php:25-31`) |
| 4 | Cache katmanı ne zaman düşer? | L1 oturum sonu · L2 görev sonu · L3 manuel |
| 5 | Eski kayıt düzeltilir mi? | Append-only; düzeltme = yeni satır, eski satır silinmez |
| 6 | Disk ile hafıza çelişirse? | Disk kazanır (Zero-Hallucination) |
| 7 | Secret hafızaya girer mi? | ASLA — `[REDACTED]` (ADR-022) |

## §27 Ölçüm / Gate Notu

| # | Eşik | Araç | Not |
|---|------|------|-----|
| 1 | ≥500 tam satır | `[IO.File]::ReadAllLines($hedef, [Text.Encoding]::UTF8).Count` | üretim sonrası zorunlu |
| 2 | FM 10 alan | gözden geçirme | sıra sabit, `title`/`parent` anahtarı YOK |
| 3 | Authority son satır | gözden geçirme | birebir son satır |
| 4 | Kod bloğu | PHP gerçek alıntı (künyeli) + 6 dil illüstratif | illüstratif bloklarda uyarı etiketi zorunlu |
| 5 | Web kaynak | 3 LangChain URL + erişim tarihi | uydurma URL yasak |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
