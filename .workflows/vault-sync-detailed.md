# Vault Sync Detailed Workflow

## Purpose
Vault senkronizasyonu ve bütünlük kontrolü

## Workflow Steps

### 1. Senkronizasyon Öncesi Kontrol
- Vault durumunu kontrol et
- Değişiklikleri listele
- Çakışmaları tespit et

### 2. Senkronizasyon
- Vault dosyalarını güncelle
- Cross-reference'ları güncelle
- Index'leri güncelle

### 3. Bütünlük Kontrolü
- Link'leri kontrol et
- Format kontrolü yap
- MSA limit kontrolü yap

### 4. Doğrulama
- Wiki-link'leri doğrula
- ADR referanslarını doğrula
- Cross-reference'ları doğrula

### 5. Raporlama
- Değişiklik raporu oluştur
- Sorunları listele
- Önerileri sun

## Sync Rules
- Append-only log (log.md)
- In-place modification
- No file rename without approval
- MSA limit = 15 files/task

## Auto-Triggers
- Yeni ADR oluşturulduğunda
- Kod refactor edildiğinde
- Yeni servis eklendiğinde
- DB schema değiştiğinde
- Güvenlik değişikliği olduğunda

## Related Files
- `.ai/WORKFLOW.md`
- `.ai/MEMORY.md`
- `.ai/log.md`
- `.claude/commands/vault-sync.md`
- `.claude/commands/vault-check.md`

## Activation
- "vault-sync", "senkronize", "vault güncelle" kelimeleri
- Oturum sonu
- Değişiklik sonrası
