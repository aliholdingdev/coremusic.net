---
node_id: K013-integraion
node_type: domain
role: bagimliliklar
status: draft
date: 2026-10-12
version: 1.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.13 + ADR-097 §5.2"
group: mimari
---


# K013-integraion — integraion-bagimliliklar.md

> **Etiket kuralı:** Aşağıdaki her bağımlılık satırı **TASARIM İDDİASI**'dır —
> isimlerden ve v2 ağacından türetildi, test edilmedi. Kanıt olarak yalnızca
> (a) v2 §2.13 çocuk adları ve (b) 2026-10-12 beyanları geçerlidir.
> **İsim notu:** yeni dosya ön eki düğüm adından gelir (**integraion**);
> master dosyası v2'deki adıyla **integration-master.md**'dir (imla dokunulmaz).

## §0 Etiket sözlüğü (bu belge boyunca)

| Etiket | Anlamı |
|---|---|
| TASARIM İDDİASI | Türetilmiş, test edilmemiş tasarım — onaylanana kadar bağlayıcı değil |
| ⚠️ VERIFICATION REQUIRED / VERIFY REQUIRED | Cevabı bilinmiyor, tahmin yazılmadı |
| ✅ verified | Kanıt var (v2 metni veya 2026-10-12 beyanı) |

## §1 Giden bağımlılıklar (depends_on →)

| # | Hedef düğüm | Ne için (ELI15) | Bağımlılık tipi | Risk |
|---|---|---|---|---|
| 1 | K007-data | Senkron sonucunu saklamak | veri erişimi | Şema K007'de kalmalı; sync yazımı yarış riski ⚠️ |
| 2 | K008-domain | Kontrat tiplerini almak | tip tüketimi | Dış şema ile domain tipi eşleşmeli ⚠️ |
| 3 | K009-security | Anahtar/secret/imza güvenliği | güvenlik | Politika K009'da; K013 secret saklamaz |
| 4 | K0100-identitiy | OAuth/PKCE oturumu ve kimlik | kimlik | Yazım "identitiy" — dokunulmaz (v2 §3) |
| 5 | K010-network | HTTP vb. taşıma | taşıma | Rate limit/TLS ayrıntısı K014/K009'da |

## §2 Gelen bağımlılıklar (used_by ←)

| # | Kaynak düğüm | Ne için | Etiket |
|---|---|---|---|
| 1 | K011-application | Kullanıcının başlattığı dış çağrı akışı | TASARIM İDDİASI |
| 2 | K012-services | integration-service'in kontrat/adapter tüketimi | TASARIM İDDİASI |
| 3 | K014-media | Dış katalog/meta senkronu | TASARIM İDDİASI (sınır ⚠️) |
| 4 | Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

## §3 Entegrasyon sınırları (K013 dışına çıkış kapıları)

| # | Sınır | Hangi arayüzden geçilir | Sahip düğüm | Arayüz adı |
|---|---|---|---|---|
| 1 | Dış dünya | adaptör → üçüncü taraf API | K013 | ⚠️ UNKNOWN (biçim, sürüm stratejisi) |
| 2 | Taşıma | HTTP vb. istek | K010-network | ⚠️ UNKNOWN (istemci kütüphanesi) |
| 3 | Kimlik | OAuth/PKCE token akışı | K0100-identitiy | ⚠️ UNKNOWN (akış sahipliği §7/5) |
| 4 | Güvenlik | anahtar okuma/imza | K009-security | ⚠️ UNKNOWN (politika biçimi) |
| 5 | Kalıcılık | senkron sonucu yazma | K007-data | ⚠️ UNKNOWN (tablolar) |
| 6 | İç servis | kontrat tüketimi (K012) | K012-services | ⚠️ UNKNOWN (yön §7/3) |
| 7 | Medya | katalog senkronu (K015) | K014-media | ⚠️ UNKNOWN (sahiplik §7/2) |

### §3.1 Sınır kuralı (Dependency Rule — bu domain için)

1. K013 dış dünyaya **tek** kapıdır; iç servisler dışarıya doğrudan çıkmaz (iddia).
2. Diğer domain'ler dışarıya yalnız K013 kontratı üzerinden çıkar (iddia).
3. Hata ve tekrar-dene politikası bu kapıda toplanır (error-handling) — iddia.
4. Her kapı için arayüz adı ayrı kanıtla belgelenir (tur-2) — şimdi UNKNOWN.

## §4 Riskli bağımlılıklar

| # | Risk | Neden riskli | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | YouTube Music resmi public API yok iddiası | TOS riski, kanıtsız | hukuki/entegrasyon riski | §7/1 — ⚠️ VERIFY REQUIRED |
| 2 | synchronization iki domain'de (K013 + K015) | aynı ad, iki sahip | çakışan yazım | §7/2 — sınır ADR'si |
| 3 | Çağıran (K012) ↔ kontrat (K013) yönü | yön belirsiz | döngü riski | §7/3 — yön kilidi |
| 4 | External API sürüm değişimi | kontrat kırılır | kırılma yayılır | sürümleme stratejisi (§7/6) |
| 5 | Secret/anahtar sızıntısı | K009 dışında tutulursa | güvenlik olayı | K009 sahipliği zorunlu |
| 6 | PKCE/JWT iddiaları genel karar sanılır | beyan ≠ karar | yanlış tasarım | ADR (§7/4–5) |
| 7 | master "integration" vs yeni "integraion" | iki yazım aynı klasörde | yanlış arama | bilinçli (ADR-097 §5.4) |

**Döngü denetimi:** §1'de K013'e geri dönüş yok → graf **DAG** (2026-10-12).

## §5 Değişiklik kuralı

1. Satır eklemek/çıkarmak: Vault Steward onayı + ADR-097 §5.2 kaydı.
2. Yeni bağımlılık = yeni entegrasyon sınırı (§3 + §3.1) ve risk satırı (§4).
3. Etiket zorunlu: her satır TASARIM İDDİASI, ✅ verified veya ⚠️ ile işaretli.
4. "Kesin öyle" cümlesi bu dosyada **yazılmaz** (Truth Mode).
5. v2 adları imla düzeltmesi yapılmadan yazılır (integraion · identitiy dahil).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

---

## EK-B-2026-10-18 — Derinleştirme eki (§6–§15)

> **Etiket devamı:** Bu ek de §0 etiket sözlüğüne bağlıdır — aşağıdaki her satır
> **TASARIM İDDİASI**'dır; yalnız `[K]` kaynaklı satırlar ✅ verified etiketi
> taşır. Ek, §0–§5'i **değiştirmez** (append-only); §5'in değişiklik kuralı bu
> ek için de geçerlidir. Kaynak kısaltmaları `../integration-master.md` §19'daki
> ana tablodan alınmıştır (W-01 outbox · W-03 saga · W-08 GitHub imza · W-10
> IdempotentReceiver · W-12 Stripe imza · W-16 Pub/Sub).

## §6 Sahiplik matrisi — her varlığın tek sahibi (TASARIM İDDİASI)

§4/2'deki "synchronization iki domain'de" riskinin yapısal çözümü: **varlık bazlı
sahiplik**, klasör adı bazlı değil.

| # | Varlık | Sahip | Kullanıcı | Kullanım biçimi | İddia dayanağı |
|---|---|---|---|---|---|
| 1 | Outbox tablosu (`outbox`) | K007-data | K013 | SELECT (poll) + UPDATE (işaretle) | W-01 — yazım tek DB transaction'ında |
| 2 | Inbox tablosu (`inbox`) | K007-data | K013 | INSERT ... ON DUPLICATE + SELECT | W-10 — tekilleştirme alıcıda |
| 3 | DLQ tablosu (`dlq`) | K007-data | K013 + operasyon | INSERT (kalıcı hata) + elle okuma | KAPI-6 |
| 4 | Olay kataloğu (`event-catalog`) | K013 | K008-domain | sürüm → şema okuma | KAPI-2 |
| 5 | HMAC secret | K009-security | K013 | okuma (imzalama/doğrulama) | §1/3 — K013 saklamaz |
| 6 | DTO şemaları | K008-domain | K013 + K012 | tip tüketimi | §1/2 |
| 7 | Adapter (üçüncü taraf) | K013 | — | tek sahip | §3/1 — dış kapı K013'ün |
| 8 | OAuth/PKCE oturumu | K0100-identitiy | K013 | token akışı | §1/4 |
| 9 | HTTP istemci yapılandırması | K010-network | K013 | taşıma | §1/5 |
| 10 | Senkron job zamanlaması | K013 | — | cron/poller | iddia — tur-2 kanıtı ⚠️ |

**Kural (TASARIM İDDİASI):** bir satırda "sahip" ve "kullanıcı" ayrıysa,
**yazma yetkisi yalnız sahibindedir**; kullanıcı yalnız okur. İstisna: outbox'ta
K013 `UPDATE` yapar — çünkü outbox **yazımı** iş akışının (saga/outbox writer)
parçasıdır, şemanın değil (W-01: satır aynı transaction'da yazılır, ayrı
transaction'da yayınlanır).

### §6.1 Sahiplik çakışması denetimi (§4/2 derinlemesine)

| Senaryo | Çakışma? | Yapısal çözüm (iddia) |
|---|---|---|
| K013 + K015 ikisi de "synchronization" klasörüne yazar | EVET (mevcut durum) | Varlık matrisi (§6) ile klasör adı önemsizleşir; klasör yalnız bulunabilirlik |
| K015 kendi outbox'unu ister | Hayır (aynı varlık, ikinci kullanıcı) | K007'de **tek** `outbox` tablosu; `kaynak_domain` sütunu ile ayrım (iddia) |
| K015 kendi inbox'unu ister | Hayır | Aynı tablo; `idempotency_key` alanı domain-özel önekli (iddia) |
| İki domain aynı olayı yayınlar | EVET — olay çakışması | Olay adı öneki zorunlu: `evt.{domain}.{olay}` (§30'daki kapı eşlemesi ile aynı kural) |

## §7 Sözleşme-yüzeyi envanteri (contract surface — TASARIM İDDİASI)

K013'ün dışarıya açtığı her sözleşme yüzeyi ve dondurma durumu:

| # | Yüzey | Biçim | Donduruldu mu? | Kanıt |
|---|---|---|---|---|
| 1 | Olay şeması (outbox → kuyruk) | JSON + `event_type` + `event_version` | ✅ donduruldu | `event-contracts/event-sozlesme.md` §KAPI-1 |
| 2 | Inbox kayıt anahtarı | `idempotency_key` (TEXT PK) | ✅ donduruldu | `synchronization/sync-kod-ornekleri.md` §23 şeması |
| 3 | Webhook imza başlığı | `t=<ts>,v1=<hex>` (Stripe) / `sha256=<hex>` (GitHub) | ✅ iki biçim | W-12 · W-08 — §31 · §34 |
| 4 | DTO (servislerarası) | K008-domain tipleri | ⚠️ TASARIM İDDİASI | §1/2 — dış şema eşleşmesi doğrulanmadı |
| 5 | Adapter çıktı DTO'su | üçüncü-parça API'ye göre | ⚠️ UNKNOWN | §3/1 — biçim ve sürüm stratejisi bilinmiyor |
| 6 | Gateway routing kuralı | host eşleme + timeout | ⚠️ TASARIM İDDİASI | `protocol-adapters/proto-sozlesme.md` ekleri |
| 7 | DLQ müdahale betiği arayüzü | CLI (Bash/PS) | ✅ kod örnekli | §33 · §34 |

**Yüzey-dondurma kuralı (iddia):** bir yüzey dondurulduysa, değişiklik **yalnız**
geriye-uyumlu (additive) olabilir; kıran değişiklik yeni sürüm numarası açar ve
eski sürüm **en az bir geçiş dönemi** yaşar (W-12'nin 3 güne kadar canlı retry
davranışı gibi — dış bağımlılık eski sürümü hâlâ gönderebilir).

## §8 DTO ve olay akış analizi — uçtan uca yolculuk (TASARIM İDDİASI)

Bir dış çağrının (ör. ödeme webhook'u) tam yolculuğu ve her adımda sahibi:

```text
[üçüncü taraf] --webhook--> [K010-network taşır] --> [K013 gateway]
   1. taşıma: TCP/TLS (K010 sahipliğinde — K013 yalnız okur)
   2. imza doğrulama: K013 (ham gövde + tolerance — W-12)
   3. idempotency: K013 → inbox (K007'de — W-10)
   4. olay şema doğrulama: K013 (event-catalog — KAPI-2)
   5. iş mantığı: K012-services (K013 yalnız kontrat verir)
   6. yanıt: K013 → 200 (hızlı) · iş asenkron (outbox'a yazılır)
   7. outbox poll: K013 → kuyruk (W-01 — en az bir kez)
   8. diğer domain inbox: alıcı tekilleştirir (W-10)
```

| Adım | Başarısızsa | Sahip | Davranış (iddia) |
|---|---|---|---|
| 1 | TLS el sıkışması | K010 | üçüncü taraf retry — K013 görmez |
| 2 | İmza geçersiz | K013 | 401 + DLQ'ya YAZMAZ (güvenlik olayı — log) |
| 2 | İmza süresi aşılmış | K013 | 401 — yeniden gönderim işe yaramaz (§33 filtresi) |
| 3 | Aynı anahtar + farklı gövde | K013 | 409 + log (§27 `ValueError` karşılığı) |
| 4 | Şema sürümü katalogda yok | K013 | DLQ (KAPI-6 — elle müdahale) |
| 5 | İş mantığı hatası | K012 | hata domain'e döner; K013 retry uygulamaz (kalıcı hata) |
| 6 | Yanıt verilemez | K013 | üçüncü taraf tekrar dener — inbox korur (W-10) |
| 7 | Kuyruk publish başarısız | K013 | outbox satırı kalır; backoff+jitter (§28) |
| 8 | Alıcı yok | alıcı | kuyruk retry + DLQ (W-16 `max_delivery_attempts`) |

## §9 Kopuş analizi — hangi bağımlılık koparsa ne olur (failure modes)

| # | Kopuş | Belirti | Yayılım | Kurtarma (iddia) | Öncelik |
|---|---|---|---|---|---|
| 1 | Dış API erişilemez (timeout) | yükselen latansı | outbox birikir | backoff+jitter + DLQ eşiği alarmı (§36) | HIGH |
| 2 | Dış API 5xx (kalıcı) | tekrarlayan hata | DLQ dolar | elle inceleme (KAPI-6) | MEDIUM |
| 3 | Secret değişimi (K009) | tüm imzalar düşer | webhook'lar 401 | iki-anahtar geçiş penceresi (iddia ⚠️) | HIGH |
| 4 | Şema sürümü düşüklüğü (alıcıda) | DLQ'da `sürüm-yok` | tek olay tipi durur | alıcı kataloğu güncelle (KAPI-2) | HIGH |
| 5 | DB yazım gecikmesi (K007) | outbox poll boş döner | yayın gecikir | poller uyarısı (§28 log) | MEDIUM |
| 6 | Kuyruk broker kaybı | publish hata | outbox birikir | W-16 at-least-once — satır kaybolmaz | HIGH |
| 7 | Eşzamanlı retry fırtınası | latansı patlaması | tüm alıcılar yavaşlar | jitter ZORUNLU (§17) — jitter'siz yol yok | HIGH |
| 8 | Yarım-kalmış saga (crash) | stok rezerve, ödeme yok | stok sızar | saga kayıt + geri çekme (§26 §30) | HIGH |
| 9 | Inbox `processing` kilitlenmesi | tekrar gönderim `TimeoutError` | tek anahtar durur | süre-sonu denetimi (iddia ⚠️ — tur-2) | MEDIUM |
| 10 | DLQ sessiz dolması | veri kaybı görünmez | kayıp fark edilmez | alarm 60 sn (§36) — sessizlik YASAK | CRITICAL |

**Kopuş analizi kuralı (iddia):** her kopuş satırı ya bir **alarm** (§36) ya bir
**fiziksel mekanizma** (backoff/DLQ/inbox) ya da açık bir `⚠️ VERIFICATION
REQUIRED` ile bitmelik — **"sonra bakarız" satırı yazılmaz**.

## §10 Bağımlılık yönü ve çevrilmesi (dependency inversion — TASARIM İDDİASI)

§3.1'in derinleşmesi: K013, dış servislere (K012) **bağımlı değil, çevrilidir**:

```text
K012-services  →  K013 kontrat (arayüz)  ←  K013 adaptör uygulamaları
                        ↑
            K008-domain tipleri (şema)
```

1. K012, K013'ün **arayüzünü** (kontrat) bilir; hangi adaptörün çalıştığını değil.
2. K013 adaptörleri bu arayüzü uygular (port/adapter — içe-dönüklük).
3. Şema K008'dedir; iki taraf da aynı tipten okur (§1/2 riski bu noktada azalır).

**Doğrulanmamışlık:** bu yön iddia edilir; K012 kodunda gerçek `use`/`import`
yönü tur-2 kanıtıdır (`⚠️ VERIFICATION REQUIRED` — §11 backlog madde 3).

## §11 Tur-2 kanıt backlog'u (her madde: ne, nasıl doğrulanır, sahip)

| # | Madde | Doğrulama yöntemi | Sahip | Durum |
|---|---|---|---|---|
| 1 | YouTube Music API durumu (§4/1) | hukuki + teknik inceleme | K014-media | ⚠️ VERIFY REQUIRED |
| 2 | synchronizasyon sınır ADR'si (§4/2) | ADR yazımı + onay | Vault Steward | ⚠️ ADR BEKLİYOR |
| 3 | K012→K013 yönü (§10) | kodda `use` taraması | K012 sahibi | ⚠️ |
| 4 | PKCE/JWT kararları (§4/6) | ADR-010 bütünlüğü + yeni ADR | K0100-identitiy | ⚠️ |
| 5 | Adapter biçim/sürüm stratejisi (§3/1) | üçüncü-parça dokümanı | K013 | ⚠️ UNKNOWN |
| 6 | HTTP istemci kütüphanesi (§3/2) | K010 kod envanteri | K010-network | ⚠️ UNKNOWN |
| 7 | Senkron job zamanlaması (§6/10) | K013 kod envanteri | K013 | ⚠️ |
| 8 | İki-anahtar secret geçişi (§9/3) | K009 politikası | K009-security | ⚠️ |
| 9 | Inbox `processing` süresonu (§9/9) | kod + test | K013 | ⚠️ |
| 10 | Outbox poll periyodu (§28 `tick`) | ADR ile sabitleme | K013 | ⚠️ VERIFICATION REQUIRED |

**Backlog kuralı:** bu tablo tamamlanmadan §1–§4'teki hiçbir TASARIM İDDİASI
"✅ verified" etiketine **yükseltilemez** (Truth Mode).

## §12 Değişim-etàki matrisi (change impact — TASARIM İDDİASI)

Hangi değişiklik hangi düğümleri vurur:

| # | Değişiklik | Vurulan düğüm(ler) | Geriye-uyumlu mu? | Zorunlu kapısı |
|---|---|---|---|---|
| 1 | Olay şemasına alan ekleme | yalnız üretici | ✅ evet (additive) | KAPI-1 |
| 2 | Olaydan alan çıkarma | tüm alıcılar | ❌ hayır — yeni sürüm | KAPI-2 |
| 3 | İmza algoritması değişimi | tüm webhook alıcıları | ❌ hayır — iki-anahtar penceresi | KAPI-3 |
| 4 | Outbox şeması (`outbox` sütunu) | K007 + K013 | ⚠️ pencere | KAPI-1 |
| 5 | Retry taban-değeri | yalnız poller | ✅ evet | KAPI-7 |
| 6 | DLQ eşiği (`max_delivery_attempts`) | operasyon + alarm | ✅ evet (§36 değişkeni) | KAPI-6 |
| 7 | Adapter üçüncü-parça sürüm atlaması | yalnız adaptör | ⚠️ adaptöre bağlı | §3/1 |
| 8 | Gateway timeout değeri | tüm dış çağrılar | ✅ evet | proto-sozlesme |

## §13 Çalışma-zamanı bağımlılık grafiği (runtime — TASARIM İDDİASI)

Bağımlılık-dosya grafiği (§1–§2) ile **çalışma-zamanı** grafiği farklıdır:

```text
Başlangıç:  K009 (secret) + K007 (DB havuzu) + K008 (şema) yüklenir
İstek:      K010 → K013 gateway → inbox (K007) → K012 (iş) → yanıt
Yayın:      outbox poll (K013) → broker (⚠️ UNKNOWN — K010 mı, ayrı mı, §11/6)
Alıcı:      broker → inbox → upcast (KAPI-2) → iş mantığı
Arıza:      DLQ (K007) → alarm (§36) → operatör (§33)
```

**Runtime-öncelik kuralı (iddia):** başlangıçta yüklenmeyen bağımlılık
(ör. dış API) istek sırasında kilitlenme üretmez — **asenkron outbox** bunun
yapısal güvencesidir (W-01: HTTP yanıtı dış API'ye bloklanmadan döner).

## §14 Bağımlılık-sağlığı göstergeleri (ölçülebilir — TASARIM İDDİASI)

| # | Gösterge | Eşik | Ölçüm yeri | Alarm |
|---|---|---|---|---|
| 1 | outbox bekleyen satır | ⚠️ ADR | poll sorgusu (§28) | yükselen trend |
| 2 | outbox en-eski satır yaşı | ⚠️ ADR | `MIN(olusturulma)` | 5 dk ✅ (Stripe toleransı ile hizalı iddia) |
| 3 | inbox `processing` yaşı | ⚠️ ADR | süre-sonu denetimi | kilitlenme (§9/9) |
| 4 | DLQ mesaj sayısı | `> 0` | §36 alarmı | 60 sn doğrulama |
| 5 | imza-red oranı | ⚠️ ADR | gateway log | güvenlik olayı (§8/2) |
| 6 | dış API latansı p95 | ⚠️ ADR | adaptör metriği | devir-bırak (circuit breaker — W-04 ailesi) |

## §15 Ek kapanışı — bu ek neyi değiştirmedi, neyi iddia ediyor

1. **Değişmeyenler:** §0–§5, frontmatter, authority, `## §GRUPLAR`-dışı hiçbir
   mevcut satır silinmedi; tüm ek §6–§15'tir (append-only).
2. **İddia edilenler:** §6 sahiplik · §7 sözleşme-yüzeyi · §8 akış · §9 kopuş ·
   §10 yön · §12 değişim-etki · §13 runtime · §14 ölçütler — hepsi TASARIM
   İDDİASI etiketlidir.
3. **Kanıtlananlar:** yalnız dış kaynaklı olgular (W-01 · W-08 · W-10 · W-12 ·
   W-16 — `../integration-master.md` §19) ve bu vault'ta kod örnekli bölümler
   (`sync-kod-ornekleri.md` §20–§38).
4. **Sonraki tur:** §11 backlog'u 10 maddesi — tur-2 kanıtı olmadan hiçbir satır
   ✅'a yükselmez (Truth Mode §11 kuralı).

---

*EK-B-2026-10-18 · integraion-bagimliliklar.md · Authority: Bayram Ali / Vault Steward · Mode: Red Team · Human Mode · Truth Mode*

---

## EK-C-2026-10-18 — Ek dalga 2 (§16–§21): derinlemesine bağımlılık analizi

> Etiket devamı: §0–§15 olduğu gibi korunur; bu dalga yalnız **ek**tir.

## §16 Örnek olay — YouTube Music senkron bağımlılık zinciri (TASARIM İDDİASI)

§4/1'deki riskli bağımlılığın somut analizi:

```text
[YouTube Music] → adaptör (K013) → olay şeması (K008) → outbox (K007) → kuyruk
   ↑ risk:      ↑ risk:            ↑ risk:             ↑ risk:        ↑ güvenli
   TOS/hukuki   biçim UNKNOWN     sürüm              W-01           W-16
```

| # | Halka | Durum | Neden | Kapatma yolu (iddia) |
|---|---|---|---|---|
| 1 | YouTube Music API | ⚠️ VERIFY REQUIRED | resmi public API yok iddiası (§4/1) | hukuki inceleme + resmi doküman kanıtı |
| 2 | Adaptör biçim katmanı | ⚠️ UNKNOWN | §3/1 — biçim/sürüm stratejisi bilinmiyor | adapter-sozlesme.md ekleri + kanıt |
| 3 | Olay şeması | ✅ donduruldu | §7/1 — event-sozlesme.md | mevcut |
| 4 | Outbox | ✅ güvenli | W-01 — aynı transaction | mevcut (§6/1) |
| 5 | Kuyruk | ✅ güvenli | W-16 — at-least-once | mevcut (§9/6) |

**Zincir dersi (iddia):** güvenli halkalar (4–5) zinciri **kurtarmaz** — 1–2
kapalı olmadan senkron **başlamaz bile**; bu yüzden §11 backlog maddesi 1
(YouTube Music hukuki incelemesi) **ön koşuldur**, sonraki maddedir.

## §17 Bağımlılık tipi sözlüğü — hangi tip ne demektir (TASARIM İDDİASI)

§1'deki "bağımlılık tipi" sütununun sözlüğü:

| Tip | Tanım | Kırılma biçimi | Örnek (§1'den) |
|---|---|---|---|
| veri erişimi | tablo/şema okuma-yazma | şema değişimi | K007-data |
| tip tüketimi | DTO/enum paylaşımı | alan tipi değişimi | K008-domain |
| güvenlik | secret/anahtar okuma | secret rotasyonu | K009-security |
| kimlik | token/oturum doğrulama | JWT claim değişimi | ⚠️ |
| taşıma | HTTP/TLS/istek | timeout/protokol | K010-network |
| kontrat | arayüz sözleşmesi | imza değişimi | K012 ↔ K013 |
| politika | kural/kısıt | kural revizyonu | error-handling |

## §18 Kapı → bağımlılık eşlemesi (her kapı hangi bağımlılığa dayanır)

| Kapı | Dayandığı bağımlılık | Koparsa (§9 karşılığı) |
|---|---|---|
| KAPI-1 (sözleşme dondurma) | K008-domain (tip tüketimi) | tip değişimi → sözleşme ihlali (§9/4) |
| KAPI-2 (sürüm uyumluluğu) | event-catalog (K013 sahipliğinde) | katalog gecikmesi → DLQ (§9/4) |
| KAPI-3 (HMAC) | K009-security (güvenlik) | secret değişimi → 401 fırtınası (§9/3) |
| KAPI-4 (saga kapı eşlemesi) | K007-data (veri erişimi) | crash → yarım-kalmış saga (§9/8) |
| KAPI-5 (outbox eşitleme) | K007-data + kuyruk | broker kaybı → outbox birikmesi (§9/6) |
| KAPI-6 (DLQ) | K007-data + operasyon | sessiz dolma → kayıp (§9/10) |
| KAPI-7 (retry+jitter) | K010-network (taşıma) | latansı fırtınası (§9/7) |

## §19 Bağımlılık envanteri — sayım disiplini (ölçüm kuralı)

1. Her §6 satırı bir **varlık**tır; varlık sayımı değişirse bu tablo yeniden
   ölçülür (`[System.IO.File]::ReadAllLines` ile — § ölçüm kuralı).
2. Varlık eklenirse §5/2 gereği **yeni entegrasyon sınırı + risk satırı** da
   eklenir; ikisiz varlık **kayıtsız-varlık** sayılır ve DRIFT'tir.
3. Bu ek dalgada eklenen varlık: **yoktur** (yalnız analiz) — sayım 10'da kalır
   (§6 tablosu).

## §20 DRIFT kayıtları (bu ek dalganın orkestratöre raporu)

| # | DRIFT | Açıklama | Karar |
|---|---|---|---|
| D-1 | `integraion` imla hatası | dosya/düğüm adı imla hatalı ama dokunulmaz (v2 §3 + §5/5) | bilinçli koruma — arama `integraion` ile |
| D-2 | synchronization iki domain'de | K013 + K015 — §6 matrisi çözümü iddia ediyor, ADR bekliyor | §11 backlog madde 2 |
| D-3 | `integration-master.md` vs `integraion-*` | iki yazım aynı klasörde (§4/7) | bilinçli — master'ın adı v2'den |
| D-4 | KAPI-1…KAPI-7 kapıları | bu dosyada §18'de tanımlı; kapı sahipliği K013 dosyalarında | eşleme tutarlı — çelişki yok |

## §21 Ek dalga 2 kapanışı

1. Bu dalga yalnız **analiz** ekledi; §6'daki 10 varlık **değişmedi** (§19/3).
2. §16 zinciri gösterdi: güvenli halkalar zayıf halkayı **kapatmaz**.
3. §18 eşlemesi 7 kapıyı bağımlılıklara bağladı — kopuş-çarpışması (§9) ile
   kapı-çarpışması (§18) aynı satırda izlenebilir.
4. §20 DRIFT kayıtları orkestratöre iletilmek üzere hazır.

---

*EK-C-2026-10-18 · integraion-bagimliliklar.md (§16–§21) · Authority: Bayram Ali / Vault Steward · Mode: Red Team · Human Mode · Truth Mode*

---

## EK-D2-2026-10-18 — Ek dalga 3 (§22–§27): bağımlılık-sağlığı derinlemesi

## §22 Outbox-inbox-saga üçlüsü — hangi bağımlılık hangi mekanizmaya bağlanır

| Mekanizma | Dayandığı bağımlılık | Kopuş senaryo | Bağlantı |
|---|---|---|---|
| outbox | K007-data (veri erişimi) | DB yazım gecikmesi | §9/5 · W-01 |
| inbox | K007-data + K008 (şema) | şema sürümü düşüklüğü | §9/4 · W-10 |
| saga geri çekme | K007 + dış servis | crash → yarım-kalmış saga | §9/8 · W-03 |
| DLQ | K007 + operasyon | sessiz dolma | §9/10 · KAPI-6 |

## §23 Bağımlılık-risk skorlaması (TASARIM İDDİASI — skor: etki × olasılık)

| # | Bağımlılık | Etki (1–3) | Olasılık (1–3) | Skor | Öncelik |
|---|---|---|---|---|---|
| 1 | §4/1 YouTube Music API | 3 | 3 | **9** | CRITICAL (hukuki) |
| 2 | §4/2 synchronization sınırı | 2 | 3 | **6** | HIGH (ADR) |
| 3 | §9/3 secret rotasyonu | 3 | 2 | **6** | HIGH (§G3) |
| 4 | §9/7 retry fırtınası | 3 | 2 | **6** | HIGH (jitter ZORUNLU) |
| 5 | §4/3 K012↔K013 yönü | 2 | 2 | **4** | MEDIUM (§11/3) |
| 6 | §4/4 dış sürüm değişimi | 2 | 2 | **4** | MEDIUM (§12/1–2) |
| 7 | §4/5 secret sızıntısı | 3 | 1 | **3** | MEDIUM (K009) |

**Skor kuralı (iddia):** skor ≥6 → HIGH/CRITICAL kapısı **ön-koşuldur**; skor ≤3
olasılık düşük olsa bile **gözlemlenir** (§14 göstergeleri) — gözlem yoksa
olasılık tahmini **uydurmadır**.

## §24 Bağımlılık grafiği — düğüm-bazlı özet (§6 + §1 + §2 tek tabloda)

| Düğüm | Giden (→) | Gelen (←) | Kritik tek-nokta? |
|---|---|---|---|
| K013 | K007 · K008 · K009 · K0100 · K010 | K011 · K012 · K014 | ✅ evet — dış kapı tek (§3.1/1) |
| K007 | — | K013 (+K015 iddia) | ✅ DB tek |
| K008 | — | K013 | ⚠️ şema dondurulmuş (KAPI-1) |
| K009 | — | K013 | ✅ secret tek |
| K010 | K013 | — | ⚠️ istemci kütüphanesi UNKNOWN (§11/6) |

**Tek-nokta kuralı (iddia):** K013 dış dünyaya **tek** kapıdır (§3.1/1) — bu
hem avantaj (tek denetim noktası) hem risk (kapı arızası tüm dış iletişimi
durdurur); §G6 devreye-bırak hiyerarşisi bu tek-noktanın **sigortasıdır**.

## §25 Bağımlılık-ölçüm defteri — ne, ne zaman, kim ölçer

| # | Ölçüm | Araç | Sıklık | Sahip |
|---|---|---|---|---|
| 1 | outbox bekleyen satır | §28 poller | sürekli | K013 |
| 2 | DLQ mesaj sayısı | §36 alarmı | 60 sn | operatör (§33) |
| 3 | dış latansı p95 | adaptör metriği | sürekli | K013 (§G7) |
| 4 | imza-red oranı | gateway log | günlük | K009 (§G7/5) |
| 5 | bağımlılık-grafı tutarlılığı | `index.md` §GRUPLAR + bu dosya §24 | her revizyon | Vault Steward |

## §26 §11 backlog'unun skor etkileşimi

Backlog maddesi 1 (YouTube Music hukuki — §11) skor **9** (§23/1) taşır: bu
madde kapanmadan **hiçbir** dış adaptör kurulmaz (§16 zinciri + §G2 kapısı).
Madde 2 (sync sınır ADR'si) skor **6**: ADR yazılmadan §6 sahiplik matrisi
**onaylı** sayılmaz — mevcut hâliyle TASARIM İDDİASI olarak kalır.

## §27 Ek dalga 3 kapanışı

1. §22–§27, §6'daki 10 varlığa **yeni varlık eklemez** (§19/3 kuralı korundu).
2. Skorlama (§23) keyfi değildir: etki-olasılık 1–3 ölçeği **açık** tanımlanmıştır;
   skor ≥6 kapıları önceliklendirir.
3. Tek-nokta tespiti (§24): K013 dış-kapısı tek — bu bilinçli mimari tercih
   (§3.1) ve §G6 ile sigortalıdır.
4. Bu ek dalganın DRIFT kaydı **yoktur** (§20'deki D-1…D-4 değişmedi).

---

*EK-D2-2026-10-18 · integraion-bagimliliklar.md (§22–§27) · Authority: Bayram Ali / Vault Steward · Mode: Red Team · Human Mode · Truth Mode*

---

## EK-D3-2026-10-18 — Ek dalga 4 (§28–§33): bağımlılık-topolojisi son sözleşmeler

## §28 Düğümler-arası mesafeler — hangi bağımlılık "uzak"tır (mesafe = müdahale maliyeti)

| # | Bağımlılık | Mesafe (katman) | Müdahale maliyeti | İddia |
|---|---|---|---|---|
| 1 | K007 (DB) | aynı süreç (PDO/istek) | düşük — tek konfigürasyon | TASARIM İDDİASI |
| 2 | K008 (şema) | derleme/iç aktarım | orta — sürümleme kapısı (KAPI-1) | TASARIM İDDİASI |
| 3 | K009 (secret) | çalışma-zamanı okuma | orta — iki-anahtar penceresi (§G3) | TASARIM İDDİASI |
| 4 | K010 (taşıma) | ağ | yüksek — dış bağımlılık (timeout eşitsizliği §D1.1) | TASARIM İDDİASI |
| 5 | dış API | ağ + hukuki | çok yüksek — kapı-1 (§G2) | TASARIM İDDİASI |

**Mesafe kuralı (iddia):** mesafe arttıkça **otomatik** kurtarma azalır, **insani**
müzakere artar: katman 1–2'de rollback/ADR yeterli; katman 4–5'te hukuki/operasyon
kapıları devreye girer.

## §29 Ters-bağımlılık denetimi — kim kimi "bilir" (§10'un sayımı)

| # | Bilen | Bildiği | Bilme biçimi | İhlal? |
|---|---|---|---|---|
| 1 | K012 | K013 kontratı | interface (port) | ❌ ihlal yok |
| 2 | K013 | K008 tipleri | doğrudan import | ❌ (şema paylaşımlı) |
| 3 | K013 | K009 secret | okuma arayüzü | ❌ (üretmez — §1/3) |
| 4 | adaptör | dış API | HTTP + imza | ❌ (tek kapı) |
| 5 | dış API | CoreMusic | **asla** | ❌ (adreslenmez — §D1/1) |

## §30 Bağımlılık-bildirimi sözleşmesi — yeni bağımlılık nasıl eklenir (§5/2 derinleşmesi)

1. **Talep:** yeni bağımlılık satırı + tipi (§17 sözlüğü) + risk skoru (§23).
2. **Sınır:** yeni entegrasyon sınırı (§3/§3.1) + kapı eşlemesi (§18).
3. **Kopuş:** kopuş satırı (§9) + gözlem sinyali (§14).
4. **Onay:** Vault Steward + ADR-097 §5.2 kaydı (§5/1).
5. **Ölçüm:** §25 ölçüm defterine satır.

Beş adım tamamlanmadan yeni bağımlılık satırı **eklenmez**; eklenirse DRIFT'tir.

## §31 Graf-döngü (cycle) denetimi — bu dalganın son ölçümü

§1'de (2026-10-12) döngü yoktu (DAG). Bu dalgalardan sonra:

| Kenar | Artış? |
|---|---|
| K013 → K007 · K008 · K009 · K0100 · K010 | hayır (mevcut) |
| K011 · K012 · K014 → K013 | hayır (mevcut) |
| Yeni kenar | **0** (§19/3 kuralı — ekler analizdir) |

**Sonuç:** graf hâlâ **DAG**; döngü sayısı **0** (2026-10-18 ölçümü).

## §32 Bağımlılık-anayasası kısa özeti (tek sayfa — §6+§23+§24 birleşimi)

```text
10 varlık · 5 giden · 4 gelen · 7 kapı · 1 tek-nokta (dış kapı) · DAG (0 döngü)
En yüksek risk: hukuki (skor 9) → kapalı kapı: adaptör kurulmaz
En düşük müdahale maliyeti: K007 (aynı süreç)
En yüksek: dış API (ağ + hukuki)
```

## §33 Ek dalga 4 kapanışı

1. Bu dalga yalnız **ölçüm ve sözleşme** ekledi; varlık sayısı 10'da sabit (§19/3).
2. DAG denetimi (§31): döngü **0** — §1/2026-10-12 beyanı **korundu**.
3. Yeni bağımlılık-bildirimi 5 adımı (§30) sonraki tur için bağlayıcıdır.
4. DRIFT: yeni **yok** (§20 D-1…D-4 değişmedi).

---

*EK-D3-2026-10-18 · integraion-bagimliliklar.md (§28–§33) · Authority: Bayram Ali / Vault Steward · Mode: Red Team · Human Mode · Truth Mode*
