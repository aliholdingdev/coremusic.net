# workflow-dispatch — Risk, İzin Matrisi, HITL ve Escalation

> **Kaynak bağlamı:** Bu dosya, `SKILL.md` §20 indeks satırındaki
> "Risk L0-L4, R.A.I.L. matrisi, HITL 4 kontrol noktası, escalation matrisi,
> skill oluşturma protokolü" maddesinin tam metnidir (orchestration merge
> 2026-10-07'de indekslendi; içerik bu dosyada yaşar). Boot'ta okunmaz —
> **dispatch öncesi, onay/escalation kararında** okunur.
>
> **Skill:** agent-orchestrator · **Updated:** 2026-10-21

---

## §1 Risk Seviyeleri (L0–L4)

> Seviye = **yanlış yapılırsa geri dönüş maliyeti** (süre değil).
> Her dispatch'te görevin seviyesi handover'a açıkça yazılır.

| Seviye | Ad | Kapsam | Örnek | Geri dönüş |
|--------|----|--------|-------|------------|
| **L0** | Salt-okunur keşif | Dosya okuma, ölçüm, analiz, rapor taslağı | dizin yapısı haritası, kod incelemesi, satır ölçümü | anında (zaten değişmedi) |
| **L1** | Yerel/geri alınabilir yazım | Geri alınabilir dosya yazımı: skill/references/examples, test dosyası, geçici plan | SKILL.md taslağı, test yazımı, `plans/` notu | `git checkout` / dosyayı geri al |
| **L2** | Kaynak kod değişimi | `*.php` / `*.js` / `*.css` / `*.sql` prodüksiyon yüzeyi | endpoint değişikliği, CSS düzeltmesi | diff + rollback (test kanıtı şart) |
| **L3** | Paylaşılan yapı / entegrasyon | CI, ayar dosyaları, bağımlılık sürümü, şema migration | `settings.json`, workflow `.yml`, migration | kilitli / geri alması pahalı → **onay kapısı** |
| **L4** | Dışa dönük / geri alınamaz | Production deploy, secret üretimi/döndürme, git commit (yetkili tarafta), ödeme/bildirim gönderimi, veri silme | canlıya deploy, `git push`, anahtar rotasyonu | **HITL zorunlu** (§3) |

**Kural (bağlayıcı):** Seviye **L2+** ise handover'da seviye + geri-alınabilirlik
yöntemi yazılır; **L3-L4** ise dispatch ÖNCESİ §3 onay kapısından geçer.

**Belirsizlik kuralı:** Seviye iki arada kaldığında **yüksek olan** seçilir
(şüphe = L4 varsay, onaya sor).

## §2 R.A.I.L. İzin Matrisi

> Her subagent çağrısına verilen **izin harfi** — handover'da açıkça belirtilir.
> Varsayılan: **R**. Genişletme = bilinçli karar, otomatik değil.

| Harf | İzin | Ne yapabilir | Ne yapamaz | Tipik rol |
|------|------|--------------|------------|-----------|
| **R** | Read-only | oku, ölç, araştır, raporla | dosya DEĞİŞTİRME (ne edit ne write) | `explore`, `researcher`, `reviewer` |
| **A** | Append-only | yalnız ekler: yeni reference/örnek dosyası, log satırı, changelog girdisi | mevcut satır DEĞİŞTİRME/SİLME | `docs-writer`, vault-sync görevleri |
| **I** | Implement | hedef dosya(lar)da düzenleme + yeni dosya (belirlenen kapsamda) | kapsam dışı dosya, `git commit`, deploy | `developer`, `data-engineer`, `test-writer` |
| **L** | Lead (karar/yürütme) | plan/ADR taslağı, dispatch, **onaylı** commit/deploy adayı hazırlama | onaysız L4 eylemi (commit/deploy/secret) | orkestratör (MO) — kendisi için ayrı |

**Matris kuralları:**

1. **Kapsam daraltma serbest, genişletme yasak:** R→A→I→L genişlemesi yalnız
   açık handover değişikliğiyle olur; subagent kendi iznini genişletemez.
2. **I ≠ L:** Kod yazmak I'dir; commit atmak L'dir — subagent **asla** L almaz
   (commit kararı ve eylemi orkestratöre aittir; kök `AGENTS.md` §7/§8).
3. **A yüzeyi hassas:** `log.md` append-only'dir; mevcut satır düzenleme = ihlal.
4. **Yasak yüzeyler her izinde geçerli:** `.ai/`'ye yazma (yalnız vault-sync
   yolu), secret değerini `.md`/`.log`'a yazma, frozen ADR metni değiştirme.
5. İzin harfi handover'ın ilk satırıdır: `İZİN: I · SEVİYE: L2`.

## §3 HITL — 4 Kontrol Noktası (Human-In-The-Loop)

> HITL = insan onayı olmadan geçilemeyen kapı. Kapıyı **subagent değil,
> orkestratör/user** açar.

| # | Kapı | Ne zaman | Ne onaylanır | Atlarsan ne olur |
|---|------|----------|--------------|------------------|
| **H1** | **Plan onayı** | L2+ çok dosyalı iş başlamadan | kapsam, dosya listesi, kabul kriteri, yasak yüzeyler | kapsam kayması, yanlış dosyaya yazım |
| **H2** | **Güvenlik / veto** | auth, secret, şema, bağımlılık, CSP değişikliğinde | güvenlik-agentı raporu (veto yetkili) | OWASP ihlali production'a sıçrar |
| **H3** | **Yürütme onayı** | L4: commit / deploy / secret / dışa dönük gönderim | diff/plan özeti + geri alma planı | yetkisiz commit, geri alınamaz dış etki |
| **H4** | **Kapanış onayı** | iş bittiğinde (ölçüm + rapor sonrası) | PASS/BLOCK raporu, ölçüm kanıtı | kanıtsız "bitti", vault kirlenmesi |

**Kurallar:**

- Kapılar **sıralıdır**: H1 geçmeden H3'e atlanamaz; H4'te ölçüm yoksa onay yok.
- Onay **yazılıdır** (mesaj kaydı); sözlü/varsayılan onay sayılmaz.
- Red → ya kapsam revize edilir ya iş durur; **onay yoksa üretim yok**.
- İstisna: **L0-R işleri** (salt okuma/araştırma) H1-H3'ten muaftır; rapor
  H4'te yine sunulur.

## §4 Escalation Matrisi

| Durum | Tetik | Aksiyon | Sınır |
|-------|-------|---------|-------|
| **Deneme** | ilk hata (net hata mesajı var) | düzelticiye geri dön + hata + kök neden | 1. deneme |
| **Tekrar** | ikinci aynı hata | farklı yaklaşım/zaman aşımı; şüpheli varsayımı açıkça yaz | 2. deneme |
| **Blok** | 3. aynı hata | **DUR** — şüpheli varsayım + üst karar isteği yazılı | **3 başarısız tur = dur** |
| **Veto** | güvenlik-engineer ihlal bildirdi | iş durdurulur; düzeltme + yeniden denetim | veto kaldırılmadan devam yok |
| **Çatışma** | iki agent aynı dosya/d Kararda | orkestratör çözer (tek yazıcı prensibi) | subagent çatışmayı kendi çözemez |
| **Belirsizlik** | tahmin maliyeti yüksek soru | tek kısa soru → kullanıcıya | uzun soru zinciri yasak |

**Çıkış formatı (blok/veto'da):**

```text
DURUM: BLOCKED
- Ne denendi: ...
- Hata kanıtı: (dosya:satır / log)
- Şüpheli varsayım: ...
- Önerilen üst karar: ...
- Deneme sayısı: n/3
```

**Zaman aşımı kuralı:** Bir dispatch beklenenden 3× uzun sürüyorsa (kendi
tahminiyle) ara rapor + devam kararı istenir; sessizce ilerlemez.

## §5 Skill Oluşturma Protokolü (keşfedilen yeni yetenek)

> Dispatch sırasında **tekrar eden** bir iş akışı görünce: kodlamadan önce
> protokolü işlet; subagent kendi başına skill **oluşturmaz**.

```text
1. ÖRNEKLEME  → iş 2+ kez elle yapıldı mı? (kanıt: oturum/log kaydı)
2. TESPİT     → tekrar eden adımlar + girdi/çıktı biçimleri listelenir
3. ONAY (H1)  → kullanıcıya: "X skill'i öneriyorum — kapsam bu" (kapsam + dosya)
4. ÜRETİM     → skill-maker kalıbıyla SKILL.md (N1-N10) + references/checklist.md
5. ÖLÇÜM      → satır/orphan/frontmatter kapıları (skill-maker ADIM 5)
6. KAYIT      → envanter (CONTEXT.md / registry) güncellenir — yalnız orkestratör
```

- **Yalnız `skill-maker`** format otoritesidir; bu protokol onun kalıbını
  kullanır, yeniden tanımlamaz.
- Adım 3 onaysız üretim = yetkisiz yetenek (H1 ihlali).
- Şablonsuz skill üretimi yasak (Guardrail #16).

## §6 Dispatch Şablonu (handover — her çağrıda zorunlu)

```text
İZİN: R|A|I        · SEVİYE: L0..L4        · KAPı: H1..H4 (gerekliyse)
GÖREV: 1 cümle (ne DEĞİL ne dahil)
HEDEF: dosya(lar) — mutlak yol
KABUL: ölçülebilir kriter (satır/örnek/çalışma kanıtı)
YASAK: git commit · .ai/ · kapsam dışı dosya · (varsa CLAUDE.md sidecar)
ÇIKTI: kanıt + değişiklik listesi + eksik/BLOCK satırı
```

**Dispatch öncesi 3 kontrol:** (1) seviye yazıldı mı → (2) izin harfi ve
yasaklar var mı → (3) H2/H3 gerektiren yüzey mi (gerekirse ONAY almadan
başlatma).

## §7 Dispatch Anti-Pattern'leri (gözlemlenen hata kalıpları — her biri RED)

| # | Anti-pattern | Belirti | Doğrusu |
|---|--------------|---------|---------|
| 1 | **Belirsiz handover** | "şu işi hallet, bakarsın" — hedef dosya/kabul kriteri yok | §6 şablonu: GÖREV + HEDEF + KABUL + YASAK + ÇIKTI (her satır dolu) |
| 2 | **İzin şişirme** | Her göreve `I` verilir; subagent kapsam dışı dosyaya dokunur | Varsayılan `R`; genişletme bilinçli karar (`R→A→I`) |
| 3 | **Sessiz L4** | Subagent kendi başına commit/deploy/secret üretir | `L` yalnız orkestratöre; H3 onayı olmadan dışa dönük eylem yok |
| 4 | **Paralel-write çakışması** | İki subagent aynı dosyayı düzenler; son yazan kazanır | **Tek yazıcı prensibi**: aynı dosya = sıralı, değilse paralel |
| 5 | **Soru fırtınası** | 15+ soru tek turda; kullanıcı boğulur | ≤8 soru/tur · maks 3 tur · P0 öncelikli sıralama |
| 6 | **Kanıtsız kapanış** | "bitti" + ölçüm yok; eksik iş görünmez | H4: satır/çalıştırma/test kanıtı yoksa onay yok |
| 7 | **Deneme sınırsızlığı** | Aynı hata 5+ kez denenir; süre şişer | **3 başarısız tur = DUR** (§4) + üst kararı |
| 8 | **Veto geçişi** | Güvenlik ihlali bildirilip iş devam ettirilir | Veto = durdurucu; düzeltme + yeniden denetim şart |
| 9 | **Kapsam kayması** | Dispatch sırasında yeni "küçük" işler eklenir | Kapsam değişikliği = yeni H1; mevcut handover'a ekleme yasak |
| 10 | **Kayıpsız handover** | Agent değişince bağlam kaybolur; aynı keşif tekrarlanır | Handover'da: kanıt listesi + şüpheli varsayım + sonraki adım |

**Denetim satırı:** Her dispatch sonrası bu tablo taranır; anti-pattern
isabeti varsa H4 kapanışı öncesi düzeltilir. Örnek uygulama tam döngü:
`examples/full-dispatch-loop.md`.

## §8 Hazır Dispatch Kalıpları (kopyala-değiştir)

### §8.1 Salt-okunur keşif (R · L0)

```text
İZİN: R · SEVİYE: L0 · KAPI: — (H4 raporu yeterli)
GÖREV: <konu> için dosya/sembol keşfi — oku/araştır, DEĞİŞTİRME.
HEDEF: <dizin/dosya yolları>
KABUL: bulgu listesi gerçek yollarla (her satırda dosya:satır).
YASAK: edit/write · git commit · .ai/ yazma.
ÇIKTI: bulgu tablosu + eksik/UNKNOWN satırı.
```

### §8.2 Uygulama (I · L2 — kaynak kod)

```text
İZİN: I · SEVİYE: L2 · KAPI: H1 (çok dosyalıysa)
GÖREV: <değişiklik> — ne DEĞİL: <kapsam dışı>.
HEDEF: <dosya yolları>
KABUL: php -l / test / ölçülebilir çıktı kanıtı + değişiklik listesi.
YASAK: git commit · .ai/ · kapsam dışı dosya · frozen ADR metni.
ÇIKTI: diff özeti + doğrulama komutu + eksik/BLOCK satırı.
```

### §8.3 Append-only kayıt (A · L1)

```text
İZİN: A · SEVİYE: L1 · KAPI: —
GÖREV: <dosya>'ya YALNIZ ekle (log/changelog satırı) — mevcut satır DEĞİŞMEZ.
HEDEF: <dosya yolu>
KABUL: append edilen satır + mevcut satır sayısı korundu kanıtı.
YASAK: mevcut satır düzenleme/silme · git commit.
ÇIKTI: eklenen satır metni + toplam satır sayısı.
```

---

*agent-orchestrator · references/workflow-dispatch.md · Updated: 2026-10-21*
