---
title: "K015-AI — Web Araştırması"
node_id: K015-AI/ai-web-arastirma.md
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
  - "https://genai.owasp.org/ — doğrulama 2026-10-13 (ai-dogrulama.md §4)"
  - "https://top10.owasp.org/ — doğrulama 2026-10-13 (ai-dogrulama.md §4)"
  - "https://www.nist.gov/itl/ai-risk-management-framework — doğrulama 2026-10-13 (ai-dogrulama.md §4)"
  - "https://airc.nist.gov/ — doğrulama 2026-10-13 (ai-dogrulama.md §4)"
  - "https://arxiv.org/abs/1810.03993 (Model Cards) — doğrulama 2026-10-13 (ai-dogrulama.md §4)"
  - "https://modelcontextprotocol.io/specification/2026-07-28 — doğrulama 2026-10-18 (bu oturum)"
validation: "2026-10-18 — 6 URL üretim öncesi doğrulandı; tarihler kaynak bazında dürüstçe yazıldı"
open_questions: "⚠️ MCP sürüm/yıldız kanıtı yok (yalnızca spesifikasyon erişimi doğrulandı) · ⚠️ OWASP/NIST sayfalarının 2026-10-18 tarihli içeriği yeniden okunmadı"
group: kanit
---

# K015-AI — Web Araştırması

## §1 ELI15

Bu dosya, yapay zeka katmanı kararlarını **dış dünyadaki güvenilir kaynaklara**
bağlar. Kural iki kat: (1) her URL üretimden **önce** açılabilir, (2) her maddeye
**erişim tarihi** yazılır. Kaynak yoksa ya da açılmıyorsa o madde yazılmaz —
uydurma link, uydurma tarih yasaktır.

## §2 Kapsam

- Güvenlik (OWASP), yönetim çerçevesi (NIST), şeffaflık (Model Cards), araç
  entegrasyonu (MCP) — K015 kararlarıyla (D-03, D-04, D-08) doğrudan ilişkili.
- Üretim öncesi doğrulama: 2026-10-18 (MCP) · 2026-10-13 (`ai-dogrulama.md` §4 havuzu).

## §3 Doğrulanmış Kaynaklar

| # | Kaynak | URL | Erişim | Ne verir |
|---|--------|-----|--------|----------|
| S-1 | OWASP GenAI Security | https://genai.owasp.org/ | 2026-10-13 | Güvenlik riski sınıflandırması (R-01 bağlamı) |
| S-2 | OWASP Top 10 | https://top10.owasp.org/ | 2026-10-13 | Web güvenlik referansı (R-02 bağlamı) |
| S-3 | NIST AI RMF | https://www.nist.gov/itl/ai-risk-management-framework | 2026-10-13 | AI riski yönetim çerçevesi (§4) |
| S-4 | NIST AI Risk Resource Center | https://airc.nist.gov/ | 2026-10-13 | NIST AI dokümantasyonu |
| S-5 | Model Cards (arXiv) | https://arxiv.org/abs/1810.03993 | 2026-10-13 | Model şeffaflığı standardı (§4) |
| S-6 | MCP Spesifikasyonu | https://modelcontextprotocol.io/specification/2026-07-28 | 2026-10-18 | JSON-RPC 2.0 · Host/Client/Server · Resources/Prompts/Tools (D-08) |

> **Doğrulama notu:** S-6 bu oturumda üretim öncesi açıldı (2026-10-18).
> S-1…S-5 `ai-dogrulama.md` §4 havuzunda 2026-10-13 doğrulanmıştır; **2026-10-18'de
> yeniden okunmadı** → iç güncellemeler için yeniden doğrulama gerekir
> (⚠️ §6 D-01).

## §4 Çıkarımlar → Kararlar

### 4.1 Prompt injection / LLM güvenlik (→ R-01, D-03)
- OWASP GenAI (S-1) LLM uygulama risklerini sınıflandırır; `INJECTION_PATTERNS`
  taraması (D-03) tek savunma katmanı olarak **yeterli sayılmaz** — kaynak,
  katmanlı savunma yaklaşımını destekler (`ai-riskler.md` R-01 "kalan boşluk").
- **Eylem:** tarama kalıpları OWASP GenAI bulgularıyla periyodik karşılaştırılır
  (cadance politikası UNKNOWN → R-01 U-04).

### 4.2 AI risk yönetimi (→ R-06, R-08)
- NIST AI RMF (S-3): ölç → yönet → yönetişim döngüsü. Bizde karşılığı: runtime
  etiketi (R-08) ve kapsam sınırı (R-06) kapatılmadan "yönetiliyor" diyemeziz.
- **Eylem:** `ai-master.md` §G açık soruları NIST "measure" adımına bağlanır.

### 4.3 Model şeffaflığı → bilinmeyen metrikler
- Model Cards (S-5): model hakkında amaç/performans/limit bilgisi standart biçimde
  belgelenir. K015'te karşılığı: runtime etiketi + evaluation (K006) sınırı
  (R-08/R-06) — kart henüz yok (UNKNOWN).
- **Eylem:** hangi modelin/sağlayıcının kullanıldığı **kanıtlanana kadar**
  kartlanmaz; K006 ile kademe-3 kararı beklemede (D-07).

### 4.4 MCP entegrasyonu (→ D-08, R-05)
- MCP spesifikasyonu (S-6) erişilebilir: JSON-RPC 2.0 tabanlı, Host/Client/Server
  rolleri, Resources/Prompts/Tools yetenekleri. Bu, **spesifikasyonun var olduğunu**
  kanıtlar; **CoreMusic'in kullandığı sürüm/yıldız bilgisini vermez** →
  sürüm kanıtı hâlâ yok (⚠️ `ai-master.md` §G).
- **Eylem:** D-08 kararı ADR'ye, sürüm kanıtı eklenerek bağlanır.

## §5 Karşı Kaynaklar (arama kapsamı)

- Kaynaklar K015 ile sınırlı; K006 (yapay-zeka) içeriği bu pakette **çekilmedi**
  (beyazliste — başka K'ya yazma/yayma yasağı).
- Rakip/alternatif çerçeveler (ör. başka AI güvenliği rehberleri) için
  **research-analyst** ile ikinci tur gerekir → §6 D-02.

## §6 Bilinmeyenler (⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Not |
|---|-----------|-----|
| D-01 | S-1…S-5 içeriğinin 2026-10-18 durumu | 2026-10-13 doğrulaması taze değil |
| D-02 | Karşılaştırmalı ikinci kaynak turu | yapılmadı (kapsam dışı bırakıldı) |
| D-03 | MCP sürüm/yıldız kanıtı | spesifikasyon ≠ CoreMusic bağımlılığı |
| D-04 | K015'in kullandığı somut model/sağlayıcı | UNKNOWN → Model Cards (S-5) bağlanamıyor |

## §6.1 Kaynak Güvenilirlik Matrisi

| # | Kaynak | Tür | Öncelik | Kullanım alanı |
|---|--------|-----|---------|----------------|
| S-1 | OWASP GenAI | Konsorsiyum rehberi | 1 — bağlayıcı yakın | tehdit sınıflandırma (R-01) |
| S-2 | OWASP Top 10 | Sektör standardı | 1 — bağlayıcı yakın | web güvenlik çerçevesi (R-02) |
| S-3 | NIST AI RMF | Ulusal standart | 1 — yönetişim | risk döngüsü (R-06/R-08) |
| S-4 | NIST AIRC | Resmi dağıtım | 2 — destekleyici | NIST doküman hattı |
| S-5 | Model Cards (arXiv) | Akademik | 2 — önerilir | şeffaflık biçimi |
| S-6 | MCP Spesifikasyonu | Proje spesifikasyonu | 2 — aday | araç entegrasyonu (D-08) |

> **Öncelik kuralı:** 1 = karar metninde doğrudan dayanak · 2 = destekleyici
> referans. Akademik/akım kaynağı (S-5) tek başına karar dayanağı yapılmaz,
> proje içi kanıtla birlikte kullanılır.

## §6.2 Kaynak Tazelik Kontrolü

1. Doğrulama tarihi **kaynak bazında** yazılır (bu dosyada: 2026-10-13 ·
   2026-10-18) — tek tarih yazmak tazeliği şişirir.
2. 30 günden eski doğrulamalar karar metnine girmeden **yeniden okunur**
   (S-1…S-5 için bu kural uygulanacak → §6 D-01).
3. URL taşındıysa (yönlendirme) eski adres değil, erişilen gerçek adres yazılır.
4. Açılamayan kaynak listede kalır ama **içeriği aktarılmaz** — "erişilemedi"
   notu düşülür.

## §7 İlgili Dosyalar

- `ai-kararlar.md` — D-03/D-07/D-08 kararları
- `ai-riskler.md` — R-01/R-05/R-06/R-08 riskleri
- `ai-dogrulama.md` §4 — 2026-10-13 URL doğrulama havuzu
- `../K009-security/` — OWASP bağlamının geneli

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
