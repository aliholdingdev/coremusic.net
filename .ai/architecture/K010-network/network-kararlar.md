---
node_id: K010-network/kararlar
node_type: subdomain
status: draft
parent_id: K010-network
children: []
depends_on:
  - K006-codebase
  - K008-superapp
  - K009-api
  - K011-ui-kit
  - K012-security
  - K016-observability
evidence:
  - ".ai/.decisions/index.md §3-§6 — okundu 2026-10-18: ADR-032/039/084/086/094/095/101 + draft-103"
  - ".ai/architecture/K010-network/network-master.md §4 (doğrulanmış RFC'ler) — okundu 2026-10-18"
validation: "Tüm ADR numaraları .ai/.decisions/index.md ile çapraz doğrulandı — 2026-10-18"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — draft-103 kabul edilmeden karar sayılmaz;
  başlık/durum üretim öncesi index.md'den teyit edilecek.
group: mimari
---

# K010-network — Kararlar

## §1 — ELI15: Bu nedir?

Ağ katmanının "şu soruya şu cevabı verdik, çünkü" defteri. **ADR** = vault'un
genel kayıtları (`.ai/.decisions/index.md`, numara uydurulmaz); **KN** =
K010'a özgü yerel kararlar (burada KN- önekiyle başlar, ADR değildir).

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | K010'a bağlı ADR atıfları + KN- yerel kararlar |
| Kapsam dışı | Yeni ADR numarası tahsisi (sadece index.md) · K003 KA- / K004 KD- kararları |

## §3 — Doğrulanmış ADR seti (K010'a bağlananlar)

| ADR | Başlık (kaynak: index.md) | K010 etkisi | Durum |
|---|---|---|---|
| ADR-032 | (index.md §3) | Ağ/API kararı | accepted ✓ |
| ADR-039 | (index.md §3) | Ağ/API kararı | accepted ✓ |
| ADR-084 | (index.md §3) | Ağ/API kararı | accepted ✓ |
| ADR-086 | (index.md §3) | Ağ/API kararı | accepted ✓ |
| ADR-094 | (index.md §3) | Ağ/API kararı | accepted ✓ |
| ADR-095 | (index.md §3) | Ağ/API kararı | accepted ✓ |
| ADR-101 | (index.md §3) | Ağ/API kararı | accepted ✓ |
| ADR-103 | (index.md draft) | İlgili taslak karar | ⚠️ draft — üretim öncesi teyit |

> **Kural:** ADR başlık/numara bu dosyadan değiştirilemez. Tablodaki başlıklar
> "index.md §3" kaynaklıdır — bu oturumda §3 listesi okundu, numaralar
> doğrulandı (zero-hallucination: başlık metni uydurulmadı).

## §4 — KN- yerel kararlar (K010'a özgü, ADR değil)

| Kod | Karar | Durum | Kanıt |
|---|---|---|---|
| KN-N1 | Transport önceliği: SSE (tek yönlü canlı) → WebSocket (çift yönlü zorunlu olduğunda) → polling (fallback) | Aktif | `network-master.md` §2 · `player-api.mock.js` L136 ✓ |
| KN-N2 | Stream: HTTP Range tek aralık → 206; ETag + Last-Modified zorunlu (seek + cache doğruluğu) | Aktif | `MusicStreamHandler.php` L16-L18 ✓ |
| KN-N3 | Retry: üstel backoff + jitter; sabit aralık yasak (thundering herd) | Aktif | `network-master.md` §3 (C-net-4) |
| KN-N4 | Offline-first: K01005-sync; ağ yokken yazım kuyruğa alınır, gelince uygulanır | Aktif | `network-master.md` §3 (C-net-6) |
| KN-N5 | WebSocket comms: 5 torun (session/audio/presence/notification/sync) tek bağlantıda multiplex | Aktif | `network-master.md` §2 |
| KN-N6 | Health probe: K01007 heartbeat; sonuç K016-observability'a metrik | Aktif | `network-master.md` §3 (C-net-5) |
| KN-N7 | Ölü K referansı temizliği: `K0100-identitiy` (bagimliliklar) + `K017-observability` (master §2.2 — gerçeği K016) düzeltmesi beyazliste dışı; ⚠️ işaretli | Açık ⚠️ | `network-bagimliliklar.md` · `network-master.md` §2.2 |

## §5 — Karar-öncelik sırası (vault-içi otorite)

```
.ai/CLAUDE.md §2  >  ADR (index.md)  >  KN- yerel karar (bu dosya)  >  network-master §3
```

Çelişki halinde üstteki kazanır; ADR ile KN- çakışırsa ADR kazanır ve KN- yeniden yazılır.

## §6 — Kanıt ve doğrulama durumu

| İddia | Kanıt | Durum |
|---|---|---|
| 7 accepted + 1 draft ADR | `.ai/.decisions/index.md` §3-§6 | ✓ Doğrulandı 2026-10-18 |
| KN-N1 SSE gerçeği | `player-api.mock.js` L136-L156 | ✓ Okundu 2026-10-18 |
| KN-N2 stream gerçeği | `MusicStreamHandler.php` L16-L18 | ✓ Okundu 2026-10-18 |
| ADR-103 başlığı | index.md draft-only | ⚠️ VERIFICATION REQUIRED |
| K017→K016 düzeltmesi | `network-master.md` §2.2 | ⚠️ Tespit, silinmedi (KN-N7) |

## §7 — Açık sorular

| # | Soru | Sahip |
|---|---|---|
| 1 | ADR-103 başlığı ne, kabul tarihi var mı? | Vault Steward |
| 2 | KN-N1'de polling fallback hâlâ gerekli mi (SSE her yerde destekli)? | K010-network |
| 3 | KN-N7 ölü referans düzeltmesi hangi turda? (beyazliste dışı) | Vault Steward |

## §8 — Karar kayıt şablonu (sonraki KN- kayıtları için)

```yaml
kod: KN-N8            # KN- önek + K010 sıra; ADR numarası ASLA tahsis edilmez
karar: "tek cümlelik karar"
durum: aktif | tartışılıyor | kapandı
tarih: YYYY-MM-DD
kanit: "<dosya> §<bölüm>"   # disk kanıtı yoksa: UNKNOWN
bagli_adr: ADR-0xx | null   # varsa index.md'den doğrulanmış numara
```

### Karar yaşam döngüsü

| Durum | Anlamı | Geçiş kuralı |
|---|---|---|
| tartışılıyor | Henüz uygulanmadı | kanıt + onay → aktif |
| aktif | Uygulanır, bağlayıcı | yeni kanıt → revize veya kapandı |
| kapandı | Yerine yenisi geldi | neden + successor kodu yazılır |

### Bu dosyadaki karar dağılımı

| Tür | Adet | Liste |
|---|---|---|
| Doğrulanmış ADR (accepted) | 7 | ADR-032 · 039 · 084 · 086 · 094 · 095 · 101 |
| ADR (draft — sayılmaz) | 1 | ADR-103 ⚠️ |
| KN- yerel (aktif) | 6 | KN-N1..KN-N6 |
| KN- yerel (açık ⚠️) | 1 | KN-N7 (ölü referans temizliği) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
