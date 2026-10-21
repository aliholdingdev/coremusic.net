# Örnek — Ham Prompt'tan Yapılandırılmış Prompt'a (tam döngü)

> **N5 kanıtı:** Bu dosya skill'in tam döngüsünü gösterir — ham Türkçe istek →
> P0-P3 soru turu → 23-bölüm mimarisiyle yapılandırılmış prompt → işlenmiş paket
> (8 bölüm) → rubrik puanı. Gerçekçi ama basitleştirilmiş bir CoreMusic senaryosu.
>
> **Skill:** prompt-maker · **Updated:** 2026-10-21

---

## 1. Girdi — kullanıcının ham prompt'u (düzeltme YOK, okunur)

```text
kullanıcı: "sarki listesine sarki ekleme olayini hallet ama guvenli olsun,
baska bisey kirilmasin, test de yaz"
```

Ham prompt özellikleri (ADIM 1 — Yargılama):
- Dil: Türkçe · Ton: günlük · yazım hataları var (ham metin korunur).
- Kapsam belirsiz: "hallet" = ne yapılacak net değil (endpoint mi, UI mi?).
- Güvenlik talebi var ama somut değil ("güvenli olsun").
- Test talebi var ama kapsamı yok.
- **P0 boşluğu büyük → soru turu zorunlu.**

## 2. ADIM 2 — Soru turu (maks 3 tur, ≤8 soru/tur)

### Tur 1 (5 soru — P0 ağırlıklı)

| # | Seviye | Soru | Cevap |
|---|:------:|------|-------|
| Q1 | P0 | Bu özellik hangi katmanda? (A: PHP endpoint `app/routes/` · B: Vanilla JS UI · C: ikisi) | C — endpoint + arayüz butonu |
| Q2 | P0 | "Şarkı listesi" hangi tablo/modül? (A: `playlists` + `playlist_songs` mevcut mu?) | A — mevcut, migration yok |
| Q3 | P0 | Yetki: ekleyebilen? (A: sahip kullanıcı · B: admin · C: herkes) | A — playlist sahibi + admin |
| Q4 | P0 | Eş kuralı: aynı şarkı iki kez eklenebilir mi? (A: hayır · B: evet, tekrar sayacı) | A — hayır (UNIQUE) |
| Q5 | P1 | Test kapsamı? (A: yalnız birim · B: birim + endpoint · C: + E2E) | B |

### Tur 2 (2 soru — P1/P2)

| # | Seviye | Soru | Cevap |
|---|:------:|------|-------|
| Q6 | P1 | Sıralama: elle sıra (position) mı, tarih otomatik mi? | A — elle sıra (`position`) |
| Q7 | P2 | Hata mesajı tonu? (A: teknik · B: kullanıcı-dostu Türkçe) | B |

**%95 doldu → tur kapanır.** [UNKNOWN] kalemi yok. Hard/soft yazıldı:
hard = OWASP + PDO parametreli + `SELECT *` yasak + strict_types;
soft = mesaj tonu, slug formatı.

## 3. ADIM 4/5 — Yapılandırılmış prompt (23-bölüm özeti)

```text
ROL: Senior PHP 8.4 backend geliştirici — CoreMusic hybrid SPA
BAĞLAM: playlists + playlist_songs tabloları mevcut; Vanilla JS SPA (ADR-001);
  PDO + MySQL 8.0; ITCSS/--cm-* UI katmanı
GİRDİ: user_id (oturum) · playlist_id · song_id · position
KISITLAR (hard): strict_types=1 · PDO bindValue (ADR-002) · SELECT * yasak ·
  CSRF token zorunlu (state-changing) · yetki = sahip veya admin ·
  UNIQUE(playlist_id, song_id) · OWASP Top 10:2025
ÇIKTI: (1) migration SQL (UNIQUE index) (2) endpoint PHP (3) JS servis çağrısı
  (4) birim + endpoint testleri
HATA DAVRANIŞI: 403 (yetki) · 409 (tekrar) · 404 (playlist/song yok)
REDDEDİLEN ÇÖZÜMLER: GET ile yan etki · client-side yalnız doğrulama ·
  string concat SQL
KABUL: php -l temiz · testler yeşil · checklist.md PASS
```

## 4. ADIM 6 — İşlenmiş paket (8 bölüm — teslim formatı özeti)

| # | Bölüm | Çıktı |
|---|-------|-------|
| 1 | Eli10 | "Playlist'e şarkı ekleme: yetki + tekrar kontrolü olan endpoint + buton + test." |
| 2 | İstenen şey | Endpoint + UI + test (Q1-Q7 kapsamı) |
| 3 | Vault referansları | `.ai/.decisions/` ADR-001/002 · `.ai/CLAUDE.md` §5 |
| 4 | Proje kod referansları | `app/routes/playlists.php` · `assets/js/services/playlist.js` |
| 5 | Kullanıcı kararları | Q1-Q7 tablosu (hard/soft işaretli) |
| 6 | Orijinal prompt | ham metin (bölüm 1 — düzeltilmeden) |
| 7 | Görev dökümü | 4 görev: migration → endpoint → JS → test |
| 8 | İşlenen satır | `Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: 4` |

## 5. ADIM 7 — Rubrik puanı (§12.3)

| Kategori | Puan | Not |
|----------|-----:|-----|
| Completeness | 14/15 | -1: sayfalama (playlist limiti) P3'e ertelendi, defterde |
| Consistency | 10/10 | çelişki yok |
| Production-Ready | 15/15 | kabul kriteri somut |
| Security | 95/100 | -5: rate-limit eşiği P1 varsayım (bildirildi) |
| Scalability | 9/10 | -1: 1000+ şarkılı listeye JOIN davranışı not edildi |
| Clarity | 10/10 | tek okunuşta anlaşılır |
| Depth | 9/10 | -1: UNIQUE index lock davranışı kısa kaldı |
| Documentation | 10/10 | ADR + kaynak kaydı |
| **Toplam** | **87/100** | ≥85 ✓ · Security ≥90 ✓ → **GATE: PASS** |

## 6. Öğrenilen dersler (tekrar eden hata kalıpları)

1. **"hallet" + "güvenli olsun"** = klasik P0 boşluğu — kapsam/örnek/kabul
   üçlüsü sorulmadan üretim başlamaz.
2. Cevap geldikten sonra **eski cevapla çelişki** çıkarsa (ör. Q3'te "herkes"
   denip sonra "sahip" denirse) §5.3 gereği yeniden sorulur.
3. Ertelenen her P3 (sayfalama) **deftere yazılır** — sessiz kaybolmaz.
