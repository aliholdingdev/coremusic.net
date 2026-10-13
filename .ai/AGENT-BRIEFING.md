---
node_id: "K000-isletim-sistemi/AGENT-BRIEFING"
node_type: "evidence"
status: "verify-required"
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "isletim-sistemi-kernel"
evidence:
  - "C:\\www\\coremusic.net\\CLAUDE.md"
  - "C:\\www\\coremusic.net\\.claude\\skills\\skill-maker\\SKILL.md"
validation: "Her bölüm disk kanıtına bağlı; iddiasız satır yok — parent index gerektirmez (bağımsız brief)"
open_questions:
  - "Global skill dizini iddiası (C:\\.claude\\skills) diskte YOK → registry ile çelişki, §5'te kayıtlı"
group: "kanit"
---

# AGENT BRIEFING — Çekirdek Doğrulama ve İş Protokolü

**Amaç:** Bu belge, CoreMusic deposunda çalışan her ajanın (insan veya AI) uyması
gereken dört çekirdek ilkeyi ve güncel toplu iş listesini tek yerde toplar.
Kanıt ilkeliği (evidence-first) burada GEÇERLİDİR: aşağıdaki her iddia disk
gözlemine dayanır; gözlemlenmeyen her şey `UNKNOWN` veya `⚠️ VERIFICATION REQUIRED`
olarak işaretlenir.

---

## §1 Zero-Hallucination (TDD ve kanıt)

**TDD = Test-Driven Development.** Uygulama sırasında kanıtlanabilirlik, kod
yazımının ön koşuludur: davranış önce testle tanımlanır, sonra implemente edilir.

| Kural | Uygulama |
|---|---|
| 1. Varsayım yok | Depo, dosya, dizin, URL, API, sınıf, metod, sürüm, bağımlılık, config **asla tahmin edilmez** — disk veya HTTP yanıtından okunur |
| 2. Bilinmeyen = `UNKNOWN` | Değer soruluyorsa ve ölçülmüyorsa uydurulmaz, `UNKNOWN` yazılır |
| 3. Doğrulanamayan = `⚠️ VERIFICATION REQUIRED` | Kaynak var ama içerik okunmadıysa iddia işaretlenir |
| 4. Model hafızası vault'un altındadır** | Hatıradaki "bilgi", depo içi kanıtın üstüne yazılamaz |
| 5. Tahminle yol bulunmaz** | 2026-10-12 ölçümü: tahmin edilen 14 Android doküma yolu **14'ü HTTP 404**; indeks sayfasındaki `href` listesinden çıkarılan yollar **10/10 HTTP 200** |
| 6. Kod kanıtı iki kaynaklıdır** | `[RESMİ ALINTI · URL · tarih]` veya `**[ORİJİNAL ÖRNEK · derlenmedi]**` — bu depoda 0 `.cpp/.h` olduğu için `Dosya:/Satır:` referansı ÜRETİLEMEZ |

**Testi ne zaman çalıştırırım:** Bir iddia yazıldığı anda — kaynak HTTP 200 mü,
gövde okundu mu, tarih ne? Üçü de yoksa satır ya silinir ya işaretlenir.

---

## §2 Evidence-First ve Verification Before Completion

**Sıra asla tersine çevrilemez:** kanıt → iddia. İddia → kanıt aramak (sonradan
"doğrularım" yaklaşımı) bu depoda yasaktır.

| Aşama | Zorunlu çıktı | Red nedeni |
|---|---|---|
| 1. Topla | Gerçek URL/dosya yolu + HTTP durumu + tarih | "Kaynak var" deyip link vermeme |
| 2. Oku | Sayfa gövdesinden alıntı veya başlık kanıtı ayrımı | Başlık kanıtını içerik kanıtı gibi sunma |
| 3. İşaretle | Her hücrede kanıt düzeyi (gövde / başlık / okunmadı) | İşaretsiz eksiklik |
| 4. Yaz | Yalnız okunan kadarı yazılır | Okunmayandan iddia üretme |
| 5. Doğrula | "Bitti" demeden ÖNCE yeniden ölç | Ölçüm olmadan "tamamlandı" |

**Verification Before Completion — "bitti"den önce kontrol listesi:**

- [ ] Dosya sayısı ve satır sayısı yeniden sayıldı mı? (hedefi tutturmak ölçülür, varsayılmaz)
- [ ] Frontmatter alan sayısı 10 mu? `node_id` dosya yolunun `.md`siz hâli mi?
- [ ] URL'ler dosyalar arası KESİŞİYOR mu? (kesişim > 0 ise hata)
- [ ] Dolgu/kısaltma yer tutucuları (üç nokta, T·B·D, "yakında" benzeri) var mı?
- [ ] Mojibake / yanlış karakter taraması yapıldı mı? (UTF-8, PowerShell yerine `Get-Content -Encoding UTF8`)
- [ ] `git commit` ATILMADI mı? (commit yetkisi orkestratöre aittir)

---

## §3 Verification İstekleri — nasıl ve ne zaman

| Tetikleyici | Ajan eylemi | Format |
|---|---|---|
| Yeni dosya/dizin iddiası | `Test-Path` / glob ile diskte var mı bak | yol + SONUC |
| URL iddiası | `Invoke-WebRequest` GET (**HEAD değil**) + tarayıcı User-Agent | URL + HTTP kodu + tarih |
| İçerik iddiası | Gövdeyi indir, alıntı yap, "Last updated" tarihini kaydet | alıntı + URL + tarih |
| Satır/sayı sayımı | `Get-Content -Encoding UTF8` ile say | dosya + sayı |
| Dosyalar arası URL kesişimi | regex `https://[^\s\)..."<]+` ile kümeleri kesiştir | kesişim kümesi (boş olmalı) |
| "Bitti" iddiası | Yukarıdaki checklist'in tamamı çalıştırılır | ölçüm çıktısı + sonuç |

**Kritik pitfall kayıtları (2026-10-12, bu depoda ölçüldü):**

| Pitfall | Gözlenen sonuç | Kural |
|---|---|---|
| `Invoke-WebRequest -Method Head` | Bazı sayfalarda 404, GET ile 200 | Her zaman GET |
| Varsayılan User-Agent | Bazı sayfalarda 404 | Tarayıcı UA'sı kullan |
| Bot duvarı (Anubis v1.19.1) | `reactos.org/wiki/*` → HTTP 200 ama gövde bot-challenge (4188 bayt) | Durum kodu ≠ içerik kanıtı |
| Tahmini URL yolu | 14/14 → 404 | Yol indeks `href` listesinden çıkarılır |
| PowerShell ile UTF-8 yazım** | Türkçe karakter bozulma riski | Yazım `write` aracıyla; doğrulama `Get-Content -Encoding UTF8` |

---

## §4 Ek kontrol — derin-dal işi (platform klasörleri) güncel durum

**Kapsam:** `K000-isletim-sistemi/isletim-sistemi-turu/` altına 5 yeni platform
derin-dalı (`reactos`, `android`, `ios`, `tizen`, `webos`) — her biri 6 içerik
dosyası (≥500 satır) + `index.md` (40–100 satır, `## §GRUPLAR`) + parent index
`children`/§GRUPLAR güncellemesi.

| Klasör | Durum (ölçüm) | Not |
|---|---|---|
| `reactos/` | 6/6 içerik + `index.md` YAZILDI | 61 tekil URL · kesişim 0 · Anubis bot-duvarı belgelendi |
| `android/` | 2/6 içerik + (`audio-mimarisi` 552 satır · `aaudio-ve-opensl` ≈500) | 28 URL doğrulandı (HTTP 200) · 4 dosya + index kaldı |
| `ios/` | 0/6 | 56 URL havuzu hazır · **2 URL 404 — kullanılmayacak** |
| `tizen/` | 0/6 | 77 URL havuzu |
| `webos/` | 0/6 | 97 URL havuzu (60 webOSTV + 37 GStreamer) · "webOS TV GStreamer kullanır mı" = `⚠️ VERIFICATION REQUIRED` |
| parent `index.md` | BEKLEMEDE | yalnız `children` + §GRUPLAR satırları eklenecek |

**Bağlayıcı kısıtlar:** whitelist dışı dosya/dizin YAZILMAZ · `windows/linux/macos`,
`wasapi/`, root `os-*.md`, ADR'ler, Band-2..7 DOKUNULMAZ · `git commit` ATILMAZ ·
subagent derinlik sınırı dolu (1) → tüm yazım oturum içi (inline).

**Kalite kapısı (her dosya için):** 10 frontmatter alanı · `group` doğru
(`index`+`architecture` → `mimari`, `-kod-ornekleri` → `kod`, `-web-arastirma` → `kanit`) ·
≥8 tekil resmi URL ve dosyalar arası kesişim 0 · `## §N` bölümleri ·
`| İddia | URL | Tarih: 2026-10-12 | Durum |` doğrulama matrisi ·
imla hataları KORUNUR (SSOT §3 KURAL 1) · dolgu yer tutucu YOK ·
footer `Authority: Bayram Ali / Vault Steward`.

---

## §5 Toplu iş listesi — top-5 (öncelik sırasıyla)

| # | İş | Kabul kriteri | Durum |
|---|---|---|---|
| 1 | `android/` kalan 4 dosya + `index.md` | her dosya ≥500 satır · 10 alan · URL kesişim 0 · index 40–100 satır + §GRUPLAR 7 satır | DEVAM |
| 2 | `ios/` (6 + index) | `-coreaudio-stacki` (depends_on `K003-audio`) · `-audio-session` · `-audio-unit-ve-avaudioengine` · `-background-ve-lifecycle` · `-kod-ornekleri` · `-web-arastirma` · 56 URL · **2×404 URL hariç** | BEKLEMEDE |
| 3 | `tizen/` (6 + index) | `-multimedia-stacki` · `-audio-api` (depends_on `isletim-sistemi-api`) · `-kernel-ve-surucu` · `-app-ve-lifecycle` · `-kod-ornekleri` · `-web-arastirma` · 77 URL | BEKLEMEDE |
| 4 | `webos/` (6 + index) | `-multimedia-stacki` · `-audio-pipeline` · `-gstreamer-entegrasyonu` (depends_on `K015-media`) · `-app-ve-lifecycle` · `-kod-ornekleri` · `-web-arastirma` · 97 URL | BEKLEMEDE |
| 5 | parent `isletim-sistemi-turu/index.md` + final rapor | `children`'a 5 klasör + §GRUPLAR'a 5 satır · silme YOK · rapor formatı `✅ YENİ PLATFORM DERİN-DAL TAMAM` · **commit ATILMADI** | BEKLEMEDE |

**Kapanış notu:** İş bittiğinde ölçüm olmadan "tamamlandı" denmez (§2). Her klasör
için satır sayısı, frontmatter, URL kesişim sayısı ve dolgu taraması rapora sayı
olarak yazılır.

---

**Authority: Bayram Ali / Vault Steward**
**Tarih:** 2026-10-12
**Mod:** Red Team · Human Mode · Truth Mode
