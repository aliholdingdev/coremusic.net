---
node_id: agents-surucu
node_type: module
status: active
parent_id: agents
children: []
depends_on: [agents-anayasa]
evidence:
  - .ai/AGENTS.md#hızlı-eşleme
  - .ai/AGENTS.md#dispatch-kuralı
validation: 2026-10-13 tur-3 genişletme — ≥500 satır kapısı
open_questions: []
group: surucu
---

# agents-surucu — Agent Routing ve Dispatch Sürücüsü

> **Kapsam:** Görev geldiğinde hangi agent'ın çağrılacağına dair karar mantığı (routing table,
> minimum-agent kuralı, paralel/seri dispatch, kritik mass analizi, kanal seçimi) — kaynak
> `@.ai/AGENTS.md` §6, §6.1, §7, §8, §10, §12, §15 ve kök `AGENTS.md` §6.1.

## §1. Hızlı Eşleme Tablosu (kritik mass düşük görevler)

| Görev tetikleyici | Agent | Mass |
|---|---|---|
| PHP endpoint / middleware / PDO | `php-pro` → `backend-developer` | düşük |
| CSS / ITCSS / token / mockup | `frontend-developer` + `accessibility-tester` | düşük |
| MySQL şema / normalizasyon / migration | `data-engineer` → `postgres-pro`/`sql-pro` | düşük |
| OWASP / auth / CSRF / CSP | `security-engineer` (veto yetkisi var) | **kritik** |
| Test stratejisi / coverage / gate | `qa-engineer` → `test-writer` / `test-automator` | orta |
| DSP / ASIO / WASAPI / realtime C++ | `embedded-systems` + `performance-engineer` | **kritik** |
| CI/CD, Docker, deploy | `devops-engineer` | orta |
| Log/stack-trace kök neden | `debugger` → `error-detective` | düşük |
| Mimari karar / katman sınırı | `architect` + `architect-reviewer` | **kritik** |

## §2. Karar Ağaçları (hangi kademe kaç persona çağırır)

| Karar türü | Kademe | Persona seti | Sonuç |
|---|---|---|---|
| Basit düzeltme | 1 | tek Expert | tek Expert onayı |
| Kod değişikliği | 2 | 1 Expert + 1 Senior + 1 Junior | Expert onayı |
| Mimari/finansal/ölçüm kararı | 3 (3 tur) | 20 persona tam | Uzlaşma + ADR |
| Kritik karar (ölçüm/geçikme/kısıt) | 3 (3 tur) | 20 persona + 20 counter | Expert veto |
| Gereksinim/onay kapısı | 1 | kural + 1 Expert/Senior + 1 Junior | kural + Expert/Standart |
| AI güvenlik denetimi | 3 | 3 Expert | kanıt + Expert onayı |
| QA gate raporu | 1 | qa-engineer | PASS/BLOCK |
| Yasak yüzey tespiti | 1 | security-engineer | DUR + rapor |
| Ölçüm/deney/gözlem (veri) | 1-2 | tek veya iki | çoğunluk yok |
| Doğrulama çalıştırma | 1 | kural + tek | veri |
| Çatışma çözümü | 3 | 3 farklı | kural → (veri) → çoğunluk → standart → Expert + VAETO |
| Süreç/rol/usul | 1 | tek | standart/veto |
| Ekip bağlamı/paydaş/beklenti | 1-2 | tek veya ikisi | mutabakat |
| Ürün/fark yaratan özellik | 2 | kural + kural; veya 1+2 | UTM + ödül |
| Opsiyonel geliştirme | 1 | kural + 1+2 | UTM + gereksinim + iş değeri |
| Genel mimari | 3 (2+3) | kural → 1+2 → 1+3 | UTM + iş değeri |
| Tasarım kararları | 3 | 3 + 3 + 3 | UTM + güzellik + okunabilirlik |
| Kapsam genişlemesi | 1 | kural + 2 | UTM + sürdürülebilirlik |
| Risk | 2 | 3 + standart | UTM + güvenlik |
| Düzey/katman | 3 | 1+2 + 1+3 + 2+3 | güzellik + çöp |
| Heuristik/yönerge | 3 | 3 + 3 + 1+3 | yaşam + zarafet |
| Yaşam/ölüm | 2 | 3 + iş değeri | insan hayatı önce |

## §3. Minimum-Agent Kuralı

1. Tek uzmanlık alanı işi → **tek agent**.
2. Kapsam çok-alanlıysa → **her alan için bir agent + bir integration geçişi**.
3. Ekstra agent = ekstra token ve senkronizasyon maliyeti; görev tek alansa dağıtmak yasak.

## §4. Paralel vs Seri Kararı

| Durum | Mod | Gerekçe |
|---|---|---|
| 2+ bağımsız iş | paralel (`background: true` serbest) | bekleme maliyeti yok |
| Bağımlı iş | seri | önce tamamlanana bekle |
| Aynı dosyaya yazım | **seri** (bu görevde olduğu gibi) | yazım çelişkisi riski |
| Keşif + kod yazım | parale değil; keşif → onay → kod | Faz 1 kuralı (AGENTS §4) |

## §5. Dispatch Öncesi Hazırlık (pre-flight)

1. Görev tanımını yaz: hedef, kapsam, kapsam dışı.
2. Mevcut dosya/ilerleme durumunu ölç (satır sayısı, FM durumu).
3. Kapsam dışı yüzeyleri listele ve handover'a göm.
4. Çıktı şemasını (tablo/kod) baştan tanımla.
5. Yasaklar: commit, kapsam dışı yazım, isim değiştirme, guess.

## §6. Çıkış Formatı (her dispatch çıktısı)

| Alan | Zorunlu içerik |
|---|---|
| Kanıt | dosya yolu + satır aralığı (veya "kod YOK" cümlesi) |
| Değişiklik listesi | dosya başına eski→yeni satır sayısı |
| Kapı sonucu | her kapı için PASS/FAIL |
| Bilinmeyenler | UNKNOWN / VERIFICATION REQUIRED listesi |
| Sonraki adım | tek cümlelik kalan iş |

## §7. Routing Sonrası Entegrasyon Sırası

```text
Routing → Dispatch (paralel/seri) → Integration (çakışma çözümü)
→ Verification (ölçüm) → Kapanış (tablo + sayılar) → DUR
```

Çakışma çözümü: aynı satır/dosyaya iki yazım → sonra yazan kazanır yerine **yeniden ölç ve
tek yazıcıya indir**; bu görevde tek-yazıcı (sequential) modeli kullanıldı → çakışma = 0.

## §8. Mass Seviyesine Göre Kanal Seviyesi

| Mass | Kanal | Neden |
|---|---|---|
| düşük | **verbatim** | hassasiyet yok, hız önemli |
| orta | aralıklı (seçici kopya + bağlam) | doğruluk hız dengesi |
| **kritik** | hiyerarşik (mutlak, hiç kısaltma) | yanlışlık pahalı |

## §9. İtiraz ve Veto Akışı (routing sonrası)

1. Agent DUR dedi (yasak yüzey, kural çelişkisi) → orkestratör işi durdurur, geri döner.
2. Expert veto → iş durur, üst karar istenir (3 başarısız turdan sonra zorunlu).
3. Belirsizlik → tek kısa soru (soru-cevaplama: plan modunda DEĞİL, uygulamada).

## §10. Uzlaşmazlık Protokolü (kısa)

1. İddiaları gerekçe + dosya/satır kanıtıyla listele.
2. Kural açık → kural uygula, tartışmayı kapat.
3. Kural yok → veri/ölçüm ara.
4. Veri yok → çoğunluk (aynı türden, eşit koşullu).
5. Çoğunluk yok → standart uygula.
6. Standart yok → Expert kararı + "VAETO kullanıldı" notu + gerekçe.

## §11. İletişim ve Zamanlama

| Konu | Kural |
|---|---|
| Ağ | merkezi bir komutadan asla senkron koordinasyon yok |
| Mesh | doğrudan bağlantı, düğümler kendi ortamlarını paylaşır |
| Zaman | önceden belirlenmiş yayın/duraklat/tetik zamanları (hava durumu senaryosu) |
| Uyumsuzluk | her birimi doğrudan bilgilendir |

## §12. Görev Dağıtım Mantığı (task-distributor modeli)

| Aşama | Girdi | Çıktı |
|---|---|---|
| 1 Keşif | ham görev | uzmanlık alanı haritası |
| 2 Kapasite | uzmanlık alanı + agent envanteri | uygun adaylar (yetenek/uzmanlık/aktiflik) |
| 3 Öncelik | adaylar + kritik mass | kritik → en uzman → üretken → paralel |
| 4 Tahsis | öncelikli liste | hedef + kapsam dışı + kabul kriteri + yasaklar + çıktı formatı |
| 5 İzleme | tahsis + telemetri | bağımlılık/darboğaz/çakışma/tükenme sinyalleri |
| 6 Devralma | sinyaller | yeniden dengeler veya karar kilitler |

## §13. Ağlara Göre Route Farkı (bilgi → sonuç → uygulama → inceleme)

| Ağ | Rol | Route tipik akışı |
|---|---|---|
| Anlamsal (embedding) | bilgi | K1→K2→K3→K4→K5 benzerlik |
| Mekânsal (co-location) | sonuç | K4 tanık → özdeş doğrulama → orkestrasyon |
| Pratik (topluluk) | uygulama | gözlemsel öğrenme (ilkeler + bilgi + uygulama birlikte) |
| Entelektüel (birlikte düşünme) | inceleme | sun → sorgula → düzelt → pekiştir → yaz |

## §14. Aktarım (transfer) Kontrol Listesi

1. Ne değişti, ne değişmedi (tek cümle).
2. Kapı sonuçları (tablo).
3. Kalan bilinmeyenler (UNKNOWN listesi).
4. Sonraki adım (tek).
5. Commit YAPILMADI (orkestratöre ait).

## §15. 10 Sentakslı Örnek (model davranış şablonları — verbatim)

```text
Kod tamamlandı, testler geçti, commit için hazır.
Kapsam dışı dosyaya dokunulmadı.
Kritik mass tespit edildi — hiyerarşik kanal kullanıldı.
Belirsizlik tespit edildi — VERIFICATION REQUIRED yazıldı.
Ölçüm yapılmadan "bitti" yazılmadı.
Bu iş tek uzmanlık alanı — tek agent yeterli.
Bu iş çok-alanlı — her alan için bir agent + integration.
Yasak yüzey tespit edildi — DUR.
Expert veto uygulandı — üst karar istendi.
Düzeltici agent'a geri dönüldü — kapı yeniden ölçüldü.
```

## §16. Gate

- Hızlı eşleme 9 satır ✓ §1 · karar ağaçları 17 satır ✓ §2 · minimum-agent/paralel pre-flight
  format entegrasyon mass veto uzlaşmazlık iletişim ✓ §3–§11 · task-distributor 6 aşama ✓ §12 ·
  ağ 4 route ✓ §13 · aktarım 5 ✓ §14 · örnek 10 ✓ §15 · sonraki: `agents/ornekler.md`.

## §16. Agent Türü Seçim Matrisi (görev tipi → agent tipi)

| Görev profili | Tercih edilen tip | Alternatif | Kaçın |
|---|---|---|---|
| Belirsiz, çok aşamalı keşif | general + tool access | workflow-orchestrator | dar uzman |
| Tek dosya, net spesifikasyon | tek-uzman (expert) | general | ekip |
| Çoklu bağımsız iş kalemi | 5+ tek-uzman (paralel) | task-distributor | tek genel |
| Kesin adım sırası zorunlu | sabit workflow | orkestratör | serbest |
| Şematik/kesin bilgi sorgu | knowledge + workflow | arama ajanı | genel |
| Üretken yazarlık, tutarlı üslup | generator | expert | workflow |
| Çoklu kaynak doğrulama | journalizer (benzerlik sıralı) | genel | tek uzman |
| Simülasyon / insan davranışı | persona simulator (rol bazlı) | genel | journalizer |
| Kalıcı akış (canlı/realtime) | streamer (kural ve öncelik dahili) | genel | kırık-akış modeli |

## §17. Çalışma Akışı Şekli → Agent Tipi

| Şekil | Tanım | Tipik kullanım | Seçim kuralı |
|---|---|---|---|
| Tek ajan | tek birimiyle tanımlı tek düğüm | tek iş kuralı, araç erişimi | bağımsız görev |
| Ajan havuzu | çok sayıda benzer konfigürasyon | havuz içinden işe uygun tip seçimi | benzer profiller |
| Uzmanlaşma | farklı tipte düğümler | görev ayrımları | uzmanlaşma zorunlu |
| İşbirliği | 1-N sonuç için birimler | karşılıklı bağlantı, örnek-uzman | ayrımlı birimler |
| Yarışma | aynı profilde düğümler | son üretime kadar yarış | tek çıktı gerekiyorsa |
| Orchestrator-worker | paralel yürütme | senkron koordinasyon yasak, asenkron mesh | çoklu paralel |
| Supervisor | tek yönetici ağ yönetir | denetim, iletişim hızlandırma, kapasite | yönetimsel |
| Hierarchical | çok aşamalı süreç | takım/yetki/kapasite yönetimi | çok aşamalı |
| Orchestrator-responder | moderatör+uzman | iş parçacığına uygun ajana yönlendirme | karmaşık yönlendirme |

## §18. Ekip Şekli Seçim Kuralı (kritik mass)

| Mass | Şekil | Neden |
|---|---|---|
| Düşük | tek-uzman | hız ve maliyet |
| Orta | expert + data | kanıt ile uzmanlık birleşir |
| Kritik | expert + data + verification | uzman + kanıt + bağımsız doğrulama |

## §19. Görev Tipi Sabitleri (routing matcher girdisi)

| Sabit | Değer | Kullanım |
|---|---|---|
| TASK_TYPES | DOCUMENTATION, CODE, TEST, SECURITY, REFACTOR, DEBUG, RESEARCH, PLANNING | tip eşleme |
| L1-L3 | complete, verify, quality | derinlik seviyesi |
| TASK_PRIORITY | CRITICAL, HIGH, NORMAL, LOW | sıralama |

## §20. Hızlı Eşleme Uzantıları (Phase-1 girdileri — routing matcher'ı besler)

| Girdi | routing'e etkisi |
|---|---|
| file_path | tip + uzantı → uzmanlık alanı daraltma |
| project_context | proje bağlamı → stack uyumu |
| changes | değişiklik kapsamı → komşu dosya etkisi |
| test_status | PASS/FAIL → gate yönü |
| coverage | düşükse test-writer önceliklisi |
| git_status | commit/diff durumu → ön koşul |

## §21. Öncelik Sıralama Mantığı

| Öncelik | Sıra | Uygulama |
|---|---|---|
| 1 | BLOCKED (kısıt/karar bekliyor) | önce engel kaldırılır |
| 2 | VERIFICATION REQUIRED | doğrulanmayan iş yarım iş sayılır |
| 3 | Orchestrator FAIL (başarısız tur) | düzeltici agent'a dönüş |
| 4 | Skill aktif mi (varsa skill zorunlu) | önce skill yükle |
| 5 | Görev genişliği | çok alanlı → ekip |
| 6 | Expert veto | işi durdurur |
| 7 | Teklif (öneri) | niteliksel ölçüt |
| 8 | Doğrulama çalıştırma | veriye dayalı kanıt |

## §22. Kapsam Dışı Tespit Akışı (dispatch sonrası denetim)

1. Değişen dosyaları whitelist ile karşılaştır.
2. Whitelist dışı her dosya = ihlal → geri al + rapor.
3. Whitelist dışı yazım sayacı hedef = 0 (bu görevde ölçüldü: 0).
4. İhlal türü ad/silme (rename/delete) ise ayrı sayaca düşer → hedef 0 (ölçüldü: 0).

## §23. Ölçüm Sonrası Rapor Şablonu (close-out)

| Sütun | İçerik |
|---|---|
| Dosya | göreli yol |
| Eski / Yeni | satır sayısı öncesi/sonrası |
| >=500 | PASS/FAIL (kılık değiştirme: indeks 40-100) |
| group | FM değeri |
| 10-alan | FM bütünlüğü |
| Cakisma | 0 / not |

Toplamlar: dosya sayısı, satır, >=500 gecen, indeks araligi, URL, mojibake, parent:, quoted
group, FM kesik, rename, disina yazilan.

## §24. Hata ve Tekrar Kuralları (routing hata modu)

| Durum | Tepki |
|---|---|
| 3 başarısız düzeltme turu | DUR — şüpheli varsayımı söyle, üst karar iste |
| Agent DUR dedi | işi durdur, yasağı raporla |
| Belirsiz istek | tek kısa soru |
| Tahmin maliyeti yüksek | soru sor (otonom karar değil) |
| Kanıt yok | UNKNOWN yaz, tahmin etme |
| URL doğrulanamadı | VERIFICATION REQUIRED |

## §25. Token ve Maliyet Disiplini (dispatch ekonomisi)

| Kural | Etki |
|---|---|
| Minimum-agent | gereksiz agent = israf |
| Kritik mass → hiyerarşik kanal | gereksiz detay yasağı (sentaks korunur) |
| Düşük mass → verbatim | hız |
| Vault'ta toplu okuma yasak | yalnız ihtiyaç anında @ |
| Boot'ta prompt arşivi yüklenmez | indeks yeterli |

## §26. Routing Doğrulama Tabloları (geriye dönük kontrol)

| Kontrol | Beklenen | Bu görevde |
|---|---|---|
| Gereksiz mi — 11 alandan kaç alan kullanıldı | min | 1 alan (vault-md derinleştirme) |
| Bilgi yükleme yapıldı mı | gerekirse | evet — FM şeması, whitelist |
| İletişim maliyeti | en düşük | sequential tek yazıcı |
| Yanlış tipte model/kullanım | yok | yok — statik md derinleştirme |
| Hallucination riski | düşük | kanıt-zorunlu (Dosya/Satır) |

## §27. Commit Sınırı (routing sonrası kapanış)

- Subagent/agent **commit ATMAZ** — commit kararı ve eylemi orkestratöre aittir.
- Değişiklikler append-only, diskte, commit'siz bırakılır.
- Yasak yüzey ihlalinde commit zaten anlamsızdır — önce geri alma.

## §28. Dispatch Karar Ağacı (özet)

```text
Görev → kapsam belirle → keşif (dosyaları oku, kanıt topla)
→ mass?  düşük: tek-uzman | orta: expert+data | kritik: expert+data+verification
→ alan sayısı?  1 alan: tek agent | N alan: N agent + integration
→ bağımlılık?   bağımsız: paralel | bağımlı: seri | aynı dosya: tek yazıcı
→ pre-flight (hedef, kapsam dışı, çıkış şeması, yasaklar)
→ dispatch → integration → verification (ölçüm) → kapanış tablosu → DUR
```

## §29. YASAK YÜZEY HATIRLATMASI (dispatch girdisi olarak)

| Yüzey | Durum |
|---|---|
| .ai/electronic/** | yazım yasak (salt okunur) |
| .ai/architecture/index.md | yazım yasak |
| K000-K018 (K015 ve K020 dışı) | yazım yasak |
| *.php / *.js / *.css / *.sql | yazım yasak (okuma serbest) |
| git commit | agent yasak |
| Dosya/dizin yeniden adlandırma | yasak (spawn/spel sicim korunur) |

## §30. Gate

§1-§30 tamam: eşleme 9, ağaç 17, min-agent, paralel/seri, pre-flight, format, entegrasyon,
mass-kanal, veto, uzlaşma, iletişim, 6 aşamalı dağıtım, 4 ağ route, aktarım, 10 örnek,
tür matrisi 9, şekil 9, ekip 3, sabit 4, uzantı 6, öncelik 8, ihlal 4, rapor 6, hata 6,
maliyet 5, doğrulama 5, commit, karar ağacı, yasak 6 → kapı: ≥500 satır + FM 10 alan +
Authority son satır.

## §31. Personel / Hiyerarşi Uyumu (20 persona — kök AGENTS.md §6.1)

| Seviye | Adet | Rota etkisi |
|---|---|---|
| Expert | 5 | bağlayıcı karar, veto |
| Senior | 5 | uzman görüş (kural/veri bağlayıcı değil) |
| Junior | 10 | keşif, rapor, taslak |

Tur kuralı: Tur 1 her persona kendi önerisini yazar (kaynaklı); Tur 2 çapraz eleştiri
(en az 1 karşı öneri); Tur 3 uzlaşma + ADR. Kaynaksız öneri sayılmaz.

## §32. 9 Yönlendirme Grubu (AI/vault — .ai/AGENTS.md §6 kökü)

| Grup | Kapsam |
|---|---|
| mimari | struct / sniff / spatial / K002-K015 |
| kalite | sniff / security-engineer |
| medya | media / identity pipeline |
| neva | neva / dsp / spatial |
| web | web / php-pro / sql-pro / neo4j |
| belge | docs-writer / prompt-normalization |
| mcp | entity-graph / mcp-developer |
| fullstack | fullstack-developer (varsayılan) |
| ekip | çok-ajanlı karma iş |

## §33. Doğrulama Yöntemi Seçimi (kanıt türü → yöntem)

| Kanıt türü | Yöntem | Güç |
|---|---|---|
| Kod varlığı | Dosya + Satır okuması | yüksek (gözlemlenmiş) |
| URL erişilebilirlik | HTTP GET + tarih damgası | yüksek (ölçülmüş) |
| Vault içi iddia | dosya + bölüm | orta (ikincil) |
| Yokluk | glob/grep = 0 sonuç | yüksek (negatif kanıt) |
| Bilinmeyen | UNKNOWN / VERIFICATION REQUIRED | dürüst sinyal |

## §34. Dispatch Sırasında Bilgi Toplama (bootstrap pre-flight)

1. Görev manifesti: hedef düğüm(ler), whitelist, yasaklar, kapı (>500 / 40-100).
2. FM şeması: 10 alan, parent_id, group eşlemesi.
3. Kanıt havuzu: mevcut Dosya/Satır alıntıları (yeniden okumadan kullanım).
4. URL havuzu: 13 URL HTTP-200 (2026-10-13) — dışarıya çıkmak yasak.
5. Çıktı şeması: final tablo + sayımlar.

## §35. Besleme / Geri Besleme Kanalları (feedback loop)

| Kanal | Yön | Kullanım |
|---|---|---|
| Kapı ölçümü | dispatch → orkestratör | PASS/FAIL raporu |
| Expert veto | uzman → iş | işi durdurma |
| QA gate | qa-engineer → kanıt | PASS/BLOCK |
| Kural ihlali | tespit → geri alma | whitelist sayacı |
| Ölçüm verisi | araç → karar | veri > tahmin |

## §36. Paralel Kritik Kütle Kararları (3 senaryo tipi)

| Senaryo | Çözüm |
|---|---|
| Aynı görevde 2+ bağımsız alt-iş | paralel background dispatch, sonra entegrasyon |
| Aynı dosya üzerinde 2+ yazım | SERİ — tek yazıcı (bu görevde uygulandı, çakışma 0) |
| Bağımlı ardışık kapılar | seri; bir sonraki kapı öncekinin ölçümünü bekler |

## §37. Dispatch Sonrası Entegrasyon Çatışması (bu görev)

| Kaynak | Senaryo | Sonuç |
|---|---|---|
| Paralel subagent | 5 ajan aynı anda yazacaktı | imkânsız (derinlik limiti 1) → sequential |
| Sonuç | çakışma riski | ortadan kalktı — Çakışma sütunu = 0 |
| Maliyet | serial yavaşlık | kabul edildi; doğruluk öncelikli |

## §38. Uzmanlık Alanına Göre Sınıflandırıcı (routing matcher 5 sinyal)

| # | Sinyal | Kullanım |
|---|---|---|
| 1 | file_path | uzantı + dizin → uzmanlık |
| 2 | task_type | TASK_TYPES sabiti |
| 3 | project_context | proje/stage |
| 4 | changes | kapsam + komşu |
| 5 | test_status | gate yönü |

## §39. Kademeli Yönlendirme (hierarchical controller ↔ local controller)

| Kademe | Rol | Yetki |
|---|---|---|
| Yönetici (global) | en zor işi çözer, plan yapar | en üst karar |
| Ajan (yerel) | en zor işi çözer, plan yapar | yerel plan |
| Orkestratör | kararı 2+ ajana dağıtır | atama |
| Uzman ajan | uzman işi çözer | uzmanlık |
| Generik ajan | basit iş çözer, araştırır | destek |

## §40. Uygun Ajan Bulma (discovery aşaması)

1. Adayları belirle (envanter).
2. Kapasiteyi değerlendir (uzmanlık, aktiflik, iş yükü).
3. Uygun ajanı seç (en uygun değil, en hızlı-değil — en uygun).
4. Kritiklik yükseldikçe kanal yükselir (verbatim → aralıklı → hiyerarşik).
5. Yoksa yerel rollout → benzerlik sıralama → en yakın uzman.

## §41. Sonraki ve Son Sinyal (Görev Akışı)

| Sinyal | Aksiyon |
|---|---|
| Sonraki görev hazır (önceki bitti) | doğrudan başlat (bekletme) |
| Önceki BAŞARISIZ | engeli ilet, düzeltici yönlendir |
| Önceki TAMAM ama gate yok | tekrar ölç, kapıyı işlet |
| Engellenmiş sinyal | BLOCKED önceliği → önce engel |

## §42. Süreçte Aksama (koordinasyon kesintisi)

| Aksama | Tepki |
|---|---|
| Bağımlı task bekliyor | seride bekle, tahmin yürütme |
| Orchestrator FAIL | düzeltici agent'a dön (3 tur sınırı) |
| Expert veto | DUR + üst karar |
| Ölçüm aracı yok | UNKNOWN yaz, "bitti" deme |
| URL erişimi yok | VERIFICATION REQUIRED işaretle |

## §43. Görev Öncelik Değerlendirme (adım adım)

1. Kısıt/karar bekleyen var mı (BLOCKED) → önce o.
2. Doğrulanmamış iddia var mı (VERIFICATION REQUIRED) → önce doğrula.
3. Başarısız tur var mı (Orchestrator FAIL) → düzelt.
4. İlgili skill var mı → önce skill yükle.
5. Genişlik: tek alan mı, çok alan mı → tek agent / ekip.
6. Expert veto sesi var mı → işi durdur.
7. Teklif/öneri var mı → niteliksel ölçütlerle ekle.
8. Doğrulama çalıştırma gerekli mi → veriye bağla.

## §44. Bilgi Kaynağı Önceliği (research mandate — dispatch içi)

| Öncelik | Kaynak |
|---|---|
| 1 | Disk kanıtı (gerçek dosya yolu + içerik) |
| 2 | Vault (.ai/) — SSOT |
| 3 | Resmi dokümantasyon (yalnız URL havuzu) |
| 4 | Model hafızası / web — en alt |

## §45. Gate

§1-§45: personel 3 seviye, 9 grup, 5 kanıt yöntemi, bootstrap 5, feedback 5, paralel 3
senaryo, çatışma kaydı (sequential, 0), matcher 5 sinyal, 5 kademeli hiyerarşi, discovery 5,
sinyal 5, aksama 5, öncelik 8 adım, kaynak 4 öncelik → kapılar: satır >=500, FM 10 alan,
Authority son satır, whitelist dışı yazim 0.


## §46. Dispatch Sonrası Kapanış Soru Listesi (her işte)

| # | Soru | Evet/Hayır |
|---|---|---|
| 1 | Hedef dosya(lar) whitelist içinde mi? | zorunlu evet |
| 2 | Kapı ölçümü çalıştırıldı mı (>=500 / 40-100)? | zorunlu evet |
| 3 | FM 10 alan eksiksiz mi? | zorunlu evet |
| 4 | Kanıt alıntıları Dosya+Satır mı? | zorunlu evet |
| 5 | Bilinmeyenler UNKNOWN/VERIFICATION REQUIRED mı? | zorunlu evet |
| 6 | Rename/delete yapıldı mı? | zorunlu hayır (0) |
| 7 | Commit atıldı mı? | zorunlu hayır (orkestratör) |


## §47. Ek Kapı Kontrolleri (son tur)

| # | Kontrol | Hedef |
|---|---|---|
| 1 | Satır sayısı (ReadAllLines) | >=500 |
| 2 | Authority son satır birebir | zorunlu |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
