---
title: "LLM Wiki — İçerik Kataloğu"
type: index
created: 2026-10-06
updated: 2026-10-06
sources: [test-kaynak, not-repo-envanteri, not-readme-vizyon]
tags: [wiki, index]
---

# Wiki Index

Format: `- [[dosya-adı]] – tek cümlelik açıklama`

## Kaynaklar

- [[test-kaynak]] – AI ajanlarında hafızanın nedeni, üç katmanı ve iki depolama yaklaşımı anlatan örnek kaynak (raw/test-kaynak.md).
- [[özet]] – Tüm kaynakların ana tablosu: kaynak sayısı, son güncelleme ve 6 sütunlu envanter.
- [[not-repo-envanteri]] – Disk kanıtlı CoreMusic repo envanteri: 6 modül, PHP 8.4 omurgası, test araçları.
- [[not-readme-vizyon]] – README v2.0.1 vizyon notu: 21 katman/1095 bileşen, Neva Engine, MySQL 9 BCNF, Authority Bayram Ali.
- [[vault-agents]] – raw/AGENTS.md: 11 ajanın yetki/rol/iletişim registry'si (v22.0.8).
- [[vault-brain]] – raw/brain.md: mimari karar + donanım kısıtları mühendislik hafızası (v26.1.4).
- [[vault-checklist]] – raw/CHECKLIST.md: session başlangıç/orta/kapanış kontrol listesi.
- [[vault-claude]] – raw/CLAUDE.md: AI anayasası, guardrail'ler, boot protokolü (v27.3.9).
- [[vault-context]] – raw/CONTEXT.md: vault klasör context'i ve disk envanteri (2026-10-03).
- [[vault-engine]] – raw/engine.md: orkestrasyon motoru indeksi, task queue, metrics (v21.0.4).
- [[vault-glossary]] – raw/glossary.md: teknik terim SSOT'u (v2.0.0).
- [[vault-index]] – raw/index.md: vault master navigasyon indeksi (v28.4.3).
- [[vault-keys]] – raw/keys.md: kavram→dosya keyword yönlendirme haritası (v28.3.5).
- [[vault-log]] – raw/log.md: oturum/audit kayıt defteri (v1.1.0).
- [[vault-memory]] – raw/MEMORY.md: oturumlar arası persistent state indeksi.
- [[vault-plan]] – raw/PLAN.md: 19 adımlık sıralı yürütme planı (v1.1.0).
- [[vault-projects]] – raw/PROJECTS.md: 17 bölümlük proje tanımı ve envanter (v3.0.2).
- [[vault-role]] – raw/ROLE.md: Senior Software Architect rol tanımı (22 bölüm).
- [[vault-todo]] – raw/TODO.md: öncelikli açık iş listesi (P0 commit + interface taşıma).
- [[vault-ultra-thinking]] – raw/ULTRA-THINKING.md: karar öncesi zorunlu düşünme protokolü.
- [[vault-vision]] – raw/VISION.md: vizyon, felsefe, pazar analizi (21 bölüm, v3.0.2).
- [[vault-workflow]] – raw/WORKFLOW.md: süreç SSOT'u, Zero Code Before Plan + Hard Gate (v22.1.5).
- [[agents-sub-registry]] – .agents/AGENTS.md: 11 ajan profilinin alt-registry'si (534 satır).
- [[agent-master-orchestrator]] – Koordinasyon ajanı profili (514 satır).
- [[agent-audio-hardware-engineer]] – Donanım & analog tasarım ajanı profili (540 satır).
- [[agent-backend-architect]] – PHP backend ajanı profili (527 satır).
- [[agent-data-engineer]] – Veritabanı ajanı profili (517 satır).
- [[agent-devops-engineer]] – Dağıtım & altyapı ajanı profili (567 satır).
- [[agent-dsp-firmware-engineer]] – Gömülü ses & DSP firmware ajanı profili (555 satır).
- [[agent-embedded-engineer]] – Neva Engine / audio DSP ajanı profili (512 satır).
- [[agent-qa-engineer]] – Test ajanı profili: coverage line %80 / branch %75 (556 satır).
- [[agent-security-engineer]] – OWASP/güvenlik ajanı profili (511 satır).
- [[agent-ui-designer]] – ITCSS/BEM UI tasarım ajanı profili (517 satır).
- [[agent-windows-software-engineer]] – Windows platform/C# ajanı profili (556 satır).
- [[vault-decisions]] – .decisions/index.md: ADR kataloğu, 86 MD, 73 kabul (v1.1.4).
- [[vault-personas]] – .personas/index.md: 68 persona / 6 grup katalog özeti (v1.0.1).
- [[png-mockup]] – raw/.png: 19 PNG mockup + 4 bağlam notu, Guardrail #11 frontend otoritesi.

- [[arch-katman]] - raw/architecture koku: AGENTS/CLAUDE/INDEX/README/WORKFLOW (5 dosya).
- [[arch-k0-isletim-sistemi]] - K0 isletim sistemi: 4 bolum / 14 dosya (platform, mekanizma, izolasyon, tasinabilirlik).
- [[prompt-api]] - API & Router prompt ikilileri: plan + kod (4 dosya).
- [[prompt-auth]] - Auth promptlari: uzman rol promptlari (3 dosya).
- [[prompt-ui]] - UI/CSS/Figma promptlari: ITCSS refactor, Figma MCP, PNG init (4 dosya).
- [[prompt-vault-plan]] - Vault & mimari planlama: Agent Refactor Engine, Electron vault (5 dosya).
- [[prompt-genel]] - Genel: onarim, yerel sunucu, notlar b.md/c.md (4 dosya).

- [[ecosystem]] - raw/ecosystem: surucu sozlesmesi, streaming pipeline dersleri, acik kaynak DSP (8 dosya).
- [[servers]] - raw/servers: Linux+Nginx, Windows+Apache, Windows+IIS yapilandirma rehberleri (3 dosya).
- [[servers-deployment]] - raw/servers derin okuma: Nginx/Apache/IIS config + IMPLEMENTED/PLANNED matrisi (467 satir).
- [[ecosystem-genel]] - ekosistem genel harita: 7 servis / 10 panel / 11 subdomain + servis entegrasyon protokolleri.
- [[ecosystem-mimarileri]] - ekosistem mimarileri + muzik streaming sunucu pipeline dersleri.
- [[ecosystem-audio-dsp]] - ASIO/WASAPI surucu rehberi + acik kaynak ses/DSP projeleri.
- [[ecosystem-donanim]] - donanim/devre referanslari (DAC/ADC, amplifikator, guc kaynagi).

## Kavramlar

- [[ai-agent-memory]] – AI ajanının oturumlar arası kalıcı hafıza katmanının genel tanımı.
- [[context-window]] – Modelin aynı anda görebildiği token sınırı ve hafızayı zorunlu kılması.
- [[episodic-memory]] – Ne zaman ne olduğunu tutan, eskidiğinde silinebilen hafıza katmanı.
- [[semantic-memory]] – Proje/olgu bilgisini kaynağıyla yıllarca tutan hafıza katmanı.
- [[procedural-memory]] – İş akışı ve protokol adımlarını tutan "nasıl yapılır" katmanı.
- [[vector-database]] – Parçaları embedding uzayında saklayıp benzerlikle sorgulayan depolama.
- [[file-based-memory]] – Hafızayı okunabilir markdown sayfaları olarak tutan şeffaf yaklaşım.
- [[embedding]] – Metni sayısal vektöre çevirerek benzerlik aramasını mümkün kılan adım.
- [[chunking]] – Kaynağı anlamlı parçalara ayırma; embedding öncesi pipeline adımı.
- [[bcnf]] – BCNF normalizasyon kademesi: her çözücü bağımlılığı aday anahtar olmalı; 18 şemada zorunlu.

## Kişiler

- [[bayram-ali]] – CoreMusic deposunun dokümante otoritesi; rolü Vault Steward (README:262).

## Araçlar

- [[php-8-4]] – Tüm modüllerin zorunlu çalışma zamanı (composer: "php": ">=8.4").
- [[composer]] – 6 modülün bağımlılık yöneticisi; test ve statik analiz scriptlerini tanımlar.
- [[phpunit]] – Backend test aracı (^10.5); her modülde tests/ + phpunit.xml.
- [[phpstan]] – Statik analiz (^1.10, level 5); `composer stan` scripti.
- [[pdo]] – Zorunlu veritabanı uzantısı (ext-pdo), tüm modüllerde ortak.
- [[apcu]] – Zorunlu önbellek uzantısı (ext-apcu), shared cache katmanını besler.
- [[playwright]] – E2E test aracı (^1.62.1), config assets.coremusic.net'de.
- [[vitest]] – Frontend birim testi, assets.coremusic.net/vitest.config.js.
- [[neva-engine]] – C++20 ses motoru: zero-allocation, lock-free, OS ses katmanını baypas eder.
- [[mysql-9]] – Veritabanı: 18 BCNF normalleştirilmiş 18 DB, 156 tablo, + Redis + APCu.
- [[vanilla-javascript]] – Frontend yığını: ES2022 SPA Router, ITCSS 9 katman, BEM, Glassmorphism.
- [[redis]] – Uygulama-uzamsal önbellek/analog anahtar-değer katmanı (K5 Veri Yönetimi); apcu process içi, redis süreçler arası.

## Projeler

- [[coremusic-platform]] – Tek repositoryde 6 modüllü çatı proje (shared + 5 alt alan adı).
- [[shared-infrastructure]] – coremusic/shared-infrastructure v2.0.0, proprietary PHP kütüphanesi.
- [[home-coremusic-net]] – Ana web yüzü: pages/, header/footer.php, responsive test.
- [[api-coremusic-net]] – API modülü: config/, include/, index.php, tests/.
- [[auth-coremusic-net]] – Kimlik doğrulama: handler/, routes/, pages/.
- [[media-coremusic-net]] – Medya/GUI modülü: bin/, src/, docs/.
- [[assets-coremusic-net]] – Statik varlıklar + Playwright/Vitest test altyapısı.
- [[subdomains]] – Subdomain sistemi ELI10: 5 mevcut oda + shared, 5 planlı oda, vhost/DNS truth tablosu.

## Vault Dokümanları

Eski vault'un (`raw/` kopyası) 18 kök dokümanının wiki sayfaları — `type: dokuman`.

- [[vault-agents]] – Agent Registry: 11 ajan, dispatch/routing/handover/escalation kuralları.
- [[vault-brain]] – Engineering Brain: Neva Engine, surround, PHP pipeline, 18 BCNF kısıtları.
- [[vault-checklist]] – Session Checklist: boot ≤36s, durum doğrulama, TODO taraması.
- [[vault-claude]] – AI Constitution: guardrail'ler, otorite sırası, boot protokolü.
- [[vault-context]] – Vault Context: karar tablosu, disk kazanır, append-only log.
- [[vault-engine]] – Orchestration Engine: task queue, health, metrics, troubleshooting.
- [[vault-glossary]] – Glossary: 32 kanonik + teknoloji terimleri, kanıt haritası.
- [[vault-index]] – Master Index: vault navigasyonu, quick reference.
- [[vault-keys]] – Keyword Map: anahtar kelime → hedef dosya router'ı.
- [[vault-log]] – Session Log: Enterprise Architecture v4.0.0 kaydı dahil audit trail.
- [[vault-memory]] – Memory System: boot 20 adım, §6 5 soru, session state.
- [[vault-plan]] – Sıralı Yürütme Planı: 19 adım + oturum planı + durum tablosu.
- [[vault-projects]] – Proje Envanteri: 10 yetenek, 9 senaryo, test/deploy/CI-CD stratejileri.
- [[vault-role]] – Rol Tanımı: mimari vizyon, kodlama sırası, security practices.
- [[vault-todo]] – TODO: 105 değişiklik sınıflandırma + interface→contract kararı.
- [[vault-ultra-thinking]] – Düşünme Protokolü: 4 katman, 10 adım doğrulama, ADR-005.
- [[vault-vision]] – Vizyon & Pazar: felsefe, persona, rekabet, gelir modeli, yol haritası.
- [[vault-workflow]] – Workflow: Zero Code Before Plan, Hard Gate, 500 K domain tablosu.
- [[agents-sub-registry]] – Agent Alt-Registry: profil envanteri, yazım kuralları, şablon eşleşmesi.
- [[agent-master-orchestrator]] – Orchestrator profili: dispatch, koordinasyon, keyword routing.
- [[agent-audio-hardware-engineer]] – Donanım profili: glob kanıtı + BOM taraması doğrulaması.
- [[agent-backend-architect]] – Backend profili: PHP API/middleware/domain yetkileri.
- [[agent-data-engineer]] – Data profili: şema/BCNF/migrasyon yetkileri.
- [[agent-devops-engineer]] – DevOps profili: CI dosyası kanıtı, IMPLEMENTED onay akışı.
- [[agent-dsp-firmware-engineer]] – DSP firmware profili: toolchain kanıtı doğrulaması.
- [[agent-embedded-engineer]] – Embedded profili: Neva Engine DSP sorumluluğu.
- [[agent-qa-engineer]] – QA profili: phpunit.xml SSOT, coverage eşiği %80/%75.
- [[agent-security-engineer]] – Security profili: OWASP denetim yetkileri.
- [[agent-ui-designer]] – UI profili: ITCSS/BEM/token tasarım yetkileri.
- [[agent-windows-software-engineer]] – Windows profili: C# glob kanıtı doğrulaması.
- [[vault-decisions]] – ADR kataloğu sayfası: durum tablosu, frozen/active/rejected aralıkları.
- [[vault-personas]] – Persona kataloğu sayfası: 68/6 sayımı, ADR-023 bağlamı, test kullanımı.
- [[png-mockup]] – PNG mockup seti: klasör envanteri (12+1+6), referans sıralaması, auth SSOT'u.

- [[arch-katman]] - Mimari katman koku sayfasi: 5 yonlendirme dosyasi tablosu.
- [[arch-k0-isletim-sistemi]] - K0 IS sayfasi: 5 platform + 4 mekanizma + 2 izolasyon + 1 tasinabilirlik.
- [[prompt-api]] - API/router prompt sayfasi: plan-kod ikilileri, Red Team onekli.
- [[prompt-auth]] - Auth prompt sayfasi: 3 ikiz dosya, uzman rol acilisi.
- [[prompt-ui]] - UI/CSS/Figma prompt sayfasi: 52.5KB CSS refactor + PNG init.
- [[prompt-vault-plan]] - Vault planlama prompt sayfasi: 104.2KB en buyuk prompt dahil.
- [[prompt-genel]] - Genel prompt sayfasi: onarim + yerel sunucu + notlar.

- [[ecosystem]] - Ekosistem referanslari sayfasi: 8 dosya tablosu, ASIO/WASAPI + streaming + DSP.
- [[servers]] - Sunucu yapilandirmalari sayfasi: uc platform, uc dosya.
- [[servers-deployment]] - Sunucu derin okuma sayfasi: config bloklari + IMPLEMENTED/PLANNED matrisi.
- [[ecosystem-genel]] - Genel harita sayfasi: 7 servis / 10 panel / 11 subdomain + entegrasyon protokolleri.
- [[ecosystem-mimarileri]] - Mimari sayfasi: ekosistem mimarileri + streaming pipeline dersleri.
- [[ecosystem-audio-dsp]] - Ses sayfasi: ASIO/WASAPI surucu sozlesmesi + acik kaynak DSP.
- [[ecosystem-donanim]] - Donanim sayfasi: DAC/ADC, amplifikator, guc kaynagi referanslari.

## Kararlar (ADR)

- `.decisions/index.md` → [[vault-decisions]] – Mimari karar kataloğu: 73 kabul / 12 red / 37 dondurulmuş ADR (`accepted/`, `rejected/`, `draft/`). Kayıt noktası yalnız burasıdır; bireysel ADR dosyaları tek tek listelenmez.

## Personalar

- `.personas/index.md` → [[vault-personas]] – Persona kataloğu: 68 persona · 6 grup (`erkek-cocuk`, `kiz-cocuk`, `genc-erkek`, `genc-kiz`, `yetiskin-erkek`, `yetiskin-kadin`) + methodology, mood-taxonomy, research-bank, test-senaryolari.
