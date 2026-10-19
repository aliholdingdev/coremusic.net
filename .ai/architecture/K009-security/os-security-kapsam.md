---
node_id: K009-security/os-security-kapsam
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-kapsam
  - K000-isletim-sistemi/isletim-sistemi-security
evidence:
  - "K009-security/index.md §4 satır 82 (OS düzeyi güvenliği kapsam dışı — sahibi K000)"
  - "K000-isletim-sistemi/isletim-sistemi-security/index.md satır 44–49, 62–73 (3 alt-dal × 8 OS kartı = 24 kart + kod-ornekleri 15 kart)"
  - "K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-kapsam.md §3.1 satır 153–166 (C1–C12 saldırı sınıfı matrisi)"
  - "K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-kod-seti.md — 706 satır (index.md satır 20, 60 kanıtı)"
validation:
  - "2026-10-15 — kullanıcı kararı (ikisi birlikte): K000'e 8 platform `<os>-guvenlik.md` + K009'a bu sınır köprüsü (dosyası)"
  - "2026-10-15 — 8 platform `<os>-guvenlik.md` paralel üretimde (aynı kararın K000 tarafı); §3.2 Durum = planlandı, wiki-link'ler kasıtlı ileri bağlantı"
  - "2026-10-15 — bu dosya 0 harici URL kullanır (K009 URL havuzu tekrarı 0); kanıt = disk (Dosya·Satır) + wiki-link"
open_questions:
  - "K000 S2 (uygulama ↔ OS sorumluluk sınırı) bu köprü ile BELGELENDİ ama kapanış kriteri (sınır matrisi onayı) hâlâ açık — K000'de kalır"
  - "K000 S1 (OS katmanı devreye alınacak mı) bu dosyadan bağımsızdır — [UNKNOWN] K000'de kalır, bu köprü kapatmaz"
group: mimari
---

# K009 · os-security-kapsam · İşletim Sistemi Güvenliği Sınırı

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

> **Kapsam:** Bu dosya, K009-security kökünün "işletim sistemi düzeyi güvenlik" satırının
> **sınır köprüsüdür** — OS düzeyi güvenliğin sahibi `K000-isletim-sistemi/security`'tir; K009 bu
> yüzeyi **kapsam dışı** ilan eder ve yalnız sınır referansını taşır.

## §1 Neden bu dosya

| # | Gerçek | Kanıt (disk) |
|---|---|---|
| 1 | K009 kök index §4 sınır tablosu, "İşletim sistemi düzeyi güvenlik (kernel, disk, servis)" satırını **kapsam dışı** ilan eder; sahibi `K000-isletim-sistemi/security`'tir. | `index.md` · satır 82 |
| 2 | K009 kapsam dosyası bu konuyu yalnız negatif listede anar (ayıntı yok). | `security-kapsam.md` · §KAPSAM-DIŞI |
| 3 | K000 tarafında konunun tam sahibi bir subdomain vardır: 3 alt-dal (cekirdek-guvenlik · yetki-ve-erisim-modeli · uygulama-guvenlik-siniri) × 8 OS kartı = 24 kart + kod-ornekleri 15 kart + kök kod-seti (706 satır). | `isletim-sistemi-security/index.md` · satır 44–49, 58, 60, 62–73 |
| 4 | **Kullanıcı kararı (2026-10-15, ikisi birlikte):** (a) K000-isletim-sistemi-turu'ndaki 8 platforma `<os>-guvenlik.md` üretilir; (b) K009 köküne bu **sınır köprüsü** yazılır. Bu dosya kararın (b) tarafıdır. | görev manifesti (orkestratör eliyle) |
| 5 | Bu köprü olmadan K009 kök index'indeki "yalnız sınır referansı" ifadesi **hedefsiz** kalır — okur nereye gideceğini bilemez. | `index.md` · satır 82, 84 |

**Bu dosyanın işi (3 madde):**

| # | İş | Değil |
|---|---|---|
| J1 | K000↔K009 sınırını **tek yerde** cümleleştirir (devir kuralları, §4) | Sınır kararını yeniden üretmez — K000'deki C1–C12 matrisini **linkler**, kopyalamaz |
| J2 | K000 tarafı envanterini K009 kökünden görünür kılar (§3) | K000 dosyalarını düzenlemez, taşımaz, özetlemez |
| J3 | K009 okuruna "şu konu benim değil, şuraya git" der (§2, §4) | OWASP denetimini, kripto seçimini, CSRF/rate-limit tasarımını **etkilemez** |

## §2 Sorumluluk matrisi — 12 saldırı sınıfı × 2 katman

> **Tekrar yasağı:** Sınıf listesi ve katman dağılımı K000 tarafında **çoktan yazılmıştır**
> (`isletim-sistemi-security-kapsam.md` §3.1 satır 153–166). Bu tablo o matrisi **tekrarlamaz**;
> her satır yalnız "bu sınıfın hangi katmanda durduğu ve köprünün ne söylediği"ni taşır.
> Ayrıntı (OS savunması mekanizması, uygulama savunması kanıtı, repo durumu) → ilgili K000 dosyası.

| # | Saldırı sınıfı (kaynak: K000 kapsam §3.1) | Sahip katman | Köprü cümlesi (K009 tutumu) |
|---|---|---|---|
| C1 | Dizin traversal (CWE-22) | OS (K000) + uygulama (K009 kanıt yüzeyi) | `realpath` kanıtı uygulama katmanındadır; traversal'ın **OS kısıtı** (namespace/Landlock) K000'dir — K009 yalnız uygulama doğrulamasını denetler |
| C2 | Komut enjeksiyonu (CWE-78) | OS (K000) + uygulama (K009) | `escapeshellarg` kanıtı uygulama katmanında; syscall filtreleme (seccomp) K000'dir — K009'a taşınmaz |
| C3 | Yetki yükseltme | OS (K000) + uygulama (K009) | BypassAuth fail-closed kanıtı uygulama katmanında; capability/setuid denetimi K000'dir |
| C4 | Clickjacking / MIME sniffing | Uygulama (K009) — OS dışı sınıf | Header üretimi (X-Frame-Options, nosniff) K009 kapsamındadır; K000 bu sınıfı almaz |
| C5 | CSRF (CWE-352) | Uygulama (K009) — OS dışı sınıf | Token + `hash_equals` tamamen K009'dur; K000'e devri YASAK |
| C6 | Kaba kuvvet / rate limit | Uygulama (K009) + ağ (K013) | APCu rate limit K009'dur; netfilter/conntrack karşılığı ağ katmanındadır — OS security dalının konusu değil |
| C7 | Süreç hafızası ifşası | OS (K000) — uygulama katmanı savunmasız | ptrace yasağı / dumpable bit K000'dir; K009'da karşılığı YOK |
| C8 | Dizin izni gevşekliği (777) | Her iki katman | `mkdir 0750` uygulama çağrısıdır; POSIX/DAC çekirdek denetimi K000'dir — köprü iki katmanı da işaretler |
| C9 | Bilgi sızıntısı başlıkları | Uygulama (K009) | CSP nonce, Referrer-Policy, `Server` unset → K009 (`security-master.md` §10–§14) |
| C10 | Kötü niyetli yerel kullanıcı | OS (K000) — uygulama katmanı savunmasız | MAC/capability/namespace K000'dir; K009 bu sınıfı savunmaz, savunamaz |
| C11 | Yüksek yetkili rol yanlış kullanımı | Uygulama (K009) + OS (K000) | RBAC kararı (`requiredRole`/`requiredPermission`) K009'dur; UID/GID+ACL denetim akışı K000'dir |
| C12 | Geçersiz origin/CORS genişlemesi | Uygulama (K009) | Origin allow-list + tam host eşleşmesi K009'dur; OS katmanında karşılığı yok |

| Köprü kuralı | Açıklama |
|---|---|
| K-1 | Tablodaki "Sahip katman" sütunu **K000 kapsam §3.1 matrisinin yeniden yazımı değildir** — okur ayrıntı için K000 dosyasına gider |
| K-2 | "Uygulama (K009)" işareti, sınıfın OWASP denetiminin K009'da olduğunu; "OS (K000)" işareti ise mekanizma/kısıt sahibinin K000 olduğunu söyler |
| K-3 | C7 ve C10'da uygulama katmanı **savunmasızdır**; bu, K009'un eksiği değil, sınırın gerçeğidir (K000 kapsam §3.3 boşluk tablosu) |

### §2.1 Hızlı karar akışı (okur hangi dosyaya gider?)

Bir güvenlik konusu elinize geldiğinde bu sırayı izleyin; ilk eşleşmede durun:

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Konu çekirdek/dosya sistemi/servis/süreç kısıtı mı? (kernel, disk, servis, sandbox, patch) | **K000** → `isletim-sistemi-security/` (§3.1–§3.3) — K009'a gelmeyin | 2'ye geçin |
| 2 | Konu parola/CSRF/rate-limit/session/CSP/OWASP mı? | **K009** → ilgili çocuk dal (§4.2) — K000'e gitmeyin | 3'e geçin |
| 3 | Konu kimlik modeli / kullanıcı varlığı mı? | **K0100-identitiy** | 4'e geçin |
| 4 | Konu ağ (firewall, DNS, TLS sonlandırma) mı? | **K013-integraion** | 5'e geçin |
| 5 | Konu hâlâ sınırda mı (iki katman da dokunuyor)? | Bu dosya §4'teki devir kurallarına başvurun; karar yoksa **orkestratöre eskalasyon** | Konu sahibi dalın dosyasında çözülür |

| Akış kuralı | Açıklama |
|---|---|
| A-1 | Bu akış bir **yönlendirme** aracıdır; hiçbir adımda karar üretilmez |
| A-2 | 1. ve 2. adım tek başına %90 soruyu çözer; 3–5 yalnız sınır vakaları içindir |

## §3 K000 tarafı envanteri (köprüden görünüm)

### §3.1 Alt-dal envanteri — `isletim-sistemi-security/` (diskte MEVCUT)

| Öğe | Ne içerir | Durum | Kanıt |
|---|---|---|---|
| `cekirdek-guvenlik/` | anayasa · sürücü · örnekler + 8 OS kartı — koruma halkaları, güvenli önyükleme, mitigasyon, sürücü sınırı | MEVCUT (12 dosya) | `isletim-sistemi-security/index.md` · satır 46 |
| `yetki-ve-erisim-modeli/` | anayasa · sürücü · örnekler + 8 OS kartı — DAC/MAC/RBAC/BEL, ACL, capability, denetim akışı | MEVCUT (12 dosya) | `isletim-sistemi-security/index.md` · satır 47 |
| `uygulama-guvenlik-siniri/` | anayasa · sürücü · örnekler + 8 OS kartı — syscall yüzeyi, sandbox türleri, konteyner kısıtları | MEVCUT (12 dosya) | `isletim-sistemi-security/index.md` · satır 48 |
| `kod-ornekleri/` | 15 kart: 7 dil kartı (php · python · js · rust · csharp · c · go) + 8 OS kartı | MEVCUT (15 kart; index.md YOK) | `isletim-sistemi-security/index.md` · satır 49, 56 |
| OS kartları toplamı | 3 alt-dal × 8 platform = **24 kart** (`group: kod`, 150–260 satır) | MEVCUT | `isletim-sistemi-security/index.md` · satır 62–73 |
| `isletim-sistemi-security-kod-seti.md` | cross-platform izin/sandbox/kaynak limiti kod seti (GRUP A/B/C) | MEVCUT — **706 satır** | `isletim-sistemi-security/index.md` · satır 20, 60 |

> Toplam: 5 kök + 36 alt-dal + 15 kod-ornekleri = **56 dosya** (2026-10-14 sayımı — K000 index'i satır 58).

### §3.2 8 platform `<os>-guvenlik.md` (2026-10-15 kararı — paralel üretimde)

> **Üretim notu:** Bu 8 dosya 2026-10-15 kullanıcı kararının paralel iş kolları olarak
> **şu anda üretilmektedir** (başka subagent'lar). Bu köprüdeki wiki-link'ler kasıtlı **ileri bağlantılardır**;
> Durum sütunu üretim bitene kadar `planlandı` kalır, dosyalar tamamlandıkça orkestratör
> `MEVCUT` olarak günceller.

| # | Dosya | Platform dizini | Beklenen rol (karar bağlamı) | Durum |
|---|---|---|---|---|
| 1 | [[windows/windows-guvenlik]] | `isletim-sistemi-turu/windows/` | platform-özel OS güvenlik yüzeyi (Win32/NT) | planlandı (2026-10-15) |
| 2 | [[linux/linux-guvenlik]] | `isletim-sistemi-turu/linux/` | platform-özel OS güvenlik yüzeyi (capabilities/seccomp/LSM) | planlandı (2026-10-15) |
| 3 | [[macos/macos-guvenlik]] | `isletim-sistemi-turu/macos/` | platform-özel OS güvenlik yüzeyi (Darwin/XNU) | planlandı (2026-10-15) |
| 4 | [[ios/ios-guvenlik]] | `isletim-sistemi-turu/ios/` | platform-özel OS güvenlik yüzeyi (sandbox/profile) | planlandı (2026-10-15) |
| 5 | [[android/android-guvenlik]] | `isletim-sistemi-turu/android/` | platform-özel OS güvenlik yüzeyi (SELinux/sandbox) | planlandı (2026-10-15) |
| 6 | [[tizen/tizen-guvenlik]] | `isletim-sistemi-turu/tizen/` | platform-özel OS güvenlik yüzeyi | planlandı (2026-10-15) |
| 7 | [[webos/webos-guvenlik]] | `isletim-sistemi-turu/webos/` | platform-özel OS güvenlik yüzeyi (Luna/service) | planlandı (2026-10-15) |
| 8 | [[reactos/reactos-guvenlik]] | `isletim-sistemi-turu/reactos/` | platform-özel OS güvenlik yüzeyi (Wine/Win32) | planlandı (2026-10-15) |

| Kural | Açıklama |
|---|---|
| P-1 | Bu 8 dosya üretilse bile **K000-isletim-sistemi-security alt-dallarının yerine geçmez**; onlar 24 OS kartıyla çapraz kalır |
| P-2 | `<os>-guvenlik.md` dosyaları K009'a **içerik devretmez** — köprü yalnızca yönlendirme yapar |
| P-3 | Bu tablonun wiki-link'leri **kasıtlı ileri bağlantıdır** (paralel üretim); Durum `planlandı` iken "MEVCUT" veya "tamamlandı" yazmak yasaktır |

### §3.3 K000 güvenlik kök dosyaları (K009 okuru için giriş noktaları)

| Dosya | Ne verir | group |
|---|---|---|
| `isletim-sistemi-security/index.md` | klasör künyesi, 4 çocuk + kod-ornekleri, komşu sınır matrisi, S1/S2 | mimari |
| `isletim-sistemi-security-kapsam.md` | 12 saldırı sınıfında katman ayrım matrisi (§3.1), sınır komşuları (§5), S1/S2 (§7) | mimari |
| `isletim-sistemi-security-dogrulama.md` | 68 URL (HTTP 200) + 43 disk kanıtı + negatif kanıt defteri | kanit |
| `isletim-sistemi-security-ornekler.md` | 18 gerçek repo kod örneği (Dosya + satır) | kod |
| `isletim-sistemi-security-kod-seti.md` | 706 satır — izin/sandbox/kaynak limiti kod seti | kod |

## §4 Devir kuralları (net sınır — iki yönlü yasak listesi)

### §4.0 Tek bakışta — bağlayıcı yasak özeti (2026-10-15 kararı)

| Yön | YASAK olan konular (bu listekiler K000'de kalır) | Bağlayıcı cümle |
|---|---|---|
| K000 → K009 | **kernel · disk · servis · sandbox · patch** — çekirdek mekanizması, dosya/servis kısıtları, sandbox politikası, yama yönetimi asla K009 köküne veya çocuklarına taşınmaz | "Bu beş konu K000-isletim-sistemi/security'nin tekelindedir; K009 bunları **kapsam dışı** ilan eder (kök index §4, satır 82)" |
| K009 → K000 | **parola · CSRF · rate-limit · session** — parola hash'i/pepper, CSRF token, rate limit politikası, oturum yaşam döngüsü asla K000 security dosyalarına taşınmaz | "Bu dört konu K009 çocuklarının tekelindedir; K000 yalnız **OS dosya/süreç kısıtını** tutar (oturum dosyasi izni 0750 gibi) — K000 index §5.3–§5.4 ile uyumlu" |

> Bu özet §4.1–§4.2'nin **tek satırlık kısaltmasıdır**; çelişki durumunda §4.1–§4.2 tabloları
> (D1–D12) otoritedir. Özet, günlük hızlı kontrol içindir — yeni madde bu özetten eklenmez.

### §4.1 K009'a TAŞINMAZ — bunlar K000'de kalır

| # | Konu | Sahibi | Bağlayıcı sınır cümlesi |
|---|---|---|---|
| D1 | Çekirdek mekanizması (LSM, seccomp yorumlayıcısı, capability çekirdeği, namespace/cgroup) | `isletim-sistemi-security/cekirdek-guvenlik` + `isletim-sistemi-kernel` | "Çekirdek mekanizmasının kullanımı modeli K000'de; K009 bu mekanizmayı **seçmez, kurmaz, denetlemez**" |
| D2 | Disk/servis yüzeyi (dosya sistemi izin denetimi, servis çalıştırma kullanıcıları, umask/ACL) | `yetki-ve-erisim-modeli` | "Dizin izni `0750`'nin **OS denetimi** K000'dedir; K009 yalnız uygulama çağrısını kanıt olarak okur" |
| D3 | Sandbox / konteyner kısıtları (bubblewrap, Job Object, sandbox-exec, namespace izolasyonu) | `uygulama-guvenlik-siniri` + `isletim-sistemi-runtime` | "Sandbox kurulumu ve politika yazımı K000/runtime'dır; K009 sandbox **yazmaz**" |
| D4 | Yama/patch yönetimi, güvenli önyükleme, mitigasyon çerçevesi (SMEP/SMAP/KASLR) | `cekirdek-guvenlik` | "Patch ve önyükleme güvenliği K000'dir; bağımlılık güncelliği (A06) K009'da ayrı konudur (tarama K018)" |
| D5 | Yetki denetim akışı (syscall → VFS → DAC → ACL → MAC) | `yetki-ve-erisim-modeli` | "Denetim akışının sahibi OS katmanıdır; uygulama RBAC bu akışın **üstünde** çalışır" |
| D6 | OS katmanının devreye alınması kararı (S1) | mimari sahibi + ADR `>=088` | "K009 bu kararı **beklemez ve üretmez**; S1 [UNKNOWN] K000'de kalır" |

### §4.2 K000'e TAŞINMAZ — bunlar K009'da kalır

| # | Konu | Sahibi | Bağlayıcı sınır cümlesi |
|---|---|---|---|
| D7 | Parola (Argon2id + pepper), kimlik kanıtı kabulü | K009 `encryption` / `authentication` (+ K010 kimlik modeli) | "Parola hash'i ve doğrulama akışı K009'dur; OS katmanı yalnız dosya/süreç kısıtı verir" |
| D8 | CSRF token üretimi + `hash_equals` doğrulaması | K009 `session-security` | "CSRF OS dışı sınıftır (C5); K000'e devri YASAK" |
| D9 | Rate limit politikası (sayaç, 429, brute-force) | K009 `rate-limiting` | "Rate limit'in uygulama katmanı K009'dur; ağ karşılığı K013'tür — K000 security dalı **değildir**" |
| D10 | Session yaşam döngüsü (cookie bayrakları, fixation, ID yenileme) | K009 `session-security` (+ K010 kavramı) | "Oturum iş mantığı K009/K010'da; K000 yalnız oturum dosyasının OS iznini (0750) tutar" |
| D11 | CSP / security header üretimi, Origin/CORS allow-list | K009 `session-security` / `authorization` | "Başlık üretimi uygulama katmanındadır; `.htaccess` kanıtı K000 kapsam dosyalarında **okunur**, K009'a karar olarak taşınmaz" |
| D12 | OWASP Top 10 denetimi, kripto algoritma seçimi (ADR-010/013/022) | K009 (kök + çocuklar) | "OWASP denetimi ve kripto seçimi bu dalın tekelindedir; K000 yalnız **sinir etkisini** işaretler" |

| Devir kuralı | Açıklama |
|---|---|
| T-1 | Sınır **iki yönlü tek satırlık cümledir**: "OS kısıtı K000'de, uygulama kararı K009'da" — bu cümle K000 security index §5.3–§5.4 ile birebir uyumludur |
| T-2 | Sınır ötesi her iddia ilgili dalın dosyasında kanıt bekler; bu köprü **kanıt üretmez**, yalnız **yönlendirir** |
| T-3 | Bu dosyadan bir mekanizma tanımı, politika kararı veya ADR çıkarılmaz; çıkan olursa ihlal kaydı açılır ve içerik sahibi dala taşınır |

## §5 Çapraz referanslar (BAĞLANTILAR — tüm hedefler diskte MEVCUT)

| # | Hedef dosya (diskte var) | Ne verdiği | Bu köprüdeki karşılığı |
|---|---|---|---|
| X1 | `K009-security/index.md` | §4 sınır tablosu (satır 82): OS düzeyi güvenliği kapsam dışı, sahibi K000 | §1 gerçek #1 — köprünün dayanağı |
| X2 | `K009-security/security-kapsam.md` | §KAPSAM-DIŞI negatif liste + K000 sınır satırları | §1 gerçek #2 — köprü bu dosyanın boşluğunu doldurur |
| X3 | `K000-isletim-sistemi/isletim-sistemi-security/index.md` | 3 alt-dal + 24 OS kartı + 15 kod kartı envanteri, komşu sınır matrisi, S1/S2 | §3.1, §3.3, §6 |
| X4 | `K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-kapsam.md` | C1–C12 katman ayrım matrisi (§3.1), boşluk tablosu (§3.3), komşu matrisi (§5), S1/S2 (§7) | §2 (link), §4.1, §6 |
| X5 | `K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-kod-seti.md` | 706 satır izin/sandbox/kaynak limiti kod seti | §3.1 |
| X6 | `K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-dogrulama.md` | 68 URL + 43 disk kanıtı + negatif kanıt defteri | §3.3 (okur bu dosyadan kanıta iner) |
| X7 | `K000-isletim-sistemi/isletim-sistemi-security/isletim-sistemi-security-ornekler.md` | 18 gerçek repo kod örneği | §3.3 |
| X8 | `K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/index.md` | koruma halkaları, önyükleme, mitigasyon alt-dalı | §4.1 D1, D4 |
| X9 | `K000-isletim-sistemi/isletim-sistemi-security/yetki-ve-erisim-modeli/index.md` | DAC/MAC/RBAC/ACL denetim akışı alt-dalı | §4.1 D2, D5 |
| X10 | `K000-isletim-sistemi/isletim-sistemi-security/uygulama-guvenlik-siniri/index.md` | syscall yüzeyi, sandbox türleri alt-dalı | §4.1 D3 |
| X11 | `K000-isletim-sistemi/isletim-sistemi-turu/index.md` | 8 platform dizini envanteri (§PLATFORMLAR) | §3.2 (planlanan 8 dosyanın dizinleri) |
| X12 | `K009-security/security-master.md` | K009 başlık envanteri ve master kapsam | §2 C9 |
| X13 | `K009-security/security-dogrulama.md` | K009 web iddia matrisi (OWASP proactive-controls · NIST 800-30) | §2 (K009 tarafı kanıt girişi) |

| Bağlantı kuralı | Açıklama |
|---|---|
| L-1 | X1–X13 hedeflerinin tamamı bu dosyanın yazım anında diskte **mevcuttur** (glob ile ölçülmüştür) — bunlar **geriye dönük doğrulanmış bağlantılardır** |
| L-2 | §3.2'deki 8 `<os>-guvenlik.md` wiki-link'i bu tabloya **alınmamıştır** — bunlar **kasıtlı ileri bağlantıdır** (paralel üretim, Durum = planlandı); disk kanıtı alınca §5'e X14–X21 olarak taşınır |
| L-3 | K009 URL tahsis haritasında (`security-web-arastirma.md` · §14 "URL tahsis HARİTASI") tekrar eden URL bu dosyada **0'dır**; kanıt birimi = Dosya·Satır |

### §5.1 Bağlantı biçimi

| Biçim | Kullanım yer | Anlamı |
|---|---|---|
| `Dosya.md · satır` / `Dosya.md · §N` | §1, §2, §3.1, §3.3 kanıt sütunları | disk kanıtı — okur doğrudan satıra gider |
| `[[dal/dosya]]` wiki-link | §3.2 (ileri bağlantı) | hedef paralel üretimde; Durum sütunu esastır |
| Ham yol (`isletim-sistemi-turu/<os>/`) | §3.2 dizin sütunu | kararın bağlandığı fiziksel dizin |
| `K000-...` / `K009-...` kısaltması | §4 sınır cümleleri | dal adı geçişidir, link değildir — ayrıntı §5 tablosunda |

## §6 Açık sorular

> **Kaynak kuralı:** Aşağıdaki sorular uydurma değildir; her biri diskte mevcut bir dosyadan
> alınmıştır. K009 kök `index.md`'deki açık sorular (satır 32–35) taranmıştır: OS sınırına
> **doğrudan değen soru YOKTUR** (K009↔K010 sınırı, v2 çocuk rolleri, K009↔K010 ID CONFLICT —
> hiçbiri bu konuya girmez). OS konusunu ilgilendiren açık sorular K000 tarafındadır:

| # | Soru | Kaynak (disk) | Bu köprünün tutumu | Durum |
|---|---|---|---|---|
| Q1 | Uygulama ↔ OS güvenlik sorumluluk sınırı (kim hangini uygular)? — S2 | `isletim-sistemi-security/index.md` satır 96 · `isletim-sistemi-security-kapsam.md` §7 | Bu köprü sınırı **belgeledi** (§2, §4); kapanış kriteri K000'deki "sınır matrisi onayı"dır — onay mercii orkestratör/mimari sahibidir | AÇIK (köprü hazır, onay bekliyor) |
| Q2 | OS güvenlik katmanı (capabilities/seccomp/LSM) hedef mimaride devreye alınacak mı? — S1 | `isletim-sistemi-security/index.md` satır 95 · `isletim-sistemi-security-kapsam.md` §7 | Bu dosyayı **etkilemez** — S1 kapansa da bu köprünün devir kuralları (§4) değişmez; yalnız §4.1 D1/D4 satırlarındaki "hedef referans" etiketi güncellenir | `[UNKNOWN]` (K000'de kalır, kapatılmaz) |
| Q3 | 8 platform `<os>-guvenlik.md` üretildi mi? | glob 2026-10-15: 0 isabet | Üretim tamamlanınca §3.2 tablosu orkestratör eliyle "MEVCUT" olarak güncellenir | `⚠️ VERIFICATION REQUIRED` |

| Kapanış kuralı | Açıklama |
|---|---|
| C-1 | Q1'in kapanması bu köprüyü **geçersiz kılmaz** — köprü yalnızca onaylanmış sınırın kaydırıdır |
| C-2 | Q2'de `[UNKNOWN]` etiketi kaldırılmaz; "üretildi/üretilmedi" gibi spekülatif ifadeler yazılmaz |
| C-3 | Q3, 2026-10-15 kararının tek **yapısal** açık kalemidir; kalan 7 satır da aynı durumdadır |

### §6.1 K009 kök index açık sorularının bu konuya etkisi (tarama kaydı)

> K009 kök `index.md` açık soruları (satır 32–35) tek tek taranmıştır; **hiçbiri** OS
> sınırına girmez. Kayıt şeffaflığı için nedenleri:

| # | K009 kök açık sorusu (satır) | OS sınırına etkisi | Gerekçe |
|---|---|---|---|
| 1 | K009 ↔ K010 sınırı (session-security vs K010 token) — satır 33 | YOK | Konu kimlik/token paylaşımıdır; OS katmanı içeri girmez (K000 kapsam §5.3: "oturum iş mantığı K010'da, OS yalnız dosya izni") |
| 2 | v2 §2.9 çocuklara yalnız ad verilmesi — satır 34 | YOK | Çocuk rol tanımı dokümantasyon eksikliğidir, güvenlik katmanı sınırı değil |
| 3 | K009 ↔ K010 ID CONFLICT kaydı — satır 35 | YOK | Kimlik adlandırma çakışmasıdır; bu köprü yalnız K000↔K009 OS sınırını taşır |

| Sonuç | |
|---|---|
| OS konusuna değen K009 kök açık sorusu | **0** |
| OS konusunu ilgilendiren açık sorular | K000 tarafında: S1, S2 (§6 Q1–Q2) |

## §7 Ölçüm ve kontrol listesi (bu dosya için)

| # | Kontrol | Beklenen | Durum (yazım anı) |
|---|---|---|---|
| 1 | Dosya satır aralığı | 300–500 | yazım sonrası ölçülecek (orkestratör doğrular) |
| 2 | Frontmatter 10 anahtar (sıra: node_id → group) | tam | EVET |
| 3 | `parent:` anahtarı | YOK (yalnız `parent_id`) | EVET |
| 4 | Harici URL sayısı | ≤ 3 (havuz tekrarı yasak) | **0** → havuz tekrarı 0 |
| 5 | Wiki-link hedefleri diskte mevcut | 13/13 (§5 L-1) | EVET (glob ölçüldü) |
| 6 | §3.2 iddiası | ileri bağlantı + Durum `planlandı` (MEVCUT yazmak yasak) | EVET |
| 7 | `*.php` / `*.js` / `*.css` / `*.sql` yazımı | YOK (salt-okunur vault) | EVET — yalnız okundu |
| 8 | Başka dosyaya dokunma | yalnız bu dosya + `index.md` §4/§5 | EVET (§2 child tablosuna dokunulmadı) |
| 9 | `git commit` | YASAK (orkestratöre ait) | ATILMADI |
| 10 | S1/S2 kapatıldı mı | HAYIR (K000'de kalır) | EVET — açık tutuldu |

### §7.1 Ölçüm yöntemi (bu dosyadaki iddialar nasıl toplandı)

| # | İddia kümesi | Yöntem | Kanıt |
|---|---|---|---|
| M1 | K009 kök sınır satırı | `index.md` tam okuma (100 satır) | §4 · satır 82 |
| M2 | K000 alt-dal + kart envanteri | `isletim-sistemi-security/index.md` tam okuma (98 satır) | satır 44–49, 56–58, 60, 62–73 |
| M3 | C1–C12 sınıf adları | `isletim-sistemi-security-kapsam.md` §3.1 okuma (satır 153–166) | §2 tablosunun kaynak sütunu |
| M4 | 706 satır kod-seti | K000 index satır 20 ve 60 (sayım kaydı) | §3.1 |
| M5 | X1–X13 disk varlığı | glob (`hidden: true`, `.ai/architecture/**`) | §5 L-1 |
| M6 | §3.2 plan satırları | görev manifesti (orkestratör kararı 2026-10-15) | §1 gerçek #4 |
| M7 | Harici URL | bu dosyada **0** kullanıldı → havuz tekrarı **oluşamaz** | §7 satır 4 |

## §8 İhlal işaretleri (bu köprü bozulursa)

| # | İhlal | Yakalama | Sonucu |
|---|---|---|---|
| I1 | Bu dosyaya kernel/sandbox/patch **kararı** yazılması | içerik okumasında "kuracağız/seçeceğiz" ifadesi | İhlal → metin K000'e taşınır; burada yalnız sınır cümlesi kalır |
| I2 | Bu dosyaya parola/CSRF/rate-limit/session **tasarımı** yazılması | içerik okumasında politika/parametre tanımı | İhlal → metin K009 çocuk dosyasına taşınır |
| I3 | §3.2 satırlarından birine `MEVCUT` yazılması (üretim bitmeden) | Durum sütunu | İhlal → satır `planlandı`ya döner; disk ölçümü olmadan "var" denmez |
| I4 | K000 kapsam dosyalarının bu köprü için kopyalanması | §2'de mekanizma/kanıt sütununun belirmesi | İhlal → kopya satır silinir, kaynak dosyaya link kalır |
| I5 | S1/S2'nin bu köprüde kapatılması | "kapatıldı/çözüldü" ifadesi | İhlal → revert; kapanış yalnız K000'de + mimari sahibi onayıyla |
| I6 | Havuzdan (`security-web-arastirma.md` §14) URL alınması | URL grep ↔ havuz kesişimi ≠ 0 | İhlal → URL silinir; köprü zaten 0 URL taşır |

## §9 Değişiklik günlüğü

| Tarih | Değişiklik | Yapan |
|---|---|---|
| 2026-10-15 | İlk yazım — sınır köprüsü (§1–§9); kök `index.md` §4 + §5'e 2 satır eklendi | K009 bridge subagent (orkestratör eliyle sevk) |
| 2026-10-15 | Düzeltme turu: §14 başlığı "URL tahsis HARİTASI" olarak kesinleştirildi (adet iddiası yok) · H1 sonrası `> **Kapsam:**` blockquote · §4.0 yasak özeti · §3.2 wiki-link'li planlandı tablosu | aynı |
| 2026-10-15 | `git commit` ATILMADI — commit kararı orkestratöre aittir | kayıt |

| Sonraki güncelleme tetikleyicisi | Yapılacak iş | Yapan |
|---|---|---|
| §3.2'deki 8 dosyanın paralel üretimi bitince | Durum sütunları glob kanıtıyla `MEVCUT` yapılır; disk kanıtlı hale gelenler §5'e X14–X21 olarak eklenir | orkestratör |
| K000 S2 sınır matrisi onayının alınması | §6 Q1 satırı "onaylandı" olarak güncellenir; §4.0 özeti onaylı sınırın kaydı sayılır | orkestratör |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
