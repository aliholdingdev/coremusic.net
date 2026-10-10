---
type: architecture
category: index
title: "CoreMusic — Katman Master İndeksi (K000-K020)"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# CoreMusic — Katman Master İndeksi (K000-K020)

> Dayanak: [[coremusic-mimari-plani]] (v1.0.0, 2026-10-09) · ADR listesi: `.ai/.decisions/index.md` ·
> A0-A5 alan etiketleri: `.ai/AGENTS.md` §5 · Durum etiketleri disk kanıtıyla (2026-10-09 glob/grep).
> Bağımlılık kenarları: [[katman-baglilik-matrisi]] · Kural: her katman yalnız alt katmana bağımlıdır.

## §1 Katman Tablosu

| K | Ad | Alan | Sorumlu agent | Bağlı ADR'ler | Durum |
|---|----|----|----|----|----|
| K000 | İşletim Sistemi | A0 | data-engineer (AGENTS §24.3) | ADR-015 · 082 · 085 | IMPLEMENTED |
| K001 | Donanım | A0 | embedded-engineer (AGENTS §24.3) | ADR-061 · 038 | PLANNED |
| K002 | Sürücü | A0 | windows-software-engineer | ADR-032 · 038 · 017(ortak) | PLANNED |
| K003 | Ses Motoru | A0 | embedded-engineer | ADR-017 · 019 · 025 · 062 · 037 | PLANNED |
| K004 | Yapay Zeka | A0 | MO (registry §4'te AI agent yok — UNKNOWN) | ADR-030 · 035 · 036 · 049 · 075 | PLANNED |
| K005 | Veri Yönetimi | A0 | data-engineer | ADR-002 · 003 · 014 · 033 · 040 · 041 · 050 · 081 · 072-079 | IMPLEMENTED |
| K006 | Güvenlik | A1 | security-engineer | ADR-008 · 010 · 011 · 012 · 013 · 020 · 022 · 034 · 043 · 047 · 052 · 056 · 058 · 059 · 094 · 095 | IMPLEMENTED |
| K007 | Middleware | A1 | backend-architect (§6; ikincil security-engineer) | ADR-008 · 010 · 012 · 013 · 020 · 056 · 094 | IMPLEMENTED |
| K008 | Servisler | A2 | backend-architect | ADR-039 · 085 · 086 · 072-079 | IMPLEMENTED (kısmi) |
| K009 | API | A2 | backend-architect | ADR-004 · 020 · 084 · 009 · 016 · 021 | IMPLEMENTED (OpenAPI PLANNED) |
| K010 | Uygulama | A3 | backend-architect | ADR-056 · 085 · 086 | IMPLEMENTED (port/adaptor PLANNED) |
| K011 | UX | A3 | ui-designer | ADR-001 · 004 · 018 · 044 · 045 · 046 · 048 · 093 | IMPLEMENTED |
| K012 | İzleme | A4 | devops-engineer (§6 monitoring) | ADR-006 (log'a özel ADR YOK) | IMPLEMENTED |
| K013 | CI/CD | A4 | devops-engineer | ADR-082 (CI'ya özel ADR YOK) | IMPLEMENTED |
| K014 | Ağ | A4 | devops-engineer (network agent YOK — UNKNOWN) | ADR-004 · 009 · 012 · 016 · 021 · 043 · 047 | IMPLEMENTED |
| K015 | Medya | A4 | backend-architect | ADR-026 · 027 · 028 · 092 | IMPLEMENTED |
| K016 | Amplifikatör | A5 | audio-hardware-engineer | ADR-089 · 090 | PLANNED |
| K017 | Güç Kaynağı | A5 | audio-hardware-engineer | ADR-089 | PLANNED |
| K018 | Termal | A5 | audio-hardware-engineer | (termal başlıklı ADR YOK — UNKNOWN) | PLANNED |
| K019 | PCB | A5 | audio-hardware-engineer | ADR-063 | PLANNED |
| K020 | Üretim | A5 | audio-hardware-engineer (üretim agent'ı YOK — UNKNOWN) | ADR-064 | PLANNED |

**Dallanma kaydı (2026-10-10):** K000 ve K001 alt klasörleri ilk kez dolduruldu →
`[[K000-isletim-sistemi/index]]` (§4 dizin: 7 alt klasör + `isletim-sistemi-turu/` 8 md) ·
`[[K001-donanim/index]]` (§5 dizin: 4 alt klasör + 4 iç dal). Katman **durumları değişmedi**
(K000 IMPLEMENTED · K001 PLANNED) — alt dosyalar kanıt/UNKNOWN taşır, status yazmaz.

## §2 Topoloji (plan §3 özeti)

```
Tarayıcı (Vanilla JS SPA · ITCSS --cm-* token)                     [K011 UX]
   ├─ home.coremusic.net   → public/uygulama yüzeyi (sunum)
   ├─ auth.coremusic.net   → kimlik/oturum/JWT RS256/MFA (ADR-043/052/059/095)
   ├─ api.coremusic.net    → Gateway/BFF (ADR-084) · Origin/CSRF (ADR-094)
   ├─ media.coremusic.net  → medya arşivi/teslim (ADR-092 ULID)
   └─ assets.coremusic.net → statik varlık (ITCSS katmanları, token'lar)   [K014 AĞ]
          │   kural: subdomain'ler birbirine kod IMPORT etmez;
          │   yalnız shared/ + API sözleşmesi (Parnas'72 — plan §3)
       shared/ → Middleware hattı (K007) · PageRouter · Database (PDO)     [K006-K010]
          │
       MySQL 9 — BCNF DB'ler (K005); cross-DB = outbox + WAL (ADR-081)
```

## §3 Durum Özeti

- **IMPLEMENTED (12):** K000 · K005 · K006 · K007 · K008(ısmi) · K009 · K010 · K011 · K012 · K013 · K014 · K015
  (K008/K009/K010 kısmi — PLANNED kalan işler katman dosyalarında).
- **PLANNED (9):** K001 · K002 · K003 · K004 · K016 · K017 · K018 · K019 · K020.

## §4 Risk / Not (dikkat çeken bulgular)

1. ⚠️ VERIFICATION REQUIRED — "18 BCNF DB" sayı iddiası: diskte 20 `.sql` dosyası; ADR-003 "9 BCNF" derken ADR-040 "18 BCNF" der (K005 §6).
2. ⚠️ VERIFICATION REQUIRED — PSR-15: middleware'ler `IMiddleware` implement ediyor; `Psr\Http\Server\MiddlewareInterface` grep = 0 (K007 §6).
3. ⚠️ VERIFICATION REQUIRED — CI geçmişi: GitHub Actions çalışma geçmişi diskte yok (K013 §6).
4. ⚠️ VERIFICATION REQUIRED — A0 alan etiketi "Altyapı/Donanım"; K004 (AI) ve K005 (Veri) bu aralıkta — matris etiket uyuşmazlığı ([[katman-baglilik-matrisi]] §1).
5. ADR-083/087/088/091 dosyasız (brain-only) — index §6 notu; bu katmanlarda uygulama kanıtı taşınmaz.

## §8 Makro Eşleme (K0–K4)

| K | Ad | Makro | Makro kimliği |
|---|----|----|----|
| K000 | İşletim Sistemi | K0 | A0 |
| K001 | Donanım | K0 | A0 |
| K002 | Sürücü | K1 | A0 |
| K003 | Ses Motoru | K1 | A0 |
| K004 | Yapay Zeka | K3 | A0 |
| K005 | Veri Yönetimi | K2 | A0 |
| K006 | Güvenlik | K2 | A1 |
| K007 | Middleware | K2 | A1 |
| K008 | Servisler | K3 | A2 |
| K009 | API | K4 | A2 |
| K010 | Uygulama | K3 | A3 |
| K011 | UX | K4 | A3 |
| K012 | İzleme | CROSS | A4 |
| K013 | CI/CD | CROSS | A4 |
| K014 | Ağ | K4 | A4 |
| K015 | Medya | K3 | A4 |
| K016 | Amplifikatör | **A5 — girmez** | A5 |
| K017 | Güç Kaynağı | **A5 — girmez** | A5 |
| K018 | Termal | **A5 — girmez** | A5 |
| K019 | PCB | **A5 — girmez** | A5 |
| K020 | Üretim | **A5 — girmez** | A5 |

**Makro karşılaştırma özeti (5 satır):**

| Makro | K sayısı | Durum dağılımı (§1/§3) | Alt hedefi |
|---|---|---|---|
| K0 | 2 | 1 IMPLEMENTED (K000) · 1 PLANNED (K001) | **tek zemin** |
| K1 | 2 | 2 PLANNED (K002 · K003) | K0 |
| K2 | 3 | 3 IMPLEMENTED (K005 · K006 · K007) | K0 |
| K3 | 4 | 3 IMPLEMENTED (K008 kısmi · K010 · K015) · 1 PLANNED (K004) | K2 |
| K4 | 3 | 3 IMPLEMENTED (K009 OpenAPI PLANNED · K011 · K014) | K3 |

> **CROSS (K012 · K013)** ve **A5 (K016–K020)** bu 5 makro dışında taşınır — CROSS cross-cutting'tır ve
> yalnız K000'e bağımlıdır; A5 **makro K0–K4'e girmez** (A5 = K0'un hedef donanımı). Ayrıntı ve istisna
> defteri: `[[../katmanli-mimari-k0-k4/index]]` §1.1 · §6.1 · §6.2.
> **SSOT uyarısı:** bu tablo **türetilmiştir**; bağlayıcı gruplama SSOT'u `[[../katmanli-mimari-k0-k4/index]]` §1.1,
> kenarlar `[[../katman-baglilik-matrisi]]` §2/§4'tür. **§5–§7 arası bu bölümde boş bırakılmıştır** (mevcut en yüksek bölüm §4 idi — bildirildi).
