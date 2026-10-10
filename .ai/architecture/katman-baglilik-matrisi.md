---
type: architecture
category: matrix
title: "CoreMusic — Katman Bağımlılık Matrisi (K000-K020)"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT — A0-A5 etiketleri .ai/AGENTS.md §5; kenarlar mimari plandan
---

# CoreMusic — Katman Bağımlılık Matrisi

> `.ai/AGENTS.md` §5 bu dosyayı "kanonik K matrisi" olarak işaret eder; çalışma ağacında
> yoktu ve bu revizyonda üretildi. Etiketler AGENTS §5'ten harfiyen alınmıştır (uydurma yok).

## §1 A0-A5 Alan Matrisi

| Alan | K katmanları | Kapsam (AGENTS §5) | Not |
|---|---|---|---|
| A0 | K0-K5 | Altyapı/Donanım (çekirdek, firmware, sürücü, DSP, seyyar, PCB) | K004 (AI) ve K005 (Veri) bu aralığa sayısal olarak düşer; etiket kapsamı ile örtüşmez → ⚠️ VERIFICATION REQUIRED |
| A1 | K6-K7 | Güvenlik | K006 primitifler · K007 pipeline |
| A2 | K8-K9 | Routing/Backend | K008 domain servisleri · K009 Gateway/BFF |
| A3 | K10-K11 | Presentation/Gösterim | K010 uygulama (etiket geniş yorum) · K011 UX |
| A4 | K12-K15 | Veri/Entegrasyon | K012 izleme · K013 CI/CD · K014 ağ · K015 medya |
| A5 | K16-K20 | Bileşenler (donanım) | K016-K020 donanım bileşen grubu |

## §2 Bağımlılık Kenarları (yalnız alt katmana — Dependency Rule)

| Üst katman (çağıran) | Alt katman (bağımlı olunan) | Dayanak |
|---|---|---|
| K011 UX | K009 API · K014 ağ | HTTP/API sözleşmesi; kod importu YASAK (plan §3) |
| K009 API | K007 middleware · K010 uygulama | plan §4 (Gateway → controller) |
| K007 middleware | K006 güvenlik · K000 | plan §4 (sıra 1-4) + APCu zemini |
| K006 güvenlik | K005 veri · K000 | session/token tabloları (ADR-095 jti) |
| K014 ağ | K007 · K006 · K000 | edge başlıkları (CSP ADR-012) |
| K010 uygulama | K008 servisler | plan §4 (controller → service) |
| K008 servisler | K005 veri · K004 yapay zeka · K015 medya | plan §5.1-§5.2 (port/adaptor) |
| K005 veri | K000 | MySQL/PDO zemini (ADR-002) |
| K003 ses motoru | K002 sürücü | plan §2-1 (sayısal) |
| K002 sürücü | K001 donanım | plan §2-1 (sayısal) |
| K001 donanım | K000 | plan §2-1 (sayısal) |
| K016-K020 (A5) | A0 zemini (K000-K005) | sayısal kural; **A5 içi kenarlar: UNKNOWN** (plan §3 yalnız web topolojisini çizer) |
| K012 · K013 (cross-cutting) | K000 | tüm katmanları kapsar; bağımlılık yine yalnız alt zemine |

## §3 İhlal Kuralı

- Katman ihlali (ör. controller'ın doğrudan PDO çağırması) → **derhal revert + log ERROR** (plan §4 · AGENTS §5 Layer Violation).
- Denetim: bu matris + her katman dosyasının §3 bölümü; ikinci kaynak üretilmez.

## §4 Makro K0–K4 Eşleme (türetilmiş etiket — 2026-10-10)

> **Numara çelişkisi (bildirildi):** talimat bu bölümü `## §3 Makro K0–K4 Eşleme` olarak istemiştir; bu
> dosyada `## §3` zaten **İhlal Kuralı** olarak mevcuttur (yukarıda, değişmedi) → başlık **§4** olarak
> eklendi. SSOT: `[[../katmanli-mimari-k0-k4/index]]` §1.1 (gruplama) · `[[../00-master-index]]` §8 (21 K → makro).

| Makro | K katmanları | Alan (AGENTS §5) | Kapsam |
|---|---|---|---|
| **K0** | K000 · K001 | A0 | Altyapı/Donanım zemini |
| **K1** | K002 · K003 | A0 | Sürücü & Ses Motoru |
| **K2** | K005 · K006 · K007 | A0 · A1 · A1 | Veri · Güvenlik · Middleware |
| **K3** | K004 · K008 · K010 · K015 | A0 · A2 · A3 · A4 | Yapay Zeka · Servis · Uygulama · Medya |
| **K4** | K009 · K011 · K014 | A2 · A3 · A4 | API · UX · Ağ |
| **CROSS** | K012 · K013 | A4 · A4 | İzleme · CI/CD (cross-cutting) |
| **A5** *(makro K0–K4'e girmez)* | K016 · K017 · K018 · K019 · K020 | A5 | Donanım bileşenleri |

```text
[A5]  K016 K017 K018 K019 K020   · PLANNED ×5 · makro K0–K4'e GİRMEZ
 ⋮  . . . kesikli skip-edge: A5 → A0 zemini (matris §2 — aralık bağımlılığı) . . .
 ↓
[K4]   K009 API · K011 UX · K014 Ağ
 ↓
[K3]   K004 AI · K008 Servis · K010 Uygulama · K015 Medya
 ↓
[K2]   K005 Veri · K006 Güvenlik · K007 Middleware
 ↓
[K1]   K002 Sürücü · K003 Ses Motoru
 ⋮  . . . kesikli skip-edge: [CROSS] K012 İzleme · K013 CI/CD → yalnız K000 zeminine . . .
 ↓
[K0]   K000 İşletim Sistemi · K001 Donanım     ← tek A0 zemini
```

> ⚠️ **Uyarı (bağlayıcı):** oklar **yalnız aşağı**dır; kesikli (`⋮`) çizgiler **bağımlılık değil**,
> aralık bağımlılığı/cross-cutting atıfıdır. **A5 → K0–K4 bağımlılık değildir**; **A5 içi kenarlar
> UNKNOWN**dır (§2 son satır — plan §3 yalnız web topolojisini çizer). Bu bölüm **türetilmiş** etikettir;
> bağlayıcı gruplama SSOT'u `[[../katmanli-mimari-k0-k4/index]]` §1.1'dir, bu tablo ikinci bir SSOT değildir.
