---
node_id: K010-network/riskler
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
  - ".ai/architecture/K010-network/network-master.md §7 (§7/1-§7/8 açık sorular) — okundu 2026-10-18"
  - ".ai/architecture/K010-network/network-bagimliliklar.md (ölü K0100-identitiy) — okundu 2026-10-18"
  - ".ai/architecture/K010-network/network-master.md §2.2 (ölü K017-observability — gerçeği K016) — okundu 2026-10-18"
validation: "Riskler network-master §7 açık sorularıyla hizalı; ölü referanslar ⚠️ işaretli, silinmedi"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — §7/1-§7/3 açık soruları (çakışma çözümü, WS auth,
  heartbeat periyodu) karara bağlanmadı; production öncesi kapatılacak.
group: kanit
---

# K010-network — Riskler

## §1 — ELI15: Bu nedir?

Ağ katmanının "burada patlayabilir" listesi. Her satır: olasılık (ne sık?),
etki (patlarsa ne olur?), azaltma (ne yapıyoruz?), sahip (kim çözüyor?).
En görünür risk "bağlantı koptu" — kullanıcı akışın ortasında takılır;
azaltma: backoff + offline kuyruk + durum makinesi.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Ağ riskleri: kopma, backoff, WS auth, sync çakışma, thundering herd, ölü referanslar |
| Kapsam dışı | DSP riskleri (K004) · firmware riskleri (K005) · auth tasarımı (K012) |

## §3 — Risk matrisi (Olasılık × Etki × Azaltma × Sahip)

| # | Risk | Olasılık | Etki | Azaltma | Sahip | Kanıt |
|---|---|---|---|---|---|---|
| RN-1 | Bağlantı kopması (akış/WS ortasında) | Yüksek | Orta | C-net-1 durum makinesi + KN-N3 backoff + K01005 offline | K010-network | `network-master.md` §3 |
| RN-2 | Thundering herd: koptuğunda tüm istemciler aynı anda döner | Orta | Yüksek | KN-N3: üstel backoff + %25 jitter (kod §4.1/§4.9) | K010-network | `network-master.md` §3 |
| RN-3 | WS auth token süresi doldu → sessiz kopma | Orta | Yüksek | Yenileme akışı + FAILED durumunda yeniden auth (K012) | K012-security | network-master §7 (§7/2) ⚠️ |
| RN-4 | Offline sync çakışması (iki cihaz aynı kaydı değiştirdi) | Orta | Yüksek | Çakışma çözümü kararı (LWW/CRDT) — **karar verilmedi** ⚠️ | K010-network | network-master §7 (§7/1) ⚠️ |
| RN-5 | Heartbeat periyodu yanlış: aşırı yük veya geç fark | Düşük | Orta | Periyot kararı ⚠️; K016 metrikleriyle kalibre | K010-network | network-master §7 (§7/3) ⚠️ |
| RN-6 | Range/ETag uyuşmazlığı → yarım dosya veya bozuk cache | Düşük | Orta | If-Range sözleşmesi (kod §4.7); ETag md5(path|size|mtime) | K010-network | `MusicStreamHandler.php` L80 ✓ |
| RN-7 | SSE buffer taşması (uzun oturum, uzak sunucu) | Düşük | Düşük | Periyodik heartbeat + istemci reconnect | K010-network | C-net-2 |
| RN-8 | Ölü K referansları (K0100-identitiy · K017-observability) kafa karıştırır | Gerçekleşti | Düşük | ⚠️ işaretlendi; düzeltme beyazliste dışı — sonraki turda registry adıyla eşle | Vault Steward | `network-bagimliliklar.md` · `network-master.md` §2.2 |
| RN-9 | Offline kuyruk uygulamasında sıralama bozulması (LWW yanlışlığı) | Düşük | Orta | Kuyruk sırası korunur, kısmi drain YASAK (kod §4.6) | K010-network | kod-ornekleri §4.6 |
| RN-10 | REST rate-limit → istemci 429 storm'u | Orta | Orta | 429'da Retry-After'a saygılı backoff (K009-api köprüsü) | K009-api | network-master §7 ⚠️ |

## §4 — Açık sorular (network-master §7 ile hizalı)

| # | Soru | Etkilediği risk | Durum |
|---|---|---|---|
| §7/1 | Sync çakışma çözümü: LWW mi CRDT mi? | RN-4 | ⚠️ Açık |
| §7/2 | WS auth yenileme (token expiry) | RN-3 | ⚠️ Açık (K012) |
| §7/3 | Heartbeat periyodu | RN-5 | ⚠️ Açık |

## §5 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| 10 risk (RN-1..RN-10) | bu dosya §3 | ✓ 2026-10-18 |
| ETag formülü | `MusicStreamHandler.php` L80 | ✓ Okundu 2026-10-18 |
| §7/1-7/3 açık sorular | `network-master.md` §7 | ✓ Okundu 2026-10-18 (karar ⚠️) |
| K0100-identitiy · K017-observability ölü referans | bagimliliklar · master §2.2 | ✓ Tespit, ⚠️ işaretli |

## §5 — Risk kayıt şablonu (sonraki RN- kayıtları için)

```yaml
kod: RN-11                 # RN- önek + K010 sıra
risk: "tek cümlelik risk"
olasilik: yüksek | orta | düşük | kapandı
etki: yüksek | orta | düşük
azaltma: "somut, ölçülebilir önlem"
sahip: K010-network | K009-api | K012-security | ...
kanit: "<dosya> §<bölüm>" | "⚠️ VERIFICATION REQUIRED"
durum: açık | kapandı | izleniyor
```

### Azaltma türü sınıflandırması

| Tür | Açıklama | Bu dosyadaki örnek |
|---|---|---|
| Önleyici (prevent) | Risk hiç gerçekleşmesin | Jitter (RN-2) · If-Range (RN-6) |
| Tespit (detect) | Gerçekleşirse hemen gör | Heartbeat (RN-5) · K016 metrik |
| Kurtarma (recover) | Gerçekleşse de toparlan | Backoff (RN-1) · offline kuyruk (RN-9) |
| Devir (transfer) | Başkasına aktar | K012 auth (RN-3) · K009-api (RN-10) |

### Durum dağılımı (bu sürüm)

| Durum | Adet | Kayıtlar |
|---|---|---|
| Açık (azaltılmış) | 8 | RN-1..RN-7, RN-9, RN-10 (kısmen) |
| Gerçekleşti (düşük etki) | 1 | RN-8 (ölü referans) ⚠️ |
| Karar bekleyen | 3 | RN-3 (§7/2) · RN-4 (§7/1) · RN-5 (§7/3) ⚠️ |

### İzleme periyodu

| Risk sınıfı | Periyot | Kanal |
|---|---|---|
| Runtime (RN-1/2/3/5/10) | Sürüm başına | K016 metrikleri ⚠️ (henüz yok) |
| Veri doğruluğu (RN-6/9) | Sürüm + schema değişimi | Test kapısı (test-stratejisi §5) |
| Dokümantasyon (RN-8) | Vault senkronunda | Vault Steward |

### Karar bekleyen risklerin kapanış yolu

| Risk | Karar | Kapış kriteri |
|---|---|---|
| RN-4 | §7/1: LWW vs CRDT | Karar verilip KN- kodu yazılınca kapanır |
| RN-3 | §7/2: WS token yenileme (K012) | K012 ile ortak sözleşme yazılınca |
| RN-5 | §7/3: heartbeat periyodu | K016 metrikleriyle kalibre edilince |

### Risk-hedef eşleme (hangi risk hangi testle kanıtlanır)

| Risk | Test senaryosu (test-stratejisi) |
|---|---|
| RN-1 (kopma) | T-N6 (kasıtlı kopma → reconnect) |
| RN-2 (thundering herd) | T-N1 (jitter/backoff birim testi) |
| RN-6 (ETag) | T-N3 · T-N5 (If-Range) |
| RN-9 (kuyruk sırası) | T-N4 · T-N9 (offline drain) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
