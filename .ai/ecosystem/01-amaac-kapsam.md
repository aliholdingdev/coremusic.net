---
title: "Amaç & Kapsam"
type: guide
category: ecosystem
version: 1.0.2
status: active
authority: reference
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24)
updated: 2026-10-12
---# Amaç & Kapsam## §1 Amaç

Bu doküman, **.ai/ecosystem/** klasörünün ana indeksidir: CoreMusic'in dış ekosistemden (açık kaynak müzik sunucuları, ses/DSP kütüphaneleri, donanım referans devreleri, büyük müzik platformu mimarileri, ASIO/WASAPI sürücü ekosistemi) çıkardığı **dersleri**, her birini **hangi katmana** somut olarak bağladığını ve hangi kaynağın **lisans olarak CoreMusic'e girebileceğini/giremeyeceğini** tek yerde toplar.

| Alan | Değer |
|------|-------|
| Hedef kitle | Embedded Engineer, Audio HW Engineer, Backend Architect, Windows SW Engineer, DevOps Engineer, Vault Steward |
| Kapsadığı ekosistem | 6 ana konu alanı — streaming sunucuları, DSP, donanım, platform mimarileri, sürücü API'leri |
| Ana çıktı | Ders → K katmanı çapraz matrisi (§3.4, §3.5) + lisans tabloları (her dosyanın §4.4'ü) |
| Neden şimdi (2026-09-24) | exa web doğrulaması tazelendi; yeni referanslar (YUP, OpenStudio, wolfsound-dsp-utils, ddabidov) github-referanslari.md v2.0 ile vault'a girdi |
| Vault bağlantısı | Anayasa [[../CLAUDE.md]] §5 |
| Kural | Bu dosya **reference** taşır; SSOT iddiası yoktur — çelişkide CLAUDE.md kazanır (§2.1 SSOT sırası) |

**Tek cümlelik tanım:** Ekosistem dosyaları "neyi kopyalamayız"ın da "neyi fikir olarak alırız"ın da tek kayıt defteridir.

---

## §2 Kapsam

### §2.1 Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| .ai/ecosystem/ altındaki tüm .md dosyalarının envanteri ve amaçları | Katman dokümanlarının kendisi |
| Dış kaynak derslerinin K katmanına eşlemesi (çapraz referans) | GitHub referans havuzunun kendisi |
| Lisans uyumluluğu özet kararları (MIT/AGPL/GPL/GPL-comercial) | ADR üretimi (→ [[../brain]] / .ai/.decisions; bu dosya karar üretmez) |
| exa doğrulamalı ekosistem verisi (tarih + kaynak etiketi) | Kod implementasyonu (Guardrail #1: Zero Code Before Plan) |
| NE kopyalanır / NE kopyalanmaz kararlarının özeti | Servis port/komut detayları (→ [[README]], [[service-integration]]) |

*Alt konular:* §3.1 harita → §3.2 envanter → §3.4/§3.5 K çapraz referans → §4 lisans kuralları → §5 okuma akışı → §6 doğrulama.
Kapsam dışı için: karar → ADR, servis özeti → [[README]].

### §2.2 Hedef Kitle ve Okuma Sırası

| Kitle | Önce Okuması Gereken | Neden |
|-------|---------------------|-------|
| Embedded Engineer (K2/K3) | ses-dsp-acik-kaynak.md + asio-wasapi-rehber.md | JUCE lisans kararı ve ASIO dağıtımı K3/K2 planını doğrudan bağlar |
| Audio HW Engineer (K1/K16-K20) | donanım-devre-referanslari.md | ddabidov/Headphone-DAC-AMP = XU316 referans tasarımı |
| Backend Architect (K8/K9) | muzik-streaming-sunuculari.md + ekosistem-mimarileri.md | Koel/Ampache desenleri ve Spotify pipeline ayrıştırması |
| Windows SW Engineer (K0/K2) | asio-wasapi-rehber.md | 32-bit ASIO driver gerçeği → platform stratejisi |
| DevOps / MO | index.md (bu dosya) → ihtiyaç'a göre diğer 5 dosya | routing, AGENTS.md §24.3: DevOps → .ai/ecosystem/*.md |

### §2.3 Bağımlılıklar (Bu Dosyadan Önce Okunanlar)

| Sıra | Dosya | Amaç |
|------|-------|------|
| 1 | [[../CLAUDE.md]] | 16 Hard Guardrail, §5 katman sınırları, §21 Forbidden Patterns |
| 2 | [[../AGENTS.md]] §24.3 | DevOps/Ekosistem okuma listesi |
| 3 | [[../.templates/index]] §7.1 | Şablon seçimi (Guardrail #16) |

---

