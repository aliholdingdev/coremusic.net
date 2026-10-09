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
