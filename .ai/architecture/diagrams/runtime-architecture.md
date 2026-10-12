---
title: "CoreMusic — Runtime Architecture (RUNTIME kolu + Okuma Sırası)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §1.2 (RUNTIME/PROCESS kolu) · §5.3 (hedef okuma sırası)"
---

# Runtime Architecture — RUNTIME/PROCESS Kolu ve Okuma Sırası

> v2 §1.2'deki üç koldan (DOMAIN · RUNTIME · DATA) **RUNTIME kolu** ile
> **§5.3 hedef okuma sırasının (karmaşıklıktan sadeliğe) akış diyagramı**.

## §A RUNTIME / PROCESS Kolu — v2 §1.2 (alt-küme, düğüm eklenmedi)

```text
                         ┌─────────────────────┐
                         │ K000–K5000          │
                         │ ARCHITECTURE        │
                         │ REGISTRY            │
                         └──────────┬──────────┘
                                   │
              ┌────────────────────┼────────────────────┐
              ▼                    ▼                    ▼
           DOMAIN               RUNTIME                DATA
                                 │
                                 ▼
                              PROCESS
```

- v2 §1.2'de `RUNTIME → PROCESS` kolunun devamında alt kademe **tanımlı değildir**
  (DOMAIN kolu SUBDOMAIN'e, DATA kolu BOUNDARY'ye iner; RUNTIME kolu PROCESS'te biter).
- `⚠️ VERIFICATION REQUIRED`: PROCESS altı node'lar Band-1 üretimine kadar açık değildir;
  uydurulmaz.
- Tam görünüm (üç kol + sekiz kademe): `enterprise-architecture.md` §B.

## §B Hedef Okuma Sırası — v2 §5.3 (birebir)

```text
  Complexity      ↓  Structure
  Dependencies    ↓  Boundaries
  Technology      ↓  Abstraction
  Hardware        ↓  Drivers
  Drivers         ↓  Audio Engine
  Audio Engine    ↓  Application
  Application     ↓  API / Services
  Services        ↓  Clients
  Clients         ↓  Users
```

**Kapılar (her `↓` = bir okuma kapısı — kapı numaralandırması uyarlamadır, düğüm/içerik değişmedi):**

```text
   K1  Complexity      ↓  Structure
   K2  Dependencies    ↓  Boundaries
   K3  Technology      ↓  Abstraction
   K4  Hardware        ↓  Drivers
   K5  Drivers         ↓  Audio Engine
   K6  Audio Engine    ↓  Application
   K7  Application     ↓  API / Services
   K8  Services        ↓  Clients
   K9  Clients         ↓  Users
```

**Akış yönü:** solda (karmaşıklık/hardware) başlar → sağda (users) biter; kapı sırası
**zorunlu okuma sırasıdır**, atlanan kapı = kanıtsız adım (v2 §5.3 başlığı: "karmaşıklıktan sadeliğe").

## Lejant

| Sembol | Anlam |
|---|---|
| `▼` | akış / iniş yönü (yukarıdan aşağıya) |
| `↓` | okuma sırası geçişi (karmaşıklık → sadelik) |
| `├────┼────┤` | üç ana kol: DOMAIN · RUNTIME · DATA |
| `K1…K9` | okuma kapısı numarası (**uyarlama** — v2 §5.3 satırlarından türetildi) |
| `RUNTIME → PROCESS` | v2 §1.2'de bu kolun tamamı; altı yok |

## Kaynak Atfı

- **v2 §1.2** "Hiyerarşi Şeması (kuralın kendisi)" → §A (RUNTIME/PROCESS kolunun alt-kümesi).
- **v2 §5.3** "Hedef Okuma Sırası (karmaşıklıktan sadeliğe)" → §B (birebir + kapı uyarlaması).
- **Kaynak: v2 §5.3** — okuma sırası akışının tek dayanağı budur; kapı numaraları (K1–K9)
  v2'de yoktur, **açıklama katmanıdır** (node/kenar olarak grafiğe alınmadı).
- Dönüşüm: `.ai/architecture/index.md` §3 (v2 §1.1 birebir).

## Durum Notu

- `status: draft` — kaynak sadakati taşır, tamamlanmışlık iddiası taşımaz.
- **Kenarlar doldukça genişler:** RUNTIME/PROCESS altı node'lar Band-1 üretiminde
  `index.md` alanları doldukça eklenecek (uydurma yasak).
