---
node_id: "K009-security/threat-model/threat-model-web-arastirma"
node_type: "module"
status: "verify-required"
parent_id: "K009-security"
children: []
depends_on: []
evidence:
  - "7 kaynak URL'si 2026-10-15'te canlı doğrulandı (6'sı doğrudan HTTP 200; Microsoft SDL bot engeli nedeniyle browser User-Agent ile 200)"
validation: "7/7 URL canlı; her iddia §2'de Kod Xref ile CoreMusic yüzeyine bağlandı; ⚠️ 0 — sahte iddia yok"
open_questions: []
group: "kanit"
---

# K009 · threat-model · Web Araştırması

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Web Araştırması: tehdit-modelleme çerçeveleri, gizlilik mühendisliği, savunma kataloğu ve güvenli serileştirme

---

## §1 DOĞRULAMA TABLOSU (canlı kaynaklar — 2026-10-15)

> Kural: bu dosya **yalnızca** aşağıdaki 7 URL'yi taşır; `threat-model/index.md` ve kök web-arastırma dosyalarındaki
> OWASP Cheat Sheet (Threat Modeling), Secure Product Design, attack.mitre.org ve NIST 800-30 r1 URL'leri **tekrarlanmaz**.

| # | İddia (kaynak ne diyor) | URL | Tarih: 2026-10-15 | Durum |
|---|---|---|---|---|
| 1 | OWASP Threat Modeling Process: 4 temel soru (ne korunuyor, ne tehdit ediyor, ne yapılabilir, ne yapıldı?) + yaklaşımlar (data flow, attack trees, abuse cases); sayfa başlığı "Threat Modeling Process (Historical)" — OWASP bu sayfayı arşiv/historical olarak işaretlemiş | https://owasp.org/www-community/Threat_Modeling_Process | Tarih: 2026-10-15 | HTTP 200 |
| 2 | OWASP Threat Dragon: açık kaynak, masaüstü + web tehdit-modelleme aracı; DFD çizimi, STRIDE sınıflandırması, Apache 2.0 lisansı | https://www.owasp.org/www-project-threat-dragon/ | Tarih: 2026-10-15 | HTTP 200 |
| 3 | LINDDUN: privacy engineering için tehdit-modelleme çerçevesi — 7 sınıf (Linkability, Identifiability, Non-repudiation, Detectability, Disclosure, Unawareness, Non-compliance); STRIDE'ın gizlilik eşleniği | https://www.linddun.org/ | Tarih: 2026-10-15 | HTTP 200 |
| 4 | MITRE D3FEND: savunma tekniklerinin ontolojik kataloğu — "D3FEND Matrix"; her savunma tekniği bir saldırgan tekniğine (D3-ATLAS/ATT&CK) karşılık gelir | https://d3fend.mitre.org/ | Tarih: 2026-10-15 | HTTP 200 |
| 5 | NIST Privacy Framework v1.0: gizlilik risklerini işlevsel kategorilerle (Identify-P, Govern-P, Control-P, Communicate-P, Protect-P) yönetir; NIST Cybersecurity Framework'un gizlilik kardeşi | https://www.nist.gov/privacy-framework | Tarih: 2026-10-15 | HTTP 200 |
| 6 | Microsoft Security Development Lifecycle (SDL): 7 aşamalı güvenlik-mühendisliği süreci (Training → Requirements → Design → Implementation → Verification → Release → Response); tehdit modelleme "Design" aşamasında zorunlu | https://www.microsoft.com/en-us/securityengineering/sdl | Tarih: 2026-10-15 | HTTP 200 (not §3a) |
| 7 | OWASP Deserialization Cheat Sheet: güvenli olmayan serileştirme savunması — veri bütünlüğü doğrulaması, imzalı objeler, allowlist tip kontrolü, `unserialize()` yerine JSON gibi güvenli formatlar | https://cheatsheetseries.owasp.org/cheatsheets/Deserialization_Cheat_Sheet.html | Tarih: 2026-10-15 | HTTP 200 |

### §1a HAVUZ ÇAPRAZ MATRİSİ — index.md'deki 4 KAYIT (iddia × durum × tarih)

> **URL tekrar yazılmaz (havuz tekrar yasağı):** `security-web-arastirma.md` §14 "Tek URL = tek dosya" kuralı ve bu dosyanın §1 kuralı gereği
> aşağıdaki 4 kaydın URL satırı buraya kopyalanmaz; kayıt yeri `threat-model/index.md` URL tablosudur (2026-10-13 ölçümü).
> Bu 4 kayıt bu dosyanın **7 kaynağından ayrıdır** — yeni URL eklenmedi (2026-10-15 tur-2: ekleme 0).

| # | Kaynak (katalog) | İddia — kayıtta ne diyor | URL kayıt yeri | Durum | Tarih |
|---|---|---|---|---|---|
| H-1 | OWASP Threat Modeling Cheat Sheet | tehdit modelleme süreci + STRIDE uygulaması — `index.md` §2 STRIDE ↔ K009 çocuk eşlemesinin dış kaynağı | `threat-model/index.md` URL tablosu (satır 1) | HTTP 200 | 2026-10-13 |
| H-2 | OWASP Secure Product Design Cheat Sheet | güvenli ürün tasarımı — tasarım aşamasında tehdit odaklı üretim kuralı | `threat-model/index.md` URL tablosu (satır 2) | HTTP 200 | 2026-10-13 |
| H-3 | MITRE ATT&CK (attack.mitre.org) | tehdit yüzeyi / saldırı teknikleri referansı — `index.md` evidence: "v2 §2.9 satır 327 — STRIDE + attack.mitre.org / CWE eşlemesi" | `threat-model/index.md` URL tablosu (satır 3) | HTTP 200 | 2026-10-13 |
| H-4 | NIST SP 800-30 r1 | risk = olasılık × etki puanlaması — anayasa §4 (§1 satır 37 "NIST 800-30 r1 puanı") | `threat-model/index.md` URL tablosu (satır 4) | HTTP 200 | 2026-10-13 |

| Matris Kuralı | |
|---|---|
| Durum/tarih | bu dosyanın iddiası DEĞİL — index.md 2026-10-13 ölçümünün **naklidir** (2026-10-15'te yeniden fetch edilmedi → tarih eskitilmez) |
| Yeni URL | eklenmedi; eklenecekse webfetch ile 2026-10-15 tarihli doğrulanır ve `security-web-arastirma.md` §14 havuzunda olmadığı + tahsis edilmediği görülür |
| §3c ilişkisi | §3c aynı kümeyi "tekrar yok" diye taşır; §1a farkı: iddia + durum + tarih sütunları |

---

## §2 KOD XREF (her kaynak → CoreMusic yüzeyi — Dosya·Satır)

| # | Kaynak iddiası | CoreMusic karşılığı | Dosya·Satır / durum |
|---|---|---|---|
| 1 | 4 temel soru (neyi koru / tehdit / yap / kanıtla) | anayasa §5 zinciri Z-1..Z-5 + §1 soru satırı | `threat-model-anayasa.md` §1, §5 — süreç birebir aynı soru seti |
| 2 | Threat Dragon / DFD çizimi | **DFD diskte YOK** → anayasa §12 tablo ikamesi | anayasa U-1 (`⚠️ VERIFICATION REQUIRED`) — DFD üretilmeden "tamamlandı" denmez |
| 3 | LINDDUN gizlilik sınıfları | K009 kapsamı gizlilik **sınırlı**: V-1 session cookie, V-2 pepper, V-8 log redaction | anayasa §11 · R-07/R-09 (privacy eşlenikleri: linkability→cookie domain, disclosure→log) — LINDDUN tam uygulama K009 kapsam dışı (bkz kapsam §2) |
| 4 | D3FEND savunma eşlemesi | anayasa §13 tehdit → K009 çocuk eşlemesi | `threat-model-anayasa.md` §13 (12 satır) — D3FEND mantığı: her tehdit tek savunma tekniğine bağlanır |
| 5 | NIST Privacy Framework | anayasa §4 skorlama NIST 800-30 r1 tabanlı (index.md §1); Privacy Framework ayrı | skorlama: anayasa §4; gizlilik işlevi (Control-P/Protect-P eşleniği) K009'da: session/secrets çocukları |
| 6 | Microsoft SDL (tehdit modelleme zorunlu) | anayasa §7 tetikleyicileri (yeni uç/şema/middleware → modele alma zorunlu) | `threat-model-anayasa.md` §7 — SDL "Design aşamasında tehdit modelleme" kuralının karşılığı |
| 7 | Güvenli serileştirme (unserialize yasak, imzalı/allowlist) | global kural: `unserialize(user_input)` yasak; R-06 enjeksiyon sınıfı | repo'da `unserialize` yüzey taraması **bu modellemede yapılmadı** (⚠️) — §3.2-7 sınıf 7 için yüzey kanıtı yok (ornekler §3) |

---

## §3 NOTLAR (doğrulama sırasında tespit)

### §3a Microsoft SDL erişim notu

- Doğrudan sunucu tarafı fetch **HTTP 403** (bot engeli) döndürdü; **browser User-Agent** ile **HTTP 200** alındı,
  sayfa başlığı: `Microsoft Security Development Lifecycle`. Kaynak kullanılabilir; tekrar doğrulamada UA gerekir.

### §3b OWASP Threat Modeling Process "Historical" etiketi

- Sayfa başlığı `Threat Modeling Process (Historical) | OWASP Foundation` — OWASP bu topluluk sayfasını arşiv
  olarak işaretlemiş. Süreç içeriği (4 soru, yaklaşımlar) hâlâ geçerli referanstır ama **güncel OWASP konumu
  Cheat Sheet Series'tir** — o da `threat-model/index.md`'dedir (bu dosyada tekrarlanmaz).

### §3c Kesişen kataloglar (tekrar yok)

| Katalog | Nerede | Bu dosyadaki durum |
|---|---|---|
| OWASP Threat Modeling Cheat Sheet | `threat-model/index.md` | tekrar YOK (aynı URL burada yazılmaz) |
| OWASP Secure Product Design | kök security-web-arastırma | tekrar YOK |
| attack.mitre.org (ATLAS) | `threat-model/index.md` | tekrar YOK — D3FEND (kaynak 4) ATT&CK'nin savunma ikizi olduğu için ayrıca taşındı |
| NIST 800-30 r1 | `threat-model/index.md` §1 (skorlama kaynağı) | tekrar YOK — Privacy Framework (kaynak 5) ayrı çerçeve |

### §3d ERİŞİM KAYDI (hangi URL hangi tarihte, kaç kez erişildi)

> Amaç: aynı URL'nin "kaç kez / ne zaman" erişildiğini tek yerde tutmak — durum/tarih iddialarının
> kaynağını görünür kılmak. **Yeni URL eklenmez** (§1 yasağı); tablo yalnız erişim olaylarını kaydeder.

| Erişim olayı | Tarih | Kapsam | Sonuç |
|---|---|---|---|
| Tur-1 — index.md URL doğrulaması | 2026-10-13 | `threat-model/index.md` URL tablosu (4 kayıt: H-1..H-4) | 4/4 HTTP 200 |
| Tur-2 — bu dosyanın 7 kaynağı | 2026-10-15 | §1 tablosu (kaynak 1..7) | 7/7 HTTP 200 — 6'sı doğrudan, kaynak 6 (Microsoft SDL) bot engeli nedeniyle browser UA ile 200 (§3a) |
| Tur-2 — yeni URL ekleme | 2026-10-15 | havuz (`security-web-arastirma.md` §14) | **ekleme 0** — tekrar yok, tahsis yok |
| Tur-2 — index 4 kaydın yeniden fetch'i | 2026-10-15 | H-1..H-4 | **yapılmadı** — durum/tarih 2026-10-13 naklidir, eskitilmedi (§1a Matris Kuralı) |

| Erişim Kuralı | |
|---|---|
| Sayım | bir satır = bir erişim olayı (tur); URL başına istek sayısı ayrı sayaç tutulmaz — `UNKNOWN` |
| UA zorunluluğu | kaynak 6 (Microsoft SDL) her yeniden erişimde browser User-Agent ister (aşağıdaki §6) |
| Kanıt hiyerarşisi | bu tablo erişim *kaydı*dır; iddia metni §1, kod bağlamı §2'dedir — üçü birlikte okunur |

---

## §4 AÇIK KONULAR

| # | Konu | Etiket |
|---|---|---|
| W-1 | LINDDUN sınıflarının K009'a tam uygulanması (gizlilik tehdit modeli ayrı mı?) | Vault Steward kararı — kapsam §2 hariç maddesi |
| W-2 | DFD (Threat Dragon formatı) üretilmesi | anayasa U-1 ile aynı kayıt |
| W-3 | `unserialize` yüzey taraması (kaynak 7) | §3.2-7 sınıfı — security-testing çocuğuna devir |

---

## §5 KARDEŞ DOSYA BAĞI (bu dosyanın K009 içindeki yeri)

| Kardeş | Bu dosyayla ilişkisi | Kanıt yönü |
|---|---|---|
| [[threat-model-anayasa]] | §1 4 soru + §5 zinciri Z-1..Z-5 → kaynak 1; §7 tetikleyiciler → kaynak 6 (SDL) | kaynak → anayasa (§2 satır 1, 6) |
| [[threat-model-surucu]] | Örnek yürütmeyi sürer; tur-2 kanıt tazeleme kaydı bu dosyanın 2026-10-15 okumalarıyla hizalıdır | kardeş → kanıt (surucu §11) |
| [[threat-model-ornekler]] | Ör-07..Ör-12 kod örnekleri bu dosyadaki kaynakların CoreMusic yüzeyindeki karşılığıdır | kaynak → örnek (§2) |
| [[threat-model-kapsam]] | LINDDUN tam uygulamasının kapsam dışı olması kapsam §2'de kayıtlıdır (kaynak 3) | kaynak → kapsam (§2 satır 3) |
| [[threat-model-dogrulama]] | AD-12..AD-17 doğrulama adımları bu dosyanın kod xref'iyle beslenir | kardeş → kanıt |
| [[threat-model-bilesenler]] | Middleware envanteri (11 dosya / 10 sınıf) §2 kod xref'inin yüzey listesidir | kardeş → envanter |
| [[security-riskler]] | R-01..R-14 ile hizalama: her kaynak iddiası bir risk sınıfına bağlanır (§2) | kaynak → risk |
| [[security-web-arastirma]] | §14 havuz haritası bu dosyanın 7 URL tahsisini teyit eder; tekrar yasağı oradan gelir | havuz → bu dosya |

**Bağıl kural:** bu dosya tek başına "güvenlik araştırması" değildir — 7 kaynak **yalnız** K009
tehdit-modelleme çocuklarıyla (yukarıdaki 8 kardeş) birlikte anlam taşır; kök `security-web-arastirma.md`
genel havuzu, bu dosya ise threat-model'e tahsisli dilimidir.

---

## §6 YENİDEN ERİŞİM / YENİLEME KURALI

| # | Kural | Ayrıntı |
|---|---|---|
| 1 | Yenileme tetiği | §1'deki bir durum/tarih **eskiyorsa** (kanıt tazeleme turu, ADR değişikliği, `security-riskler.md` R-xx güncellemesi) URL yeniden fetch edilir |
| 2 | Tarih güncelleme | Yeniden fetch edilen satırın tarihi **gerçek erişim günü**ne çekilir; fetch edilmeyen satırın tarihi **eskisinde kalır** (eskitme yasağı — §1a Matris Kuralı) |
| 3 | UA gereksinimi | Microsoft SDL (kaynak 6) sunucu tarafı fetch'te HTTP 403 döndürür — yenilemede **browser User-Agent** kullanılır (§3a) |
| 4 | Yeni URL ekleme | **Yasak** (§1). Eklenecekse sırasıyla: (a) `security-web-arastirma.md` §14 havuzunda olmadığı, (b) tahsis edilmediği, (c) webfetch ile o gün doğrulandığı görülür; sonra §1 tablosuna satır eklenir |
| 5 | Ölü kaynak | Bir URL HTTP 404/410 dönerse satır **silinmez** — durum sütunu `DEAD (erişim: YYYY-MM-DD)` olur ve §4 Açık Konular'a W-kaydı açılır |
| 6 | Kapsam sınırı | Yenileme yalnız §1'deki 7 URL'yi ve §1a'daki 4 index kaydını kapsar; kardeş/kök dosyaların URL'leri bu dosyada yenilenmez (tekrar yasağı) |

---

## § BAĞLANTILAR

- [[threat-model-anayasa]] · [[threat-model-surucu]] · [[threat-model-ornekler]] · [[threat-model-kapsam]] · [[threat-model-dogrulama]] · [[threat-model-bilesenler]] · [[index]]
- [[security-riskler]] · [[security-web-arastirma]] · [[../security-testing/index]]

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
