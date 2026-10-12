# examples/01 — Tam Oturum Örnekleri (Girdi → Çıktı)

> 4 teknik için gerçek-görev döngüsü. Örnekler öğretim amaçlıdır; içlerindeki olgular
> örnek konuya aittir ve `rules.md` R5'e tabidir.

---

## Örnek A — Sokratik (girdi → oturum)

**Girdi (kullanıcı):**
```text
Şu an PDO prepared statement'ları üzerinde çalışıyorum ve "neden bazen
bindValue yerine bağlama parametresini sorgu içine gömüyormuş gibi davranıyor"
noktasında takıldım. Senden bir Sokrates gibi...
... şuna kadar yaptıklarım: sorguyu string birleştirme ile yazınca çalıştı,
prepare/bindValue ile yazınca sonuç farklı çıktı.
```

**Çıktı (uygun oturum — 3 tur):**

| Tur | Modelin çıktısı | Kural |
|-----|-----------------|-------|
| 1 | "Bağla değişkenini ne olarak tanımladın ve sorguda hangi yer tutucu biçimini kullandın (isimli `:x` mi, konumsal `?` mi)?" | R1: tek soru; cevap yok |
| 2 (kullanıcı: "? ile bindValue(1, $x)) | "İlk turda `bindValue`'ya verdiğin sıra ile `?`'lerin sorgudaki sıra sırası mı? Sorguyu satır satır okuyup işaretle." | ipucu 1: çerçeve daralttı, cevap hâlâ yok |
| 3 (kullanıcı: "eşleşiyor") | "Peki o farklı çıktığında beklediğin ile aldığın değeri karşılaştırdın mı — tip farkı (int vs string) olabilir mi? Nerede karşılaştırdın?" | doğru yönde olduğu söylendi (R2), hâlâ cevap yok |

**Kapanış:** kullanıcı kendi hatasını bulduğunda oturum biter; model cevabı **yazmaz**.

---

## Örnek B — Aktif Hatırlama (girdi → puan → rapor)

**Girdi:** 8 soru istendi; notlardan üretilen Soru 4'e kullanıcı cevabı:
"Foreign key, iki tablo arasında ilişki kurar ve tekrar eden veriyi engeller."

**Çıktı:**

```markdown
**5/10.** Doğru yönde ama iki kritik parçayı atladın: (1) foreign key'in asıl işlevi
bütünlük **zorlamaktır** (yabancı anahtar olmayan değer yazılamaz) — "engeller" dediğin
şey bunun sonucu; (2) kendisi tekrarı engellemez, normalizasyon (BCNF) eder. Notlarında
bu ayrım §2'de geçiyor.
```

**Oturum sonu (R4) — örnek:**

```markdown
## Oturum Raporu
- Ortalama puan: 6.1/10 (8 soru)
- Zayıf noktalar: FK vs normalizasyon ayrımı (5/10) · ON DELETE CASCADE davranışı (4/10)
- Yanlış cevaplananlar: S4 (bütünlük-zorlama) · S7 (cascade) → tekrar soruldu, 7/10
- Tekrar listesi (~3 gün sonra): 5 soru (farklı ifadeyle, uygulama soruları ağırlıklı)
- Sonraki çalışma: dağınık tekrar — günde 2 soru, 3 gün
```

---

## Örnek C — Şeytanın Avukatı (girdi → puanlı itiraz)

**Girdi:** "Yeni servis için ORM kullanmalıyız; geliştirme hızı için." (sebepler: süre, az boilerplate)

**Çıktı (özet):**

| # | İtiraz | Puan |
|---|--------|------|
| 1 | Bu projenin ADR-002'si `no ORM` diyor — "hız" gerekçesi bir ADR'yi tek başına ezmez; yeni ADR gerekir | **9/10** (kanıt: repo kararı) |
| 2 | "Geliştirme hızı" dayanaksız varsayım: ölçülmemiş (hangi görevde ne kadar kazanç?) | 7/10 |
| 3 | Mümkün önyargı: seçilmiş örnek projelerin görünürlüğü (LinkedIn/viral) | 5/10 |
| 4 | Fikri değiştirecek kanıt: aynı ekibin ORM'li dalında ölçülen teslim süresi farkı | — (aranacak kanıt) |
| 5 | Sağlam taraf: Repository katmanı yoksa tekrar eden sorgu kodu gerçek | 6/10 |

---

**Not:** Örneklerde geçen kural numaraları (R1–R5) `references/rules.md`'ye bağlanır;
hiçbiri uydurma kural değildir.
