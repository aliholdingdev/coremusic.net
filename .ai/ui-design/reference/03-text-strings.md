---
title: "UI Text Strings Reference"
type: reference
category: ui-text
updated: 2026-08-11
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# UI Text Strings Reference

**Zorunlu Baglantilar:** [[00-mockup-index]] · [[01-component-inventory]] · [[04-vault-registration]]

---

## 1. Amaç

CoreMusic arayüzünde kullanılacak tüm Türkçe metin dizesinin tek kaynağıdır. Ajanlar bu dosyadan string'leri okur, hardcoded string kullanmaz.

---

## 2. Navigation Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `nav.home` | Ana Sayfa | Home | Sidebar/nav link |
| `nav.discover` | Keşfet | Discover | Sidebar/nav link |
| `nav.albums` | Albümler | Albums | Sidebar/nav link |
| `nav.artists` | Sanatçılar | Artists | Sidebar/nav link |
| `nav.browse` | Göz At | Browse | Sidebar/nav link |
| `nav.history` | Geçmiş | History | Sidebar/nav link |
| `nav.settings` | Ayarlar | Settings | Sidebar/nav link |
| `nav.about` | Hakkımızda | About | Sidebar/nav link |
| `nav.library` | Kütüphane | Library | Tab bar |
| `nav.search` | Ara | Search | Tab bar |
| `nav.favorites` | Favoriler | Favorites | Tab bar |

---

## 3. Auth Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `auth.welcome` | Hoş Geldin | Welcome | Auth screen başlık |
| `auth.create_account` | Hesap Oluştur | Create Account | Register başlık |
| `auth.lets_know_you` | Seni Tanıyalım | Let's Get to Know You | Gender select başlık |
| `auth.continue` | Devam Et | Continue | Register buton |
| `auth.login` | Giriş Yap | Login | Login buton |
| `auth.register` | Kayıt Ol | Register | Register link |
| `auth.forgot_password` | Şifremi Unuttum | Forgot Password | Link |
| `auth.or` | veya | or | Separator |
| `auth.remember_me` | Beni Hatırla | Remember Me | Checkbox |
| `auth.no_account` | Hesabın yok mu? | Don't have an account? | Register yönlendirme |
| `auth.has_account` | Zaten hesabın var mı? | Already have an account? | Login yönlendirme |
| `auth.logout` | Çıkış Yap | Logout | Button |
| `auth.session_expired` | Oturumunuz sona erdi | Your session has expired | Toast |

---

## 4. Gender Strings

| Key | Türkçe | İngilizce | Değer | Kullanım |
|-----|--------|-----------|-------|----------|
| `gender.female` | Kız | Female | `female` | Gender buton |
| `gender.male` | Erkek | Male | `male` | Gender buton |
| `gender.prefer_not` | Cinsiyetimi belirtmek istemiyorum | Prefer not to say | `neutral` | Gender buton |
| `gender.select_title` | Cinsiyetini seç | Select your gender | — | Modal başlık |
| `gender.select_subtitle` | Tema tercihini belirlememize yardımcı ol | Help us set your theme preference | — | Modal alt başlık |

---

## 5. Button Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `btn.play_now` | Hemen Çal | Play Now | Ana buton |
| `btn.play_shuffle` | Karışık Çal | Shuffle Play | Karışık çalma |
| `btn.connect` | Bağlan | Connect | WiFi/BT bağlama |
| `btn.cancel` | İptal | Cancel | İptal butonu |
| `btn.start` | Başla | Start | Başla butonu |
| `btn.browse` | Göz At | Browse | Göz at butonu |
| `btn.save` | Kaydet | Save | Kaydet butonu |
| `btn.delete` | Sil | Delete | Sil butonu |
| `btn.edit` | Düzenle | Edit | Düzenle butonu |
| `btn.close` | Kapat | Close | Kapat butonu |
| `btn.back` | Geri | Back | Geri butonu |
| `btn.next` | İleri | Next | İleri butonu |
| `btn.skip` | Geç | Skip | Geç butonu |
| `btn.retry` | Tekrar Dene | Retry | Hata sonrası |
| `btn.apply` | Uygula | Apply | Ayarlar |
| `btn.confirm` | Onayla | Confirm | Onay butonu |
| `btn.share` | Paylaş | Share | Paylaş butonu |
| `btn.download` | İndir | Download | İndirme |

---

## 6. Label Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `label.song_name` | Şarkı Adı | Song Name | Form label |
| `label.album_name` | Albüm Adı | Album Name | Form label |
| `label.artist` | Sanatçı | Artist | Form label |
| `label.duration` | Süre | Duration | Tablo başlık |
| `label.favorite_star` | Favori Yıldızı | Favorite Star | Yıldız etiketi |
| `label.genre` | Tür | Genre | Form label |
| `label.year` | Yıl | Year | Form label |
| `label.bitrate` | Bit Hızı | Bitrate | Bilgi etiketi |
| `label.sample_rate` | Örnekleme Hızı | Sample Rate | Bilgi etiketi |
| `label.channels` | Kanallar | Channels | Bilgi etiketi |
| `label.format` | Format | Format | Bilgi etiketi |
| `label.file_size` | Dosya Boyutu | File Size | Bilgi etiketi |
| `label.email` | E-posta | Email | Form label |
| `label.password` | Şifre | Password | Form label |
| `label.username` | Kullanıcı Adı | Username | Form label |
| `label.name` | Ad | Name | Form label |

---

## 7. Status Strings

| Key | Türkçe | İngilizce | Değer | Kullanım |
|-----|--------|-----------|-------|----------|
| `status.connected` | Bağlı | Connected | `connected` | Durum göstergesi |
| `status.connecting` | Bağlanıyor | Connecting | `connecting` | Durum göstergesi |
| `status.strong` | Güçlü | Strong | `strong` | Sinyal gücü |
| `status.medium` | Orta | Medium | `medium` | Sinyal gücü |
| `status.weak` | Zayıf | Weak | `weak` | Sinyal gücü |
| `status.disconnected` | Bağlantı Yok | Disconnected | `disconnected` | Durum göstergesi |
| `status.offline` | Çevrimdışı | Offline | `offline` | Durum |
| `status.online` | Çevrimiçi | Online | `online` | Durum |
| `status.playing` | Çalınıyor | Playing | `playing` | Player durumu |
| `status.paused` | Duraklatıldı | Paused | `paused` | Player durumu |
| `status.stopped` | Durduruldu | Stopped | `stopped` | Player durumu |
| `status.loading` | Yükleniyor | Loading | `loading` | Genel |

---

## 8. Platform Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `platform.system_disk` | System Disk | System Disk | Disk browser |
| `platform.nas_drive` | NAS Drive | NAS Drive | Disk browser |
| `platform.hdd_drive` | HDD Drive | HDD Drive | Disk browser |
| `platform.ssd_drive` | SSD Drive | SSD Drive | Disk browser |
| `platform.usb_drive` | USB Drive | USB Drive | Disk browser |
| `platform.sd_card` | SD Kart | SD Card | Disk browser |
| `platform.external` | Harici Disk | External Disk | Disk browser |

---

## 9. Player Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `player.now_playing` | Şu An Çalınıyor | Now Playing | Başlık |
| `player.upcoming` | Gelecek Şarkılar | Upcoming | Liste başlık |
| `player.previous` | Önceki Şarkı | Previous Song | Tooltip |
| `player.next` | Sonraki Şarkı | Next Song | Tooltip |
| `player.shuffle` | Karıştır | Shuffle | Toggle |
| `player.repeat` | Tekrarla | Repeat | Toggle |
| `player.repeat_one` | Tekrarla (1) | Repeat One | Toggle |
| `player.repeat_all` | Tekrarla (Tümü) | Repeat All | Toggle |
| `player.volume` | Ses Seviyesi | Volume | Tooltip |
| `player.mute` | Sessiz | Mute | Tooltip |
| `player.fullscreen` | Tam Ekran | Fullscreen | Tooltip |
| `player.mini_player` | Küçük Oynatıcı | Mini Player | Tooltip |
| `player.add_to_playlist` | Çalma Listesine Ekle | Add to Playlist | Menü |
| `player.add_to_queue` | Kuyruğa Ekle | Add to Queue | Menü |
| `player.remove_from_queue` | Kuyruktan Çıkar | Remove from Queue | Menü |

---

## 10. Search Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `search.placeholder` | Şarkı, albüm veya sanatçı ara | Search songs, albums or artists | Input placeholder |
| `search.no_results` | Sonuç bulunamadı | No results found | Empty state |
| `search.recent` | Son Aramalar | Recent Searches | Liste başlık |
| `search.clear` | Aramayı Temizle | Clear Search | Buton |
| `search.filter` | Filtrele | Filter | Buton |

---

## 11. Error Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `error.generic` | Bir hata oluştu | An error occurred | Genel hata |
| `error.network` | Ağ hatası | Network error | Ağ hatası |
| `error.auth_failed` | Kimlik doğrulama başarısız | Authentication failed | Login hatası |
| `error.invalid_email` | Geçersiz e-posta adresi | Invalid email address | Doğrulama |
| `error.password_short` | Şifre en az 8 karakter olmalı | Password must be at least 8 characters | Doğrulama |
| `error.password_mismatch` | Şifreler eşleşmiyor | Passwords don't match | Doğrulama |
| `error.session_expired` | Oturumunuz sona erdi, lütfen tekrar giriş yapın | Your session expired, please log in again | Session |
| `error.permission_denied` | Yetkiniz yok | Permission denied | Yetki |
| `error.not_found` | Bulunamadı | Not found | 404 |
| `error.server` | Sunucu hatası | Server error | 500 |

---

## 12. Success Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `success.login` | Giriş başarılı | Login successful | Toast |
| `success.register` | Kayıt başarılı | Registration successful | Toast |
| `success.saved` | Kaydedildi | Saved | Toast |
| `success.deleted` | Silindi | Deleted | Toast |
| `success.connected` | Bağlandı | Connected | Toast |
| `success.disconnected` | Bağlantı kesildi | Disconnected | Toast |

---

## 13. Confirmation Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `confirm.delete` | Silmek istediğinizden emin misiniz? | Are you sure you want to delete? | Modal |
| `confirm.logout` | Çıkış yapmak istediğinizden emin misiniz? | Are you sure you want to logout? | Modal |
| `confirm.overwrite` | Bu dosyanın üzerine yazılacak. Devam etmek istiyor musunuz? | This file will be overwritten. Do you want to continue? | Modal |

---

## 14. File Manager Strings

| Key | Türkçe | İngilizce | Kullanım |
|-----|--------|-----------|----------|
| `fm.new_folder` | Yeni Klasör | New Folder | Buton |
| `fm.upload` | Yükle | Upload | Buton |
| `fm.rename` | Yeniden Adlandır | Rename | Menü |
| `fm.copy` | Kopyala | Copy | Menü |
| `fm.paste` | Yapıştır | Paste | Menü |
| `fm.move` | Taşı | Move | Menü |
| `fm.select_all` | Tümünü Seç | Select All | Menü |
| `fm.items_selected` | öğe seçildi | items selected | Durum |

---

## 15. Quick Reference

| Kullanım Alanı | Dosya |
|----------------|-------|
| Sidebar | `nav.*` strings |
| Auth screens | `auth.*`, `gender.*` strings |
| Player | `player.*` strings |
| File manager | `fm.*` strings |
| Search | `search.*` strings |
| Error handling | `error.*`, `success.*` strings |
| Forms | `label.*`, `btn.*` strings |

---

## 16. Cross References

| Kaynak | Hedef |
|--------|-------|
| Bu dosya | [[00-mockup-index]] — PNG referansları |
| Bu dosya | [[01-component-inventory]] — Bileşen metinleri |
| Bu dosya | [[04-vault-registration]] — Vault kayıt planı |
| Bu dosya | [[ADR-044-dynamic-user-theme-engine]] — Tema string'leri |

---

## 17. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Total Strings | 100+ |
| Categories | 10 |
| Languages | TR (primary), EN (secondary) |
| Status | Red Team · Human Mode · Truth Mode verified |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-11
**Mode:** Red Team · Human Mode · Truth Mode
