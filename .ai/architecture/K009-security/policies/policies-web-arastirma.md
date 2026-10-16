---
node_id: K009-security/policies/policies-web-arastirma
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on: K009-security/policies/policies-anayasa
evidence: 7 dış kaynak (HTTP 200, 2026-10-15) — §1 tablosu
validation: partial (URL'ler 2026-10-15 HTTP 200; içerik indirilmedi — iddialar yerel vault kurallarıyla çaprazlandı)
open_questions: içerik doğrulaması sınırlandırıldı (standart metinleri okunmadı, uydurulmadı)
group: kanit
---

# K009 · policies · policies-web-arastirma

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Dış Kaynak Kanıtı
> Politikaların dış kaynak dayanağı. **Yalnız 7 URL**; `index.md`'deki 3 URL
> (proactive-controls · top10.owasp.org/2025 · csrc sp800/30 r1) **TEKRARLANMAZ**
> ve başka URL eklenmez (kapsam sınırı: anayasa §1 kaynak hiyerarşisi madde 5).

## BAĞLANTILAR

- [[policies-anayasa]] — bu kaynakların dayandığı politikalar (P-01..P-18)
- [[policies-surucu]] — §2 kod xref
- [[index]] — 3 URL'ün sahibi (tekrar edilmez)
- [[../audit/index]] — kanıt katmanı

---

## 1. Kaynak Tablosu

| # | İddia (politika dayanağı) | URL | Tarih | HTTP 200 | Kod xref (§2) |
|---|---|---|---|---|---|
| 1 | MUST/SHOULD bağlayıcı dili RFC2119'dur (anayasa §2 dilbilgisi) | https://www.rfc-editor.org/rfc/rfc2119 | 2026-10-15 | 200 | policies-anayasa.md §2 |
| 2 | Güvenlik kuralları olgunluk için ölçülür/kaydedilir (P-10, AD-xx disiplini) | https://csrc.nist.gov/pubs/sp/800/100/final | 2026-10-15 | 200 | policies-dogrulama.md §1 (AD-01..17) |
| 3 | OWASP SAMM = güvenlik olgunluk modeli (P-10 çerçevesi) | https://owaspsamm.org/ | 2026-10-15 | 200 | policies-kapsam.md §1 (kalem 6) |
| 4 | ISO/IEC 27001 = bilgi güvenliği yönetim standardı (P-10 çerçevesi) | https://www.iso.org/standard/27001 | 2026-10-15 | 200 | policies-kapsam.md §1 (kalem 6) |
| 5 | KVKK = kişisel veri mevzuatı zemin (kapsam dışı devir sınırı) | https://www.kvkk.gov.tr/ | 2026-10-15 | 200 | policies-kapsam.md §3 (KVKK metni OPEN) |
| 6 | Politika onay/yazım süreci için örnek governance kaynağı (anayasa yazım kuralı) | https://owasp.org/www-policy/ | 2026-10-15 | 200 | policies-anayasa.md §4 (atıf notu) |
| 7 | NIST Cybersecurity Framework = çerçeve referansı (P-10 denetim gerekçesi) | https://www.nist.gov/cyberframework | 2026-10-15 | 200 | policies-surucu.md §7 (P-10 kanıtı) |

**Sayım**: 7/7 · HTTP 200: 7/7 (2026-10-15) · index 3 URL tekrarı: 0 · ek URL: 0.

---

## 2. Koddan Cevrisi (xref)

Her iddia, kod/vault yüzeyine bağlanır (`policies-surucu.md` §2 tablosu):

1. **RFC2119** → anayasa §2: MUST/MUST NOT ihlali RED, SHOULD ihlali BLOCK.
   Kanıt: anayasa §4'te 18 politika satırının tamamı büyük harf MUST/SHOULD +
   kaynak + kapı içeriyor (AD-15 GREEN).
2. **SP 800-100** → dogrulama §1: her politikanın beklenen kanıtı yazılır
   (ölçülebilir kanıt = olgunluk kaydı); AD-xx disiplini bu fikrin uygulaması.
3. **SAMM** → kapsam §1 kalem 6: olgunluk denetimi `audit` node'una devredilir.
4. **ISO 27001** → kapsam §1 kalem 6; kontrol seti mantığı (doküman + kanıt +
   denetim) anayasa/doğrulama ikilisine karşılık gelir.
5. **KVKK** → kapsam §3: saklama metni üretilmedi → `⚠️ ONAY BEKLİYOR`
   (dogrulama §6 madde 3; sahiplik K020-comilance).
6. **www-policy** → anayasa §4 atıf notu: kaynak doğrulanmadan politika
   yazılmaz; `⚠️` ile işaretlenir (zero-hallucination uygulaması).
7. **NIST CSF** → surucu §7: P-10 (OWASP %100) için denetim toplama noktası;
   çerçeve, OWASP eşleminin gerekçe katmanıdır.

---

## 3. Yontem (zero-hallucination)

- URL'ler yalnız sayfa yanıt kontrolü için kullanıldı: **HTTP 200**, 2026-10-15.
- İçerik **indirilmedi/alıntılanmadı**; iddialar yerel vault kurallarıyla
  (`.ai/CLAUDE.md` · `.ai/AGENTS.md` · anayasa) çaprazlanır → `validation: partial`.
- Standart metinleri (ISO 27001 maddeleri, KVKK kanun metni, RFC metni)
  **uydurulmadı**; metin gerekirse ayrıca belgede `⚠️ VERIFICATION REQUIRED`
  ile istenir.
- Var olmayan URL/sürüm/tarih yazılmadı; 7 satır diskteki listeyle sınırlı.
- Anayasa §2'deki RFC2119 adresi (`.html` sürümü) bu tabloyla aynı kaynağın
  parçasıdır; sayım iki katına çıkarılmaz (0 tekrar kuralı ihlal edilmez).

---

## 4. Kapsam Disi Kaynaklar (bilerek yok)

| Kaynak | Neden yok |
|---|---|
| index.md'nin 3 URL'i (proactive-controls · top10.owasp.org/2025 · csrc sp800/30 r1) | tekrar yasağı — sahibi index |
| RFC 6265 / RFC 7231 vb. ek RFC'ler | görev 7 URL ile sınırlı |
| Vendor/blog yazıları | kanıt seviyesi yetersiz |
| İç linkler (vault wiki-link) | BAĞLANTILAR § başlıkta; URL sayılmaz |

---

## 5. Yenileme Kurali

HTTP durumları **2026-10-15** içindir; 30+ gün sonra yeniden kontrol edilir.
404/redirect olursa satır RED'e döner, URL **silinmez** — raporlanır
(surucu §5 C1/C3 mantığı: kanıt düşer, kayıt kalır).

---

## 6. Kaynak Ozeti

| Metrik | Değer |
|---|---|
| Toplam URL | 7 (yalnız §1 tablosunda) |
| HTTP 200 (2026-10-15) | 7 |
| index.md tekrarı | 0 |
| İçerik alıntısı | 0 (indirilmedi) |
| ⚠️ işaretli iddia | 1 (KVKK metni → OPEN) |

## 6. Sorular (SSS)

**Neden yalnız 7 URL?** — Görev tanımı sabit: 7 kaynak; `index.md` 3 URL'ün sahibi,
başka URL ekleme yasağı var (§4). Sayım şişirilmez.

**index URL'leri neden tekrar yazılmadı?** — Tekrar yasağı (§4): aynı iddia iki
dosyada iki kez doğrulanamaz; sahibi indeks, kontrolü onun satırındadır.

**İçerik indirildi mi?** — Hayır (§3): yalnız HTTP yanıt kontrolü. İddialar yerel
vault kurallarıyla çaprazlandı → `validation: partial`.

**ISO/KVKK metni neden alıntılanmadı?** — Uydurma yasağı: standart metni okunmadan
aktarılmaz; gerekirse `⚠️ VERIFICATION REQUIRED` ile ayrıca istenir (anayasa §19).

**404/redirect olursa?** — Satır RED'e döner, URL silinmez (§5); kayıt kanıt
kaybını belgeler.

**Tarih neden tek (2026-10-15)?** — Hepsi aynı kontrol turunda; ikinci bir tarih
uydurulmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
