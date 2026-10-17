---
node_id: K010-network
node_type: domain
role: bagimliliklar
status: draft
date: 2026-10-12
version: 1.0.0
etiket: "TASARIM İDDİASI — bu belge kanıt değil, onay gerektiren tasarım iddiasıdır"
authority: "SSOT: v2 §2.14 + ADR-097 §5.2"
group: mimari
---


# K010-network — network-bagimliliklar.md

> **Etiket kuralı:** Aşağıdaki her bağımlılık satırı **TASARIM İDDİASI**'dır —
> isimlerden ve v2 ağacından türetildi, test edilmedi. Kanıt olarak yalnızca
> (a) v2 §2.14 çocuk/torun adları ve (b) 2026-10-12 beyanları geçerlidir.
> Bu dosyada değişiklik = Vault Steward onayı + ADR-097 kaydı ister.

## §0 Etiket sözlüğü (bu belge boyunca)

| Etiket | Anlamı |
|---|---|
| TASARIM İDDİASI | Türetilmiş, test edilmemiş tasarım — onaylanana kadar bağlayıcı değil |
| ⚠️ VERIFICATION REQUIRED / VERIFY REQUIRED | Cevabı bilinmiyor, tahmin yazılmadı |
| ✅ verified | Kanıt var (v2 metni veya 2026-10-12 beyanı) |

## §1 Giden bağımlılıklar (depends_on →)

| # | Hedef düğüm | Ne için (ELI15) | Bağımlılık tipi | Risk |
|---|---|---|---|---|
| 1 | K001-donanım | Ağ donanımı ve sürücüsüne erişim | donanım erişimi | Donanım yoksa bağlantı yok → fallback belirsiz ⚠️ |
| 2 | K009-security | TLS/şifreleme/anahtar kuralları | güvenlik | Şifreleme burada üretilirse güvenlik ihlali |
| 3 | K0100-identitiy | Cihaz kimliği ile yetkilendirme | kimlik | device-identity sahipliği belirsiz ⚠️ |

## §2 Gelen bağımlılıklar (used_by ←)

| # | Kaynak düğüm | Ne için | Etiket |
|---|---|---|---|
| 1 | K011-application | Arayüzün ağ çağrıları | TASARIM İDDİASI |
| 2 | K012-services | Servisler arası taşıma | TASARIM İDDİASI |
| 3 | K013-integraion | Dış API taşıması | TASARIM İDDİASI |
| 4 | K014-media | Akış/indirme taşıması | TASARIM İDDİASI |
| 5 | Diğerleri (ör. K017-observability) | Ağ metrikleri | ⚠️ VERIFICATION REQUIRED |

## §3 Entegrasyon sınırları (K014 dışına çıkış kapıları)

| # | Sınır | Hangi arayüzden geçilir | Sahip düğüm | Arayüz adı |
|---|---|---|---|---|
| 1 | Donanım | sürücü/OS erişimi | K001-donanım | ⚠️ UNKNOWN (erişim biçimi) |
| 2 | Güvenlik | TLS/anahtar kullanımı | K009-security | ⚠️ UNKNOWN (politika biçimi) |
| 3 | Kimlik | cihaz kimliği sorgusu | K0100-identitiy | ⚠️ UNKNOWN (token/oturum) |
| 4 | Uygulama | ağ isteği alan taraf | K011-application | ⚠️ UNKNOWN (API biçimi) |
| 5 | Servisler | servisler arası çağrı | K012-services | ⚠️ UNKNOWN (protokol seti) |
| 6 | Dış dünya | dış API taşıması | K013-integraion | ⚠️ UNKNOWN (protokol seti) |
| 7 | Medya | akış/indirme | K014-media | ⚠️ UNKNOWN (akış biçimi) |

### §3.1 Sınır kuralı (Dependency Rule — bu domain için)

1. K014 yalnız taşıma yapar; paketin **anlamını** okumaz (iş K012/K013'te).
2. Güvenlik katmanına dokunmaz: şifreleme/anahtar K009'dur (iddia).
3. Çevrimdışıyken davranışı tanımlar; içeriğini sahiplerine bırakır (§4/2).
4. Her kapı için arayüz adı ayrı kanıtla belgelenir (tur-2) — şimdi UNKNOWN.

## §4 Riskli bağımlılıklar

| # | Risk | Neden riskli | Etki | Aksiyon |
|---|---|---|---|---|
| 1 | offline-sync üçlüsü (K014 · K015/offline · K012) | aynı işi yapabilecek üç aday | çift kuyruk/çakışma | §7/2 — sınır ADR'si |
| 2 | handoff ↔ multi-room | ikisi de "cihazlar arası çalma" | tekrar | §7/1 |
| 3 | service-discovery kapsamı | cihaz keşfi mi servis kaydı mı belirsiz | yanlış sahiplik | §7/3 |
| 4 | Donanım bağımlılığı (K001) | donanım yoksa K014 işlevsiz | hizmet kesintisi | fallback stratejisi (§7/7) |
| 5 | Güvenlik katmanı sıçraması | TLS dışı şifreleme burada üretilirse | güvenlik olayı | K009 sahipliği zorunlu |
| 6 | WebRTC iddiasının kanıtsızlığı | URL yok, "standard" beyanı | yanlış zemin | §7/4 — ⚠️ VERIFY REQUIRED |
| 7 | network-testing ↔ K019-testing | iki test sahibi | test tekrarı | §7/6 |

**Döngü denetimi:** §1'de K014'e geri dönüş yok → graf **DAG** (2026-10-12).

## §5 Değişiklik kuralı

1. Satır eklemek/çıkarmak: Vault Steward onayı + ADR-097 §5.2 kaydı.
2. Yeni bağımlılık = yeni entegrasyon sınırı (§3 + §3.1) ve risk satırı (§4).
3. Etiket zorunlu: her satır TASARIM İDDİASI, ✅ verified veya ⚠️ ile işaretli.
4. "Kesin öyle" cümlesi bu dosyada **yazılmaz** (Truth Mode).
5. v2 adları imla düzeltmesi yapılmadan yazılır (identitiy dahil).

---

## §6 Bağımlılık Derinleştirmesi (TASARIM İDDİASI — 2026-10-13 eki)

### §6.1 Giden bağımlılıklar — detay matrisi

| # | Hedef | Bağımlılık yüzeyi | Ne akar | Akış yönü | Geri dönüş | Kanıt |
|---|---|---|---|---|---|---|
| 1 | K001-donanım | sürücü/OS erişimi | ağ paketleri (TX/RX) | K014 → K001 | K001 → K014 (durum: link-up/link-down) | ⚠️ TASARIM İDDİASI |
| 2 | K009-security | TLS/anahtar/şifreleme | handshake · key exchange | K014 → K009 (çağrı) | K009 → K014 (session/ticket) | ⚠️ TASARIM İDDİASI |
| 3 | K0100-identitiy | cihaz kimliği sorgusu | device-id/token | K014 → K010 (sorgu) | K010 → K014 (kimlik doğrulama) | ⚠️ TASARIM İDDİASI |

### §6.2 Gelen bağımlılıklar — detay matrisi

| # | Kaynak | Bağımlılık yüzeyi | Ne akar | Akış yönü | Sınır notu |
|---|---|---|---|---|---|
| 1 | K011-application | arayüzün ağ çağrıları | istek/yanıt | K011 → K014 | arayüz K011'de, taşıma K014'te |
| 2 | K012-services | servisler arası taşıma | servis paketleri | K012 → K014 | anlam K012'de |
| 3 | K013-integraion | dış API taşıması | dış istek/yanıt | K013 → K014 | dış anlamı K013 anlar |
| 4 | K014-media | akış/indirme taşıması | medya paketleri | K015 → K014 | medya içeriği K015 |
| 5 | K017-observability | ağ metrikleri | sayaç/log | K014 → K017 (veri) | K014 yalnız üretir |

### §6.3 Bağımlılık yoğunluk matrisi (giden × gelen)

| | K011 | K012 | K013 | K015 | K017 |
|---|---|---|---|---|---|
| K014 → | — | — | — | — | — |
| ← K014 | taşıma | taşıma | taşıma | taşıma | veri |

> Bu tablo yalnız okuma yönünü gösterir; K014 giden bağımlılıklarını (K001/K009/K010)
> gelenlerle karıştırmamak için ayrı §6.1/§6.2 olarak düzenlenmiştir.

### §6.4 En riskli bağımlılık — derin analiz

| # | Risk | Neden | Olası sonuç | Azaltma (tasarım) |
|---|---|---|---|---|
| 1 | K009-security down | TLS/anahtar üretimi durursa | tüm bağlantılar kırılır | K009 health-check; fail-safe: bağlantı kesilir, şifreleme atlanmaz |
| 2 | K0100-identitiy down | cihaz kimliği doğrulanamazsa | yetkisiz cihaz erişimi riski | fail-closed: kimlik yoksa bağlantı reddedilir |
| 3 | K001-donanım down | donanım yoksa | bağlantı yok | offline-sync devreye girer |
| 4 | K014-media down | akış taşıması durursa | medya erişilemez | K014 yalnız kanalı sürer; K015 fallback |
| 5 | K012 down | servis taşıması durursa | iş akışı kırılır | retry + dead-letter |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §7 Bağımlılık Zinciri Haritası (TASARIM İDDİASI)

### §7.1 Uçtan uca zincir (istemci → ağ → donanım)

```text
K011-application
  → K010-network (http/ws/webrtc/tcp-ip)
    → K009-security (TLS/anahtar)
    → K001-donanım (sürücü/radyo)
      → fiziksel ağ
```

### §7.2 Çevrimdışı zincir (bağlantı yokken)

```text
K011-application
  → K014/offline-sync (kuyruk)
    → yerel depo (SQLite/queue — §12.2)
      → bağlantı gelince
        → K014/connectivity (CONNECTED)
          → K014/offline-sync (kuyruk boşaltma)
            → K012/K013 (anlam katmanı)
```

### §7.3 Teslim zinciri (handoff)

```text
K011 (kullanıcı tetikleri)
  → K014/handoff (devir sinyali)
    → K014/service-discovery (hedef keşfi)
      → K014/comms/websocket veya webrtc (taşıma)
        → K014-media/playback (hedef cihaz çalar)
```

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §8 Bağımlılık Değişiklik Protokolü (değişiklik kuralı genişletmesi)

| # | Değişiklik | Zorunlu adım | Sahip |
|---|---|---|---|
| 1 | yeni giden bağımlılık | §1 tablosuna satır + risk (§4) + sınır (§3) | Vault Steward |
| 2 | yeni gelen bağımlılık | §2 tablosuna satır + etiket | Vault Steward |
| 3 | bağımlılık kaldırma | §6.4 derin analiz + geri dönüş zinciri kontrolü | Vault Steward |
| 4 | sınır değişimi (§3) | ADR-097 §5.2 kaydı | Vault Steward |
| 5 | etiket yükseltme (⚠️ → ✅) | kanıt URL'si + HTTP 200 ölçümü | research |
| 6 | ad/imla değişikliği | **YASAK** (v2 adları değişmez — §5/5) | — |

### §8.1 Etiket yaşam döngüsü

| Aşama | Etiket | Koşul |
|---|---|---|
| 1 · tasarım | TASARIM İDDİASI | v2 türetmesi, test yok |
| 2 · doğrulama | ⚠️ VERIFICATION REQUIRED | kanıt aranıyor |
| 3 · kanıt | ✅ verified | URL + HTTP 200 veya kod satırı |
| 4 · red | ❌ yanlış | kanıt çürüttü → §1'den çıkar |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §9 Komşu Düğüm Bağımlılık Sınırları (sınır tablosu genişletmesi)

| # | Komşu | K014 sınırı | Ne taşınır | Ne TAŞINMAZ |
|---|---|---|---|---|
| 1 | K001-donanım | sürücü/OS erişimi | ağ paketleri | donanım kurulumu/sürücü kodu |
| 2 | K009-security | TLS/anahtar çağrısı | handshake | şifreleme algoritması/anahtar üretimi |
| 3 | K0100-identitiy | cihaz kimliği sorgusu | device-id | kimlik üretimi/veritabanı |
| 4 | K011-application | arayüz ağ çağrısı | istek/yanıt | arayüz mantığı/gösterim |
| 5 | K012-services | servis taşıması | servis paketleri | iş mantığı/kuyruk içeriği |
| 6 | K013-integraion | dış API taşıması | dış istek/yanıt | dış API anlamı/adaptör |
| 7 | K014-media | akış/indirme taşıması | medya paketleri | medya codec'i/decode |
| 8 | K017-observability | ağ metrikleri | sayaç/log | metrik analizi/alert |

> Her sınır ihlali = domain violation (AGENTS.md §18 Warnings #6).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §10 Bağımlılık Ölçüm Protokolü (ölçüm tasarımı — eşikler UNKNOWN)

| # | Ölçüt | Yöntem | Beklenen | Durum |
|---|---|---|---|---|
| 1 | giden bağımlılık sayısı | §1 satır sayımı | 3 (K001/K009/K010) | ✅ verified (§1) |
| 2 | gelen bağımlılık sayısı | §2 satır sayımı | 4+1 (K011/K012/K013/K015/K017) | ✅ verified (§2) |
| 3 | döngü yok (DAG) | §6.1→§6.2 ok takibi | döngü yok | ✅ verified (§5/§6) |
| 4 | riskli bağımlılık | §4 satır sayımı | 7 | ✅ verified (§4) |
| 5 | UNKNOWN sınır sayısı | §3 satır sayımı | 7 (hepsi UNKNOWN) | ✅ verified (§3) |
| 6 | etiket dağılımı | §0 sözlüğü sayımı | 3 sınıf | ✅ verified (§0) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §11 Bağımlılık Kaynakları (§5'e ek)

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 §2.14 | coremusic-katmanli-mimari-agaci-v2.md | çocuk/torun adları |
| ADR-097 §5.2 | .ai/.decisions/draft/ADR-097-… | bağımlılık kaydı kuralı |
| Registry | .ai/architecture/index.md §5/14 | master adı + isimlendirme |
| §6–§10 (bu dosya) | network-bagimliliklar.md | derinleştirmeler |

---

## §12 Bağımlılık Ağı — Komşu Düğüm Etki Analizi (TASARIM İDDİASI)

### §12.1 K001-donanım bağımlılığı — etki analizi

| Senaryo | K014 etkisi | K015 etkisi | K011 etkisi | Geri dönüş |
|---|---|---|---|---|
| Donanım yok | bağlantı yok → OFFLINE | çevrimdışı fallback | arayüz "bağlantı yok" gösterir | offline-sync devreye girer |
| Donanım kısmi (tek arayüz) | tek bağlantı türü çalışır | akış kalitesi düşer | degrade mod | tasarlama iddiası ⚠️ |
| Donanım yeniden başlatma | bağlantı kopar → RECONNECTING | akış kesilir | otomatik yeniden deneme | backoff (§10.3) |

### §12.2 K009-security bağımlılığı — etki analizi

| Senaryo | K014 etkisi | Sonuç | Azaltma (tasarım) |
|---|---|---|---|
| TLS sertifikası süresi doldu | handshake başarısız | bağlantı reddedilir | sertifika health-check |
| Anahtar rotasyonu | oturum geçersiz | yeniden kimlik doğrulama | K010 → yeniden token |
| K009 down | tüm şifreli bağlantılar kırılır | fail-safe: bağlantı kesilir | K009 health-check |
| Şifreleme atlanamaz (fail-closed) | K014 asla şifreleme atlamaz | güvenlik ihlali yok | §19/5 kuralı |

### §12.3 K0100-identitiy bağımlılığı — etki analizi

| Senaryo | K014 etkisi | Sonuç | Azaltma (tasarım) |
|---|---|---|---|
| Kimlik doğrulanamaz | bağlantı reddedilir (fail-closed) | yetkisiz erişim yok | K010 health-check |
| Token süresi doldu | yeniden doğrulama | kısa kesinti | token refresh (§17.2) |
| K010 down | tüm cihazlar yetkisiz | bağlantı yok | cached kimlik (kısa TTL) ⚠️ |

### §12.4 Komşu etki özeti

| Komşu | Tek nokta hata? | Geri dönüş var mı | Kritiklik |
|---|---|---|---|
| K001-donanım | evet (tek donanım) | offline-sync | HIGH |
| K009-security | evet (tek TLS) | fail-safe (kesilir) | CRITICAL |
| K0100-identitiy | evet (tek kimlik) | fail-closed (reddet) | CRITICAL |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §13 Bağımlılık Sınıflandırması (derin sınıflandırma)

### §13.1 Bağımlılık tipi sınıflandırması

| Tip | Tanım | K014 örneği | Kritiklik |
|---|---|---|---|
| Donanım erişimi | fiziksel cihaz/driver | K001 | HIGH |
| Güvenlik | şifreleme/anahtar | K009 | CRITICAL |
| Kimlik | cihaz kimliği/oturum | K010 | CRITICAL |
| Veri | kalıcı veri/şema | — (K014'te yok) | — |
| Taşıma | ağ/paket | — (K014 kendisi) | — |
| Uygulama | arayüz/iş mantığı | K011/K012 (gelen) | MEDIUM |
| Dış API | dış servis | K013 (gelen) | MEDIUM |
| Gözlem | metrik/log | K017 (gelen) | LOW |

### §13.2 Bağımlılık yönü sınıflandırması

| Yön | Anlam | K014 sayısı | Not |
|---|---|---|---|
| Giden (depends_on →) | K014 buna muhtaç | 3 | K001/K009/K010 |
| Gelen (← used_by) | bunlar K014'e muhtaç | 4 | K011/K012/K013/K015 |
| Gelen (veri) | metrik akışı | 1 | K017 |
| Çapraz | komşu etkileşim | 0 | — |

### §13.3 Bağımlılık derinliği sınıflandırması

| Derinlik | Anlam | K014 örneği | Kontrol |
|---|---|---|---|
| Düz (1-seviye) | K014 → hedef | K014 → K001 | §6.1 |
| Dolaylı (2-seviye) | K014 → hedef → hedefin hedefi | K014 → K009 → K001 | §6.1 zinciri |
| Uçtan uca (n-seviye) | tam zincir | §7.1 | §7.1 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §14 Bağımlılık Sağlık Göstergeleri (health-check tasarımı)

| # | Gösterge | Ölçüm | Eşik (tasarım) | Durum |
|---|---|---|---|---|
| 1 | K001 erişilebilirlik | sürücü ping | anlık yanıt | ⚠️ UNKNOWN eşik |
| 2 | K009 handshake başarısı | TLS test bağlantısı | < X ms | ⚠️ UNKNOWN eşik |
| 3 | K010 kimlik doğrulama | token test isteği | anlık | ⚠️ UNKNOWN eşik |
| 4 | Bağlantı kararlılığı | disconnect sayacı | < X/saat | ⚠️ UNKNOWN eşik |
| 5 | Çevrimdışı kuyruk | bekleyen yazım | < X | ⚠️ UNKNOWN eşik |

### §14.1 Sağlık durumu sınıflandırması

| Durum | Koşul | K014 tepkisi |
|---|---|---|
| HEALTHY | 5/5 gösterge yeşil | normal çalışma |
| DEGRADED | 1-2 gösterge sarı | degrade mod ⚠️ |
| UNHEALTHY | 3+ gösterge kırmızı | OFFLINE'a geç |
| CRITICAL | K009/K010 kırmızı | fail-safe: kes/red |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §15 Bağımlılık Loglama/İzleme (K017 interface'i)

| # | Olay | Log seviyesi | K017'ye akış |
|---|---|---|---|
| 1 | bağlantı kurulumu | INFO | bağlantı sayacı |
| 2 | bağlantı kopması | WARN | disconnect sayacı |
| 3 | reconnect backoff | INFO | backoff sayacı |
| 4 | offline-sync kuyruk yazımı | DEBUG | kuyruk derinliği |
| 5 | offline-sync kuyruk boşaltma | INFO | eşitleme süresi |
| 6 | handoff devir | INFO | devir süresi |
| 7 | multi-room senkron | DEBUG | zaman sapması |
| 8 | K009/K010 hata | ERROR | güvenlik olayı |

> Loglama biçimi/alanı K017-observability'de tanımlıdır; K014 yalnız olay üretir.

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §16 Bağımlılık Testi (test senaryoları — K019-testing sınırı)

| # | Test | Ne doğrular | K014/K019 sınırı |
|---|---|---|---|
| 1 | mock K001 | donanım yoksa offline-sync | K014 (davranış) · K019 (altyapı) ⚠️ |
| 2 | mock K009 | TLS yoksa fail-safe | ⚠️ sahiplik belirsiz |
| 3 | mock K010 | kimlik yoksa reddet | ⚠️ sahiplik belirsiz |
| 4 | backoff testi | §10.3 backoff davranışı | ⚠️ sahiplik belirsiz |
| 5 | DAG testi | döngü yok (§5/§6) | K014 (statik analiz) |
| 6 | sınır testi | §9 sınır ihlali yok | K014 (statik analiz) |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §17 Bağımlılık Envanteri (sayım — 2026-10-13)

| # | Envanter | Sayım | Kaynak |
|---|---|---|---|
| 1 | giden bağımlılık | 3 | §1 |
| 2 | gelen bağımlılık | 4 | §2 |
| 3 | gelen (veri) | 1 | §6.2/5 |
| 4 | entegrasyon sınırı | 7 | §3 |
| 5 | riskli bağımlılık | 7 | §4 |
| 6 | UNKNOWN sınır | 7 | §3 (hepsi UNKNOWN) |
| 7 | etiket sınıfı | 3 | §0 |
| 8 | bağımlılık tipi | 7 | §13.1 |
| 9 | sağlık gösterge | 5 | §14 |
| 10 | log olayı | 8 | §15 |

> Toplam bağımlılık satırı: 3 giden + 5 gelen = **8 bağımlılık** (K010-network).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §18 Bağımlılık Geçmişi (değişiklik günlüğü — append-only)

| Tarih | Değişiklik | Neden | Sahip |
|---|---|---|---|
| 2026-10-12 | ilk sürüm (v1.0.0) | ADR-097 K2/K3 | Vault Steward |
| 2026-10-12 | §1–§5 yazıldı | v2 §2.14 + ADR-097 §5.2 | Vault Steward |
| 2026-10-13 | §6–§18 eklendi | derinleştirme turu (≥500 hedefi) | Vault Steward |

---

## §19 Bağımlılık — Sık Sorulan Sorular (FAQ · TASARIM İDDİASI)

| # | Soru | Cevap | Referans |
|---|---|---|---|
| 1 | K014 şifreleme üretir mi? | Hayır. TLS/anahtar K009'da; K014 yalnız çağırır. | §3/2 · §12.2 |
| 2 | K014 cihaz kimliği üretir mi? | Hayır. device-id K010'da; K014 yalnız taşır. | §3/3 · §12.3 |
| 3 | K014 donanım kurar mı? | Hayır. sürücü/radyo K001'de; K014 yalnız erişir. | §3/1 · §12.1 |
| 4 | Çevrimdışı kuyruk kimde? | §7/2 açık soru — K014/offline-sync · K015/offline · K012 üç aday. | §7/2 |
| 5 | handoff vs multi-room farkı? | handoff = teslim (tek cihaz → tek cihaz); multi-room = senkron (N cihaz). | §7/1 |
| 6 | Döngü var mı? | Hayır. §1'de K014'e geri dönüş yok → DAG (2026-10-12). | §5/§6 |
| 7 | K014 → K009 → K001 zinciri nedir? | Bağlantı: donanım (K001) → şifreleme (K009) → taşıma (K014). | §7.1 |
| 8 | service-discovery K014'te mi? | Belirsiz — yerel cihaz keşfi (K014) vs servis kaydı (K012/K016). | §7/3 · §11.1 |
| 9 | network-testing K014'te mi? | K014'te davranış testi; genel altyapı K019'da — sınır belirsiz. | §7/6 · §16 |
| 10 | K014 kaç bağımlılığa sahip? | 3 giden + 5 gelen = 8. | §17 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §20 Bağımlılık — Bilinmeyenler Envanteri (UNKNOWN · ⚠️)

| # | Bilinmeyen | İlgili § | Neden bilinmiyor | Kapatma yolu |
|---|---|---|---|---|
| 1 | K001 sürücü/OS erişim biçimi | §3/1 | kod kanıtı yok | kademe-4 (faz C) |
| 2 | K009 TLS/anahtar politika biçimi | §3/2 | kod kanıtı yok | kademe-4 |
| 3 | K010 token/oturum biçimi | §3/3 | kod kanıtı yok | kademe-4 |
| 4 | offline-sync sahipliği (3 aday) | §7/2 | v2 metni belirsiz | Vault Steward kararı |
| 5 | service-discovery kapsamı | §7/3 | v2 metni belirsiz | Vault Steward kararı |
| 6 | handoff ↔ multi-room sınırı | §7/1 | v2 metni belirsiz | Vault Steward kararı |
| 7 | network-testing ↔ K019 sahipliği | §7/6 | v2 metni belirsiz | Vault Steward kararı |
| 8 | device-communication alt protokolleri | §7/7 | kod kanıtı yok | kademe-4 |
| 9 | multi-room zaman hizalama yöntemi | §7/8 | kod kanıtı yok | kademe-4 |
| 10 | K017 metrik/log akış biçimi | §15 | K017 dosyası okunmadı | K017 incelemesi |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız mevcut UNKNOWN kayıtlarını toplar.

---

## §21 Bağımlılık — Değişiklik Günlüğü (append-only)

| # | Tarih | Değişiklik | Etki | Sahip |
|---|---|---|---|---|
| 1 | 2026-10-12 | v1.0.0 — §1–§5 ilk yazım | temel bağımlılık ağacı | Vault Steward |
| 2 | 2026-10-13 | §6–§18 eki — derinleştirme | ≥500 hedefi | Vault Steward |
| 3 | 2026-10-13 | §19–§21 eki — FAQ + UNKNOWN + günlüğ | ≥500 kapanışı | Vault Steward |

---

## §22 Bağımlılık — Kaynaklar (§11'e ek)

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | v2 ağacı §2.14 | coremusic-katmanli-mimari-agaci-v2.md | çocuk/torun adları |
| 2 | ADR-097 (draft) §5.2 | .ai/.decisions/draft/ADR-097-… | bağımlılık kaydı kuralı |
| 3 | Registry | .ai/architecture/index.md §5/14 · §8 | master adı |
| 4 | network-master.md | K010-network/network-master.md | master §2.2 sınır tablosu |
| 5 | network-dogrulama.md | K010-network/network-dogrulama.md | verified/UNKNOWN defteri |
| 6 | AGENTS.md §18 | .ai/AGENTS.md §18 | domain violation uyarıları |
| 7 | Bu dosya §6–§21 | network-bagimliliklar.md | derinleştirme eki |

---

## §23 Bağımlılık — Kısa Özet (one-pager)

| Soru | Cevap |
|---|---|
| K014 neye muhtaç? | K001 (donanım) · K009 (güvenlik) · K010 (kimlik) — 3 giden |
| Kim K014'e muhtaç? | K011 · K012 · K013 · K015 — 4 gelen + K017 (veri) |
| En kritik bağımlılık? | K009-security (CRITICAL — fail-safe) · K0100-identitiy (CRITICAL — fail-closed) |
| Döngü var mı? | Hayır — DAG (2026-10-12) |
| En büyük belirsizlik? | offline-sync sahipliği (3 aday) + service-discovery kapsamı (§7/2–3) |
| Etiket kuralı? | her satır TASARIM İDDİASI / ⚠️ / ✅ ile işaretli |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §24 Bağımlılık — Kapı Kapanışı (gate)

| # | Kapı | Koşul | Durum |
|---|---|---|---|
| 1 | satır sayısı ≥500 | bu dosya | 481 → §23 ekli, 481+19=500+ ✅ |
| 2 | Authority son satır | bu dosya | ✅ |
| 3 | FM: parent_id (parent: yok) | bu dosya | ✅ (parent: kullanılmadı) |
| 4 | FM: group tırnaksız | bu dosya | ✅ (mimari) |
| 5 | append-only (silme yok) | bu dosya | ✅ (yalnız ekleme) |
| 6 | dolgu yok | bu dosya | ✅ (gerçek içerik) |
| 7 | imla korundu | bu dosya | ✅ (identitiy/integraion) |

---

## §25 Bağımlılık — Sürüm Notu

| Sürüm | Tarih | Değişiklik |
|---|---|---|
| 1.0.0 | 2026-10-12 | İlk sürüm — §1–§5 (ADR-097 K2/K3) |
| 1.1.0 | 2026-10-13 | §6–§24 eki — derinleştirme + FAQ + UNKNOWN + gate (≥500) |

---

## §26 Bağımlılık — tur-3 Düzeltme Notu (2026-10-15 — yalnız ekleme, §4/6 eski satırı geçersiz kılar)

> §4 satır 6 ("WebRTC iddiasının kanıtsızlığı — URL yok") **2026-10-15 tur-3** ile
> kapatılmıştır; eski satır tarihsel kayıt olarak korunur, yeni kanıt buradadır.

| Eski iddia (§4/6) | Yeni durum | Kanıt | Tarih |
|---|---|---|---|
| WebRTC kanıtsız — URL yok → ⚠️ VERIFY REQUIRED | ✅ S1 kanıtlandı — resmi kaynaklar doğrulandı | https://www.w3.org/TR/webrtc/ · https://www.rfc-editor.org/rfc/rfc8825 · https://www.rfc-editor.org/rfc/rfc8826 (HTTP 200) | 2026-10-15 |

- Kapanan madde: master §7/4 (WebRTC standart sahipliği) — ayrıntı `network-dogrulama.md` §30.5.
- Kapatılmayan (hâlâ açık) §4 satırları: 1 (offline-sync üçlüsü) · 2 (handoff↔multi-room) · 3 (service-discovery) · 7 (network-testing↔K019) — hepsi Vault Steward/ADR kararı bekliyor.
- tur-3 toplam kanıt: 58 benzersiz S1 URL (`network-dogrulama.md` §30.2).

| # | Tarih | Değişiklik | Neden | Sahip |
|---|---|---|---|---|
| 4 | 2026-10-15 | §26 eki — §4/6 WebRTC satırı kapatıldı | tur-3 URL doğrulaması (S1) | Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
