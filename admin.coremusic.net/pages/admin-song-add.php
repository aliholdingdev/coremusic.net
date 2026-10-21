<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Şarkı Ekleme Portalı (Light Theme)
 *
 * Yer: admin.coremusic.net/pages/admin-song-add.php
 * Tasarım: Açık Tema (Light Theme) Glassmorphism
 */

$pageTitle = 'Şarkı Ekle';
$activeNav = 'admin-song-add';

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$csrf      = (string)($_SESSION['csrf_token'] ?? '');

ob_start();
?>
<div class="adm-song-add-page" data-mode="light">
    <div class="adm-card adm-card--light adm-song-add-card">
        <div class="adm-card__header">
            <div>
                <h2 class="adm-card__title">Yeni Müzik / Parça Kataloğu</h2>
                <p class="adm-card__subtitle">Hi-Res ses dosyasını ve meta verileri sisteme yükleyiniz</p>
            </div>
            <a href="/admin-songs" class="adm-btn adm-btn--sm adm-btn--secondary" style="text-decoration:none;">
                &larr; Şarkı Listesine Dön
            </a>
        </div>

        <form class="adm-song-form" id="adm-song-add-form" method="post" action="/admin-song-add" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">

            <div class="adm-song-form__grid">
                <!-- Sol Kolon: Meta Veriler -->
                <div class="adm-song-form__col">
                    <div class="adm-form__group">
                        <label class="adm-form__label" for="song-title">Şarkı Başlığı *</label>
                        <input type="text" id="song-title" name="title" class="adm-form__input" placeholder="Örn: Baksana Talihe" required>
                    </div>

                    <div class="adm-form__row-2col">
                        <div class="adm-form__group">
                            <label class="adm-form__label" for="song-artist">Sanatçı *</label>
                            <input type="text" id="song-artist" name="artist" class="adm-form__input" placeholder="Örn: Göksel" required>
                        </div>
                        <div class="adm-form__group">
                            <label class="adm-form__label" for="song-album">Albüm</label>
                            <input type="text" id="song-album" name="album" class="adm-form__input" placeholder="Örn: Baksana Talihe (Single)">
                        </div>
                    </div>

                    <div class="adm-form__row-2col">
                        <div class="adm-form__group">
                            <label class="adm-form__label" for="song-genre">Müzik Türü / Kategori</label>
                            <select id="song-genre" name="genre" class="adm-form__select">
                                <option value="pop">Pop</option>
                                <option value="rock">Rock / Alternatif</option>
                                <option value="jazz">Jazz / Blues</option>
                                <option value="classical">Klasik / Akustik</option>
                                <option value="electronic">Elektronik / Ambient</option>
                                <option value="audiophile">Audiophile Master Recording</option>
                            </select>
                        </div>
                        <div class="adm-form__group">
                            <label class="adm-form__label" for="song-year">Çıkış Yılı</label>
                            <input type="number" id="song-year" name="release_year" class="adm-form__input" value="<?= date('Y') ?>" min="1900" max="2099">
                        </div>
                    </div>

                    <div class="adm-form__group">
                        <label class="adm-form__label" for="song-audio-profile">DSP Ses Profili</label>
                        <select id="song-audio-profile" name="audio_profile" class="adm-form__select">
                            <option value="flac-24-96">FLAC 24-bit / 96 kHz (Studio Master)</option>
                            <option value="flac-32-192">FLAC 32-bit Float / 192 kHz (Direct Stream)</option>
                            <option value="wav-24-88">WAV 24-bit / 88.2 kHz</option>
                            <option value="mp3-320">MP3 320 kbps CBR (Mobil Uyumlu)</option>
                        </select>
                    </div>

                    <div class="adm-form__group">
                        <label class="adm-form__label" for="song-lyrics">Şarkı Sözleri (Opsiyonel)</label>
                        <textarea id="song-lyrics" name="lyrics" class="adm-form__textarea" rows="4" placeholder="Şarkı sözlerini buraya ekleyebilirsiniz..."></textarea>
                    </div>
                </div>

                <!-- Sağ Kolon: Medya & Dosya Yükleme -->
                <div class="adm-song-form__col">
                    <!-- Ses Dosyası Yükleme Dropzone -->
                    <div class="adm-form__group">
                        <label class="adm-form__label">Ses Dosyası (Master Audio) *</label>
                        <div class="adm-dropzone adm-dropzone--light">
                            <div class="adm-dropzone__icon">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                            </div>
                            <strong class="adm-dropzone__title">Ses dosyasını buraya sürükleyin</strong>
                            <p class="adm-dropzone__desc">FLAC, WAV, ALAC veya MP3 (Max 250MB)</p>
                            <input type="file" name="audio_file" accept=".flac,.wav,.mp3,.m4a" class="adm-dropzone__file-input" required>
                        </div>
                    </div>

                    <!-- Kapak Görseli Seçimi -->
                    <div class="adm-form__group">
                        <label class="adm-form__label">Albüm Kapağı (1:1 Kare)</label>
                        <div class="adm-cover-upload adm-cover-upload--light">
                            <div class="adm-cover-upload__preview">
                                <img src="<?= $h($assetsUrl) ?>/Image/res-pink/default-album.png" alt="Önizleme" id="cover-preview-img" width="90" height="90">
                            </div>
                            <div class="adm-cover-upload__controls">
                                <input type="file" name="cover_file" id="cover-file-input" accept="image/png,image/jpeg,image/webp">
                                <small>Önerilen: 1400x1400px JPG/PNG (Max 5MB)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Yayın Seçenekleri -->
                    <div class="adm-form__group adm-publish-options">
                        <label class="adm-form__checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" class="adm-form__checkbox">
                            <span>Öne Çıkan Parçalar listesine ekle</span>
                        </label>
                        <label class="adm-form__checkbox-label">
                            <input type="checkbox" name="is_public" value="1" checked class="adm-form__checkbox">
                            <span>Kaydedildikten hemen sonra yayına al</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="adm-song-form__footer">
                <button type="button" class="adm-btn adm-btn--secondary" onclick="window.history.back()">Vazgeç</button>
                <div class="adm-song-form__footer-right">
                    <button type="submit" name="status" value="draft" class="adm-btn adm-btn--secondary">Taslak Kaydet</button>
                    <button type="submit" name="status" value="published" class="adm-btn adm-btn--primary">Parçayı Yayına Al</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
