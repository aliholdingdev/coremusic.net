---
node_id: K019-comilance
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - policies
  - controls
  - verification
depends_on:
  - "K000–K5000 Architecture Registry"
  - "../adr (mimari ADR serisi)"
  - "@.ai/.decisions (karar serisi — ADR-097 draft)"
evidence:
  - "v2 §2.20 mini-ağaç (comilance → index.md · compliance-master.md · policies/ · controls/ · verification/) — G:/Drive'ım/Projelerim/Project-notes/Bayram Ali Prompt Arşivi & Notlar/Coremusic/coremusic-katmanli-mimari-agaci-v2.md (satır 548–555)"
  - ".ai/architecture/index.md §5 satır 79 (draft) · §6 satır 92 (imla: 'comilance' KORUNUR)"
  - "Kontrol dayanakları: kök AGENTS.md §5 Security · .ai/AGENTS.md §16 · OWASP Top 10:2025 (top10.owasp.org/2025/en)"
  - "KVKK: 6698 sayılı Kanun m.4/m.12/m.16/m.18 — kvkk.gov.tr (2026-10-17) · ISO/IEC 27001:2022 Annex A 93/4 tema — secureframe/drata (2026-10-17)"
  - "Disk ölçümü 2026-10-17: klasör adı K019-comilance (registry'de K020-comilance) · K020-adr/K020-licence/K020-comere YOK · ../adr/ VAR · K000–K013 14 domain mevcut"
validation:
  - "2026-10-12 — v2 §2.20 şekli birebir karşılaştırıldı (5 öğe)"
  - "2026-10-12 — OWASP Top 10:2025 başlık listesi resmi OWASP kaynağından okundu (A01–A10)"
  - "2026-10-17 — Kullanıcı kararları: (1) uyum kapsamı = KVKK + ISO 27001 (GDPR koşullu) · (2) disk kanıtı esastır — klasör K019-comilance adıyla kalır, node_id'ler diske hizalandı · (3) K000–K013 domain haritası + 5 md arası wiki-link tamamlandı · (4) 11 dil kod-ornekleri.md onaylandı"
  - "2026-10-17 — tur-2 web doğrulaması: OWASP (A01–A10) + ISO Annex A (93/4) + KVKK (72s/m.16) doğrulandı; 3 kırık referans (K020-adr/licence/comere) düzeltildi"
open_questions:
  - "⚠️ ID CONFLICT (2026-10-17 kısmen kapandı): disk kanıtı esas — klasör K019-comilance kalır; registry index.md §5 madde 21 hâlâ 'K020-comilance' → registry güncellemesi üst karar (compliance-master §7 madde 5)"
  - "registry §5 madde 23'te beklenen 'K020-licence/dependency-licenses.md' diskte YOK → lisans denetimi nerede tutulacak?"
  - "'comilance' yazımı kaynağa sadık korunur; 'compliance' olarak düzeltilmez (v2 §3 KURAL 1)"
group: mimari
updated: 2026-10-18
---


# K019-comilance — Uyum (Compliance) Rafı

## §GENEL BAKIŞ

| Alan | Değer |
|---|---|
| **K Kodu** | K019-comilance |
| **K Adı** | Uyum (Compliance) |
| **Kapsam** | Kurallara uyulduğunu kanıtlayan kayıtların tutulduğu raf. Kurallar başka yerde yazılır; burada "şu tarihte kontrol ettik, şu sonuç çıktı" satırları durur — kanıtı olmayan uyum uyum sayılmaz. Kapsam: OWASP Top 10:2025 + KVKK (6698) + ISO/IEC 27001:2022 (GDPR koşullu). Çocuklar: policies/ · controls/ · verification/. Yazım `comilance` korunur. |
| **Kritik Bağımlılıklar** | yok (registry + ../adr/ mimari ADR serisi + @.ai/.decisions karar serisi — K bağımlılığı yok, kanıt: FM depends_on) |
| **Durum** | draft (3 kademe-2 çocuk · 6 md · hiçbir uyumluluk iddiası kanıtlanmış değil) |
| **Son Güncelleme** | 2026-10-17 (tur-2 web doğrulaması: OWASP + ISO Annex A + KVKK · 3 kırık referans düzeltildi) |

## ELI15 — Bu klasör ne işe yarar?

Kurallara **uyulduğunu kanıtlayan kayıtların** tutulduğu raftır. Kurallar başka
yerde yazılır (politikalar, kod kuralları); burada "şu tarihte kontrol ettik,
şu sonuç çıktı" satırları durur. Kanıtı olmayan uyum **uyum sayılmaz**.

## Çocuk kataloğu (v2 §2.20 + 2026-10-17 genişlemesi)

| Öğe | Tür | Rol | Durum |
|---|---|---|---|
| `compliance-master.md` | dosya | Uyum programının anayasası: alanlar (OWASP+KVKK+ISO), sorumlular, denetim döngüsü | v1.1.0 — kapsam kararlı |
| `policies/` | dizin | Politika kayıt defteri — 8 satır (KVKK · ISO · GDPR koşullu) | draft |
| `controls/` | dizin | Kontrol haritası — OWASP A01–A10 + ISO 4 tema + **K000–K013 domain eşlemesi** | draft |
| `controls/kod-ornekleri.md` | dosya | **11 dilde** kontrol kod şablonları (PHP·JS·Python·Go·C#·Java·C/C++·Rust·SQL·Bash·YAML+Rego) | draft |
| `verification/` | dizin | Doğrulama turu kayıtları (tur-1 2026-10-12 · **tur-2 2026-10-17**) | draft |

## Durum

- **status:** `draft` — şekil birebir + 2026-10-17 genişlemesi; **hiçbir uyumluluk iddiası kanıtlanmış değil** (denetim turları saha denetimi içermez).
- **depends_on:** registry + mimari ADR serisi (`../adr/`) + karar serisi (`@.ai/.decisions/` — ADR-097 draft).
- **Yerel dosyalar:** `index.md` · `compliance-master.md` · 3 alt `index.md` · `controls/kod-ornekleri.md` (toplam 6 md).

## K000–K013 uyum haritası (özet)

Detay: `[[controls/index]]`. 14 domain diskte doğrulandı (2026-10-17); OWASP
eşlemesi + KVKK teması + sorumlu agent atandı; **hepsi `denetlenmedi`**.

| Domain grubu | Sayı | KVKK teması |
|---|---|---|
| K000–K005 (OS · donanım · elektronik · audio · dsp · firmware) | 6 | — |
| K006–K007 (YZ · veri) | 2 | Evet (m.6 · m.10 · m.12) |
| K008–K013 (domain · security · network · identity · application · services · integration) | 6 | Evet (m.2 · m.12 · aktarım) |

## Denetim döngüsü (özet)

```text
policies/ (ne kural?) → controls/ (nasıl + kod gövdesi) → verification/ (ne zaman, sonuç)
        → açık soru → VERIFY REQUIRED → üst karar / ADR
```

## İlişkili kaynaklar

| Kaynak | Ne verir |
|---|---|
| `[[compliance-master]]` | Uyum alanları + denetim döngüsü + açık sorular |
| `[[policies/index]]` | Politika kayıt defteri (KVKK · ISO · GDPR koşullu) |
| `[[controls/index]]` | OWASP A01–A10 + ISO 93/4 + K000–K013 haritası |
| `[[controls/kod-ornekleri]]` | 11 dilde kontrol kod şablonları |
| `[[verification/index]]` | Tur kayıtları (tur-1 · tur-2) |
| `[[../adr/index]]` | Mimari ADR serisi (kendi numara uzayı) |
| `@.ai/.decisions/draft/ADR-097-…md` | K-Space V3 kurulum kararı (karar serisi) |
| `[[../K020-discovery/index]]` · `[[../K021-recomenataion/index]]` | K020 bandı kardeşleri (disk adlarıyla) |
| `@.ai/CLAUDE.md` §6 · `@.ai/AGENTS.md` §16 | Vault LINT + kalite standartları |

> **Kırık referans düzeltmesi (2026-10-17):** eski `[[../K020-licence/index]]` ve
> `../K020-adr/` hedefleri diskte **YOK** — kaldırıldı; gerçek hedefler `../adr/`
> ve `@.ai/.decisions/` ile değiştirildi, lisans boşluğu açık soru olarak kaydedildi.

## §BAĞLANTILAR

> **Yön işareti = kanıt yönü, sözleşme değil:** `→` bu K'nın kök içerik dosyaları hedefe referans veriyor · `←` hedef K'nın kök dosyaları bu K'ya referans veriyor · `↔` ikisi de · `⚠️` kök taramada kanıt yok.
> **Yöntem (2026-10-18):** her K'nın kök .md'leri (index.md hariç) taranıp hedef K adının toplam geçişi sayıldı (zero-hallucination; uydurma bağımlılık yok). Zenginleşik 4 tablo (K003/K008/K011/K012) korunmuştur.

| Hedef K | Wiki-link | Göreli dosya yolu | Yön | İlişki | Kanıt |
|---|---|---|---|---|---|
| K019-comilance | [[K019-comilance]] | ./K019-comilance/index.md | ○ | bu K (kendi kökü) | registry §5 + disk |
| K000-isletim-sistemi | [[K000-isletim-sistemi]] | ../K000-isletim-sistemi/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K001-donanım | [[K001-donanım]] | ../K001-donanım/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K002-electroncis | [[K002-electroncis]] | ../K002-electroncis/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K003-audio | [[K003-audio]] | ../K003-audio/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K004-dsp | [[K004-dsp]] | ../K004-dsp/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K005-firmware | [[K005-firmware]] | ../K005-firmware/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K006-yapay-zeka | [[K006-yapay-zeka]] | ../K006-yapay-zeka/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K007-data | [[K007-data]] | ../K007-data/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K008-domain | [[K008-domain]] | ../K008-domain/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K009-security | [[K009-security]] | ../K009-security/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K010-network | [[K010-network]] | ../K010-network/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K011-application | [[K011-application]] | ../K011-application/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K012-services | [[K012-services]] | ../K012-services/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K013-integraion | [[K013-integraion]] | ../K013-integraion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K014-media | [[K014-media]] | ../K014-media/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K015-AI | [[K015-AI]] | ../K015-AI/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K016-observability | [[K016-observability]] | ../K016-observability/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K020-discovery | [[K020-discovery]] | ../K020-discovery/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K021-recomenataion | [[K021-recomenataion]] | ../K021-recomenataion/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K17-devops | [[K17-devops]] | ../K17-devops/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |
| K18-testing | [[K18-testing]] | ../K18-testing/index.md | ⚠️ | kanıt yok | M→0 · M←0 (kök .md, 2026-10-18) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-1 |
| mimari | `compliance-master.md` | Master künye ve sorumluluk haritası | Kademe-1 |
| mimari | `policies/index.md` · `controls/index.md` · `verification/index.md` | Politika · kontrol · denetim kayıt defterleri | Kademe-2 |
| kod | `controls/kod-ornekleri.md` | 11 dilde uyum kontrolü kod şablonları | Kademe-3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
