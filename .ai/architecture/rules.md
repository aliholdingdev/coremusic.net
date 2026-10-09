---
title: "Architecture — Mimari Kurallar V2 (SSOT · K-Space Rejimi)"
type: rules
category: architecture
version: "2.1.0"
status: active
authority: "SSOT: .ai/architecture/rules.md — .ai/architecture/** tek kural kaynağı; kurucu karar: ADR-096"
updated: 2026-10-08
tier: 3
domain: architecture
ssot: true
risk: high
owner: "Vault Steward"
depends-on: [".ai/CLAUDE.md", ".ai/.decisions/accepted/ADR-096-kspace-5000-boundary-model.md"]
---

# Architecture — Mimari Kurallar V2 (SSOT · K-Space Rejimi)

> **Authority:** Bu dosya `.ai/architecture/**` için **tek kural kaynağıdır**. Çelişkide bu dosya kazanır — **ama bu dosyanın kendisi de ADR-096 + anayasa dışında kaynak üretemez.**
> **Değişiklik riski: high** → insan onayı zorunlu (R10).
> **Devralma (2026-10-08):** Eski R1–R20 (v1.3.0) **terk edildi** (R18); bu sürüm ADR-096'dan türetildi.

---

## R1 — Kapsam, Authority ve Öncelik

1. `.ai/architecture/` = CoreMusic **K-Space V2** mimari rejiminin evi (K000–K5999 · 9 bant · boundary model — ADR-096).
2. **Authority zinciri (bu dosyada):** `.ai/CLAUDE.md` / `.claude/CLAUDE.md` (anayasa) > **bu plan/donem kararları (11+1 — ADR-096 §2)** > `rules.md` (bu dosya) > katman `index.md` > görev notu. Düşük seviye yükseği override edemez.
3. **Spec kaynakları:** F1 (`​.ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md`) + F2 (`​.ai/prompts/2026-10-08-enterprise-arch-f2.md`) — **ikisi birden** esas; aralarında kalan çelişki burada veya ADR-096'da çözülür; **yeni çelişki = DUR + kullanıcıya sor** (F1 §3.4).
4. K-space anayasa ekı: `00-kspace-anayasa.md` (EK A kopyası — bant şeması + foundation kartları tek kaynaktır, burada tekrarlanmaz).
5. Eski mimari (K000–K499 / 10 domain / k0–k20 set) yalnız **salt-okunur analiz girdisidir** (`git show HEAD:…`); vault'a geri taşınmaz (ADR-096 §2.9).

## R2 — K-ID ve İsimlendirme

1. **K-space:** `K000` → `K5999`, **9 bant** (EK A §A — FOUNDATION · ENTERPRISE · SPECIALIZED · EXTENDED · DEEP · HW/MEDIA/UI · AGENT/GOV · COMMERCE/AI · RESILIENCE/FUTURE).
2. **Dizin:** `K{NNN}-{türkçe-ad}` (ör. `K000-isletim-sistemi`, `K016-class-ab`) — teknik terim özgün kalır (İngilizce/Türkçe karışım serbest: `class-ab`, `isletim-sistemi`). Küçük harf `k000-` yalnız Türkçe ek/tire kurallarına uygun yazılır; **tek biçim kuralı: 3 haneli K-ID + tire + Türkçe ad.**
3. **Kanonik K-ID asla değişmez**; teatral epitet (`«TEMEL TAŞI»`) yalnız sıfat — kimliği ezmez.
4. Dizin adları **wiki-link hedefidir, DEĞİŞTİRİLMEZ** (In-Place · H4).
5. İçerik dili Türkçe; kod yolları / API / teknik adlar özgün.

## R3 — Dosya İskeleti ve Parçalama

1. **Her katman ÇOKLU MD'dir (karar 13-14 · 2026-10-08):** `index.md` (zorunlu çekirdek: navigasyon + 16 alan özet — **dokunulmaz, In-Place**) + **en az 4 derin dosya**: `kimlik-karti.md` (EK C 20 alan tam kart) · `sorumluluk.md` (kalem tablolarının TAM derinliği) · `bagimlilik-sinir.md` (izinli/yasak matrisleri + boundary detay) · `kanit-kaynaklari.md` (3'lü kanıt zinciri + ⚠️ defteri). **Tek `index.md` ile yetinen katman EKSİKTİR.** Bölme (split) YOK — derin dosyalar index'in DERİNLEŞTİRİLMESİDİR (G5 istisnası: aynı konu, farklı derinlik; çift kaynak değil, özet↔derinlik katmanıdır).
2. **Tek dev md yASAK** (F2 §42): mimari anlatı tek dosyaya gömülmez; çoklu-md + çoklu-kategori.
3. **Boyut bandı (karar 15 · 13-16 düzeltmesi · min-500 yerine):** katman **derin dosyası ≥250 satır** · **katman toplamı ≥1000** (index dahil) · **rapor ≥5000**. Katman `index.md` ~500 bandında (kanonik üretim standardı). **Muaf:** idari/operasyonel belgeler (ADR, SESSIONS notu, decisions/index, log, kurallar, şablonlar) — doğal boyut. **Şişirme/dolgu her yerde yasak** (H1): derinlik gerçek içerikten gelir; yetmezse araştırma (R14) → yetmiyorsa `⚠️` + dosya azaltılır (kaynak yalanı değil).
4. **Rapor** (`00-final-rapor.md`) ≥5000 satır (F1 §10.3).
5. **>2000 satır → `partNN`** ile bölünür; parçalar wiki-link ile bağlı (orphan üretilmez).
6. Envanter dosyası: `inventory/{konu}-part{NN}.md` — bir part = bir tür/konu (karışık part yasak).

## R4 — Kayıt Formatı (Hibrit — ADR-096 §2.4)

1. **Özet satır (her katman — zorunlu, 16 alan):** `K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT` (F1 §10.2).
2. **Tam kimlik kartı (EK C — 20 alan):** kritik · `risk:high` · derin katmanlarda **zorunlu**; diğerlerinde özet satır yeterli (hibrit yorum — ADR-096 §2.4; F1 M10'un EK A deseniyle bağlanmış hâli).
3. **EK C alanları:** K-ID · KANONİK_AD · TEATRAL_EPİTET · DOMAIN · SUBDOMAIN · BOUNDED_CONTEXT · RUNTIME · SORUMLULUK · GIRDI · CIKTI · IZINLI_BAGIMLILIK · YASAK_BAGIMLILIK · DATA_BOUNDARY · SECURITY_BOUNDARY · FAILURE_MODE · OBSERVABILITY · TEST · KANIT (+ epitet/kalite notu).
4. **Kart kalite kapıları (EK C):** (a) her alan dolu — boş = kart eksik · (b) IZINLI ∩ YASAK = ∅ · (c) KANIT `⚠️` ise R14 research kapısında doldurulur · (d) aynı veri sınırı iki katman paylaşırsa YARGI ihlali.
5. `KANIT` alanı 3'lü format: `dosya yolu+satır | URL+tarih | ⚠️ VERIFICATION REQUIRED` (R9).

## R5 — ID Tekillik ve Yaşam Döngüsü

1. Tekillik `id-check` ile zorunlu — aynı K-ID iki kez yazılamaz.
2. Katman silinirse kayıt `deprecated` kalır; **ID asla yeniden kullanılmaz**.
3. Yeni katman = K-ID + bant + sahip + sınır + bağımlılık + kanıt olmadan **oluşmaz** (R4 dolusu eksikse OLUŞMAZ).

## R6 — Bağımlılık Yönü ve Hiyerarşi

1. **Klasik yön (ADR-096 §2 — F1 H20 + anayasa §5.1 üçlü uyum):** izinli = **alt katmanlar** (daha düşük K-ID) + port/adapter · **yasak** = üst katmana doğrudan erişim, geri çağrı (H20), katmanlar arası doğrudan veri paylaşımı (H19).
2. **Olay (event) yayını yukarı serbest**; senkron çağrı yukarı yasak.
3. **9 kademeli hiyerarşi (F2 §12):** K-Layer → Domain → Subdomain → Bounded Context → Module → Component → Service → Adapter → Implementation. Zıplama (skip) yalnız port/adapter ile.
4. Çapraz katman/kardeş ilişki: `refers-to` (doküman linki) — `değil` `depends-on`.
5. **Sınır ihlali** → derhal revert + log (anayasa §5.1) + 👤 bilgi.
6. Döngü (circular) toleransı sıfır → `dep-check` exit 1; en zayıf kenar ADR'a taşınır.

## R7 — SSOT ve Tekrar

1. Her konunun **tek canonical owner'ı** vardır; katalog/özet yalnız link + tek cümle taşır.
2. `ssot: true` yalnız konu sahibinde: `00-kspace-anayasa.md` (K-space) · `rules.md` (kurallar) · katman `index.md` (kendi katmanı) · `00-master-index.md` (giriş navigasyonu — K-space'e `ssot: false` referans verir).
3. Bağımsız/çelişkili ikinci kural kaynağı **yasak**; F1/F2 metinleri vault'a **arşiv** olarak kopyalanır, kural olarak KOPYALANMAZ (kural = bu dosya).

## R8 — Çelişki Çözümü

1. Öncelik: anayasa > ADR-096 kararları > `rules.md` > katman `index.md` > görev notu.
2. Çelişkide agent taraf tutmaz: `Kaynak A / Kaynak B / Etki / Öneri / Karar` formatı → **DUR + kullanıcıya sor** (F1 §3.4) — özellikle F1↔F2↔anayasa üçgeni.
3. Bilinen açık maddeler: **OPEN-1..3** (F2 §45-47 → vault kök CLAUDE/AGENTS/WORKFLOW mimari bölümleri) → R4 Kapı 10'da 👤.
4. ADR ile çözülmeyen kural değişikliği yapılamaz (H08/R10).

## R9 — Kaynak ve Kanıt (Zero-Hallucination)

1. **3'lü kanıt:** web (URL + tarih) · ADR (`.ai/.decisions/`) · repo grep (dosya yolu + satır).
2. **2/3** → `⚠️ VERIFICATION REQUIRED` · **1/3** → `UNKNOWN` · **0/3** → kayıt yazılmaz (H1/H2 · ADR-005).
3. `KANIT` + `kanit-tarihi` alanları zorunlu; eski kanıt `STALE` işaretlenir (R11 taraması).
4. **Hedef ≠ kanıt ayrı yazılır** (H10): "5000+ hedef" ile "X kaydın kanıtı" aynı cümlede birleştirilmez.
5. Research üssü = F1 **EK B** (37 URL, 2026-10-08); yeni iddialar EK B vault kopyasına eklenir.

## R10 — Onay Matrisi

| Değişiklik | Onay |
|---|---|
| `rules.md` · K-ID şeması · bant şeması · kayıt formatı · validator şeması · domain/bant sayısı | 👤 insan **her seferinde** (+ ADR — I6 zorunlu) |
| Katman üretimi (yeni K-ID / set / envanter) | 👤 **faz/bant başına** onay (Kapı 10) |
| `git commit` | 👤 **ayrı** açık onayı (H6 — plan onayı commit değildir) |
| Durum geçişi (PROPOSED → ACTIVE) | 👤 + kanıt (R9) |
| validator çalıştırmak | otomatik (rapor üretir, dosya değiştirmez) |
| Agent APPROVE/GOVERN | **yoktur** (yetki yalnız READ/PROPOSE/MODIFY — G2/I3) |

## R11 — Kapılar ve Denetim (F1 §7 + §11)

1. **On Kapı** bağlayıcı: [1] vault oku [2] exploration gate [3] keşif notu (`.ai/SESSIONS/`) [4] P0/P1/P2 soruları [5] intent router [6] instruction (50K) [7] web research [8] decomposition [9] üretim [10] review→test→verify→**user**.
2. Çıkış kriterleri (§7.1): KAPI 5 kimlik dolu · KAPI 6 dep ihlali YOK · **KAPI 7 §11 ≥85/100** · **KAPI 8 §9 ≥90** · KAPI 9 hallucination damgası temiz · **KAPI 10 kullanıcı onayı**.
3. Her üretim dosyası/girişimi yazımı sonrası: `node .ai/scripts/validate.mjs --check` → **exit 0** (yoksa DUR).
4. **3 başarısız düzeltme → DUR** + şüpheli varsayım + 1 kısa soru (anti-overthink).
5. Faz sonu + haftada 1 tam tarama; kapı sonucu `log.md`'ye append.

## R12 — Validator

1. `node .ai/scripts/validate.mjs [--check]` → **11 check:** `fm · link · ssot · dep · tier · orphan · id · budget · depdir · fanout · idrange`.
2. Kapsam: `.ai/*.md` (log hariç) + `.agents/ · .rules/ · .decisions/index.md` + `architecture/**` (**`prompts/` tarama dışı** — R0 keşfi). `budget` = `architecture/context.md ≤ 660`.
3. Genişletme **yalnız `validate.mjs` içinde**; yeni validator scripti yazılmaz; yeni check/arama deseni = ADR kapısı (R10/I6).
4. Uyarılar (dead-link borcu vb.) ihlal değildir ama borçtur — faz sonu raporunda sayılır.

## R13 — Sürüm

1. **Global mimari sürümü:** `00-master-index.md → version` (semver) — tek nokta.
2. `00-kspace-anayasa.md` (EK A) kendi `version`'ını taşır (anayasa eki — globalü ezmez).
3. Katman `index.md` kendi sürümünü taşır; global bump = master-index + `log.md`.

## R14 — Web Research

1. Her üretim/iddda turu F1 **§7.3** kurallarıyla: minimum kaynak sayısı §11.1 · **iddiaların ≥%80'i kaynaklı** (M11) · ikincil kaynak çapraz doğrulanır (§7.4 redleri).
2. Kayıtsız iddia sayılmaz → `⚠️` (R9.2).
3. Araştırma çıktıları EK B vault kopyasına `#N` numarasıyla kaydedilir (defter sürekliliği).
4. ADR §1.3 web araştırması 9 alan dolu olmadan ADR yayımlanamaz (şablon §6.2 — DUR sinyali).

## R15 — Yetki Seviyeleri

`READ` (serbest) → `PROPOSE` (serbest, dosya değiştirmez) → `MODIFY` (yalnız onaylı kapsam) → `APPROVE` (**yalnız 👤 insan**) → `GOVERN` (**yalnız 👤 insan**). Agent'ta APPROVE/GOVERN **yoktur**.

## R16 — Durum Etiketleri

1. **FM `status` enum'u:** `active | draft | proposed | approved | rejected | deprecated` (validator `fm-check`).
2. **Katman durumu:** `PROPOSED` (K-ID ayrıldı, içerik yok) → `ACTIVE` (özet satır + kanıt dolu, 👤 onaylı) → `DEPRECATED` (ID korunur, içerik kapatılır).
3. `IMPLEMENTED` iddiası = **repo dosya yolu kanıtı** ile; tasarım/plan seviyesi `DESIGN`/`PLANNED`.

## R17 — Mutlak Yasaklar (özet — tam metin F1 §5)

Yapay katman/Rezerve dolumu · uydurma K-ID/sayı/kanıt · tek dev md · credential (U1) · ADR'siz mimari karar (H08) · vault okumadan üretim (H09) · onaysız commit (H6) · Frozen ADR düzenleme · `SELECT *`/ORM/framework (ADR-001/002) · içerik şişirme (H1 dolgu).

## R18 — Eski Rejim (Terk Kaydı)

| Terk edilen | Yerine | Geri dönüş yolu |
|---|---|---|
| K000–K499 sabit 500 | K000–K5999 · 9 bant | `git show HEAD:.ai/architecture/` (salt-okunur) |
| 10 domain × 50 tablo (+Rezerve) | boundary-gerekçeli bant/katman üretimi | GÖREV 02 analizi (`.ai/SESSIONS/2026-10-08-kspace-kesif.md` §4) |
| Eski R1–R20 (v1.3.0) | bu dosya (v2.0.0) | git HEAD + staging arşivi |
| Eski 5-MD set + 12 alan R6 | hibrit özet + EK C 20 (R3/R4) | git HEAD |
| Eski domain tabloları d01–d10 | EK A bant şeması | `00-kspace-anayasa.md` |

## R19 — Governance ve Üretim

1. **Faz planı:** R0 keşif ✅ → R1 ADR+kurallar → R2 iskelet → R3 bant üretimi (5000+) → R4 rapor + Kapı 10 → R5 commit (ayrı onay).
2. **Üretim modu:** multi-agent — ajan başına münhasır dizin/bant; ajan yazar → yazım-onaylayan + gate (`--check`) ana oturumda; **402/ajan ölümü → inline fallback** (kesintisiz devam).
3. **Kayıt:** her faz sonu `log.md` append (J5) + `.ai/prompts/{tarih}-{slug}.md` (varsa yeni prompt).
4. OPEN-1..3, çelişki defteri ve riskler R4 raporunda 👤.
5. Eşzamanlı peer gate: her `--check` öncesi `git status --short -- .ai/architecture/` — beklenmeyen D/?? = DUR + eskale.

## R20 — Devam (Continuation) Protokolü

1. Kesintide: `PART N/M` + son doğrulanmış bölüm; REQUIrement/K/DEPENDENCY/CONFLICT/EVIDENCE/MIGRATION state'i **asla atılmaz** (F1 §3.6).
2. "devam" gelirse doğrulanmış bölümü tekrarlamadan sürdür; baştan üretim yasak.
3. Snapshot zorunlu alanlar: son doğrulanmış K · son bant · açık çelişkiler · açık bilinmeyenler · sonraki K-aralığı · grafik durumu.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-08
**Mode:** Red Team · Human Mode · Truth Mode