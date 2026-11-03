<?php

declare(strict_types=1);

namespace Media;

/**
 * CatalogTransformer — JSON/veri kaynaklarından MySQL satırlarına dönüşüm.
 *
 * meta.json, artist.json, album.json, koleksiyon.json dosyalarından
 * gelen ham veriyi media_catalog şemasına uygun satır dizilerine dönüştürür.
 * Eksik zorunlu alan varsa null döner (çağıran atlar).
 */
final class CatalogTransformer
{
    /**
     * Artist verisini `artist` tablosu satırına dönüştür.
     *
     * @param array<string, mixed> $v Ham artist verisi
     * @return array<string, mixed>|null Zorunlu alan eksikse null
     */
    public static function transformArtist(array $v): ?array
    {
        $k = is_array($v['kimlik'] ?? null) ? $v['kimlik'] : [];
        $temel = [
            'id' => TypeConverter::toString($v['id'] ?? null),
            'slug' => TypeConverter::toString($v['slug'] ?? null),
            'sanatci' => TypeConverter::toString($k['sanatci'] ?? null),
            'tip' => TypeConverter::toString($k['tip'] ?? null),
            'durum' => TypeConverter::toString($v['durum'] ?? null),
            'eklenme' => TypeConverter::toDateTime($v['eklenme'] ?? null),
        ];
        if (in_array(null, $temel, true)) {
            return null;
        }

        return $temel + ['varsayilan_tag' => TypeConverter::toJson($v['varsayilan_tag'] ?? [])];
    }

    /**
     * Album verisini `album` tablosu satırına dönüştür.
     *
     * @param array<string, mixed> $v Ham album verisi
     * @return array<string, mixed>|null Zorunlu alan eksikse null
     */
    public static function transformAlbum(array $v): ?array
    {
        $kimlik = is_array($v['kimlik'] ?? null) ? $v['kimlik'] : [];
        $ust = is_array($v['ust'] ?? null) ? $v['ust'] : [];
        $tarih = is_array($v['tarih'] ?? null) ? $v['tarih'] : [];
        $bicim = is_array($v['bicim'] ?? null) ? $v['bicim'] : [];
        $kapsam = is_array($v['kapsam'] ?? null) ? $v['kapsam'] : [];
        $gorsel = is_array($v['gorsel'] ?? null) ? $v['gorsel'] : [];
        $kredi = is_array($v['kredi'] ?? null) ? $v['kredi'] : [];

        $temel = [
            'id' => TypeConverter::toString($v['id'] ?? null),
            'slug' => TypeConverter::toString($v['slug'] ?? null),
            'album' => TypeConverter::toString($kimlik['album'] ?? null),
            'album_tip' => TypeConverter::toString($kimlik['album_tip'] ?? null),
            'fiziksel' => TypeConverter::toString($bicim['fiziksel'] ?? null),
            'durum' => TypeConverter::toString($v['durum'] ?? null),
            'eklenme' => TypeConverter::toDateTime($v['eklenme'] ?? null),
        ];
        if (in_array(null, $temel, true)) {
            return null;
        }

        return $temel + [
            'sanatci_id' => TypeConverter::toString($ust['sanatci_id'] ?? null),
            'yil' => TypeConverter::toInt($tarih['yil'] ?? null),
            'ay' => TypeConverter::toInt($tarih['ay'] ?? null),
            'ulke' => TypeConverter::toString($tarih['ulke'] ?? null),
            'sarki_sayisi' => TypeConverter::toInt($kapsam['sarki_sayisi'] ?? null),
            'sure_sn' => TypeConverter::toInt($kapsam['sure_sn'] ?? null),
            'cover' => TypeConverter::toString($gorsel['cover'] ?? null),
            'etiket_sirketi' => TypeConverter::toString($kredi['etiket_sirketi'] ?? null),
            'katalog_no' => TypeConverter::toString($kredi['katalog_no'] ?? null),
            'tag' => TypeConverter::toJson($v['tag'] ?? []),
            'guncelleme' => TypeConverter::toDateTime($v['guncelleme'] ?? null),
        ];
    }

    /**
     * Koleksiyon verisini `koleksiyon` tablosu satırına dönüştür.
     *
     * @param array<string, mixed> $v Ham koleksiyon verisi
     * @return array<string, mixed>|null Zorunlu alan eksikse null
     */
    public static function transformKoleksiyon(array $v): ?array
    {
        $temel = [
            'id' => TypeConverter::toString($v['id'] ?? null),
            'slug' => TypeConverter::toString($v['slug'] ?? null),
            'koleksiyon' => TypeConverter::toString($v['koleksiyon'] ?? null),
            'kaynak_tur' => TypeConverter::toString($v['kaynak_tur'] ?? null),
            'durum' => TypeConverter::toString($v['durum'] ?? null),
            'eklenme' => TypeConverter::toDateTime($v['eklenme'] ?? null),
        ];
        if (in_array(null, $temel, true)) {
            return null;
        }

        return $temel + [
            'sarki_sayisi' => TypeConverter::toInt($v['sarki_sayisi'] ?? null),
            'tag' => TypeConverter::toJson($v['tag'] ?? []),
        ];
    }

    /**
     * Meta verisini `asset` tablosu satırına dönüştür.
     *
     * @param array<string, mixed> $v Ham meta verisi
     * @param string $jsonYolu Repo-göreli JSON yolu (dosya_yolu hesaplaması için)
     * @return array<string, mixed>|null Zorunlu alan eksikse null
     */
    public static function transformAsset(array $v, string $jsonYolu): ?array
    {
        $kimlik = is_array($v['kimlik'] ?? null) ? $v['kimlik'] : [];
        $ust = is_array($v['ust'] ?? null) ? $v['ust'] : [];
        $teknik = is_array($v['teknik'] ?? null) ? $v['teknik'] : [];
        $gorsel = is_array($v['gorsel'] ?? null) ? $v['gorsel'] : [];
        $muzikal = is_array($v['muzikal'] ?? null) ? $v['muzikal'] : [];

        $dosya = TypeConverter::toString($teknik['dosya'] ?? null);
        $klasor = str_replace('\\', '/', dirname($jsonYolu));
        $temel = [
            'id' => TypeConverter::toString($v['id'] ?? null),
            'tip' => TypeConverter::toString($v['tip'] ?? null),
            'slug' => TypeConverter::toString($kimlik['slug'] ?? null),
            'baslik' => TypeConverter::toString($kimlik['baslik'] ?? null),
            'sira' => TypeConverter::toInt($kimlik['sira'] ?? null),
            'dosya_yolu' => $dosya === null ? null : $klasor . '/' . $dosya,
            'dosya' => $dosya,
            'format' => TypeConverter::toString($teknik['format'] ?? null),
            'boyut_bayt' => TypeConverter::toInt($teknik['boyut_bayt'] ?? null),
            'sha256' => TypeConverter::toString($teknik['sha256'] ?? null),
            'durum' => TypeConverter::toString($v['durum'] ?? null),
            'eklenme' => TypeConverter::toDateTime($v['eklenme'] ?? null),
        ];
        if (in_array(null, $temel, true)) {
            return null;
        }

        return $temel + [
            'disk' => TypeConverter::toInt($kimlik['disk'] ?? null),
            'album_id' => TypeConverter::toString($ust['album_id'] ?? null),
            'sanatci_id' => TypeConverter::toString($ust['sanatci_id'] ?? null),
            'koleksiyon_id' => null,
            'guncelleme' => TypeConverter::toDateTime($v['guncelleme'] ?? null),
            'ekleyen' => TypeConverter::toString($v['ekleyen'] ?? null),
            'kapsayici' => TypeConverter::toString($teknik['kapsayici'] ?? null),
            'sure_sn' => TypeConverter::toInt($teknik['sure_sn'] ?? null),
            'bitrate' => TypeConverter::toInt($teknik['bitrate'] ?? null),
            'kanal' => TypeConverter::toInt($teknik['kanal'] ?? null),
            'orneklem' => TypeConverter::toInt($teknik['orneklem'] ?? null),
            'derinlik' => TypeConverter::toInt($teknik['derinlik'] ?? null),
            'lufs' => TypeConverter::toFloat($teknik['lufs'] ?? null),
            'peak_db' => TypeConverter::toFloat($teknik['peak_db'] ?? null),
            'cozunurluk' => TypeConverter::toString($teknik['cozunurluk'] ?? null),
            'fps' => TypeConverter::toFloat($teknik['fps'] ?? null),
            'video_kodec' => TypeConverter::toString($teknik['video_kodec'] ?? null),
            'ses_kodec' => TypeConverter::toString($teknik['ses_kodec'] ?? null),
            'bitrate_kbps' => TypeConverter::toInt($teknik['bitrate_kbps'] ?? null),
            'bpm' => TypeConverter::toInt($muzikal['bpm'] ?? null),
            'ton' => TypeConverter::toString($muzikal['ton'] ?? null),
            'enerji' => TypeConverter::toInt($muzikal['enerji'] ?? null),
            'poster' => TypeConverter::toString($gorsel['poster'] ?? null),
            'poster_zaman' => TypeConverter::toFloat($gorsel['poster_zaman'] ?? null),
        ];
    }
}