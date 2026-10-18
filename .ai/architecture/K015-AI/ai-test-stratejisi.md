---
title: "K015-AI — Test Stratejisi"
node_id: K015-AI/ai-test-stratejisi.md
node_type: subdomain
status: draft
parent_id: K015-AI/index.md
children: []
depends_on:
  - K007-data
  - K009-security
  - K011-application
  - K012-services
evidence:
  - "shared/src/AI/AuthenticationMiddleware.php:22-24 (401 fail-closed) · 2026-10-18"
  - "shared/src/AI/ToolCalling.php:28-40 (AGENT_PERMISSIONS) · 2026-10-18"
  - "shared/src/AI/PromptEngine.php:27-35 (INJECTION_PATTERNS) · 2026-10-18"
  - "shared/src/AI/MemorySystem.php:18-31 (L1/L2/L3) · 2026-10-18"
validation: "2026-10-18 — piramit + senaryolar + kabul kriterleri; koşul öncesi testin diskte var olduğu bilgisi UNKNOWN"
open_questions: "⚠️ bu senaryoların diskteki test dosyalarıyla eşleşmesi doğrulanmadı (UNKNOWN) · ⚠️ CI'da AI testleri geçiyor mu bilinmiyor"
group: mimari
---

# K015-AI — Test Stratejisi

## §1 ELI15

Bu dosya "AI katmanı nasıl sınamalanır" sorusunu yanıtlar. Üç katmanlı bir
**piramit** kurarız: altta çok sayıla hızlı birim testi, ortada entegrasyon, en
üstte birkaç uçtan uca senaryo. Sonra her risk için somut senaryo ve
**kabul kriterlerini** (neyi görünce "geçti" diyeceğimizi) yazarız. Kanıtsız
"test edildi" demek yok — diskte testin varlığı doğrulanmadıysa UNKNOWN'dur.

## §2 Kapsam

- K015-AI bileşenleri (C-01…C-06) ve kararları (D-01…D-08).
- Test altyapısının kendisi K18-testing'in (depends_on listesinde yok; bu paketin
  bağımlılığı değil, kesişen alan).

## §3 Test Piramidi

```
        ┌────────────────────┐
        │  E2E (3-5 senaryo) │  uçtan uca: görev → agent → araç → yanıt
        ├────────────────────┤
        │ Entegrasyon (8-12) │  middleware zinciri, DB katmanı, araç kayıt
        ├────────────────────┤
        │  Birim (25+)       │  AGENT_MAP, whitelist, regex, katman doğrulama
        └────────────────────┘
```

| Katman | Hedef | Çalışma zamanı | Odak |
|--------|-------|----------------|------|
| Birim | %80+ (AI dizini) | <2 sn | saf mantık: harita, izin, regex, katman |
| Entegrasyon | kritik akışlar | <30 sn | 401 kapısı, araç çağrı zinciri, kalıcı katman |
| E2E | 3-5 senaryo | <2 dk | kullanıcı görevi → agent → araç → sonuç |

## §4 Birim Test Senaryoları

| # | Senaryo | Hedef (kanıt) | Beklenen |
|---|---------|---------------|----------|
| U-01 | Bilinmeyen görev `dispatch` | `AIOrchestrator.php:26-38` | `UnknownTaskException` (varsayılana düşmez) |
| U-02 | İzinsiz araç `allows()` | `ToolCalling.php:28-40` | `false` (default reddir) |
| U-03 | İzinli araç `allows()` | `ToolCalling.php:28-40` | `true` |
| U-04 | Injection kalıbı girdisi | `PromptEngine.php:27-35` | `InjectionDetectedException` |
| U-05 | Temiz girdi `sanitize()` | `PromptEngine.php:27-35` | kırpılmış, değişmemiş metin |
| U-06 | Bilinmeyen bellek katmanı | `MemorySystem.php:18-31` | `UnknownLayerException` |
| U-07 | L1/L2/L3 geçerli katman | `MemorySystem.php:18-31` | kayıt başarılı |
| U-08 | Token yok → auth | `AuthenticationMiddleware.php:22-24` | 401 (fail-closed) |
| U-09 | Geçersiz token → auth | `AuthenticationMiddleware.php:22-24` | 401 |
| U-10 | Geçerli token → auth | `AuthenticationMiddleware.php:22-24` | `$next($req)` çağrılır |

## §5 Entegrasyon Senaryoları (risk bağlantılı)

| # | Senaryo | Bağlı risk | Kabul kriteri |
|---|---------|-----------|----------------|
| I-01 | Prompt injection → araç çağrısı denemesi | R-01 | Tarama engeller; araç **çağrılmaz** (log + exception) |
| I-02 | Auth'sız AI ucuna istek | R-02 | 401; gövdede model çıktısı yok |
| I-03 | Agent → izinsiz araç zinciri | R-03 | Reddet; istek 403/exception ile sonuçlanır |
| I-04 | Hassas bağlamın L3'e yazımı | R-04 | Katman politikası uygulanır; L3'e yazım ya reddedilir ya işaretlenir |
| I-05 | Sağlayıcı yanıtı gelmez (timeout) | R-08 | Zaman aşımı hatası görünür; yutulmaz, "başarılı" sayılmaz |
| I-06 | Kesikli (partial) araç sonucu | R-01 | Kısmi sonuç tam sayılmaz; kullanıcıya eksik bildirilir |

## §6 E2E Senaryoları

| # | Senaryo | Akış | Kabul kriteri |
|---|---------|------|----------------|
| E-01 | "Şarkı sözünü özetle" | görev → reader-agent → kb.search → yanıt | Özet döner; araçlar whitelist içinde |
| E-02 | Kimlik doğrulanmamış istek | uc → auth | 401; agent hiç başlamaz |
| E-03 | Öneri görevi | recommend → reco-agent → user.history.read | Öneri döner; izinli araçlar aşılmaz |

## §7 Güvenlik Testi Zorunlulukları (K009 kesişimi)

1. **Injection paketi:** §4 U-04 kalıpları + bilinen yeni kalıplar (cadence §7 U-04
   `ai-riskler.md`).
2. **Fail-closed doğrulaması:** Doğrulanamayan her yol 401/exception ile biter —
   fail-open testi (yanlışlıkla geçme) **zorunlu**.
3. **Whitelist aşımı:** Bilinmeyen agent/araç çifti reddi (U-02).
4. Prompt çıktısında gizli sızıntı yok (yanıt gövdesi salt iş sonucu).

## §8 Ölçüm ve Eşikler (kabul kriterleri)

| Metrik | Eşik | Kaynak |
|--------|------|--------|
| Birim test geçiş oranı | %100 (bu paket) | CI |
| AI dizini coverage | ≥%80 | K18-testing aracı |
| Injection testi kaçırma | 0 kabul edilemez | I-01 |
| Fail-open gözlemi | 0 | I-02/U-08 |

## §9 Test Dosyaları (disk kanıtı — sınırlı)

- **UNKNOWN:** Bu senaryoların diskte karşılık gelen test dosyalarıyla eşleşmesi bu
  oturumda **doğrulanmadı** (zero-hallucination — test dosyası yolu uydurulmadı).
- K16 gözlemi örneğinde test dosyaları (`Psr3LoggerTest`, `LoggerRedactionTest`)
  okunmuştur; K015 için benzer okuma **yapılmadı** → ⚠️ VERIFICATION REQUIRED.
- Kapanış adımı: `tests/` altında AI test dosyaları grep'lenip bu bölüm "kanıt:
  dosya:senaryo" ile güncellenir.

## §10 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Etki |
|---|-----------|------|
| T-01 | AI test dosyalarının diskte varlığı | §9 — test kapsamı doğrulanamadı |
| T-02 | CI'da AI testlerinin geçtiği | raporlanamaz |
| T-03 | Coverage değeri | §8 eşiği doğrulanamadı |

## §11 İlgili Dosyalar

- `ai-riskler.md` — senaryoların bağlandığı R-01…R-08
- `ai-kod-ornekleri.md` — test edilecek davranışın örneği
- `ai-dogrulama.md` — doğrulama zinciri (bu testler buraya akar)
- `../K18-testing/` — test altyapısının geneli

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
