---
node_id: K020-discovery-riskler
node_type: subdomain
status: draft
parent_id: K020-discovery
children: []
depends_on: [K007-data, K009-security, K012-services]
evidence:
  - ".ai/architecture/K020-discovery/discovery-master.md §8 (6 risk/soru — 2026-10-18)"
  - ".ai/architecture/K020-discovery/discovery-bagimliliklar.md §5 (kırılma senaryoları — 2026-10-18)"
  - ".ai/architecture/K020-discovery/discovery-dogrulama.md §6 (5 havale bekleyen — 2026-10-18)"
validation: "Her risk Olasılık×Etki×Azaltma×Sahip taşır; değerler NİTEL (düşük/orta/yüksek) — sayısal skor uydurulmaz."
open_questions:
  - "⚠️ Deezer API kota sayısı resmi olarak yayımlanmadı (2026-10-17 web) — UNKNOWN"
  - "⚠️ Keşif kümeleme eşiği tanımsız — risk azaltmasında eşik adı yazılmaz"
group: mimari
---

# Discovery — Riskler

> K020-discovery'nin mimari/süreç riskleri. Değerler **NİTEL**tir
> (düşük/orta/yüksek) — sayısal olasılık/etki skoru **uydurulmaz** (zero-hallucination).

## §1. ELI15

Risk = "bir şey ters giderse ne kaybederiz". Her riskte dört şey zorunlu:
**Olasılık** (ne sıklıkla), **Etki** (ne kaybederiz), **Azaltma** (ne yaparız),
**Sahip** (kim çözer). Sahipsiz veya azaltmasız risk = açık ihmal.

## §2. Risk matrisi (R1-R10)

| ID | Risk | Olasılık | Etki | Azaltma | Sahip |
|---|---|---|---|---|---|
| R1 | S-1 çözülmezse K007 ↔ K020 çift "arama" yazar → tutarsız indeks | yüksek | yüksek | Geçici K-SCOPE-4 (kurallar K020, indeks K007); sahiplik ADR'si aç | data-engineer + architect |
| R2 | D-3: K008 discovery ↔ K020-discovery sahiplik örtüşmesi — aynı ad iki yerde | orta | yüksek | Sahiplik sınırı ADR'si (domain + architect); `⚠️` çift taşıma | architect |
| R3 | ID CONFLICT D-5 (6× K020 adayı: adr/comilance/comere/licence/discovery/recomenataion) | yüksek | orta | Onaysız isim DEĞİŞTİRİLMEZ; üst karara havale | Vault Steward (üst karar) |
| R4 | YTM kapısı ihlali — resmi API yok, TOS riski | düşük | yüksek | Kapı DEĞİŞMEZ: kod yasağı; ADR-097 §1/6 çapraz denetimi her keşifte | discovery (root) + security-engineer |
| R5 | Uydurma referans geri döner (eski SignalType/signal.js/log.md atıfları) | orta | yüksek | Truth Mode kaydı (master §9.3) arşivlendi; her yazım öncesi grep/0-isabet tekrarı | Vault Steward |
| R6 | Secret sızıntısı — keşif raporuna token yazma | orta | yüksek | ADR-095 §1.2: token yalnız `.env`'den anılır, `.md`'ye yazılmaz | security-engineer |
| R7 | Rate-limit ihlali → servis banı (MusicBrainz 1/sn · Musixmatch 2.000/gün) | orta | orta | Rate-aware kuyruk + 429/Retry-After kalıbı (kod-ornekleri §3.1); User-Agent zorunlu | backend-developer |
| R8 | Servis kapısı değişir (API sunset/lisans) → keşif bayatlar | orta | orta | Erişim tarihli kanıt (verified_on); web taraması periyodik tekrar | discovery (root) |
| R9 | Aşama 3/4 eşik/demet tanımsız → keyfi kümeleme | yüksek | orta | Eşik uydurulmaz; `⚠️` + ölçüm sonrası karar (kararlar Örnek-3) | discovery (agent/) |
| R10 | Onaysız kod yazımı (keşif → doğrudan entegrasyon) | düşük | yüksek | AGENTS.md §1 `APPROVAL → IMPLEMENTATION`; gate §6 denetimi | Vault Steward |

## §3. Kırılma senaryoları (bagimliliklar §5 — ne olur?)

| # | Tetik | Sonuç | Kurtarma |
|---|---|---|---|
| 1 | S-1 sahipliği çözülmez | çift indeks, tutarsız sonuç | sahiplik ADR'si (data-master.md:219 bekliyor) |
| 2 | D-1 yönü ters çevrilirse (discovery, öneri beklerse) | sıra bozulur: öneri keşfi beklemez | ok yönü korunur: `recomenataion → discovery` |
| 3 | Deezer kotası bilinmeden entegrasyon | 402/ban sürprizi | `UNKNOWN` yaz; entegrasyon öncesi canlı test |

## §4. Havale bekleyenler (risk → üst merci)

| # | Bekleyen | Üst merci | Durum |
|---|---|---|---|
| 1 | S-1 ID CONFLICT | registry SSOT + üst karar | açık |
| 2 | K008 C2 sahiplik | domain + architect | açık |
| 3 | Medya-sağlayıcı auth gate onayı | Bayram Ali (kullanıcı) | açık |
| 4 | Deezer API kota | web (yeni tarama) | UNKNOWN |
| 5 | Yeni K020 dosyalarının registry kaydı | orchestrator | **KAPANDI 2026-10-17** (§9.14) |

## §5. Risk sonrası kontrol listesi (her keşif oturumu)

- [ ] S-1 sınırı çakışması `⚠️` taşındı mı? (karar verilmedi gibi sunulmadı)
- [ ] D-1 yönü korundu mu (`recomenataion → discovery`)?
- [ ] Token/anahtar `.md`'ye yazılmadı mı (ADR-095 §1.2)?
- [ ] YTM kapısına dokunuldu mu? (dokunulmamalı)
- [ ] Ölçüm kanonu `ReadAllLines().Length` kullanıldı mı?

---

**Authority:** Bayram Ali / Vault Steward
