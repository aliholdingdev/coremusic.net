---
title: "Kurallar"
type: persona-index
category: personas
version: 1.0.1
status: active
authority: reference
updated: 2026-09-29
---# Kurallar## §4 Kurallar

### §4.0 ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*Bu dosya `[[.templates/documentation/docs-md-template]]` (8-bölüm iskelet) + `[[.templates/personas/persona-template]]` (persona domaini) okunarak üretilmiştir.*

### §4.1 Bağlayıcı Kurallar

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | **Guardrail #16 — Template Mandatory** | Her `.md` ilgili şablondan üretilir (`docs-md` + `personas/persona-template`) | Dosya geçersiz, revert |
| 2 | **Şablon Önce** | Yazmadan `.ai/.templates/` okunur | ERROR log, yazıma devam yok |
| 3 | **Kaynak etiketleme (ADR-005)** | Her gerçek-dünya iddiası etiketli: `Kaynak: [k1] + [k2]` ya da `⚠️ VERIFICATION REQUIRED` | Etiketsiz iddia silinir |
| 4 | **SSOT** | Bu dosya `authority: reference` — persona bilgisi persona dosyalarındadır; katalog yalnız indeksler | SSOT self-claim kaldırılır |
| 5 | **In-Place Refactoring** | Dosya adı/yolu değişmez; 68 persona adı eskisiyle korunur (ADR-023 şart 1c) | Dosya geri yüklenir |
| 6 | **Derinlik 500+** | Bu katalog dahil kök persona dokümanları ≥ 500 satır (`vault-utf8-writer verify` → `lines`) | Dosya tamamlanmış sayılmaz |
| 7 | **Dil / mojibake** | Türkçe (ç ğ ı İ ö ş ü); `Ã-` dizileri ve U+FFFD yasak; tek yazma arayüzü `vault-utf8-writer` | `repair` ile onarılır |
| 8 | **REDACTED / KVKK** | Secret, gerçek kişi verisi asla yazılmaz; 18 altı persona kurgusaldır | Sızıntı sayılır |
| 9 | **Append-only log** | Tüm değişiklikler `[[log]]`'ye eklenir | Geçmiş satır değişmez |
| 10 | **Persona şişirme yasağı** | Matris 20 satırda sabit; yeni persona = yeni ADR (ADR-088+) | Yeni satır silinir, karar reddedilir |
| 11 | **Domain boundary** | Persona dosyaları QA Engineer; katalog/registry MO (vault-updater) | Layer violation → revert |
| 12 | **Dizin mülkiyeti** | `research-bank.md` başka ajana ait — bu oturumda dokunulmaz | Eşzamanlı yazım → context lock |

### §4.2 Yasak / Doğru Tablosu

| ✅ Yasak | ✅ Doğru |
|---------|---------|
| 68 persona dosyasının içeriğini bu kataloğa kopyalamak | §6'da dosya yolu / isim / yaş / mood satırı taşımak |
| Eski vault'a yazmak (`coremusic.net.old/.ai/personas/`) | Salt-okunur okuma; tüm üretim `.ai/.personas/` altında |
| `personas/index.md` (noktasız) oluşturmak | `.ai/.personas/index.md` (ADR-023 şart 1c yolu) |
| Kaynaksız gerçek-dünya iddiası (nüfus, cihaz, WCAG %) | `⚠️ VERIFICATION REQUIRED` + `[[.personas/research-bank]]` pointer'ı |
| `Set-Content` / `Out-File` / `echo >` ile yazım | `node .ai/scripts/vault-utf8-writer.mjs write/append` |
| Dosya adı değişikliği (`-v2.md`, `(1).md`) | In-Place — ad sabit, içerik düzeltilir |
| `research-bank.md`'yi bu oturumda yazmak | `📋 BAŞKA AJAN` işareti + geri dönüş raporunda bildirim |

### §4.3 Wiki-Link ve Bağlantı Formatı

| ✅ Doğru | ❌ Yanlış |
|----------|-----------|
| `[[.personas/mood-taxonomy]]` (aynı dizin kardeşi) | `[mood](mood-taxonomy.md)` |
| `[[.templates/personas/persona-template]]` | `[şablon](C:\www\coremusic.net\.ai\...)` |
| `[[.decisions/accepted/ADR-023-persona-driven-testing]]` | `https://iç-sistem/adr-023` |
| Harici URL düz metin | `https://...` (wiki-link yapılmaz) |
| `[[CLAUDE.md]]` (Zorunlu Bağlantılar satırı biçimi) | Mutlak Windows yolu |

**Biçim notu:** kök dokümanların "Zorunlu Bağlantılar" satırı `.ai/` kök adlarıyla yazılır (`[[CLAUDE.md]]`, `[[.templates/index]]`); kardeş dosyalara göreli wiki-link kullanılır. Persona şablonundaki kısaltma `[[personas/...]]` bu vault'ta **`.ai/.personas/`** dizinine karşılık gelir (§7.1 durum sütunu).

### §4.4 Derinlik Standardı (500+)

| Kural | Değer |
|-------|-------|
| Kök persona dokümanı (`index`, `mood-taxonomy`, `test-scenarios-mapping`) | ≥ 500 satır (ham ölçüm — `verify` → `lines`) |
| Üretilen persona dosyası | ≥ 500 satır (persona-template §4.6 — istisnası yok) |
| Kapsam dışı | `session-log-template.md` (144 satır) |
| İhlal | 500 altındaysa dosya tamamlanmış sayılmaz; §5.2'ye göre derinleştirilir |

### §4.5 REDACTED, KVKK ve Kaynak Etiketi

| Durum | Aksiyon |
|-------|---------|
| API key, token, parola | Vault'a asla yazılmaz; `[REDACTED]` |
| Gerçek kullanıcı verisi (ad, e-posta, telefon) | Persona dosyasına yazılmaz; kurguya çevrilir |
| 18 yaş altı persona (29 çocuk + 29 teen) | Kurgusal olduğu açıkça yazılır; gerçek çocuk verisi asla |
| Gerçek-dünya iddiası (nüfus, cihaz spec, WCAG kriteri, KVKK/COPPA) | ≥2 bağımsız kaynak **veya** `⚠️ VERIFICATION REQUIRED` + `[[.personas/research-bank]]` |
| Sanatçı / BPM değeri | 2 kaynak (diskografi + müzik veritabanı); yoksa `⚠️` |
| Log'a sızan secret | `[[log]]` girişinde `[REDACTED]` |

---

