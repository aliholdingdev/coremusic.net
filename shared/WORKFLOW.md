---
title: "CoreMusic — shared Klasör İş Akışı"
type: docs
category: shared
docType: workflow
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/ + kök AGENTS.md"
---

# shared — WORKFLOW.md

**docType:** workflow · **Klasör:** `shared/` · **Sorumlu:** MO (workflow)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[CONTEXT.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu doküman `shared/` klasöründe bir görevin adım adım nasıl yürütüldüğünü tanımlar: her adımın çıktısı, neden olduğu ve atlanırsa ne olacağı. `shared/` 3 alt alan adının ortak altyapısı olduğundan hata tüm sisteme yayılır; kapılar (gates) burada zorunludur.

| Karar | Kaynak (disk) |
|-------|---------------|
| Test komutu tek yerden | `shared/composer.json` → `scripts: {test: phpunit, stan: "phpstan analyse src --level=5"}` |
| Kod öncesi repo incelemesi | Kök `AGENTS.md` §4 (Faz 1 keşif) |
| Commit subagent'a ait değil | Kök `AGENTS.md` §7/8 — "commit (subagent ATMAYACAK)" |
| Hata → kural dosyası → yeniden yaz | Kök `AGENTS.md` §7 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `shared/` üzerinde kod/config/test değişiklik akışı | Vault `.ai/` içi doküman akışı → `[[../.ai/WORKFLOW.md]]` |
| Kapılar: keşif → değişiklik → test → rapor | Deploy/CIipeline → DevOps (`workflow` kapsamı) |
| Commit kuralı (subagent atmaz) | Alt alan adı kendi iç akışları → onların `WORKFLOW.md`'si |

- **Kullananlar:** Backend/Security/Data/QA (yürütücü), MO (kapı + rapor).
- **Ön koşul:** Hedef dosya diskte mevcut; `composer.json` `scripts` bölümü okunmuş.

---

## 3. Mimari

### 3.1 Adım Tablosu (adım → neden → atlarsan ne olur)

| # | Adım | Çıktı | Neden | Atlarsan ne olur | eli10 | eli15 |
|---|------|-------|-------|------------------|-------|-------|
| 1 | Görev tanımını netleştir (prompt-maker / soru kapısı) | Onaylı görev özeti | Yanlış iş yapmayı önler | İstenmeyen değişiklik, revert | Önce ne istendiğini anlamak; yanlış anlarsan tüm iş boşa gider. | Görev kapısı (ASKING QUESTIONS) onaysızsonraki aşamaya geçmez (kök `AGENTS.md` §1). Tanım okunmadan başlarsan beklenti ile kod ayrışır. Kapıdan geçince elinde ölçülebilir bir çıktı (özeti) olur. Atlarsan yanlış işin üretimi ve zaman kaybı. |
| 2 | Klasör kurallarını oku: [[CLAUDE.md]] + [[AGENTS.md]] | Kural listesi | Klasör-specific yasaklar orada | Yasak ihlali (ör. bypass kapsamı) | Bu klasörün özel kurallarını öğrenmek. | Kurallar klasör dosyalarında tutulur çünkü genel kural her detayı taşıyamaz. Okunmadan girilirse ADR'li yasaklar (ADR-008 vb.) fark edilmez. Kural bilgisiyle başlayınca revizyon ilk seferde doğru olur. Atlarsan güvenlik kapsamı genişletme hatası. |
| 3 | Hedef dosyayı 1. kez oku + disk kanıtı topla | Okunmuş dosya, envanter | Anti-overthink: ilk okumadan KARAR VER | Aynı dosya 2. kez okunur, token israfı, karar gecikir | Dosyayı bir kez okuyup karar vermek. | Anti-waste kuralı (kök `AGENTS.md` §5) aynı dosyayı görevde 2. kez okumayı yasaklar. İlk okuma yeterli kanıtı verir; ikinci okuma "emin olmak" bahanesidir. Kararı ilk okumadan verirsen akış hızlanır. Atlarsan tekrarlı okuma ve gereksiz analiz. |
| 4 | Değişikliği yap (yalnız hedef dosya/klasör) | Kod/config diff'i | Kapsam disiplini | Fazla dosyaya dokunma, çakışma | İstenen yeri değiştirmek, başka yere dokunmamak. | Değişiklik hedefe kilitlenir çünkü eşzamanlı oturumlar var (context lock). Kapsam genişlerse başka oturumun çalışması bozulur. Kilitli diff, incelemeyi ve revert'i kolaylaştırır. Atlarsan çakışma ve temizlik işi. |
| 5 | Test ekle/koru: `tests/` altında | Yeni/güncel test dosyası | Regresyon kapısı | Sessiz kırılma, production'da arıza | Kodun doğru kaldığını kanıtlayan soru kâğıdı. | Test ayrı dosyadır çünkü kod değişince test değişmez, yalnız doğrular. Eklenmezse gelecekteki değişiklik kırığı yakalamaz. Shared herkese hizmet verdiği için kırık tüm sisteme yayılır. Kapıyı atlarsan hata ancak kullanıcıda görülür. |
| 6 | Doğrulama: `composer test` + `composer stan` | Yeşil test + temiz analiz | Statik analiz level 5 | Tip/analiz hatası production'a taşınır | Sınavı ve kontrol listesini çalıştırıp geçmek. | `composer.json` script'leri tek komutluk kapı olarak tanımlı; elle koşulsuz sonucu bilmek mümkün değil. level=5 analizi görünür hataları erken yakalar. Kapıdan geçmeyen iş tamamlanmış sayılmaz. Atlarsan hatalı değişiklik git geçmişine girer. |
| 7 | UI etkisi varsa browser testi | Ekran doğrulaması | Yerleşim kırığını yakala | Bozuk layout, kullanıcıya ulaşır | Değişiklikten sonra sayfayı tarayıcıda kontrol etmek. | Kök `AGENTS.md` §7 #7 bu adımı zorunlu kılar. DOM/layout iddiası kod ile kanıtlanamaz. Görsel kapı geçince kullanıcı deneyimi güvenceye alınır. Atlarsan gizli yerleşim hatası. |
| 8 | Rapor yaz; **commit ATMA** | Rapor | Yetki sınırı | Yetkisiz tarih, revert riski | İş bitince haber vermek; commit'i başkasının atması. | Subagent commit atmaz (kök `AGENTS.md` §7/8); commit yetkisi orkestratörde. Tarih (git log) tek elden yazılmazsa iz sürme bozulur. Rapor, kapıyı orkestratöre teslim eden çıktıdır. Atlarsan iş görünmez kalır ya da düzensiz commit oluşur. |

### 3.2 Kapılar (Gate)

```text
[Adım 1-3 KEŞİF] ──onay──▶ [Adım 4-5 UYGULAMA] ──test──▶ [Adım 6-7 DOĞRULAMA] ──▶ [Adım 8 RAPOR → ORKESTRATÖR]
        │                          │                            │
        └── red: görev geri döner ──┴── test kırmızı: geri dön ──┘
```

| Kapı | Koşul | Red durumunda |
|------|-------|---------------|
| G1 Keşif | Kural + disk kanıtı okundu, karar verildi | Görev uygulamaya alınmaz |
| G2 Uygulama | Diff yalnız hedef kapsamdaki | Kapsam daraltılır, tekrar denenir |
| G3 Doğrulama | `composer test` + `composer stan` yeşil | `.ai/.rules/error-recovery.md` → yeniden yaz → tekrar kontrol (kök `AGENTS.md` §7) |
| G4 Rapor | Commit ORCHESTRATÖRDE | Subagent commit atmaz |

### 3.3 İçerik Neden Dosyalara Ayrıldı?

| # | Gerekçe | Ne korur | Boşsa ne olur |
|---|---------|----------|---------------|
| 1 | **Bakım kolaylığı** | Diff küçük ve güvenli | Tek dosyada her şey değişir, inceleme kör olur |
| 2 | **Tek sorumluluk** | Adımın tek sahibi olur | Adım kimde sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ortak değer tek yerde | Tutarsız değerler, tarama gerekir |
| 4 | **Cihaz izolasyonu** | Cihaz farkı yayılmaz | Fark her yere sıçrar, drift |
| 5 | **Vendor karantinası** | 3. taraf ayrı durur | Güncelleme bizim kodu bozar |

> **eli10 (basit):** Adımlar küçük kutulara bölünmüş; çünkü bir kutuyu değiştirmek diğerini bozmaz.
> **eli15 (detay):** Süreç ayrı dosyalarda tutulur ki her klasör kendi kapısını bilsin; `shared/` kapısı farklı, `auth/` kapısı farklıdır. Aynı yerde toplansa her iş için tüm süreç okunur, odak kaybolur. Okunması, işe başlamadan sırayı görmeyi sağlar. Yazılması, tekrarlanabilir ve denetlenebilir bir akış bırakır. Bölünmezse "adım atlandı mı" sorusu cevapsız kalır.

---

## 4. Kurallar

| # | Kural | Neden var |
|---|-------|-----------|
| 1 | Kod öncesi Faz 1 keşif — onaysız değişiklik yok | Yanlış katman/yanlış dosya yazımını engeller (kök `AGENTS.md` §4) |
| 2 | Aynı dosya görevde 2. kez okunmaz | Anti-overthink / token israfı (kök `AGENTS.md` §5) |
| 3 | Hata → `.ai/.rules/error-recovery.md` → yeniden yaz → tekrar kontrol | Tekrarlı kör düzeltmeyi durdurur (kök `AGENTS.md` §7) |
| 4 | UI değişimi → browser testi | Kod ile yerleşim kanıtlanamaz (kök `AGENTS.md` §7) |
| 5 | **Commit subagent tarafından ATILMAZ** | Tarih ve entegrasyon yetkisi orkestratörde (kök `AGENTS.md` §7/§8) |
| 6 | 3 başarısız düzeltme → DUR + 1 soru | Kör deneme döngüsüne girilmez (kök `AGENTS.md` §5) |

> **eli10 (basit):** Bu kurallar işin hızlı ve doğru yürümesi için sırayı korur; en önemlisi commit'i başkasına bırakmak.
> **eli15 (detay):** Kurallar ayrı yazıldı çünkü süreç ile kod farklı hızda değişir; kod içine gömülse revizyonda kaybolur. Okunmaları, kapıların önceden bilinmesini sağlar. Yazılmaları, denetimin tekrarlanmasını garantiler. Kural değişince (kök `AGENTS.md` revizyonu) yalnız bu tablo güncellenir.

---

## 5. Workflow

```text
GÖREV → [K1: KURAL+KEŞİF oku] → [K2: KARAR (1. okumadan)]
  → UYGULA (hedef dosya) → TEST ekle → [K3: composer test + stan]
  → (UI ise browser testi) → [K4: RAPOR — commit yok] → ORKESTRATÖR
```

Adım ve kapıların tamamı §3.1 / §3.2 tablolarındadır; buradaki zincir sırayı özetler.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan + `docType: workflow` |
| 2 | Bölüm sırası | §1–§7 sabit |
| 3 | Adım tablosu | Her adımda "Neden" + "Atlarsan ne olur" dolu |
| 4 | Commit kuralı | §3.1 #8 + §4 #5 "ATMAZ" ifadesi mevcut |
| 5 | Placeholder | `{{` kalmadı |
| 6 | Wiki-link | `[[...]]` formatı, hedefler diskte var |
| 7 | eli10 + eli15 | §3.1 satırlarında etiketli blok |
| 8 | Disk kanıtı | `composer.json` script iddiası gerçek |
| 9 | Dokunulmaz | Mevcut `CLAUDE.md`/`AGENTS.md` değiştirilmedi |
| 10 | Emoji | Dekoratif emoji yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Klasör kuralları | [[CLAUDE.md]] | Uygulama kuralları |
| Klasör rolleri | [[AGENTS.md]] | Routing (MEVCUT) |
| Klasör context | [[CONTEXT.md]] | Envanter + harita |
| Kök master kurallar | [[../AGENTS.md]] | §4 keşif, §5 anti-overthink, §7 loop |
| Vault süreç | [[../.ai/WORKFLOW.md]] | Faz kapıları |
| Hata kurtarma | `../.ai/.rules/error-recovery.md` | G3 kapısı prosedürü |
| Paket scriptleri | `shared/composer.json` | `test` + `stan` kanıtı |
| Template kaynağı | `../.ai/.templates/frontend/context-template.md` | İskelet (Guardrail #16) |

---

**Template Version:** 1.0.0 · **Şablon:** `.ai/.templates/frontend/context-template.md`
**Last Updated:** 2026-10-03
