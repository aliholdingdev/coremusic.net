---
title: "Kurallar"
type: guide
category: ecosystem
version: 1.0.1
status: active
authority: reference
durum: active
tarih: 2026-09-24
kaynak: exa web doğrulaması (2026-09-24)
updated: 2026-09-29
---# Kurallar## §4 Kurallar

### §4.1 ZORUNLU: ŞABLON ÖNCE (Template-First)

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
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

*(Bu blok docs-md-template §3.3'ten birebir kopyalanmıştır — silinemez.)*

### §4.2 Ekosistem Dosyası Kuralları

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Frontmatter 7 zorunlu alan + durum/tarih/kaynak | title, type, category, version, status, authority, updated + durum: active, tarih, kaynak: exa web doğrulaması | Dosya geçersiz, revert |
| 2 | Her ana dosya ≥ 500 satır | Ham ölçüm (vault-utf8-writer verify → lines) | Eksik derinlik → tamamlanır |
| 3 | Yeni bilgi doğrulanır | Doğrulanmayan iddia: VERIFICATION REQUIRED | Etiketsiz iddia silinir (Guardrail #3) |
| 4 | Lisans yazımı zorunlu | Her ekosistem dosyasının §4.4'ünde lisans tablosu vardır | Lisanssız dosya → DUR |
| 5 | K çapraz referans zorunlu | Her ders §3.7 tablosuna (veya kendi entegrasyon bölümüne) K katmanı ile yazılır | K'sız ders → işlenemez sayılır |
| 6 | 0 dosya silme | Mevcut README.md ve service-integration.md değiştirilmez/silinmez | Silme tespit → git checkout + log ERROR |
| 7 | SSOT self-claim yasak | Bu dizin SSOT değildir; authority: reference | Çelişkide CLAUDE.md kazanır |
| 8 | Tek yazma arayüzü | vault-utf8-writer (UTF-8, BOM yok) | Bozuk dosya repair edilir |

### §4.3 Yasak / Doğru

| ✅ Yasak | ✅ Doğru |
|----------|----------|
| AGPL kodunu CoreMusic'e kopyalamak | Yalnız fikir/mimari dersi al; beyaz oda doküman |
| Lisanssız "referans" kod yapıştırmak | Kaynak + lisans + tarih ile referans ver |
| Ekosistemi ikinci SSOT ilan etmek | authority: reference; katman SSOT = architecture/index |
| Yeni web araştırması üretip eski doğrulamayı geçersiz kılmak | Kaynak tarihini koru; yeni veri → tarih + kaynak etiketiyle eklenir |
| Dosya adında Türkçe karakter | Dosya/klasör adı İngilizce (index.md, asio-wasapi-rehber.md) |
| Eski ekosistem dosyasını "yenisinin yerine" silmek | In-Place genişletme; silme yok (0 dosya silme) |

### §4.4 Lisans Kapısı (Özet — detay her dosyanın §4.4'ünde)

| Lisans | Örnek | CoreMusic (kapalı kaynak) Kararı |
|--------|-------|----------------------------------|
| MIT / ISC / BSD / Zlib / Public Domain | Koel (MIT), YUP (ISC), sndfilter (BSD-0), PortAudio (PD) | ✅ Fikir serbest; kod kopyalanırsa atıf + lisans dosyası korunur |
| GPL v2/v3 | JUCE (GPL), EasyEffects, HISE, FAUST | ⚠️ Kod linklenirse dağıtım GPL olur → ADR gerekir; fikir/algoritma serbest |
| AGPL-3.0 | Ampache | ❌ KOD ASLA kopyalanmaz — yalnız mimari fikir |
| LGPL | libsndfile, FFmpeg (varsayılan LGPL) | ⚠️ Dinamik link ile esnek; statik linkte kaynak paylaşım yükü |
| Steinberg ASIO SDK (açık kaynak varyant) | ASIO SDK | ✅ Dağıtılabilir; LICENSE şartları + atıf korunur (detay: asio-wasapi-rehber.md) |

---

