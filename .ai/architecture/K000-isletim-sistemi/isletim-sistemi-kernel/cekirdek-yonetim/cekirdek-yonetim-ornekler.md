---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim/cekirdek-yonetim-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel/cekirdek-yonetim
children: []
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man2/fork.2.html
  - https://man7.org/linux/man-pages/man2/clone.2.html
  - https://man7.org/linux/man-pages/man2/wait.2.html
  - https://man7.org/linux/man-pages/man2/mmap.2.html
  - https://learn.microsoft.com/en-us/windows/win32/memory/memory-management
validation: "2026-10-13 · repo satırları grep ile birebir (shell_exec:295 · proc_open:253 · mkdir:35 · usleep:117 · LOCK_EX:74 · php_uname:35) · negatif kanıt grep 0 (spinlock/insmod/modprobe/init_module/request_irq/file_operations · tüm *.php) · URL'ler envanterden yeniden kullanıldı (HEAD 200 · 2026-10-13)"
open_questions:
  - "core ↔ kernel sınırı (Q2) → ⚠️ VERIFICATION REQUIRED"
  - "Hedef çekirdek (Q4) → UNKNOWN"
  - "Repo'da gerçek fork/exec/wait çağrısı var mı? → grep kapsamı yalnız PHP; PHP → libc köprüsü dolaylı"
group: kod
---

# cekirdek-yonetim-ornekler — Örnek Kataloğu ve Repo Karşılıkları (Kademe-4)

## §0 ELI15

Bu dosya, `-anayasa`'daki kavramları (halka, sayfa, bağlam) ve `-surucu`'daki
durum makinesini **gözlemlenebilir örneklere** çevirir. CoreMusic'te çekirdek
kodu doğrudan yazılmaz — çekirdek her zaman **PHP kodunun altında** çalışır
(`shell_exec` → fork/exec · `usleep` → bekleme durumu · `LOCK_EX` → dosya
kilidi). Bu dosya bu **dolaylı köprüyü** örneklerle kurar, repo satırı ile
kanıtlar ve repo'da **olmayan** çekirdek kodunun negatif kanıtını taşır.

| Öğe | Değer |
|---|---|
| Dosya rolü | Örnek kataloğu (Kademe-4 · `group: kod`) |
| Bölüm omurgası | §1-§6 (bu dosya) |
| Bağlı dosyalar | `cekirdek-yonetim-anayasa.md` (§0-§4, §6) · `cekirdek-yonetim-surucu.md` (§5-§10) · kök `index.md` §1 |
| Kapsam | PHP → çekirdek köprüsü örnekleri · durum makinesi karşılıkları · negatif kanıt |
| Kapsam dışı | Çekirdek içi C kodu (repoda yok) · zamanlama politikası (`-plan`) · gözlem (`-observability`) |

> **Kanıt kuralı:** Her repo örneği `dosya:satır` ile işaretlidir ve grep ile
> birebir doğrulanmıştır (2026-10-13). Kavramsal akışlar kod bloğu olarak
> **kopyalanmaz**; yalnız satır numarasıyla işaretlenir.

## §1 Örnek kataloğu — her satır tek bir gözlemlenebilir örüntü

### §1.1 Katalog tablosu

| # | Örüntü | Dosya | Satır | Kanıt sınıfı | Karşılık |
|---|---|---|---|---|---|
| K1 | alt süreç başlatma (komut) | `shared/src/AI/AIEngine.php` | 292, 295 | repo (+) | fork/exec (§2.1) |
| K2 | alt süreç başlatma (tutucu + pipe) | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 253 | repo (+) | fork/exec + fd devri (§2.2) |
| K3 | bekleme (uyku) | `home.coremusic.net/include/Auth/HomeAuthBridge.php` | 25, 117 | repo (+) | waiting durumu (§2.3) |
| K4 | dizin izni (0750) | `shared/src/Session/SessionConfig.php` | 20, 35 | repo (+) | erişim denetimi (§2.4) |
| K5 | geçici dizin + dosya kilidi | `shared/src/Component/TemplateEngine.php` | 73, 74 | repo (+) | mmap/kilit komşusu (§2.5) |
| K6 | platform kimliği | `shared/src/Api/Bff/DesktopBff.php` | 35 | repo (+) | OS sorgusu (§2.6) |
| K7 | süreç yaratma syscall'i | upstream docs (man7) | §5 URL | upstream | fork(2)/clone(2) |
| K8 | süreç bekleme syscall'i | upstream docs (man7) | §5 URL | upstream | wait(2) |
| K9 | sayfa/eşleme syscall'i | upstream docs (man7) | §5 URL | upstream | mmap(2) |
| K10 | çekirdek içi kilit kodu | (tüm `*.php`) | — | repo (−) | **yok** (§3 grep 0) |
| K11 | modül yükleme kodu | (tüm `*.php`) | — | repo (−) | **yok** (§3 grep 0) |
| K12 | çekirdek eşik/sayaç değeri | — | — | yokluk | **yazılmaz** |

### §1.2 Kanıt sınıfları — dört sınıf aynı tabloda birleştirilmez

| Sınıf | Ne demek | Katalogdaki | Güvenilirlik |
|---|---|---|---|
| repo (+) | dosya+satır grep ile birebir | K1-K6 | en yüksek (gözlemlenmiş) |
| repo (−) | grep 0 isabet = o kod yok | K10, K11 | yüksek (eksiksiz tarama) |
| upstream | resmi doküman URL'si (HTTP 200) | K7-K9 | doküman düzeyi |
| yokluk | bilinçli yazılmayan değer | K12 | kural (İlke B10) |

**Sınıf kuralı:** repo (+) bir örneğin "doğru" olduğu anlamına gelmez —
**gözlemlendiği** anlamına gelir; doğruluk ayrı incelemedir (§4).

## §2 Durum makinesi karşılıkları — repo örneği ↔ çekirdek olayı

> Bu bölüm `-surucu` §5.1'deki durum makinesini repo örnekleriyle eşler.
> Her satır bir **dolaylı köprüdür** (PHP → libc → syscall); syscall'un
> kendisi repoda **bulunmaz** (§3 dürüstlük kaydı).

### §2.1 K1 — `AIEngine` alt süreç başlatma

| Adım | Repo satırı | Ne olur | Durum makinesi karşılığı |
|---|---|---|---|
| 1 | `:292` | `escapeshellarg($filePath)` — argüman kaçırma | (hazırlık) |
| 2 | (ara) | komut dizgesi kurulur | — |
| 3 | `:295` | `$output = shell_exec($cmd);` | fork/exec → parent **waiting** |
| 4 | (motor) | alt süreç biter, çıktı döner | child **exit** → parent **runnable** |

**Not:** `shell_exec` bloklayıcıdır — PHP süreci bu sürede **CPU bırakır**
(bekleme durumu). Bekleme süresi `UNKNOWN` (ölçülmedi).

### §2.2 K2 — `ApiKeyCreateCommandTest` `proc_open`

| Adım | Repo satırı | Ne olur | Durum makinesi karşılığı |
|---|---|---|---|
| 1 | `:253` | `$handle = proc_open(` | fork/exec + tutucu |
| 2 | (ara) | pipe/stdio devri | fd paylaşımı (kavramsal) |
| 3 | (test) | süreç sonu bekleme | wait(2) karşılığı |
| 4 | (test) | tutucu kapatma | kaynak serbest bırakma |

**Ayrım:** `shell_exec` (K1) çıktıyı tek seferde toplar; `proc_open` (K2)
**canlı tutucu** verir — ikisi de aynı durum makinesini kullanır, farklı
G/Ç yüzeyi sunar.

### §2.3 K3 — `HomeAuthBridge` bekleme

| Adım | Repo satırı | Ne olur | Durum makinesi karşılığı |
|---|---|---|---|
| 1 | `:25` | `RETRY_DELAY_US = 200_000` (200 ms) | (sabit) |
| 2 | `:117` | `usleep(self::RETRY_DELAY_US);` | **waiting** (zamanlayıcı uyandırır) |
| 3 | (motor) | süre dolar | **runnable** |

**Not:** `usleep` kesme-zamanlama alanıdır; zamanlama politikası `-plan`
dalındadır — burada yalnız durum karşılığı taşınır.

### §2.4 K4 — `SessionConfig` dizin izni

| Adım | Repo satırı | Ne olur | Durum makinesi karşılığı |
|---|---|---|---|
| 1 | `:20` | `DIR_PERMISSIONS = 0750` | izin politikası sabiti |
| 2 | `:35` | `@mkdir($savePath, 0750, true);` | dosya sistemi çağrısı → syscall |
| 3 | (motor) | çekirdek izin denetimi | erişim reddedilebilir |

**Not:** `0750` çekirdekte **inode izni** olarak uygulanır; PHP bunu
**isteyen** taraftır, denetleyen **değil**.

### §2.5 K5 — `TemplateEngine` geçici dizin + kilit

| Adım | Repo satırı | Ne olur | Durum makinesi karşılığı |
|---|---|---|---|
| 1 | `:73` | `sys_get_temp_dir()` + rastgele dosya adı | geçici dosya yolu |
| 2 | `:74` | `file_put_contents(..., LOCK_EX)` | dosya kilidi (senkronizasyon) |
| 3 | (motor) | kilidi alan süreç yazar | eşzamanlılık denetimi |

**Not:** Kilit **kullanıcı uzayı** dosya kilididir; çekirdek içi
spinlock/mutex ile **karıştırılmaz** (§3 K10). Komşu ilişki:
`kesme-ve-senkronizasyon` dalının alanı.

### §2.6 K6 — `DesktopBff` platform kimliği

| Adım | Repo satırı | Ne olur | Durum makinesi karşılığı |
|---|---|---|---|
| 1 | `:35` | `'platform' => php_uname('s'),` | OS sorgusu (syscall) |
| 2 | (istek) | yanıt gövdesine yazılır | — |

**Not:** Hedef çekirdek `UNKNOWN` (Q4); bu satır yalnızca "hangi OS'un
çalıştığı **sorgulanabilir**" kanıtıdır — cevabı taşımaz.

### §2.7 Köprü özeti — PHP satırı ↔ syscall ↔ durum

| Repo satırı | Muhtemel syscall (dolaylı) | Durum etkisi | Kanıt sınıfı |
|---|---|---|---|
| `shell_exec:295` | fork/execve + waitpid | waiting → runnable | kavramsal (§2.1) |
| `proc_open:253` | fork/execve + pipe | waiting → runnable | kavramsal (§2.2) |
| `usleep:117` | nanosleep/clock_nanosleep | waiting → runnable | kavramsal (§2.3) |
| `mkdir:35` | mkdirat | durum değişmez | kavramsal (§2.4) |
| `LOCK_EX:74` | fcntl/flock | waiting (kilit bekleyebilir) | kavramsal (§2.5) |
| `php_uname:35` | uname | durum değişmez | kavramsal (§2.6) |

**Eşleme kuralı:** "Muhtemel syscall" sütunu **tahmin değil**, POSIX/
glibc köprüsünün bilinen yoludur; hangi syscall'un gerçekten çağrıldığı
bu turda **ölçülmedi** (strace yok) → ⚠️ VERIFICATION REQUIRED.

## §3 Negatif kanıt — repo'da olmayan çekirdek kodu

### §3.1 Grep tablosu (2026-10-13 · tüm `*.php`)

| # | Aranan | Kapsam | İsabet | Yorum |
|---|---|---|---|---|
| N1 | `spinlock` | tüm `*.php` | 0 | çekirdek kilit kodu yok |
| N2 | `insmod` | tüm `*.php` | 0 | modül yükleme yok |
| N3 | `modprobe` | tüm `*.php` | 0 | modül çözümleme yok |
| N4 | `init_module` | tüm `*.php` | 0 | modül başlatma yok |
| N5 | `request_irq` | tüm `*.php` | 0 | kesme kaydı yok |
| N6 | `file_operations` | tüm `*.php` | 0 | VFS operasyon tablosu yok |

Ayrıca (kardeş dal kanıtı): `spinlock|insmod|modprobe|pcntl_|flock` →
`home.coremusic.net/` = 0 isabet (2026-10-13).

### §3.2 Dürüstlük cümlesi

0 isabet "**repoda o kod yok**" der; "**çekirdek hiç kullanılmıyor**"
**demez**. Çekirdek her PHP çağrısının **altında** çalışır (§2 köprüsü);
bu dosyadaki iddialar yalnızca grep kapsamıyla sınırlıdır (2026-10-13,
tüm `*.php`).

### §3.3 Olumsuz kanıtın sonucu — ne yapılamaz

| Soru | Cevap | Neden |
|---|---|---|
| çekirdek hangi sürüm? | `UNKNOWN` | N1-N6 = 0 + Q4 |
| hangi kesme vektörleri aktif? | `UNKNOWN` | N5 = 0 |
| hangi modüller yüklü? | `UNKNOWN` | N2-N4 = 0 |
| kilit çekişmesi (contention) kaç? | `UNKNOWN` | ölçüm yok (surucu §6) |
| syscall gerçekten hangisi? | ⚠️ VERIFICATION REQUIRED | strace/ölçüm yok (§2.7) |

## §4 Doğru ve yanlış kullanım — bu repoda görülen örüntüler

### §4.1 Doğru örüntüler (mimari uygunluk)

| # | Örüntü | Repo kanıtı | Neden doğru |
|---|---|---|---|
| D1 | argüman kaçırma | `AIEngine.php:292` `escapeshellarg` | komut enjeksiyonuna karşı |
| D2 | beklemede sabit süre | `HomeAuthBridge.php:25` `200_000` | sonsuz döngü yok |
| D3 | izin sabiti tek yerde | `SessionConfig.php:20` | sihirli sayı dağılmıyor |
| D4 | kilitleme ile yazma | `TemplateEngine.php:74` `LOCK_EX` | yarış koşulu azaltılıyor |
| D5 | ölçüm yoksa eşik yazmama | bu dosyanın tabloları (`UNKNOWN`) | uydurma değer yok |

**Örnek kuralı:** "Doğru" burada **mimari uygunluk** demektir; davranışsal
doğruluk (test) `-testing` dalının alanı, bu dosya yalnız örnek kataloğu üretir.

### §4.2 Yanlış kullanım — bu repoda görülmesi muhtemel hatalar

| # | Yanlış kullanım | Sonuç | İlgili hata modu |
|---|---|---|---|
| Y1 | `shell_exec` çıktısını boyutsuz toplamak | bellek taşması | surucu §8 risk |
| Y2 | `usleep` süresini ağ/takip için büyütmek | gecikme zinciri | `-plan` alanı |
| Y3 | kilitli yazmayı unutmak (`LOCK_EX`siz) | yarış koşulu | komşu dal |
| Y4 | `mkdir` iznini 0777 yapmak | yetki açığı | `-security` alanı |
| Y5 | 0 isabeti "yok" diye genişletmek | zero-hallucination ihlali | §3.2 |

## §5 Kaynakça — envanterden yeniden kullanılan URL'ler (HEAD 200 · 2026-10-13)

| # | İddia | URL | Durum |
|---|---|---|---|
| O-W1 | fork(2) — süreç yaratma (§2.1 köprüsü) | https://man7.org/linux/man-pages/man2/fork.2.html | 200 |
| O-W2 | clone(2) — süreç/iş parçası yaratma (§2.2 köprüsü) | https://man7.org/linux/man-pages/man2/clone.2.html | 200 |
| O-W3 | wait(2) — çocuk süreç bekleme (§2.1/§2.2) | https://man7.org/linux/man-pages/man2/wait.2.html | 200 |
| O-W4 | mmap(2) — dosya/bellek eşlemesi (§2.5 komşu) | https://man7.org/linux/man-pages/man2/mmap.2.html | 200 |
| O-W5 | Windows bellek yönetimi (§2.6 platform) | https://learn.microsoft.com/en-us/windows/win32/memory/memory-management | 200 |

**Doğrulama notu:** Beş URL de bu ağacın `-dogrulama.md` envanterinden
alındı ve 2026-10-13'te HTTP 200 olarak doğrulanmıştır; bu dosyada **yeni**
URL doğrulanmadı (yeniden kullanım).

### §5.1 URL × örnek eşlemesi

| Örnek | Hangi URL destekliyor | Ne kanıtlıyor |
|---|---|---|
| K1 (§2.1) | O-W1, O-W3 | fork → wait köprüsünün varlığı |
| K2 (§2.2) | O-W2, O-W3 | clone/wait yüzeyi |
| K5 (§2.5) | O-W4 | eşleme/kilit komşuluğu (kavramsal) |
| K6 (§2.6) | O-W5 | Windows yüzeyi ayrı resmi konu |

**Eşleme kuralı:** URL yalnız **kavram** destekler; repo satırının o
syscall'ı çağırdığını **kanıtlamaz** (bkz. §2.7 ⚠️).

## §6 Açık sorular ve sonraki adımlar

### §6.1 Açık sorular

| # | Soru | Etiket | Kapanış yolu |
|---|---|---|---|
| Q1 | core ↔ kernel sınırı (Q2) | ⚠️ VERIFICATION REQUIRED | üst karar (kavram `-core`'da) |
| Q2 | hedef çekirdek (Q4) | UNKNOWN | ortam/prod okuması |
| Q3 | gerçek syscall hangisi (§2.7)? | ⚠️ VERIFICATION REQUIRED | strace/ölçüm |
| Q4 | `shell_exec` bekleme süresi dağılımı? | PLANNED | surucu §6.3 ölçüm planı |
| Q5 | `LOCK_EX` ne kadar bekletiyor? | PLANNED | kilit çekişme ölçümü |

### §6.2 Sonraki adımlar — sırayla ne yapılacak

| Adım | Eylem | Araç | Beklenen çıktı |
|---|---|---|---|
| 1 | syscall köprüsü doğrulama (Q3) | strace/perf | gerçek syscall listesi |
| 2 | bekleme süresi ölçümü (Q4) | surucu §6.3 | süre dağılımı (`UNKNOWN` → sayı) |
| 3 | kilit çekişmesi ölçümü (Q5) | surucu §6.1 sayaçları | çekişme sayısı |
| 4 | eşik üretimi | surucu §6.3 | meşru eşik (uydurma değil) |

**Plan durumu:** Adımlar 1-4 **PLANNED**'dir; hiçbiri bu turda çalıştırılmadı.

### §6.3 Dosyanın komşulara verdiği özet (handover satırı)

| Alıcı dal | Verilen bilgi | Biçim |
|---|---|---|
| `kesme-ve-senkronizasyon` | kullanıcı uzayı kilit örneği (K5) + çekirdek kilit yok kanıtı (N1) | §1, §3 |
| `surucu-ve-modul-modeli` | modül yükleme yok kanıtı (N2-N4) + VFS operasyon yok (N6) | §3 |
| `isletim-sistemi-plan` | bekleme örneği (K3) + zamanlama sınırı | §2.3 |
| `isletim-sistemi-api` | syscall köprüsü tablosu (§2.7) | §2.7 |
| `isletim-sistemi-observability` | ölçüm eksikliği kaydı (§3.3) | §3.3 |

**Handover kuralı:** Bu tablodaki alıcılar bilgiyi **kullanır**, bu dosyanın
satırlarını **değiştirmez**; değişiklik ADR/üst karar ister.

## §7 Derin örnekler — adım adım senaryolar (kavramsal akış)

> Bu bölüm §2'deki tekil örüntüleri **tam akışa** çevirir. Bloklar
> kavramsaldir; gerçek kod satırı dosyada okunur, buraya kopyalanmaz
> (yalnız satır numarasıyla işaretlenir).

### §7.1 Senaryo S1 — komut çalıştırma akışı (K1, K2)

```text
[PHP betiği] --> [escapeshellarg :292] --> [shell_exec :295]
     |                                          |
     |                                          v
     |                              [fork/exec — child süreç]
     |                                          |
     |                              [child: iş yapar, çıkar]
     |                                          |
     v                                          v
[çıktıyı okur] <-- [parent: wait — waiting durumu]
```

| Adım | Olay | Durum etkisi | Ölçüm | Repo |
|---|---|---|---|---|
| 1 | argüman kaçırma | — | yok | `:292` |
| 2 | komut başlatma | parent **waiting** | yok | `:295` |
| 3 | child çalışma | child **running** | yok | — |
| 4 | child bitiş | child **zombie** → parent uyandırma | yok | — |
| 5 | parent devam | parent **runnable** → **running** | yok | — |

**Önemli ayrım:** Adım 4'te child, `wait` okunana kadar **zombie** kalır
(anayasa §6.4). `shell_exec` bunu motor içinde yönetir; PHP bunu **görmez**.

### §7.2 Senaryo S2 — kilitlemeli dosya yazma (K5)

| Adım | Olay | Eşzamanlılık etkisi | Ölçüm | Repo |
|---|---|---|---|---|
| 1 | geçici yol üretimi | — | yok | `:73` |
| 2 | `LOCK_EX` ile yazma | kilidi alan süreç yazar | yok | `:74` |
| 3 | ikinci süreç dener | **waiting** (kilit) | yok | — |
| 4 | kilit bırakma | ikinci süreç uyandır | yok | — |

**Ayrım kuralı:** Bu kilit **kullanıcı uzayı** dosya kilididir; çekirdek
içi spinlock/mutex ile **karıştırılmaz** (§3 K10 · komşu dal alanı).

### §7.3 Senaryo S3 — uyku ile yeniden deneme (K3)

| Adım | Olay | Durum etkisi | Ölçüm | Repo |
|---|---|---|---|---|
| 1 | deneme başarısız | — | yok | — |
| 2 | `RETRY_DELAY_US` oku | — | yok | `:25` |
| 3 | `usleep` | **waiting** (zamanlayıcı) | yok | `:117` |
| 4 | süre dolar | **runnable** | yok | — |
| 5 | yeniden dener | **running** | yok | — |

**Not:** Uyku süresi sabittir (200 ms); üstel/backoff **görünmüyor** —
ancak tam dosya okunmadı → ⚠️ VERIFICATION REQUIRED.

## §8 Örnek × durum makinesi matrisi

| Örnek ↓ / Durum → | running | runnable | waiting | zombie | durdurulmuş |
|---|---|---|---|---|---|
| K1 `shell_exec` | ✓ (parent) | ✓ (çıkışta) | ✓ (bekleme) | ✓ (child) | — |
| K2 `proc_open` | ✓ (parent) | ✓ (çıkışta) | ✓ (bekleme) | ✓ (child) | — |
| K3 `usleep` | ✓ (öncesi) | ✓ (sonrası) | ✓ (uyku) | — | — |
| K4 `mkdir` | ✓ (kısa) | ✓ | — | — | — |
| K5 `LOCK_EX` | ✓ (yazarken) | ✓ | ✓ (kilit bekleyebilir) | — | — |
| K6 `php_uname` | ✓ (kısa) | ✓ | — | — | — |

**Matris kuralı:** `✓` = "o durum o örnek için mümkündür (kavramsal)";
gerçekleşme `UNKNOWN` (ölçülmedi). `—` = "o durum o örnekte tasarımsal
olarak yok". Bu matris **tahmin değil**, durum-yüzeyi haritasıdır.

### §8.1 Örnek × kaynak sınıfı ilişkisi (bellek/süreç/G-Ç)

| Örnek | Süreç etkisi | Bellek etkisi | G/Ç etkisi |
|---|---|---|---|
| K1 | yeni süreç yaratır | child kopya (COW — anayasa §2) | çıktı pipe |
| K2 | yeni süreç + fd | child kopya + pipe tamponu | stdio devri |
| K3 | süreç değişmez | — | — |
| K4 | — | dizin girişi (disk) | — |
| K5 | süreç değişmez | tampon dosyaya | dosya yazma |
| K6 | süreç değişmez | — | — |

**Sınıf kuralı:** Her örneğin **birincil** etkisi farklıdır; iki örneği
aynı "çekirdek örneği" saymak hata olur (ayrı syscall aileleri).

## §9 Bu dosyanın ölçüm tablosu

| # | Ölçü | Değer | Durum |
|---|---|---|---|
| 1 | repo örneği sayısı | 6 örüntü (K1-K6) | ölçüldü (2026-10-13) |
| 2 | negatif kanıt (grep) | 6 arama / 0 isabet (N1-N6) | ölçüldü (2026-10-13) |
| 3 | URL (yeniden kullanım) | 5 (HTTP 200) | ölçüldü (2026-10-13) |
| 4 | yeni URL doğrulama | 0 | bilinçli (envanter yeterli) |
| 5 | gerçek bekleme süresi | — | `UNKNOWN` (yok) |
| 6 | gerçek syscall listesi | — | ⚠️ (ölçülmedi) |
| 7 | açık soru | Q1-Q5 (5) | açık |

**Ölçüm dürüstlüğü:** Tablodaki 1-4 **yapısal ölçüm** (dosya/satır/grep);
5-7 **durum ölçümüdür** ve gerçek değer üretilmemiştir. İki tür birbirine
karıştırılmaz (İlke B10).

### §9.1 Yapısal ölçümün ayrıntısı — ne sayıldı

| Sayım | Yöntem | Sonuç | Tarih |
|---|---|---|---|
| K1 kanıt satırları | grep | 2 (292, 295) | 2026-10-13 |
| K2 kanıt satırı | grep | 1 (253) | 2026-10-13 |
| K3 kanıt satırları | grep | 2 (25, 117) | 2026-10-13 |
| K4 kanıt satırları | grep | 2 (20, 35) | 2026-10-13 |
| K5 kanıt satırları | grep | 2 (73, 74) | 2026-10-13 |
| K6 kanıt satırı | grep | 1 (35) | 2026-10-13 |
| negatif arama | tüm `*.php` | 6 arama / 0 isabet | 2026-10-13 |

**Yöntem notu:** Grep kapsamı tüm `*.php` dosyalarıdır (alt dizinler dahil);
0 isabet **eksiksiz tarama** sonucudur, tahmin değil.

### §9.2 Ölçüm eksikliğinin risk tablosu

| Risk | Olasılık | Etki | Mevcut koruma |
|---|---|---|---|
| syscall varsayımı yanlış çıkmak | `UNKNOWN` | orta | ⚠️ işaretleri (§2.7) |
| bekleme süresi üretimde uzamak | `UNKNOWN` | yüksek | koruma **yok** (ölçüm yok) |
| kilit çekişmesi | `UNKNOWN` | orta | koruma **yok** |
| 0 isabeti geniş yorumlanmak | düşük | orta | §3.2 dürüstlük cümlesi |
| eşik uydurma | korunuyor | yüksek | `UNKNOWN` işaretleri |

**Risk kuralı:** "Olasılık" sütununda sayı **yoktur** — olasılık için de
ölçüm gerekir. Bu tablo eksikliğin kendisini kayda geçirir, tahmin üretmez.

## §10 Sık yapılan hatalar — bu dalda görülmesi muhtemel 7 hata

| # | Hata | Belirti | Doğrusu |
|---|---|---|---|
| E1 | "repoda çekirdek kodu var" demek | N1-N6 ile çelişir | "PHP → syscall köprüsü dolaylı" yazılır |
| E2 | `shell_exec`'i anında dönen sanmak | waiting durumu görmezden gelinir | §7.1 adım 2 |
| E3 | dosya kilidini spinlock sanmak | kilit türleri karışır | §7.2 ayrım kuralı |
| E4 | zombie'yi "hata" sanmak | durum makinesi yanlış okunur | anayasa §6.4 |
| E5 | 0 isabeti "çekirdek hiç yok" diye genişletmek | iddia taşması | §3.2 |
| E6 | dolaylı köprüyü doğrulanmış syscall sanmak | tahmin kanıt olur | §2.7 ⚠️ |
| E7 | eşik/süre uydurmak | kanıtsız sayı | yasak (İlke B10) |

**Hata kuralı:** E1, E5, E6 bu dosyanın **doğruluk** hatalarıdır; diğerleri
**yorum** hatalarıdır. Doğruluk hatası bir kez işlendiğinde dosya
`draft`'tan çıkmaz (yeniden denetim gerekir).

### §10.1 Hata × kapı ilişkisi

| Hata | Hangi kapıda yakalanır | Kapı durumu |
|---|---|---|
| E1/E5 | K4 (olumsuz kanıt) | ✅ geçti (2026-10-13) |
| E6 | K2 (köprü doğrulama) | ❌ açık — strace yok (Q3) |
| E2/E3/E4 | kod/kavram incelemesi | kısmi (6 örüntü) |
| E7 | K3 (eşik) | ✅ geçti (eşik yazılmadı) |

## §11 Test senaryoları — bu dosyanın test dalına verdiği taslak

| # | Senaryo | Adım | Beklenen | Durum |
|---|---|---|---|---|
| TS1 | komut çalıştır + bekle | K1 akışı | çıktı döner, süreç biter | taslak |
| TS2 | tutucu kapatma | K2 | dosya/pipe serbest | taslak |
| TS3 | uyku süresi | K3 | ~200 ms bekler (ölçüm gerekli) | taslak |
| TS4 | izin reddi | K4 (0750, yetkisiz kullanıcı) | `mkdir` başarısız | taslak |
| TS5 | kilitleme | K5 | ikinci yazıcı bekler | taslak |
| TS6 | negatif kanıt tekrarı | N1-N6 | 0 isabet tekrarlanır | **tekrarlanabilir** |

**Senaryo kuralı:** TS1-TS5 bu turda **çalıştırılmadı**; TS6 ise yapısal
olarak **tekrarlanabilirdir** (grep yeniden çalıştırılabilir) ve 2026-10-13
için sonucu **0** olarak kayıtlıdır. Test kodu bu dosyaya **yazılmaz**
(test dalının alanı).

### §11.1 TS6 tekrarlanabilirliği — komut kapsamı

| Alan | Değer |
|---|---|
| kapsam | tüm `*.php` (alt dizinler dahil) |
| aramalar | `spinlock` · `insmod` · `modprobe` · `init_module` · `request_irq` · `file_operations` |
| sonuç | 0 isabet (her arama) |
| tarih | 2026-10-13 |
| yeniden çalıştırma | yeni turda tekrarlanmalı (kod değişirse sonuç değişebilir) |

**Güncellik kuralı:** Olumsuz kanıt **zamanla eskir**; her yeni turda
yeniden çalıştırılır ve eski "0" tek başına güncel kanıt sayılmaz.

## §12 Değişiklik günlüğü ve komşu sınır notu

### §12.1 Değişiklik günlüğü (bu dosya)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-13 | dosya oluşturuldu (§0-§6) | eksik alt-dal derinleştirme görevi |
| 2026-10-13 | repo satırları grep ile birebir doğrulandı | kanıt kapısı |
| 2026-10-13 | negatif kanıt kaydedildi (6 arama/0 isabet) | kanıt kapısı |
| 2026-10-13 | §7-§12 derinleştirme eklendi (368 → hedef aralık) | dosya derinliği |

### §12.2 Komşu sınır notu — bu dosyanın bilinçli olarak taşımadıkları

| Taşınmayan | Nereye ait | Neden taşınmadı |
|---|---|---|
| zamanlama politikası (EEVDF/CFS kararı) | `isletim-sistemi-plan` | politika, mekanizma değil |
| jitter ölçüm protokolü | `isletim-sistemi-plan` | protokol `-plan`'da |
| çekirdek içi kilit tasarımı | `kesme-ve-senkronizasyon` | kilit dalıdır |
| modül yükleme akışı | `surucu-ve-modul-modeli` | modül dalıdır |
| güvenlik denetimi (izin politikası) | `isletim-sistemi-security` | politika `-security`'de |
| test kodu | `isletim-sistemi-testing` | kod `-testing`'de |

**Sınır kuralı:** Bu dosya yalnız **örnek kataloğu** üretir; politika,
ölçüm sonucu, eşik ve test çıktısı **üretemez** (hepsi `UNKNOWN`/`PLANNED`
işaretlidir).

## §13 Bağlayıcı özet — okuma sırası ve kanıt sınıfı (2026-10-15)

> Append-only ekleme: §0–§12 değişmemiştir; bu bölüm mevcut bölümleri derler,
> yeni gözlem / yeni ölçüm üretmez.

### §13.1 Önerilen okuma sırası

| Sıra | Bölüm | Ne verir |
|---|---|---|
| 1 | §0 ELI15 | tek paragraf çerçeve |
| 2 | §1 Örnek kataloğu | her satır tek gözlemlenebilir örüntü |
| 3 | §2 Durum makinesi karşılıkları | repo örneği ↔ çekirdek olayı köprüsü |
| 4 | §4 Doğru ve yanlış kullanım | bu repoda görülen örüntüler |
| 5 | §8 Örnek × durum makinesi matrisi | çapraz okuma |
| 6 | §10 Sık yapılan hatalar | bu dalda 7 hata |
| 7 | §11 Test senaryoları | test dalına verilen taslak |

### §13.2 Kanıt sınıfı dağılımı

| Sınıf | Bölümler | Okuma kuralı |
|---|---|---|
| Doğrudan repo kanıtı | §2 · §4 | dosya/satır bağımlı |
| Negatif kanıt (repo'da olmayan) | §3 | "yok" iddiasıdır, varlık kanıtı değil |
| Tarihli kaynak (HEAD 200 · 2026-10-13) | §5 | envanterden yeniden kullanım |
| Kavramsal / ölçülmemiş | §7 | ölçüm iddiası olarak KULLANILAMAZ |
| Ölçüm (dosya içi tablo) | §9 | bu dosyanın kendi ölçümü |

### §13.3 Sınır beyanı

- Bu dosya çekirdek kodu YAZMAZ; örnek kataloğu ve matris taşır (§1 · §8).
- Repo'da olmayan çekirdek kodu iddiası yalnız §3 negatif kanıt biçimiyle geçerlidir.
- Kavramsal senaryolar ölçülmemiştir (§7); "ölçüldü" diye sunulamaz.
- Açık sorular §6'da açık olarak kalır — kapanmış gibi yazılmaz.
- `git commit` bu oturumda ATILMADI — bu ekleme working tree'dedir (commit orkestratöre ait).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
