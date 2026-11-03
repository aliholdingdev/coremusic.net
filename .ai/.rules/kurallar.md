---
title: "CoreMusic — Temel Mühendislik Direktifleri (Core Directives)"
type: rules
category: core-rules
version: 2.2.0
status: active
authority: "SSOT: .ai/.rules/kurallar.md"
updated: 2026-11-01
---

# CORE DIRECTIVES — Temel Mühendislik & Çalışma Direktifleri

> **Amaç:** Her oturumda bağlamı sıfırdan kurmadan, token tüketimini minimumda tutarak en yüksek mühendislik disipliniyle çalışmayı sağlayan tek sayfalık kompakt kural seti.

---

### KURAL-01: Kurucu Kimliği & Sistem Otoritesi (Engineering Authority)
- **Muhatap:** Bayram Ali — CoreMusic Kurucusu, Baş Mühendis & Audio/Software Mimarı.
- **Kapsam:** Ticari dijital medya ekosistemi, Hi-Fi ses DSP (C++20 Neva Engine), Class AB donanım elektroniği ve kurumsal hibrit SPA (PHP 8.4 + Vanilla JS) platformu.

### KURAL-02: İletişim & Raporlama Standardı (Communication Protocol)
- **Dil & Ton:** Türkçe; doğrudan, net ve senior seviyede. Dolgu, övgü ve retorik ifadeler yasaktır.
- **Hibrit Raporlama:** Sonuç, riskler ve dosya/satır kanıtları doğrudan verilir; atılan adımlar ve kod değişikliklerinin gerekçeleri açık ve öz bir dille izah edilir.

### KURAL-03: UI/UX & Tema Standardı (Light Mode Mandatory)
- **Yalnızca Light Mode:** CoreMusic ekosistemindeki tüm tasarımlar istisnasız Light Mode'dur; Dark Mode henüz tasarlanmamıştır/yoktur. Dark mode varsayımı, dark class'ları veya `@media (prefers-color-scheme: dark)` yazmak KESİNLİKLE YASAKTIR.
- **Tasarım Sistemi:** ITCSS 11 katman mimarisi (`01_Abstracts` → `11_OAuth`), CSS tokenleri yalnızca `01_Abstracts/a-*.css` içinde `--cm-*` ile tanımlanır. UI çalışmalarında `.ai/ui-design/` altındaki mockup referansları esastır.

### KURAL-04: Katmanlı Mimari & Teknik Guardrail'lar (Clean Architecture)
- **Backend (PHP 8.4):** `declare(strict_types=1);` zorunludur. Parametreli raw PDO kullanılır (ORM yasak, `SELECT *` yasak, N+1 sorgu yasak).
- **Frontend (Vanilla JS):** ES2022+ standartları. SPA framework'leri veya TailwindCSS kullanımı yasaktır.
- **Donanım & DSP:** Donanım amfi topolojisi kesinlikle discrete Class AB'dir (Class D yasaktır).
- **Sürüm Kontrolü:** `git commit` subagent tarafından atılamaz; commit yetkisi yalnızca kullanıcıya ve ana orkestratöre aittir.
- **Veritabanı Şeması:** SSOT `.ai/.sql/mysql/*.sql` dump'larıdır. Şema uyuşmazlığı kod esnetilerek değil, migration ile çözülür (ADR-112).
- **API Sürümleme:** V1 default + V2 paralel tek route tablosunda `{version}` segmenti ile yönetilir; ayrık rota tablosu açılamaz (ADR-113).
- **Repository Standardı (yeni kod):** `I*` interface + concrete `final class` + `IDatabaseRegistry`/PDO constructor injection + raw prepared statement + explicit kolon; mevcut repo'lar dokunulmaz, taban şablon `api.coremusic.net/include/Repository/` (ADR-114).
- **Media Yönetimi:** Admin yönetim arayüzü `admin.coremusic.net` panelinde yürütülür; media'nın kendi web paneli İPTAL (spec superseded, ADR-115) — admin ekranları media HTTP API'yi sözleşmeyle tüketir.

### KURAL-05: Tek Sayfalık Oturum Hafızası & Durum Devri (Session Handoff)
- **Oturum Başlangıcı:** AI her işe başlarken ilgili alanın `durum.md` dosyasını okur (kök için `.ai/durum.md`, subdomain için `[subdomain]/durum.md`).
- **Oturum Kapanışı:** İş tamamlandığında `durum.md`, `.ai/log.md` ve `.ai/MEMORY.md` dosyaları güncellenerek durum devredilir.

### KURAL-06: Sıfır Halüsinasyon & Kanıt Zorunluluğu (Zero-Hallucination Policy)
- **Doğrulanamayan İddia Yasaktır:** Dosya yolu, satır numarası ve disk kanıtı olmadan tespit yazılamaz.
- **Standart İfadeler:** Bilinmeyen değer = `UNKNOWN`, doğrulanamayan durum = `⚠️ VERIFICATION REQUIRED`. Şüphe halinde tahmin edilmez, kullanıcıya sorulur.

### KURAL-07: Değişiklik, Bağımlılık & Onay Kapıları (Approval Gates)
- **Onay Zorunluluğu:** Ücretli/harici servis, yeni paket/bağımlılık, mimari şema değişikliği veya canlı DDL işlemleri kullanıcı onayı olmadan başlatılamaz.

### KURAL-08: Oturum Yaşam Döngüsü & Token Koruma Protokolü (Session Triggers)
Aşağıdaki 4 durumda sohbet sürdürülmez; `durum.md` güncellenir ve **YENİ SOHBET** açılır:
1. **Konu Değişimi:** Önceki görev bittiğinde yeni modüle/sayfaya geçilirken.
2. **Tekrarlama / Ağırlaşma:** AI eski kararları unutmaya veya bağlamı karıştırmaya başladığında.
3. **Büyük Dosya / Analiz Kapanışı:** Geniş kapsamlı dosya/log incelemesi tamamlandığında (dosyayı her mesajda taşımamak için).
4. **Teslim & Revize Sonrası:** Her kod teslimatı veya revizyon onaylandığında.

### KURAL-09: Dağıtım Öncesi Hata Listesi Denetimi (Pre-Flight Error Verification)
- Her kod tesliminden önce [`.ai/.rules/hata-listesi.md`](hata-listesi.md) dosyası taranarak bilinen proje tuzaklarına (container-query, oran karşılaştırmaları, DOM init, RBAC rolleri vb.) karşı kontrol sağlanır.

### KURAL-10: Dinamik Kural Yönetimi & Yalınlık İlkesi (Rule Maintenance)
- Kural dosyası kısa ve operasyonel kalmalıdır.
- Yeni bir kalıcı kural/karar öğrenildiğinde buraya 1 satır eklenir; geçerliliğini yitiren kurallar gecikmeden silinir.

### KURAL-11: Karpathy Davranışsal İlkeleri (Think · Simplicity · Surgical · Goal-Driven)
- **Think Before Coding:** Varsayım yapma. Şüpheyi gizleme. Ödünleşimleri sun. Basit bir yol varsa itiraz et (push back).
- **Simplicity First:** Sorunu çözen minimum kod. Spekülatif soyutlama üretme. 200 satırlık iş 50 satırda çözülebiliyorsa yeniden yaz.
- **Surgical Changes:** Yalnızca dokunman gereken yere dokun. İlgisiz koda, formata ve yoruma dokunma. Kendi yetim kodlarını temizle.
- **Goal-Driven Execution:** Başarı kriterini testlerle tanımla. Doğrulanana kadar döngüde kal (`[Step] → verify: [check]`).
- **Referanslar:** [`.claude/skills/karpathy-guidelines/`](file:///c:/www/coremusic.net/.claude/skills/karpathy-guidelines/) (Skill #14), [`.cursor/rules/karpathy-guidelines.mdc`](file:///c:/www/coremusic.net/.cursor/rules/karpathy-guidelines.mdc), [`CURSOR.md`](file:///c:/www/coremusic.net/CURSOR.md).
