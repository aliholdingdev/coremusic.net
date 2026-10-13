---
node_id: K008-domain/kapsam
node_type: root
status: verify-required
parent_id: K008-domain
children:
  - music
  - media
  - user
  - library
  - playlist
  - playback
  - discovery
  - recommendation
  - device-management
  - context-mapping
depends_on:
  - K008-domain/domain-master.md
evidence:
  - shared/config/domain.php
  - .ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md
validation: 2026-10-12 — 7 subdomain config satırı (domain.php s.7–15) + v2 spec §2.8 hizası
open_questions:
  - "10 çocuk içinde 3'ü için config satırı kanıtı yok (discovery, recommendation, context-mapping) — VERIFICATION REQUIRED"
group: mimari
---

# K008-domain — Kapsam (Scope Boundary)

> **Authority: Bayram Ali / Vault Steward.** Bu dosya K008-domain'in **nei kapsadığını**
> tanım olarak açar: Bounded Context envanteri, her çocuğun sorumluluk alanı ve
> dış sınırlar. Karar/kanıt için `domain-kararlar.md` / `domain-dogrulama.md`.
> **Kaynaklar:** v2 spec §2.8 · ADR-097 §4–§5 · `shared/config/domain.php`.

## §1 KAPSAM TANIMI

| Madde | Değer |
|---|---|
| Tanım | CoreMusic domain gerçeğinin (alan modeli) tek kayıt kaynağı |
| Birim | Bounded Context (10 çocuk) + kök anayasa (12 MD) |
| Kapsam girdisi | Müzik kataloğu, medya varlıkları, kullanıcı, kütüphane, çalma listesi, oynatma, keşif, öneri, cihaz, bağlam haritası |
| Kapsam çıktısı | Sınır, aggregate, dil, event sözleşmesi, kod kanıtı, risk, test stratejisi |
| Kapsam dışı | Persistence şeması, endpoint, UI, deploy (bkz. master §2.2) |
| Dönem | 2026-10-12 — ADR-097 K-space v3 sıfırdan kurulum |

## §2 KAPSAM DIŞI SINIR (NEGATİF TANIM)

Sınırlar, içeriden neyin **girmediğinin** tanımlanmasıyla korunur:

| # | K008'e GİRMEZ | Gerekçe |
|---|---|---|
| N1 | MySQL tablo/DDL detayı | Şema data katmanına ait (`.ai/.sql/mysql/` sahipliğinde) |
| N2 | HTTP route, status code, pagination | API katmanı sahipliğinde |
| N3 | Şifreleme/Argon2id parametresi | security katmanı sahipliğinde (domain yalnız "kimlik doğrulama gerekli" der) |
| N4 | CSS token, mockup, breakpoint | ui-design sahipliğinde |
| N5 | Paket versiyonu, composer kararı | K016 |
| N6 | Servis topolojisi, saga | K006 |
| N7 | Background job kuyruğu seçimi | iş katmanı (kod dosyalarında yalnız gereksinim) |

**İhlal durumu:** K008 dosyasında N1–N7 konusunda **karar** görürseniz bu bir
hata kaydıdır → `domain-riskler.md` R-satırına işlenir ve orkestratöre raporlanır.

## §3 BOUNDED CONTEXT ENVANTERİ

### §3.1 Genel envanter

| # | BC | Çocuk | Tek cümlelik sorumluluk | Config kanıtı |
|---|---|---|---|---|
| 1 | Müzik Kataloğu | `music` | Müzik eseri, kayıt, sanatçı, albüm, radyo, podcast varlığının yaşam döngüsü | `domain.php` (7 subdomain satırı) |
| 2 | Medya Varlıkları | `media` | Ses/video dosya varlığı, transcode çıktısı, metadata okuma | Config kanıtı mevcut |
| 3 | Kimlik & Profil | `user` | Kayıt, oturum, profil, tercih, cinsiyet/yaş gibi profil nitelikleri | Config kanıtı mevcut |
| 4 | Kütüphane | `library` | Kullanıcıya ait favoriler, dinleme geçmişi, oynatma sırası | Config kanıtı mevcut |
| 5 | Çalma Listesi | `playlist` | Kullanıcı koleksiyonları, iş birliği (collaborator) ilişkileri | Config kanıtı mevcut |
| 6 | Oynatma | `playback` | Çalma oturumu, sıra, kuyruk, oynatma olaylarının yayını | Config kanıtı mevcut |
| 7 | Keşif | `discovery` | Trend/radar/keşif sorgularının domain tarafı | `⚠️ VERIFICATION REQUIRED` |
| 8 | Öneri | `recommendation` | Öneri kümesi, benzerlik, çeşitlilik kurallarının domain gereksinimi | `⚠️ VERIFICATION REQUIRED` |
| 9 | Cihaz Yönetimi | `device-management` | Çalma hedefi cihazların domain modeli | `⚠️ VERIFICATION REQUIRED` |
| 10 | Bağlam Haritası | `context-mapping` | BC'ler arası ilişki kaydı (upstream/downstream) | `⚠️ VERIFICATION REQUIRED` |

> **Kanıt notu:** `shared/config/domain.php` içinde 7 subdomain kaydı sayıldı
> (satır 7–15 aralığı, 2026-10-12 keşfi). 10 çocuktan 3'ünün config karşılığı
> aranıp `domain-dogrulama.md` §3'te satır kanıtıyla kapatılacak.

### §3.2 Çocuk başına kapsam cümleleri (kapsam.md — kısa tutulur, ayrıntı çocukta)

| Çocuk | K008'de kapsanan | K008'de KAPSANMAZ |
|---|---|---|
| `music` | Eser (work), kayıt (recording), sanatçı, albüm, radyo istasyonu, podcast kavram sınırları | Tag okuma kütüphanesi seçimi, dosya formatı çözümleyici kodu |
| `media` | Medya dosya varlığı, transcode çıktısı olarak varlık, metadata kavramı | FFmpeg/kütüphane seçimi, disk yolu politikası |
| `user` | Kullanıcı, profil, tercih, oturum kavramı | Şifreleme parametresi, JWT/OIDC akış detayı (security katmanı) |
| `library` | Favori, geçmiş, sıra (queue) varlıkları ve sahipliği | Sorgu performansı, index (data katmanı) |
| `playlist` | Çalma listesi, izleyici/katılımcı ilişkisi, sıra | Paylaşım URL/endpoint tasarımı |
| `playback` | Oynatma oturumu, gapless/kuyruk kurallarının domain dili | Web Audio/player UI davranışı |
| `discovery` | Keşif sorgusu domain gereksinimi | Trend algoritması uygulaması (K016) |
| `recommendation` | Öneri kuralları domain gereksinimi (MIN_SCORE benzeri eşiklerin sahipliği) | `AIEngine` uygulaması (kod sahibi farklı) |
| `device-management` | Cihaz varlığı ve "oynatma hedefi" kavramı | Ağ keşfi protokolü, sürücü |
| `context-mapping` | BC ilişkileri, ilişki türleri | CI/CD, repo yapılandırması |

## §4 SAHİPLİK VE ERİŞİM MATRİSİ

| Varlık | Sahip (yazma) | Okuyucu | Not |
|---|---|---|---|
| K008 kök MD (12) | K008-domain agent'ı | tüm çocuklar | master en üst otorite |
| Çocuk MD (6×6) | çocuk agent'ı | kök | kök-çocuk çelişkisinde kök kazanır |
| Domain event sınıfı | kod sahibi (shared/src) | K008 (yalnız kanıt okur) | K008 PHP yazmaz |
| Ubiquitous dil terimi | `domain-evrensel-dil.md` | tüm K008 dosyaları | terim çelişkisinde sözlük kazanır |
| CONFLICT kaydı | master §6 | herkes | kapatma yalnız orkestratör |

## §5 ÖN KOŞULLAR (bu kapsam geçerli sayılırken)

| # | Ön koşul | Durum |
|---|---|---|
| P1 | ADR-097 K-space v3 yürürlükte | Var (draft) — kesinlik `⚠️ VERIFY REQUIRED` (C3) |
| P2 | v2 spec §2.8 çocuk listesi güncel | 2026-10-12 okundu |
| P3 | Repo erişimi (kanıt için) | Var — `shared/`, `.ai/.sql/mysql/` okunabilir |
| P4 | Web erişimi (araştırma için) | Var — 2026-10-12'de ~80 URL doğrulandı |

## §6 BAŞARI KRİTERLERİ (kapsam tamamlanmış sayılırken)

| # | Kriter |
|---|---|
| K1 | 10 çocuk için sınır cümlesi ve aggregate adı yazılı |
| K2 | N1–N7 dışı hiçbir konu K008'de karar olarak açılmamış |
| K3 | Config kanıtı olmayan çocuklar açıkça `VERIFICATION REQUIRED` işaretli |
| K4 | Her çocuk kendi `-kapsam.md` dosyasında ayrıntıya inmiş (dalga 1–2) |

## §7 DIŞ BAĞLANTILAR

| Hedef | Dosya |
|---|---|
| Anayasa, CONFLICT, uyum maddeleri | `domain-master.md` |
| Bağımlılık ekseninin ayrıntısı | `domain-bagimliliklar.md` |
| Sözlük | `domain-evrensel-dil.md` |
| Kapsam iddialarının kanıtı | `domain-dogrulama.md` |

---

*Authority: Bayram Ali / Vault Steward · K008-domain kapsam · 2026-10-12*
