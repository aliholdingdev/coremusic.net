---
node_id: K009-security/encryption/ornekler
node_type: module
status: verify-required
parent_id: K009-security/encryption
children: []
depends_on:
  - K009-security/encryption
evidence:
  - "OAuthManager.php 188-235 · Password.php 43/59 (grep) · index.php 132 (grep) birebir alıntı"
validation:
  - "2026-10-12 — her alıntı dosya + satır aralığıyla doğrulandı"
open_questions:
  - "encryptionKey ataması (constructor) okunmadı → UNKNOWN"
group: kod
---

# encryption-ornekler.md — Gerçek Kod Örnekleri

## §1 Şifreleme — OAuthManager.php · 185-205

~~~php
/**
 * Access token'ı AES-256-GCM ile şifrele.
 */
private function encrypt(string $plaintext): string
{
    $iv = random_bytes(12);
    $tag = '';

    $ciphertext = openssl_encrypt(
        $plaintext,
        'aes-256-gcm',
        $this->encryptionKey,
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        '',
        16
    );

    return base64_encode($iv . $tag . $ciphertext);
}
~~~

**Yorum:**
- satır 190: IV her çağrıda yeniden üretilir → tekrar (nonce reuse) yok.
- satır 199-201: tag referansla doldurulur; tag yoksa GCM doğrulaması
  yapılamaz (PHP el kitabı §10 uyarısı).
- satır 204: tag'i de taşıyan sarma — çözmede ayrı ayrı ayrıştırılır.

## §2 Çözme — OAuthManager.php · 207-235

~~~php
public function decrypt(string $encrypted): string
{
    $decoded = base64_decode($encrypted, true);
    if ($decoded === false || strlen($decoded) < 29) {
        throw new \RuntimeException('Invalid encrypted token');
    }

    $iv = substr($decoded, 0, 12);
    $tag = substr($decoded, 12, 16);
    $ciphertext = substr($decoded, 28);

    $plaintext = openssl_decrypt(
        $ciphertext,
        'aes-256-gcm',
        $this->encryptionKey,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($plaintext === false) {
        throw new \RuntimeException('Decryption failed');
    }

    return $plaintext;
}
~~~

**Yorum:**
- satır 213: 29 = 12 (IV) + 16 (tag) + 1 (en az 1 byte) — minimum uzunluk
  denetimi, bozuk veride erken reddi sağlar.
- satır 230: başarısız çözme EXCEPTION → padding/tag doğrulaması başarısız
  olan veri asla işlenmez (fail-closed).

## §3 Parola Pepper'ı — Password.php · 43

~~~php
$peppered = hash_hmac('sha256', $this->raw, $pepper);
~~~

**Yorum:** HMAC-SHA256, argon2id ÖNCESİ ek giz katmanıdır (satır 59 verify
yolunda aynı işlem tekrarlanır). Pepper, hash'leme DEĞİL anahtarlı
özetlemedir → pepper dosyası sızarsa kaba kuvvet yine argon2id ile
ağır kalır (maliyet $argon2id$ v=19 m=65536 t=4 p=2, satır 24).

## §4 Günlük Bypass Anahtarı — auth.coremusic.net/index.php · 129-137

~~~php
if ($requestUri === '' || $requestUri === '/') {
    // Auth bypass aktifse -> direkt home'a redirect et
    if ($bypassActive) {
        $bypassKey = hash_hmac('sha256', 'bypass_' . date('Y-m-d'), defined('APP_PEPPER') ? APP_PEPPER : 'coremusic-bypass');
        $redirectUrl = (defined('MUSIC_URL') ? MUSIC_URL : 'http://home.coremusic.net:81')
            . '/auth/callback?auth_key=' . urlencode($bypassKey);
        header('Location: ' . $redirectUrl, true, 302);
        exit;
    }
~~~

**Yorum (NEGATİF ÖRNEK):**
- satır 60: $bypassActive = !isProductionEnv && FORCE_AUTH_BYPASS → yalnız
  prod olmayan ortam.
- satır 132: defined(...) ? APP_PEPPER : 'coremusic-bypass' → yedek sabit
  anahtar KODDA. Güvenli sürüm: yedek yok; env yoksa istek reddedilir.
- Günlük anahtar ('bypass_' + tarih) deterministiktir → aynı gün için
  herkes üretebilir. Bu, bilinçli dev kısayolu olarak izlenir (R-21).

## §5 State Hash'i — OAuthManager.php · 170-183

~~~php
public function validateState(string $state, string $provider, int $userId): ?array
{
    $hash = hash('sha256', $state);

    $row = $this->repository->findValidState($hash, $provider, $userId);
    if ($row === null) {
        return null;
    }

    // State'i sil (tek kullanımlık)
    $this->repository->deleteState($hash);

    return ['code_verifier' => $row['code_verifier'] ?? null];
}
~~~

**Yorum:** state, DB'ye DÜZ yazılmaz (sha256 ile), tek kullanımlıktır
(satır 179-180). CSRF + replay koruması bir arada.

## §6 Başlık Setinden Eksik — SecurityHeadersMiddleware · 26-41

~~~php
$headers = [
    'X-Content-Type-Options'         => 'nosniff',
    'X-Frame-Options'                => 'DENY',
    'X-XSS-Protection'               => '0',
    'Referrer-Policy'                => 'strict-origin-when-cross-origin',
    'Content-Security-Policy'        => $csp,
    'Require-Trusted-Types-For'      => "'script'",
    'Cross-Origin-Resource-Policy'   => 'cross-origin',
    'Permissions-Policy'             => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
    'X-Permitted-Cross-Domain-Policies' => 'none',
];
~~~

**Yorum (eksik kanıtı):** 9 başlık arasında Strict-Transport-Security YOKTUR
— OWASP TLS Cheat Sheet §4 iddiası karşılanmıyor (doğrulama kontrol 20 FAIL).

## §7 GÜVENLİSİZ vs ÜRETİMDEKİ

~~~text
GÜVENLİSİZ                                  ÜRETİMDEKİ
──────────────────────────────────────────────────────────────
ECB modu                                →   GCM (OAuthManager·195)
sabit/tekrar eden IV                    →   random_bytes(12) (190)
tag kontrolsüz çözme                    →   false → exception (230)
parolayı şifrelemek                     →   argon2id hash (Password)
yedek sabit pepper                      →   ⚠️ HÂLÂ VAR (index·132)
HSTS başlığı                            →   ⚠️ HÂLÂ YOK (26-41)
~~~

## §8 Alıntı Sınırları

1. 4 gerçek dosyadan 6 kod bloğu.
2. Özgün koda yorum EKLENMEZ.
3. Uydurma kod: YOK.
4. Negatif örnek (§4) bilinçli olarak işaretlenmiştir — üretimde dev
   kısayolu olduğu, prod dışı koşuluyla birlikte yazılır.
