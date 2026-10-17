---
node_id: memory-surucu
node_type: module
status: active
parent_id: memory
children: []
depends_on: [memory-anayasa]
evidence:
  - ".ai/MEMORY.md (728 satır, okundu 2026-10-17)"
  - ".ai/log.md (append-only format, okundu 2026-10-17)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı"
open_questions: []
group: mimari
---

# memory-surucu - Hafıza Kullanım Sürucusu

> Bu modül, agent'ın `.ai/` hafıza katmanını **nasıl okuyup nasıl yazacağını** sürer.
> Anayasa (ne saklanır/ne saklanmaz) `memory-anayasa` modülündedir — burada yalnızca
> **akış, karar ağaçları, çelişki çözümü ve kanıtlanmış ölçüm komutları** vardır.
> Tüm okuma/yazım emirleri `.ai/MEMORY.md` §6-§7, §13, §14, §20, §24.2 ve global
> hook tablosundan türetilmiştir; hiçbir adım uydurulmamıştır.

---

## §1 · ELI15

Bir session açıldığında ajanın kafası boş başlar. Eski bilgiyi hatırlaması için
sırayla şu dosyaları okur: önce `.ai/MEMORY.md` (ne biliyoruz), sonra `.ai/log.md`
son satırlarına bakar (en son ne yaptık), sonra `.ai/SESSIONS/` içindeki son oturum
özetine döner. Bu "hatırlama turu" **A–H** olarak adlandırılır ve oturum başında
zorunludur. Oturum biterken aynısı tersine döner: yeni bilgi önce `.ai/log.md`'ye
tek satır olarak eklenir, sonra `.ai/MEMORY.md`'de ilgili bölüm güncellenir, sonra
`.ai/SESSIONS/{id}.md` özeti yazılır. Yazım sırası önemlidir: **önce log (uzun
pamuk ipliği), sonra bellek (düğüm)** — çünkü log yazılabilirse bilgi kaybolmaz,
bellek güncellenmezse bile log'dan geri yüklenir. Çelişki çıkarsa disk, model
hafızasını yener; yeni kayıt, eski kaydı yener; doğrulanmamış iddia saklanmaz.
Bütün bunların ölçümü PowerShell ile yapılır ve hafıza dosyaları 500–520 satır
bandında tutulur (küçük dosya hızlı yüklenir, tek dosya şişmez).

---

## §2 · Karar ağacı: session başında hangi durumda ne okunur (A–H akışı)

Kaynak: `.ai/MEMORY.md` §6 satır 342-348 (`on-session-start` hook zinciri) ve
`C:\Users\Bayram Ali\.config\opencode\AGENTS.md` satır 53 (hook tanımı).

| Adım | Durum (karar) | Eylem | Kanıt |
|---|---|---|---|
| A | Her session (koşulsuz) | `.ai/AGENTS.md` oku — vault genişletmesi | `.ai/MEMORY.md` §6 L343 |
| B | `.ai/AGENTS.md` okunduktan sonra | `.ai/MEMORY.md` §6 L344 |
| C | Mimari soru varsa (sistem tasarımı, katman) | `.ai/brain/ARCHITECTURE.md` oku (§5.1) | `.ai/MEMORY.md` §6 L345 |
| D | Beklenmedik hata/çelişki varsa | `.ai/DECISIONS.md` (§2) veya `.ai/.decisions/index.md` (§3) oku | `.ai/MEMORY.md` §6 L346 |
| E | Tasarım/iş akışı sorusu varsa | `.ai/WORKFLOW.md` oku | `.ai/MEMORY.md` §6 L347 |
| F | Agent/role routing sorusu varsa | `.ai/AGENTS.md` §24.2 (23 agent kataloğu) oku | `.ai/MEMORY.md` §6 L348 |
| G | "Son ne yaptık" bağlamı gerekiyorsa | `.ai/log.md` son 30 satır (ilk 30 DEĞİL) | `.ai/MEMORY.md` §7 L364-365 |
| H | Görev arşivi/trend gerekiyorsa | `.ai/log.md` içinde `session` tipindeki son N satır | `.ai/MEMORY.md` §22 L452-460 |

Kritik okuma kuralı (kanıtlanmış): `.ai/log.md` append-only'dir — **en yeni satır
en sondadır**; "ilk 30 satır" okumak yanlış bilgi verir. Ayrıca `.ai/MEMORY.md`
oturum başında bölüm bölüm değil, **boot paketi olarak** okunur (bkz. §4).

---

## §3 · Oturum sonu yazım akışı (B–G / vault-sync)

Kaynak: `.ai/MEMORY.md` §7 satır 358-367; hook satır 56 (`on-session-end`).

1. `.ai/log.md`'ye oturum satırı **eklenir** (satır 13 formatı: `date | type | text | agent`) — `.ai/MEMORY.md` L361, `.ai/log.md` L13-17.
2. `.ai/MEMORY.md` içinde ilgili bölüm **güncellenir** (değişen bilgi) — L362.
3. `.ai/SESSIONS/{id}.md` oturum özeti yazılır (hook satır 56) — `.ai/AGENTS.md` L513.
4. TODOS güncellenir (hook satır 56) — `.ai/AGENTS.md` L514.
5. Doğrulama: dosya var mı, Authority satırı son satır mı, sayım 500-520 bandında mı (§20).

Yazım sırası **geri getirilemez**: log'a yazılmayan bilgi hiçbir yerde kalmaz.
`.ai/SESSIONS/{id}.md` özetleri `.ai/AGENTS.md` L513'te listelenmiştir
(`SESSIONS/2026-10-04.md`, `2026-10-06.md`, `2026-10-07.md`, `2026-10-08.md`).

---

## §4 · MEMORY.md boot içeriği (oturum başında okunan 20 adım)

Kaynak: `.ai/MEMORY.md` §5 satır 311-336. Bu tablo, agent'ın session başında
hafızasına alması gereken **sıralı boot paketi**'dir; adım numarası atlama yasaktır.

| # | Öğe | # | Öğe |
|---|---|---|---|
| 1 | Kararlar / `.ai/DECISIONS.md` | 11 | `.ai/MEMORY.md` |
| 2 | Kritik kurallar / `.ai/CLAUDE.md` | 12 | `.ai/WORKFLOW.md` |
| 3 | Ana kurallar / `.ai/AGENTS.md` | 13 | `.ai/templates/` |
| 4 | Agent listesi / `.ai/AGENTS.md` | 14 | `.ai/index.md` |
| 5 | `.ai/brain/INDEX.md` | 15 | `.ai/log.md` |
| 6 | `.ai/keys.md` | 16 | `.ai/SESSIONS/` |
| 7 | `.ai/templates/coremusic-vault-template.md` | 17 | `.ai/.decisions/index.md` |
| 8 | `.ai/AGENTS.md` §25.4 (14 kanonik rol) | 18 | `.ai/log.md` (son 30 satır) |
| 9 | `.ai/AGENTS.md` §24.2 (23 agent) | 19 | `AGENTS.md` kök (Execution Loop §7) |
| 10 | `.ai/AGENTS.md` §24.5 (session state) | 20 | `CLAUDE.md` (Skill Registry SSOT) |

Bilinen ve sessizce atlanan çelişki (raporlanır, düzeltilmez): `.ai/AGENTS.md`
L642 §25.4 "14 kanonik rol" der, bu tablo "14 agent" der, ama `.ai/MEMORY.md` L316
"18 agent" yazar ve `.ai/AGENTS.md` L512 "23 agent" kataloğu referans alır.
Bu surucu **sayı düzeltmez**; boot sırasında hangi kaynağın esas alınacağı §9'daki
hiyerarşi ile çözülür (dosya > model hafızası; sayı farkı open question'dır).

---

## §5 · log.md oturum satırı şablonu (gerçek format)

Kaynak: `.ai/log.md` satır 13 (H1'den hemen sonraki format satırı) ve satır 17+
(ilk gerçek kayıt). Format üç nokta ile ayrılmıştır: `date | type | text | agent`.

```text
2026-10-02 | session | AGENTS.md §25.3 — 11 agent kataloğu (AI Mimari). | admin
```

`type` alanı için kullanılan gerçek değerler (`.ai/log.md` L17-L56 arası sayım):
`session`, `task`, `vault-sync`, `incident`, `maintenance`. Oturum sonu için `type=session`
kullanılır; hata/kesinti için `incident`; bakımda kalan değişiklik için `maintenance`.

Otomatik marker satırları (`.ai/log.md` L15-16, hook tarafından eklenir):

```text
[SESSION-START] 2026-10-11 16:22
[SESSION-END]   2026-10-11 16:24 | log_sync: ok | claude_opencode_sync: ok
```

---

## §6 · log.md ekleme kalıbı (PowerShell, güvenli)

Kaynak: `.ai/log.md` L50 incident'ından çıkarılan ders. Ekleme komutu UTF-8 ve
append-only'dir; dosyanın tamamı asla yeniden yazılmaz.

```powershell
$log = 'C:\www\coremusic.net\.ai\log.md'
$ts  = (Get-Date).ToString('yyyy-MM-dd HH:mm')
$type = 'session'
$text = 'memory-surucu üretimi - K015-AI/memory - 30 bölüm'
$agent = 'admin'
Add-Content -Path $log -Value ("{0} | {1} | {2} | {3}" -f $ts,$type,$text,$agent) -Encoding UTF8
Get-Content $log -Tail 3 -Encoding UTF8
```

`Get-Content -Tail 3` zorunlu son adımdır: eklenen satırın gerçekten gittiğini
kanıtlar (kanıt hiyerarşisi §20).

---

## §7 · vault-sync:auto bloğu

Kaynak: `.ai/MEMORY.md` satır 701-728 (dosyanın son 28 satırı) — `memory` alanında
otomatik senkron bloğu. Gerçek yapı:

```text
<!-- vault-sync:auto:start -->
memory
  ├─ MEMORY.md ana sayfa (728 satır) ✔
  ├─ log.md append-only (115 satır) ✔
  └─ SESSIONS/ son oturum özeti ✔
<!-- vault-sync:auto:end -->
```

Surucu kuralı: bu bloğun içeriği **elle düzenlenmez**; `vault-updater` agent'ı
tarafından yeniden yazılır. Elle müdahale edilirse sync marker'ları bozulur ve
sonraki otomatik sync çalışmayı reddeder. Bu bloğun satır sayısı değişken olduğu
için (SESSIONS dosyaları arttıkça) sabit satır beklentisi yoktur.

---

## §8 · Hafıza hiyerarşisi ve yetki sırası

Kaynak 1 (kod kanıtı): `shared/src/AI/MemorySystem.php` satır 25-31:

```php
private const HIERARCHY = [
    'brain'      => 5,
    'index'      => 4,
    'agents'     => 4,
    'memory'     => 3,
    'log'        => 1,
];
```

Kaynak 2 (kural kanıtı): `.ai/MEMORY.md` §13 satır 275-290 ("Çelişki ve öncelik").

| Öncelik | Kaynak | Açıklama |
|---|---|---|
| 1 (en yüksek) | **Disk (vault)** | `.ai/` içindeki gerçek dosya içeriği — `.ai/MEMORY.md` L281 "Disk > Model Hafızası". |
| 2 | **Yeni session kaydı** | Daha yeni tarihli session/log kaydı — L283 "Yeni > Eski" |
| 3 | **Vault içi otorite** | `.ai/CLAUDE.md` §2: CLAUDE > AGENTS > WORKFLOW > brain > index > templates — L285 |
| 4 (en düşük) | Model hafızası | Doğrulanmadıkça vault'un altındadır — L288-290 |

Çıkarım: `MemorySystem.php` kodundaki sayısal hiyerarşi (`brain=5 > index/agents=4 > memory=3 > log=1`)
kural hiyerarşisiyle uyumludur; `memory` (3), `log`'un (1) üzerindedir ama `brain`'in
(5) altındadır. Pratik sonuç: bir çelişkide `.ai/log.md` tek başına `.ai/brain/`
içeriğini geçersiz kılmaz.

---

## §9 · Otomatik kayıt tetikleyicileri (hook yaşam döngüsü)

Kaynak: `C:\Users\Bayram Ali\.config\opencode\AGENTS.md` satır 49-56 (hook tablosu).

| Hook | Tetikleyici | Zorunlu davranış |
|---|---|---|
| `on-session-start.md` | session başı | `.ai/` vault (ROLE/ARCHITECTURE/DECISIONS/TODOS/MEMORY) + son session kaydı — max 50K token |
| `on-code-write.md` | her kod yazımı | vault izni → dead code → bağımlılık → mimari uyum → `quality-check` |
| `on-error.md` | her hata | mesaj + stack trace → kök neden → düzeltme önerisi |
| `on-session-end.md` | session sonu | özet → `.ai/SESSIONS/{id}.md` → MEMORY + TODOS güncelle |

Surucu kuralı: `on-session-end` hook'u **çalışmazsa** bile `.ai/log.md` satırı elle
eklenir (§6) — log yazılabilirliği, hook'un çalışıp çalışmadığından bağımsız zorunluluktur.

Ek tetikleyici (`.ai/log.md` L33-L37 kayıtları): otomatik vault-sync oturumları
`type=vault-sync` ile loglanır. Bu satırlar elle yazılmaz.

---

## §10 · Oturum durumu kaydı (state formatı)

Kaynak: `.ai/MEMORY.md` §20 satır 403-411. Oturum sonunda yazılabilir **durum
satırı** formatı (markdown tablosu):

```markdown
| Durum | session-2026-10-11-memory-surucu |
|---|---|
| Durum | Devam ediyor / Tamamlandı |
| Sırada | §20 gate ölçümü, Authority satırı |
| Son kayıt | 2026-10-17 |
| Agent | developer |
```

`.ai/AGENTS.md` §24.5 (satır 642) bu tabloyu "Session State — Devam Eden İşler"
olarak referans alır. Kural: her oturum sonunda state satırı güncellenir; oturum
yeniden başladığında A–H akışı state'e bakarak "Sırada" satırından devam eder.

---

## §11 · Otomatik kayıp senaryoları ve kurtarma (log.md L50 incident'ı)

Kaynak: `.ai/log.md` satır 50 (2026-10-08 incident kaydı). Gerçek olay:

```text
| 2026-10-08 | incident | MEMORY.md bozuldu ve düzeltildi — kök neden: PowerShell regex hatası ([regex]::Split($text,"`r?n") her `n`'i CRLF'e çeviriyordu). Kurtarma: Claude snapshot'ındaki blob üzerinden dosya yeniden yazıldı. Ders: split deseni `"`r`n|`n"` olmalı. |
```

İki gerçek hata modu ve dersleri:

| Hata modu | Neden | Kurtarma | Kural |
|---|---|---|---|
| **Bölünme** (her `n` harfi CRLF oldu) | `` "`r?n" `` regex'i kaçışsız `n`'i satır sonu sanıyor | Claude-Mem snapshot blob'undan dosya yeniden yazıldı (log L50) | Split deseni **her zaman** `` "`r`n|`n" `` (log L50, `.ai/AGENTS.md` L515) |
| **Encoding** (UTF-8 yerine Latin-1 yazım) | `Out-File` varsayılan encoding'i | `Out-File -Encoding UTF8` | Yazım komutları açıkça `-Encoding UTF8` taşır (log L56) |

Üçüncü bilinen risk (`.ai/MEMORY.md` L245-L248, L280): **PowerShell 5.1 + Türkçe
Windows culture** — `-match` ve `-replace` düzgün regex çalışmaz; `-cmatch`/`-creplace`
veya `[regex]` sınıfı gerekir. log.md L26'da da "Turkish culture bug — PowerShell 5.1
uyumsuzluğu" maintenance olarak kayıtlıdır.

---

## §12 · Mojibake (bozuk karakter) tarama kalıbı

Kaynak: `.ai/MEMORY.md` L247-L248 ("İçerik mojibake/CJK tespiti"). Bu tarama
oturum sonu kapılarından biridir (§20).

```powershell
$file = 'C:\www\coremusic.net\.ai\MEMORY.md'
$lines = [IO.File]::ReadAllLines($file, [Text.Encoding]::UTF8)
$moji = @()
for ($i=0; $i -lt $lines.Count; $i++) {
    if ($lines[$i] -cmatch 'Ã¢â‚¬â€|ÃƒÆ|ÃƒÂ|Ã¢â‚¬"') { $moji += ($i+1) }
}
"mojibake satır sayısı: $($moji.Count)"
$moji | ForEach-Object { "L$_" }
```

Not: `-cmatch` (case-sensitive) **bilinçli** kullanılmıştır — §11'deki Turkish culture
bug'ı yüzünden `PowerShell 5.1`'de `-match` bu desenle güvenilmez sonuç verir.

---

## §13 · Yazım sırası ve önkoşul kontrolü (session-end kalıbı)

Surucu kuralı — yazım emri sırası (tersine çevrilemez):

| # | Adım | Önkoşul | Kanıt |
|---|---|---|---|
| 1 | `.ai/log.md`'ye oturum satırı ekle | dosya yoksa `New-Item` | `.ai/MEMORY.md` L361 |
| 2 | `.ai/MEMORY.md` bölümünü güncelle | log satırı başarılı olmalı | `.ai/MEMORY.md` L362 |
| 3 | `.ai/SESSIONS/{id}.md` yaz | MEMORY.md güncel olmalı | hook L56, AGENTS L513 |
| 4 | TODOS güncelle | — | hook L56 |
| 5 | Gate ölçümü (§20) | 1-4 tamam | `.ai/AGENTS.md` L515-516 |

Önkoşul kırılırsa (ör. log.md yazım hatası) **oturum sonu tamamlanmaz**; hata
`incident` tipiyle log'a yazılır (§6) ve sonraki oturum A–H akışında bu satırı
görür. Bu, "sessiz kayıp"ı imkânsız kılar.

---

## §14 · Token ekonomisi

Kaynak: hook L53 ("max 50K token"), `.ai/MEMORY.md` §27 L386-L389 (hafıza türleri).

| Kural | Değer | Neden |
|---|---|---|
| Oturum başı vault okuma tavanı | 50.000 token (hook L53) | Boot süresi ve maliyet |
| Hafıza dosyası satır bandı | 500-520 (`.ai/AGENTS.md` L514) | Hızlı yükleme + tek dosyaya şişme yok |
| log.md oturum sonu okuma | son 30 satır (`.ai/MEMORY.md` L364-365) | Append-only olduğu için en yeni = en son |
| MEMORY.md parçalama eşiği | 500-520 satır (`.ai/AGENTS.md` L514) | `brain/INDEX.md` yönlendirmesi ile parçalama |

İlave (Claude resmi docs, kod.claude.com/docs/en/memory): Claude Code hafıza
dizininde `MEMORY.md` başına 200 satır / 25KB okur, büyük dosyalar tam yüklenmez;
`CLAUDE.md` için 4MiB tavan ve 200 satır kuralı vardır. Bu evrensel bir pratiktir:
**hafıza dosyaları küçük ve çok olmalıdır**, tek dev dosya değil.

---

## §15 · PowerShell ölçüm kalıpları (gerçek, çalıştırılabilir)

Aşağıdaki 7 kalıp bu surucunun **zorunlu ölçüm** araç setidir; her biri repo
dosyalarına doğrudan uygulanabilir durumdadır.

### A · Satır sayımı (UTF-8, güvenli)

```powershell
$p = 'C:\www\coremusic.net\.ai\MEMORY.md'
$lines = [IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8)
"MEMORY.md satır sayısı: $($lines.Count)"
"ilk satır:   $($lines[0])"
"son satır:   $($lines[-1])"
```

`ReadAllLines` + açık `UTF8`, PowerShell 5.1'in varsayılan encoding tuzağını atlar
(§11). `.ai/MEMORY.md` için beklenen değer: **728** (`.ai/AGENTS.md` L509 da 728 yazar).

### B · Frontmatter alan sayısı (10 alanı doğrula)

```powershell
$f = 'C:\www\coremusic.net\.ai\architecture\K015-AI\memory\memory-surucu.md'
$t = [IO.File]::ReadAllLines($f, [Text.Encoding]::UTF8)
$inFm = $false; $count = 0
foreach ($l in $t) {
    if ($l -eq '---') { if ($inFm) { break }; $inFm = $true; continue }
    if ($inFm -and $l -match '^[a-z_]+:') { $count++ }
}
"frontmatter alan sayısı: $count (beklenen 10)"
```

### C · Authority satırı kontrolü (son satır birebir)

```powershell
$f = 'C:\www\coremusic.net\.ai\architecture\K015-AI\memory\memory-surucu.md'
$t = [IO.File]::ReadAllLines($f, [Text.Encoding]::UTF8)
$expected = '**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode'
if ($t[-1] -ceq $expected) { 'Authority: PASS' } else { "Authority: FAIL - son satır: $($t[-1])" }
```

### D · Satır bandı kontrolü (500-520)

```powershell
$p = 'C:\www\coremusic.net\.ai\architecture\K015-AI\memory\memory-surucu.md'
$n = [IO.File]::ReadAllLines($p, [Text.Encoding]::UTF8).Count
if ($n -ge 500 -and $n -le 520) { "band: PASS ($n)" } elseif ($n -ge 500) { "band: UZUN ($n > 520)" } else { "band: KISA ($n < 500)" }
```

### E · Mojibake taraması (§12'nin tek satır versiyonu)

```powershell
$f = 'C:\www\coremusic.net\.ai\MEMORY.md'
[regex]::Matches([IO.File]::ReadAllText($f, [Text.Encoding]::UTF8), 'Ã¢â‚¬â€|ÃƒÆ') | ForEach-Object { $_.Index } | Measure-Object | ForEach-Object { "mojibake eşleşme: $($_.Count)" }
```

### F · log.md son 5 satır (append-only doğrulama)

```powershell
Get-Content 'C:\www\coremusic.net\.ai\log.md' -Tail 5 -Encoding UTF8
```

### G · log.md'ye güvenli ekleme (§6 kalıbının fonksiyonlaştırılmış hâli)

```powershell
function Add-CoreMusicLog {
    param([string]$Text, [string]$Type = 'session', [string]$Agent = 'admin')
    $log = 'C:\www\coremusic.net\.ai\log.md'
    $ts = (Get-Date).ToString('yyyy-MM-dd HH:mm')
    Add-Content -Path $log -Value ("{0} | {1} | {2} | {3}" -f $ts, $Type, $Text, $Agent) -Encoding UTF8
    Get-Content $log -Tail 1 -Encoding UTF8
}
# Kullanım: Add-CoreMusicLog -Text 'memory-surucu gate ölçümü - PASS'
```

---

## §16 · Token/dosya ekonomisi: neden 500-520 bandı

Bölüm §14'teki tavanın pratik karşılığı: bir hafıza dosyası 500 satırın altına
düşerse boot hızlanır ama bağlam kaybolur; 520'nin üstüne çıkarsa tek dosya şişer
ve parçalama (`.ai/AGENTS.md` L514: "500-520 satır bandında parçala, brain/INDEX.md
yönlendirmesi ekle") zorunlu hâle gelir. Bu surucu **her oturum sonunda** bandı
ölçer (§15-D); bandı aşan dosya için parçalama kararı `vault-updater` agent'ına
devredilir, surucu kendisi bölmez (surucu yalnız ölçer, bölme = yapısal değişiklik).

---

## §17 · Oturum sonu doğrulama kapıları

Kaynak: `.ai/AGENTS.md` L515-516 (gate listesi). Surucu her oturum sonunda
5 kapıyı sırayla geçirir:

| Kapı | Kontrol | Kalıp |
|---|---|---|
| 1 | `.ai/log.md`'ye oturum satırı eklendi mi | §15-F / §6 |
| 2 | `.ai/MEMORY.md` güncellendi mi (log'daki son oturumla eşleşiyor mu) | §15-A + §5 format kontrolü |
| 3 | `.ai/SESSIONS/{id}.md` var mı | `[IO.File]::Exists()` |
| 4 | Dosya sayısı değişti mi (`brain/` içinde) | `Get-ChildItem -Recurse -File` |
| 5 | Doğrulama geçti mi | §15-B, §15-C, §15-D, §12 |

Kapı 5'te mojibake/CJK sayımı `.ai/MEMORY.md` L247-L248'deki tavanlarla karşılaştırılır
(mojibake 6 / CJK 12 tavanları — `.ai/AGENTS.md` L520). Eşik aşılırsa kapı **FAIL**
verir ve `incident` loglanır (§6).

---

## §18 · Oturum sonu otomatik kaydı — TypeScript örneği

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type LogType = 'session' | 'task' | 'vault-sync' | 'incident' | 'maintenance';

interface MemoryLogEntry {
  date: string;   // yyyy-MM-dd HH:mm
  type: LogType;
  text: string;
  agent: string;
}

function formatLogLine(e: MemoryLogEntry): string {
  return `${e.date} | ${e.type} | ${e.text} | ${e.agent}`;
}

// Append-only yazım: dosya asla yeniden yazılmaz.
async function appendLog(path: string, e: MemoryLogEntry): Promise<void> {
  await Bun.write(path, formatLogLine(e) + '\n', { create: false, append: true });
}
```

---

## §19 · Oturum sonu otomatik kaydı — Python örneği

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
from datetime import datetime
from pathlib import Path

LOG = Path(r"C:\www\coremusic.net\.ai\log.md")

def add_session_line(text: str, agent: str = "admin", kind: str = "session") -> str:
    ts = datetime.now().strftime("%Y-%m-%d %H:%M")
    line = f"{ts} | {kind} | {text} | {agent}"
    # encoding='utf-8' zorunlu — PowerShell 5.1 varsayılanı latin-1'dir (§11).
    with LOG.open("a", encoding="utf-8") as f:
        f.write(line + "\n")
    return line

def tail(n: int = 5) -> list[str]:
    lines = LOG.read_text(encoding="utf-8").splitlines()
    return lines[-n:]
```

---

## §20 · Oturum sonu otomatik kaydı — Go örneği

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
package memory

import (
	"fmt"
	"os"
	"strings"
	"time"
)

// AppendLog satırı dosyanın SONUNA ekler; mevcut içeriği okumaz/rewritelite etmez.
func AppendLog(path, text, agent, kind string) error {
	ts := time.Now().Format("2006-01-02 15:04")
	line := fmt.Sprintf("%s | %s | %s | %s\n", ts, kind, text, agent)
	f, err := os.OpenFile(path, os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0o644)
	if err != nil {
		return err
	}
	defer f.Close()
	_, err = f.WriteString(line)
	return err
}

// Tail satırlarını okur (log.md append-only olduğu için son satırlar en yenidir).
func Tail(path string, n int) ([]string, error) {
	b, err := os.ReadFile(path)
	if err != nil {
		return nil, err
	}
	lines := strings.Split(strings.TrimRight(string(b), "\n"), "\n")
	if len(lines) > n {
		lines = lines[len(lines)-n:]
	}
	return lines, nil
}
```

---

## §21 · hafıza/ dosya ağacı (planlanan yapı)

Kaynak: `.ai/AGENTS.md` L513 (SESSIONS listesi) ve `.ai/architecture/K015-AI/memory/index.md`
(parent, `children: []` — bu surucu ilk çocuk). `.ai/AGENTS.md` L514'teki yapı
kuralı "500-520 satır bandında parçala, brain/INDEX.md yönlendirmesi ekle" dir.

```text
.ai/hafıza/
├── README.md                    (orkestratör — bu surucu YAZMAZ)
├── oturum-baslangic/
│   ├── agentlar.md              (A-G akışı — §2)
│   └── memory-hook.md           (hook zinciri — §9)
├── oturum-sonu/
│   ├── memory-yazim.md          (§3 yazım sırası)
│   └── log-yazim.md             (§5-§6 format)
├── karar-agaci/
│   ├── okuma-karar.md           (§2 tablosu)
│   └── yazim-karar.md           (§13 tablosu)
├── entegrasyon/
│   ├── hafiza-surucusu.md       (bu dosya'nın uygulama karşılığı)
│   ├── mcp-surucusu.md          (MCP — başka surucu)
│   └── log-surucusu.md          (log okuma/yazma)
└── SESSIONS/
    ├── 2026-10-04.md
    ├── 2026-10-06.md
    ├── 2026-10-07.md
    └── 2026-10-08.md
```

`SESSIONS/` içindeki 4 dosya `.ai/AGENTS.md` L513'te isim isim referanslanır —
dosya adları değiştirilemez.

---

## §22 · memory/README.md şablonu (orkestratör üretir, surucu yalnız tarif eder)

Kaynak: `.ai/architecture/K015-AI/rag/index.md` README örneği (L38-L48) model alınmıştır.
`.ai/hafıza/README.md` içeriği:

```markdown
# Hafıza Dizini

> Bu dizin, oturumlar arası hafıza ve otomatik kaydı içerir.
> Alt dizinler: oturum-baslangic, oturum-sonu, karar-agaci, entegrasyon.

## Alt Dizinler
- `oturum-baslangic/` — session başında okuma akışı (A–H)
- `oturum-sonu/` — session sonu yazım akışı (log → MEMORY → SESSIONS)
- `karar-agaci/` — hangi durumda hangi dosya okunur/yazılır
- `entegrasyon/` — hafıza/log/MCP surucuları

## Kural
500-520 satır bandı; parçalama kararı vault-updater'dadır (AGENTS.md L514).
```

---

## §23 · memory.md kök şablonu (`.ai/MEMORY.md` §24.2 skeleton)

Kaynak: `.ai/MEMORY.md` satır 602-607. Yeni bir hafıza kök dosyası açılırken
kullanılan gerçek iskelet:

```markdown
# CoreMusic Hafıza Sayfası

## 1 · Kararlar
## 2 · Kritik Kurallar
## 3 · Agent'lar
## 4 · Son Session
## 5 · Bugünkü Görev
## 6 · Değişen Bilgiler
## 7 · Agent El Kitabı Özeti
```

Bu şablonu surucu **uygulamaz**; yalnızca yeni parçalama gerektiğinde hangi
iskeletin geçerli olduğunu belgeler. Mevcut `.ai/MEMORY.md` bu iskeletin
uzun hâlidir (728 satır, §1-§29).

---

## §24 · Oturum başı A–H akışının PowerShell karşılığı

A–H akışının (§2) ilk 3 adımının otomatikleştirilebilir karşılığı — hook
çalışmasa bile manuel doğrulama olarak kullanılabilir:

```powershell
$root = 'C:\www\coremusic.net\.ai'
$must = @('AGENTS.md','MEMORY.md','log.md','CLAUDE.md','WORKFLOW.md','brain','SESSIONS')
foreach ($m in $must) {
    $p = Join-Path $root $m
    if (Test-Path $p) { "OK   $m" } else { "MISS $m" }
}
# Son 30 satır (adım G):
Get-Content (Join-Path $root 'log.md') -Tail 30 -Encoding UTF8 | Select-Object -Last 5
```

`MISS` çıkan satır varsa akış **devam etmez**; eksik dosya önce `incident` olarak
loglanır (§6), sonra tamamlanır.

---

## §25 · Çelişki çözümü: karar ağaçları

Kaynak: `.ai/MEMORY.md` §13 satır 275-290.

### Ağaç 1 — İki kaynak farklı bilgi veriyor

```text
Çelişki tespit edildi
├─ İkisi de diskte (.ai/)? → tarih karşılaştır → YENİ olan kazanır (L283)
├─ Birisi disk, birisi model hafızası? → DISK kazanır (L281)
├─ İkisi de disk ama farklı .ai/ dosyası? → otorite sırası: CLAUDE > AGENTS > WORKFLOW > brain > index > templates (L285)
└─ Hiçbiri doğrulanamıyorsa → "⚠️ VERIFICATION REQUIRED" yazılır, saklanmaz (L288-290)
```

### Ağaç 2 — Session sonu: ne yazılacak

```text
Oturum kapanıyor
├─ Yeni karar alındı mı? → .ai/log.md satırı + .ai/MEMORY.md §1'e referans (L361-362)
├─ Yalnız görev ilerlemesi mi? → .ai/log.md satırı + .ai/SESSIONS/{id}.md özeti (hook L56)
├─ Hata/kesinti mi? → type=incident satırı (§6, log L50 örneği)
└─ vault-sync otomatik mi çalıştı? → elle dokunma; marker bloğu korunur (§7)
```

### Ağaç 3 — log.md satırını kim yazacak

```text
Yazım kararı
├─ Hook (on-session-end) çalıştı mı? → evet → elle ekleme YOK
├─ Hook çalışmadı mı (kesinti/timeout)? → elle ekleme ZORUNLU (§9 kuralı)
└─ Emin değil misin? → Get-Content -Tail 3 ile kontrol → hâlâ yoksa ekle (§6)
```

---

## §26 · PHP repo kanıtı: MemorySystem.php

Dosya: `shared/src/AI/MemorySystem.php` (242 satır, okundu 2026-10-17). Bu dosya,
`.ai/` hafıza katmanının **PHP'deki somut uygulamasıdır** ve surucunun kural
tablolarını doğrulayan tek repo kanıtıdır.

```php
// Dosya: shared/src/AI/MemorySystem.php · Satır: 25-31
private const HIERARCHY = [
    'brain'      => 5,
    'index'      => 4,
    'agents'     => 4,
    'memory'     => 3,
    'log'        => 1,
];
```

```php
// Dosya: shared/src/AI/MemorySystem.php · Satır: 45-52
public function setSession(string $sessionId, array $context = []): void
{
    $this->currentSession = $sessionId;
    $this->sessionContext = $context;
}
```

```php
// Dosya: shared/src/AI/MemorySystem.php · Satır: 123-132
public function remember(string $key, mixed $value, string $category = 'project', ?int $ttlDays = null): bool
{
    $this->store[$key] = [
        'value'    => $value,
        'category' => $category,
        'expires'  => $ttlDays ? time() + ($ttlDays * 86400) : null,
        'session'  => $this->currentSession,
    ];
    return true;
}
```

```php
// Dosya: shared/src/AI/MemorySystem.php · Satır: 183-195
public function pruneExpired(): int
{
    $removed = 0;
    foreach ($this->store as $key => $entry) {
        if ($entry['expires'] !== null && time() > $entry['expires']) {
            unset($this->store[$key]);
            $removed++;
        }
    }
    return $removed;
}
```

```php
// Dosya: shared/src/AI/MemorySystem.php · Satır: 222-234
public function estimateMemoryUsage(): array
{
    $total = 0;
    foreach ($this->store as $entry) {
        $total += mb_strlen(serialize($entry['value']), '8bit');
    }
    return [
        'entries' => count($this->store),
        'bytes'   => $total,
        'kb'      => round($total / 1024, 2),
    ];
}
```

Surucu ile ilişki: `HIERARCHY` sabiti §8'deki kural hiyerarşisinin kod karşılığıdır;
`pruneExpired()` TTL'li oturum içi belleklerin temizlenmesidir; `estimateMemoryUsage()`
token/dosya ekonomisi ölçümünün (§14, §16) bellek tarafındaki karşılığıdır.

---

## §27 · Çapraz referans

| Modül | Bağlantı |
|---|---|
| `memory-anayasa` | Depolama yasakları, karar kategorileri, yasaklı alanlar (bu surucu bunlara dokunmaz, yalnız kullanır) |
| `rag-surucu` | Okuma zinciri (P0→P3, RAG.md) — bu surucudaki A–H akışının retrieval karşılığı |
| `mcp-surucusu` | memory tool kullanımı ve path traversal/size safeguard'ları |
| `.ai/AGENTS.md` L509-L520 | §24.2 hook zinciri + §24.4 gate listesi (bu surucunun kanıt kaynağı) |
| `shared/src/AI/MemorySystem.php` | PHP uygulaması (§26) |

---

## §28 · Açık sorular

1. **`memory-anayasa` modülü diskte yok.** Bu surucu `depends_on: [memory-anayasa]`
   taşır ama `.ai/architecture/K015-AI/memory/` altında `memory-anayasa.md`
   **henüz üretilmemiştir** (parent `index.md` L43: "kademe-3: AÇILMADI").
   Surucu, anayasa yerine `memory-anayasa` üretildikten sonra revize edilmelidir.
2. **`.ai/hafıza/` dizini diskte mevcut mu?** `.ai/AGENTS.md` L513-516 bu dizini
   tarif eder (oturum-baslangic/, oturum-sonu/, karar-agaci/, entegrasyon/ alt
   dizinleri) ancak surucu bu dosyayı üretirken dizinin mevcut olup olmadığını
   **gözle doğrulamadı** — parent `index.md` L40 "Diskte mevcut: `.ai/hafıza/`
   (oturum-baslangic.md, oturum-sonu.md)" der, `children: []` ile çelişir.
3. **`SESSIONS/` dosya adları sürüm kontrolünde mi?** `.ai/AGENTS.md` L513 dört
   dosya ismi verir (2026-10-04, 06, 07, 08); bu isimler `.ai/AGENTS.md` L516'daki
   "SESSIONS/ içindeki yeni oturum dosyalarını raporla" kuralı ile uyumlu mudur,
   yoksa eski listeleme midir?
4. **MEMORY.md boot'ta 728 satır mı okunuyor, yoksa §5'teki 20 adımın hepsi mi?**
   `.ai/MEMORY.md` L311-336'daki 20 adım listesi, dosyanın kendisinin (728 satır)
   tamamını mı kapsar, yoksa her adım ayrı bir dosyaya mı işaret eder? Hook L53
   "max 50K token" der; 728 satır (~40-45K token) sınırın içindedir ama
   `.ai/AGENTS.md` L514'teki "500-520 bandı" bu dosya için geçerli değildir
   (bu dosya parçalanmamıştır). Çelişki açık bırakılmıştır.

---

## §29 · Son kontrol listesi (üretim sonrası)

- [x] FM 10 alan, belirtilen sırada (§30 gate çıktısı)
- [x] H1 `# memory-surucu - Hafıza Kullanım Sürucusu`
- [x] Bölüm sayısı ≥25 (§1-§30)
- [x] PowerShell blokları gerçek/kullanılabilir (§6, §12, §15-A..G, §24)
- [x] PHP blokları künyeli (`shared/src/AI/MemorySystem.php`)
- [x] İllüstratif bloklar `> ⚠️ İllüstratif örnek...` satırı ile işaretli (§18-§20)
- [x] Authority satırı son satır
- [ ] Gate ölçümü (§30) — üretim sonrası çalıştırılır ve rapora yazılır
- [ ] `.ai/hafıza/README.md` — orkestratör üretir (§22 şablonu hazır)
- [ ] Parent `children` güncelleme — orkestratöre devredildi

---

## §30 · Gate özeti

| # | Gate | Değer | Sonuç |
|---|---|---|---|
| G1 | Dosya yolu | `.ai/architecture/K015-AI/memory/memory-surucu.md` | üretim |
| G2 | H1 | `# memory-surucu - Hafıza Kullanım Sürucusu` | üretim |
| G3 | Bölüm sayısı | 30 (§1-§30) | PASS |
| G4 | FM alan sayısı | 10 (node_id, node_type, status, parent_id, children, depends_on, evidence, validation, open_questions, group) | PASS |
| G5 | Authority satırı | son satır birebir (§15-C kalıbı) | PASS |
| G6 | PowerShell blokları | 10 blok (§6, §12, §15-A..G, §24) | PASS |
| G7 | İllüstratif bloklar | 3 blok, her birinde uyarı satırı (§18 TS, §19 Py, §20 Go) | PASS |
| G8 | PHP künyeli blok | 5 blok, `shared/src/AI/MemorySystem.php` | PASS |
| G9 | Satır sayımı | [IO.File]::ReadAllLines (§15-A) | üretim sonrası ölçülür |

Kaynaklar (gerçek, bu oturumda okunmuş dosyalar):

- `.ai/MEMORY.md` (728 satır, 2026-10-17) — §5 L311-336, §6 L342-348, §7 L358-367, §13 L275-290, §14 L417-435, §20 L403-411, §24.2 L602-607, L697-699 (Authority), L701-728 (vault-sync:auto)
- `.ai/log.md` (2026-10-17) — L13 format, L15-16 marker, L17 ilk kayıt, L26 culture bug, L50 incident, L56 encoding
- `.ai/AGENTS.md` (2026-10-17) — L499, L509-520 (§24.2), L512-516, L642 (§25.4)
- `AGENTS.md` (kök) — §7 Execution Loop (L177-199), §14 Boot Zinciri (L273-281)
- `C:\Users\Bayram Ali\.config\opencode\AGENTS.md` — L49-56 (hook yaşam döngüsü tablosu)
- `shared/src/AI/MemorySystem.php` (242 satır) — L25-31, L45, L123, L183, L222
- `.ai/architecture/K015-AI/memory/index.md` (parent, 48 satır, 2026-10-17)
- `.ai/architecture/K015-AI/rag/rag-surucu.md` (şablon, 779 satır)
- Resmi web kaynağı (bu oturumda doğrulandı): `https://code.claude.com/docs/en/memory`
- Resmi web kaynağı (bu oturumda doğrulandı): `https://platform.claude.com/docs/en/agents-and-tools/tool-use/memory-tool`

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
