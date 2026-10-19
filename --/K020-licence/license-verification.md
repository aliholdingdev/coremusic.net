---
title: "K020-licence — Lisans Doğrulama (License Verification)"
type: verification
category: architecture
node_id: K020-licence/license-verification
node_type: file
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Doğrulama = resmi kaynak + tarih + kayıt; kanıtsız iddia yok (kök AGENTS.md §3)"
related:
  - "dependency-licenses.md"
group: kanit
---


# K020-licence — Lisans Doğrulama

## 1. Rol (ELI15)

Lisans satırının **doğru olup olmadığını** nasıl kontrol edeceğimizi ve son
kontrolün **ne zaman** yapıldığını yazar. Kayıt `dependency-licenses.md`de,
yöntem buradadır.

## 2. Doğrulama sırası (öncelik — en güçlü kanıttan zayıfa)

1. **Resmi lisans dosyası** — paket deposunda `LICENSE` / `COPYING` (kaynak kodda).
2. **Resmi duyuru/sayfa** — üreticinin developer sayfası (ör. Steinberg ASIO sayfası).
3. **Paket manifestosu** — `composer.json` `license` alanı · npm `package.json`.
4. **Depo README'si** — yalnız 1–3 yoksa, o zaman da URL ile.
5. **Üçüncü parti liste** — en son çare; çelişirse `⚠️ VERIFY REQUIRED`.

> Sıra **ters çevrilmez**: README'de yazıyor diye lisans kesinleşmez.

## 3. Tur kayıtları

| Tur | Tarih | Kapsam | Yöntem | Sonuç | Durum |
|---|---|---|---|---|---|
| lisans tur-1 | 2026-10-12 | ASIO SDK lisans durumu | resmi Steinberg developer sayfası okundu | ASIO SDK = **GPLv3 açık kaynak** → kayıt satırı 1 açıldı | geçti (etki alanı açık) |
| lisans tur-1b | 2026-10-12 | referans proje lisansları (Koel/Ampache/Jellyfin) | web doğrulama (ADR-097 §1.6) | MIT · AGPL-3.0 · GPL-2.0 — karşılaştırma tablosuna işlendi | geçti |
| lisans tur-2 | — | Composer/Frontend bağımlılık envanteri | **çalıştırılmadı** | — | `⚠️ VERIFY REQUIRED` |
| lisans tur-3 | — | ASIO GPLv3 → dağıtım modeli etki analizi | **çalıştırılmadı** (ADR bekliyor) | — | `⚠️ VERIFY REQUIRED` |

## 4. Başarı ölçütü

- Her kayıt satırında **tarih + resmi URL** varsa tur geçer.
- Etki alanı kararı (`GPLv3 nedir? bizim için sorun mu?`) **bu dosyada verilmez** →
  karar `K020-adr` üzerinden ADR'ye gider (ADR-097 §6/5 açık soru olarak kayıtlı).

## 5. Açık sorular

1. Tur-2 envanteri ne zaman koşacak ve hangi araçla? → `⚠️ VERIFY REQUIRED`
2. ASIO etki analizi hangi ADR'de toplanacak (yeni mi, ADR-097'ye mi ek)? → `⚠️ VERIFY REQUIRED`
3. ⚠️ ID CONFLICT: 6 klasör K020 önekinde paylaşır (adr/comilance/comere/licence/discovery/recomenataion) — ADR-097 §6 · v2 §3.1

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

## 12. Tur sonuçlarının nereye işlendiği

| Sonuç türü | İşlenecek yer | Not |
|---|---|---|
| Lisans satırı doğrulandı | `[[../dependency-licenses]]` ilgili satır → durum güncellenir | bu dosyada değil |
| Yeni açık bağımlılık bulundu | `dependency-licenses.md` → **yeni satır** | append-only |
| Hukuki etki belirsiz | ADR → `.ai/.decisions/draft/` (ör. ADR-097 §6/5) | bu dosya karar vermez |
| Uyum/ politika sorunu | `../K020-comilance/policies/` + `controls/` | alan ayrımı korunur |
| Tur **çalıştırılmadı** | bu dosyanın §3 tablosunda `⚠️ VERIFY REQUIRED` kalır | sayı uydurulmaz |


## 10. Denetçi kontrol listesi (tur sonunda)

- [ ] İncelenen bağımlılığın **tam sürümü** kilit dosyadan alındı.
- [ ] Lisans metni **paket kaynağından** okundu (README tek başına yeterli değil — §2 sıra).
- [ ] Satırda **tarih + URL/yol + durum** var.
- [ ] Etki alanı kararı verildiyse **ADR no** ile bağlandı; verilmediyse `⚠️` kaldı.
- [ ] Çelişki varsa §6 kuralıyla çözüldü ve çelişki ayrıca kayda geçti.
- [ ] Bu turda "uyumlu/yasak" gibi **sonuç cümlesi** yazılmadı (hukuki yorum yasak).

## 11. Sık yapılan hatalar (red team notu)

1. **Ters sıra:** önce README, sonra LICENSE — §2 sırası zorunlu, tersi geçersiz sayılır.
2. **Tarih unutulması:** kanıt var ama tarih yok → tur tekrar çalıştırılır.
3. **Etki kararını bu dosyada vermek:** "kullanılabilir/ kullanılamaz" → ADR'ye taşınır.
4. **Tek kaynakla geniş iddia:** tek paket incelenip "bağımlılıklar temiz" denmesi.
5. **Tur kaydını düzeltmek:** append-only ihlali — yeni satır + `⚠️` eklenir.


## 6. Çelişki çözümü (kaynaklar birbirini tutmazsa)

| Senaryo | Çözüm |
|---|---|
| `README` ≠ `LICENSE` dosyası | **`LICENSE` kazanır** (paket kaynağı) + çelişki satırı `⚠️` ile kayda geçer |
| `LICENSE` ≠ `package.json/composer.json` alanları | manifest **yanlış/eksik** sayılır → upstream'e bildirim notu, kayıt `kaldı` |
| Depo URL'si 404 / sayfa değişmiş | satır `VERIFY REQUIRED` olur; arşiv/sürüm etiketli link denenir |
| Fork / yeniden adlandırılmış paket | **upstream** lisansı esas, fork ek not olarak |
| Hiçbir kaynak yok | satır **açılmaz** ("bilinmiyor" bile yazılmaz) |

## 7. Tur tetikleyicileri (ne zaman yeniden doğrulanır)

1. **Yeni bağımlılık** eklendiğinde (`composer/npm` değişikliği).
2. **Sürüm yükseltmesinde** — lisans değişimi sürümle gelebilir.
3. **Yayın/dağıtım öncesinde** (kapalı/kapalı-öncesi paketleme dahil).
4. **Fork/alma (vendor) işleminde** — kaynak dizin değişince.
5. **Yıllık genel tur** (tarih boş — sıklık ONAY bekliyor → `⚠️ VERIFY REQUIRED`).

## 8. Örnek kayıt senaryosu (ASIO · adım adım)

```text
1. Kaynak aç: https://www.steinberg.net/developers/asiosdk-open     (2026-10-12 okundu)
2. Lisans metnini bul: GPLv3 → dependency-licenses.md satır 1'e işlendi
3. Etki alanını işaretle: ⚠️ VERIFY REQUIRED: projeye etkisi
4. Karar gerekiyorsa → K020-adr → .ai/.decisions/draft/ ADR-097 §6/5 açık sorusunu kapat
5. Etki kararı verilmeden "sorun yok" satırı EKLENMEZ
```

## 9. Kabul kriterleri (bu dosya "tam" sayılır mı?)

- [ ] Her kayıt satırında **kaynak URL + tarih + durum** var.
- [ ] Tur tetikleyicileri tanımlı (madde 7) — sıklıklar ONAY'lı.
- [ ] Envanter (§8) çalıştırılmış ve sonuçları satıra dökülmüş — **henüz değil**.
- [ ] ASIO etkisi bir ADR ile kapatılmış — **henüz değil**.
- [ ] Çelişki çözümü (§6) ekipler tarafından biliniyor.

