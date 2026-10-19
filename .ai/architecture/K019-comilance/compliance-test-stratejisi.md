---
node_id: K019-compliance-test-stratejisi
node_type: subdomain
status: draft
parent_id: K019-comilance
children: []
depends_on: [K007-data, K009-security, K18-testing]
evidence:
  - ".ai/architecture/K019-comilance/compliance-master.md (düzeltme: ISO 27001:2022 93/4 kontrol — 2026-10-18)"
  - ".ai/architecture/K18-testing/testing-master.md (55 PHP test + 10 JS spec, CI gate'leri soft — 2026-10-18)"
  - ".ai/architecture/K019-comilance/compliance-policies.md (4 politika md — 2026-10-18)"
validation: "Test piramidi K019 kapsamını tarar; KVKK m.10-12 ve ISO A.5-8 senaryoları en az 1 kez kapsanır; kabul kriterleri ölçülebilir."
open_questions:
  - "⚠️ compliance-test-stratejisi.md için hazır test altyapısı var mı? Mevcut 55 PHP test KVKK/ISO senaryolarını kapsamıyor (UNKNOWN — vhdl'li 30-45 test dosyası electrical-engineering'de, KVKK senaryosu değil)."
  - "⚠️ K18-testing kapsamı K019 testlerini içerir mi? (K18 test K011/K012 ağırlıklı — kapsam dışı beyan, doğrulanmadı)."
group: architecture
---

# Compliance — Test Stratejisi

> KVKK ve ISO 27001:2022 kontrollerinin nasıl test edileceğini tarar. Kod düzeyinde
> doğrulama (`test-compliance-*.php`) ile prosedür/saha doğrulaması (iç denetim) ayrıdır.

## §1. Test Piramidi (K019 Uyarlaması)

```
        [ Saha / İç Denetim ]        ← 1-2 senaryo/yıl, manuel, ISO A.5-9+A.8-18
       [ Entegrasyon Testleri ]       ← 5-8 senaryo, API+DB KVKK/ISO akışları
      [ Birim Testleri (Unit) ]       ← mevcut: 55 PHP test (K019 kapsamı: UNKNOWN)
   [ Statik Analiz / LINT / Tarama ]  ← OWASP dep-scan + secret scan (CI'da)
```

K019'a özel **2 katman** eklenir:

| Katman | Ne test eder | Araç (vault kanıtı) | Sıklık |
|---|---|---|---|
| Statik kontrol taraması | 4 politikanın varlığı, son revizyon tarihi, sahip ataması | `compliance-index.md` tablosu + CI script (UNKNOWN — script yok) | Her PR |
| KVKK veri-subject hakkı simülasyonu | Silme/erişim/düzeltme talebi akışı | `compliance-policies.md` §2-§4 (AI servisi: dsil/dualist/dapist) | Sprint testi |

## §2. Test Senaryoları (KVKK)

| ID | Senaryo | Tip | Beklenen |
|---|---|---|---|
| KVKK-T01 | Veri-subject silme talebi → AI veri silme (dsil) | Entegrasyon | 30 gün içinde tam silme, kanıt kaydı |
| KVKK-T02 | Düzeltme talebi → veri revizyonu (dualist) | Entegrasyon | Değişiklik loglanır (audit trail) |
| KVKK-T03 | Erişim talepi → kişi/veri raporu (dpist) | Entegrasyon | Kişi ve ilgi alanları raporlanır |
| KVKK-T04 | Saklama süresi ihlali → otomatik imha (dimha) | Birim | Süre dolan kayıt silinir, saklama listesi güncellenir |
| KVKK-T05 | KVKK-Consent imzasız kayıt yakalanır | Statik | Kayıt engellenir (m.10 rıza) |
| KVKK-T06 | m.6 özel nitelikli veri (sağlık/ideoloji) hatalı işlenme | Birim | 19 bent ihlali simüle edilir, engellenir |
| KVKK-T07 | İhlal bildirim süresi (m.12, 72 saat) sayacı | Birim | Sayaç doğru başlar, eskalasyon tetiklenir |

> Veri modeli kanıtı: `.ai/data-model/models/entity-model.md` — 141 entity,
> KVKK-Consent (m.10), KVKK-DP-IhlalBildirimi (m.12), KVKK-DP-SaklamaListesi (m.9),
> `subjectSystemCodes` ilgi-alanı kümesi + `isSensitive` flag.

## §3. Test Senaryoları (ISO 27001:2022 — 93/4)

| ID | ISO Kontrol | Senaryo | Tip |
|---|---|---|---|
| ISO-T01 | A.5.15 Erişim kontrolü | Yetkisiz rolün K-deploy erişim denemesi reddedilir | Entegrasyon |
| ISO-T02 | A.5.16 Kimlik doğrulama | Zayıf parola / MFA'sız oturum engellenir | Birim |
| ISO-T03 | A.5.23 Bulut hizmeti kullanımı | `k5-capstone` sahibi olmayan deploy erişimi | Entegrasyon |
| ISO-T04 | A.7.4 Fiziksel güvenlik | Yetkisiz fiziksel erişim → olay kaydı (saha) | Saha |
| ISO-T05 | A.8.15 Kayıt tutma | Log kaydı üretimi + bütünlük koruması | Birim |
| ISO-T06 | A.8.16 İzleme | İzleme K016 → olay tespit zinciri (event_inbox) | Entegrasyon |
| ISO-T07 | A.8.24 Kriptografi | AES-256-GCM / ChaCha20-Poly1305 kullanımı | Birim + kod denetimi |
| ISO-T08 | A.9.2-9.4 Yetki dışı erişim denemesi | Brute-force → rate limit + lockout | Entegrasyon |

> Kapsam NOTU (zero-hallucination): ISO 93 kontrolünün **tamamı** için otomatik test
> mümkün değil; A.5-6 (organizasyonel, prosedüel) kontroller **iç denetim + doküman
> kanıtı** ile doğrulanır. Bu dosya A.8 (teknik) kontrolleri test eder.

## §4. Mevcut Test Altyapısı (K18'den — KVKK kapsamı doğrulanmadı)

| Varlık | Sayı | KVKK/ISO senaryosu |
|---|---|---|
| `.ai/academic/.../_tests/` PHP testleri | 55 dosya | **UNKNOWN** — KVKK senaryosu içerdiği kanıtlanmadı |
| `scripts/veo-*.test.js` JS specs | 10 dosya | **YOK** — üretilmiş görsel doğrulama |
| CI coverage gate | `continue-on-error` (soft) | Ölçüm yok |

**Açık (⚠️):** K019 için özel test dosyası (örn. `test-compliance-kvkk.php`) diskte
YOK. §2-§3 senaryoları **taslak**tır; uygulanabilirlik K18-testing'e devredilir.

## §5. Kabul Kriterleri

1. §2 KVKK senaryolarının **en az 5/7'si** otomatik veya manuel olarak yürütülmüş.
2. §3 ISO A.8 teknik kontrollerinin **en az 5/8'i** otomatik test ile kapsanmış.
3. A.5-6 prosedürel kontroller için **doküman kanıtı** (politika + revizyon tarihi) sunulmuş.
4. Test sonuçları `compliance-master.md` "Bakım" bölümüne işlenmiş.
5. İhlal simülasyonu (KVKK-T07) **72 saat sayacının** doğru çalıştığını kanıtlar.
6. Soft gate'ler (coverage/e2e) bu K'da **hard** sayılır: test geçmeden checklist
   tamamlanmaz (K18 ile ortak — ⚠️ uygulama henüz yok).

## §6. Doğrulanmayan İddialar

| İddia | Durum |
|---|---|
| Mevcut 55 PHP test KVKK senaryolarını kapsıyor | UNKNOWN (dosya içerikleri taranmadı) |
| K18 test altyapısı K019'u da kapsayacak | ⚠️ VERIFICATION REQUIRED |
| Otomatik ISO kontrol taraması CI'da çalışıyor | YOK (script kanıtı yok) |
| K019 testleri için dedicated runner | UNKNOWN |
