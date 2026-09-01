# Vault Loading Protocol (Zorunlu)

## Kural: Her Görevden ÖNCE Vault'u Oku

Frontend, backend, veya herhangi bir görevden ÖNCE:

### Adım 1: Vault Keşfi
```
Workspace root'a göre .ai/ dizinini KEŞFET:
1. {cwd}/.ai/CLAUDE.md → OKU (anayasa)
2. {cwd}/.ai/AGENTS.md → OKU (agent kayıt defteri)
3. {cwd}/.ai/WORKFLOW.md → OKU (iş akışı kuralları)
4. {cwd}/.ai/brain.md → OKU (mühendislik beyni)
5. {cwd}/.ai/keys.md → OKU (keyword yönlendirme)
6. {cwd}/.ai/index.md → OKU (master indeks)
7. {cwd}/.ai/ROLE.md → OKU (rol tanımları)
8. {cwd}/.ai/engine.md → OKU (motor kuralları)
9. {cwd}/.ai/MEMORY.md → OKU (bellek)
10. {cwd}/.ai/log.md → OKU (işlem logu)
```

### Adım 2: Mimari Dokümanlar
```
{cwd}/.ai/architecture/ dizinini OKU:
- l0-infrastructure.md → Altyapı katmanı
- l1-security.md → Güvenlik katmanı
- l2-routing.md → Yönlendirme katmanı
- l3-presentation.md → Sunum katmanı (Frontend)
- l4-domain.md → Domain katmanı
- l5-services.md → Servis katmanı
- database-architecture.md → Veritabanı mimarisi
- network-architecture.md → Ağ mimarisi
```

### Adım 3: UI Tasarım Kuralları (Frontend için ZORUNLU)
```
{cwd}/.ai/ui-design/ dizinini OKU:
- 00-mockup-index.md → 18 PNG mockup indeksi (İLK OKUNACAK)
- 01-component-inventory.md → Bileşen envanteri
- 02-implementation-plan.md → Uygulama planı
- 03-accessibility-gaps.md → Erişilebilirlik açıkları
- 04-vault-registration.md → Vault kayıt
- screens/ → Ekran tanımları (ASCII art + ölçümler)
- tokens/ → Design token'lar
- reference/ → Referans dosyaları
```

### Adım 4: PNG Cross-Reference (Frontend için ZORUNLU)
```
{cwd}/.ai/.png/ dizinini OKU ve md dosyalarıyla karşılaştır:
1. 00-mockup-index.md'den PNG dosya adını bul
2. .ai/.png/home-1024/ veya .ai/.png/shared-1024/ dizininden PNG'yi bul
3. PNG'yi oku (görsel analiz)
4. ASCII art ile karşılaştır
5. Ölçümleri doğrula
6. Fark varsa PNG'yi esas al
```

### Adım 5: Kararlar (ADR)
```
{cwd}/.ai/decisions/accepted/ dizinini OKU:
- Tüm ADR dosyalarını oku
- Kararları uygula
- Reddedilmiş kararları kontrol et (neden reddedildiğini bil)
```

### Adım 6: Agent Profilleri
```
{cwd}/.ai/.agents/ dizinini OKU:
- AGENTS.md → Agent kayıt defteri
- ui-designer.md → UI tasarımcı profili
- backend-architect.md → Backend mimarı profili
- Diğer tüm agent profilleri
```

### Adım 7: Son Durum
```
{cwd}/.ai/memory/sessions/ → Son session'ı OKU
{cwd}/.ai/memory/context/ → Proje durumunu OKU
```

## Yükleme Sırası (Öncelik)

```
1. Vault anayasa (.ai/CLAUDE.md)
2. Agent kayıt defteri (.ai/AGENTS.md)
3. İş akışı (.ai/WORKFLOW.md)
4. Mühendislik beyni (.ai/brain.md)
5. Keyword haritası (.ai/keys.md)
6. Mimari dokümanlar (.ai/architecture/)
7. UI tasarım kuralları (.ai/ui-design/)
8. PNG cross-reference (.ai/.png/)
9. Kararlar (.ai/decisions/)
10. Agent profilleri (.ai/.agents/)
11. Son durum (.ai/memory/)
```

## Dinamik Keşif Kuralları

- Hardcoded path KULLANMA
- Her proje için ayrı vault yapısı olabilir
- Workspace root'a göre keşif yap
- Dosya yoksa → atla, hata verme
- Dosya varsa → OKU, uygula
- Vault'a HER ZAMAN güven
- Vault kurallarını sorgulama
- Vault'taki ölçüleri aynen uygula

## Frontend Görevleri İçin Ek Kurallar

1. `00-mockup-index.md`'i İLK oku
2. İlgili ekranın `screens/` dosyasını oku
3. ASCII art'ı oku ve ölçümleri çıkar
4. PNG dosyasını oku ve karşılaştır
5. Fark varsa PNG'yi esas al
6. Emoji KULLANMA - sadece PNG kullan
7. Token'ları oku ve CSS'de kullan

## Kontrol Listesi

```
[ ] Vault keşfedildi mi?
[ ] CLAUDE.md okundu mu?
[ ] AGENTS.md okundu mu?
[ ] WORKFLOW.md okundu mu?
[ ] brain.md okundu mu?
[ ] keys.md okundu mu?
[ ] Mimari dokümanlar okundu mu?
[ ] UI tasarım kuralları okundu mu?
[ ] PNG cross-reference yapıldı mı?
[ ] ADR kararları okundu mu?
[ ] Agent profilleri okundu mu?
[ ] Son session log'u okundu mu?
[ ] Proje durumu okundu mu?
```
