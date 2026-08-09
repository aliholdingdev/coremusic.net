# FINTECH & PAYMENT PATTERNS — TAM REHBER
# Prompt Maker v7.2.0 | 2026-06-11 | Kiro IDE Native
# PCI DSS v4.0, iyzico, Stripe, fraud detection, idempotency

---

## PCI DSS v4.0 UYUM REHBERİ

### 12 Gereksinim Özeti

```
Requirement 1:  Network güvenliği (firewall, segmentation)
Requirement 2:  Default credential değiştirme
Requirement 3:  Kart verisi koruma (encryption at rest)
Requirement 4:  Kart verisi iletim güvenliği (TLS 1.2+)
Requirement 5:  Malware koruması
Requirement 6:  Güvenli yazılım geliştirme ← EN KRİTİK
Requirement 7:  Erişim kontrolü (least privilege)
Requirement 8:  Kimlik doğrulama (MFA zorunlu)
Requirement 9:  Fiziksel güvenlik
Requirement 10: Loglama ve monitoring
Requirement 11: Güvenlik testi (pentest, vulnerability scan)
Requirement 12: Güvenlik politikası
```

### Requirement 6 — Güvenli Yazılım Geliştirme

```
6.2: Güvenli yazılım geliştirme eğitimi
6.3: Güvenlik açıkları yönetimi
6.4: Web uygulaması güvenliği (WAF veya code review)
6.5: Tüm yazılım değişiklikleri güvenlik testi

Zorunlu kontroller:
  ✅ Input validation (her input)
  ✅ Output encoding (her output)
  ✅ Authentication + authorization
  ✅ Cryptographic key management
  ✅ Error handling (stack trace kullanıcıya gitmiyor)
  ✅ Logging (tüm erişimler)
  ✅ Secure communication (TLS 1.2+)
  ✅ Dependency vulnerability scanning
```

---

## İYZİCO ENTEGRASYONU (PHP)

### iyzico API v3 — Güvenli Ödeme

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Infrastructure\Payment\Iyzico;

use Iyzipay\Options;
use Iyzipay\Model\Payment;
use Iyzipay\Request\CreatePaymentRequest;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Address;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;

final class IyzicoPaymentService
{
    private readonly Options $options;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $secretKey,
        private readonly string $baseUrl = 'https://api.iyzipay.com',
    ) {
        // API key'leri validate et
        if (empty($this->apiKey) || empty($this->secretKey)) {
            throw new \InvalidArgumentException('iyzico credentials required');
        }

        $this->options = new Options();
        $this->options->setApiKey($this->apiKey);
        $this->options->setSecretKey($this->secretKey);
        $this->options->setBaseUrl($this->baseUrl);
    }

    /**
     * Ödeme al
     *
     * @throws PaymentException
     */
    public function charge(ChargeDTO $dto): PaymentResult
    {
        $request = new CreatePaymentRequest();
        $request->setLocale('tr');
        $request->setConversationId($dto->conversationId); // Idempotency key
        $request->setPrice((string) $dto->amount);
        $request->setPaidPrice((string) $dto->amount);
        $request->setCurrency('TRY');
        $request->setInstallment('1');
        $request->setPaymentChannel('WEB');
        $request->setPaymentGroup('PRODUCT');

        // Kart bilgileri (PCI DSS: kart numarası loglanmaz!)
        $paymentCard = new PaymentCard();
        $paymentCard->setCardHolderName($dto->cardHolderName);
        $paymentCard->setCardNumber($dto->cardNumber);
        $paymentCard->setExpireMonth($dto->expireMonth);
        $paymentCard->setExpireYear($dto->expireYear);
        $paymentCard->setCvc($dto->cvc);
        $paymentCard->setRegisterCard('0'); // Kartı kaydetme
        $request->setPaymentCard($paymentCard);

        // Alıcı bilgileri
        $buyer = new Buyer();
        $buyer->setId((string) $dto->userId);
        $buyer->setName($dto->firstName);
        $buyer->setSurname($dto->lastName);
        $buyer->setEmail($dto->email);
        $buyer->setIdentityNumber('11111111111'); // TCKN (test)
        $buyer->setRegistrationAddress($dto->address);
        $buyer->setIp($dto->ipAddress);
        $buyer->setCity($dto->city);
        $buyer->setCountry('Turkey');
        $request->setBuyer($buyer);

        // Adres
        $shippingAddress = new Address();
        $shippingAddress->setContactName($dto->firstName . ' ' . $dto->lastName);
        $shippingAddress->setCity($dto->city);
        $shippingAddress->setCountry('Turkey');
        $shippingAddress->setAddress($dto->address);
        $request->setShippingAddress($shippingAddress);
        $request->setBillingAddress($shippingAddress);

        // Sepet
        $basketItems = [];
        foreach ($dto->items as $item) {
            $basketItem = new BasketItem();
            $basketItem->setId($item->id);
            $basketItem->setName($item->name);
            $basketItem->setCategory1($item->category);
            $basketItem->setItemType(BasketItemType::VIRTUAL);
            $basketItem->setPrice((string) $item->price);
            $basketItems[] = $basketItem;
        }
        $request->setBasketItems($basketItems);

        // Ödeme yap
        $payment = Payment::create($request, $this->options);

        if ($payment->getStatus() !== 'success') {
            // PCI DSS: Hata mesajında kart bilgisi olmamalı
            throw new PaymentException(
                "Payment failed: {$payment->getErrorMessage()}",
                (int) $payment->getErrorCode(),
            );
        }

        return new PaymentResult(
            paymentId:      $payment->getPaymentId(),
            conversationId: $payment->getConversationId(),
            status:         $payment->getStatus(),
            paidPrice:      (float) $payment->getPaidPrice(),
        );
    }
}
```

---

## STRIPE ENTEGRASYONU (PHP)

### Stripe — Idempotency + Webhook

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Infrastructure\Payment\Stripe;

use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

final class StripePaymentService
{
    private readonly StripeClient $stripe;

    public function __construct(
        private readonly string $secretKey,
        private readonly string $webhookSecret,
    ) {
        $this->stripe = new StripeClient($this->secretKey);
    }

    /**
     * Ödeme niyeti oluştur (idempotency key ile)
     */
    public function createPaymentIntent(
        int    $amountCents,
        string $currency,
        string $idempotencyKey,
        array  $metadata = [],
    ): PaymentIntentResult {
        try {
            $intent = $this->stripe->paymentIntents->create(
                [
                    'amount'               => $amountCents,
                    'currency'             => $currency,
                    'automatic_payment_methods' => ['enabled' => true],
                    'metadata'             => $metadata,
                ],
                [
                    // Idempotency key: Aynı key ile tekrar çağrılırsa
                    // aynı sonucu döner (duplicate charge önleme)
                    'idempotency_key' => $idempotencyKey,
                ]
            );

            return new PaymentIntentResult(
                id:           $intent->id,
                clientSecret: $intent->client_secret,
                status:       $intent->status,
                amount:       $intent->amount,
            );

        } catch (ApiErrorException $e) {
            throw new PaymentException(
                "Stripe error: {$e->getMessage()}",
                $e->getCode(),
            );
        }
    }

    /**
     * Webhook doğrulama — HMAC-SHA256 imza kontrolü
     */
    public function handleWebhook(string $payload, string $sigHeader): WebhookEvent
    {
        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->webhookSecret,
            );
        } catch (SignatureVerificationException $e) {
            throw new InvalidWebhookException('Invalid webhook signature');
        }

        return new WebhookEvent(
            type:    $event->type,
            data:    $event->data->object->toArray(),
            created: $event->created,
        );
    }
}

// Webhook endpoint
final class StripeWebhookController
{
    public function handle(Request $request): Response
    {
        $payload   = $request->getRawBody();
        $sigHeader = $request->getHeader('Stripe-Signature');

        try {
            $event = $this->stripeService->handleWebhook($payload, $sigHeader);
        } catch (InvalidWebhookException) {
            return Response::json(['error' => 'Invalid signature'], 400);
        }

        // Event tipine göre işle
        match ($event->type) {
            'payment_intent.succeeded'       => $this->handlePaymentSucceeded($event),
            'payment_intent.payment_failed'  => $this->handlePaymentFailed($event),
            'customer.subscription.deleted'  => $this->handleSubscriptionCancelled($event),
            default                          => null, // Bilinmeyen event'leri yoksay
        };

        // Stripe 200 bekler — hata olsa bile 200 dön
        // (Stripe başarısız webhook'ları retry eder)
        return Response::json(['received' => true], 200);
    }
}
```

---

## FRAUD DETECTION

### Kural Tabanlı Fraud Detection

```php
<?php
declare(strict_types=1);

final class FraudDetectionService
{
    private const HIGH_RISK_SCORE = 80;
    private const BLOCK_SCORE     = 95;

    public function __construct(
        private readonly CacheService $cache,
        private readonly GeoIpService $geoIp,
    ) {}

    /**
     * Ödeme isteğini fraud açısından değerlendir
     */
    public function evaluate(PaymentRequest $request): FraudScore
    {
        $score  = 0;
        $flags  = [];

        // Kural 1: Yüksek tutar
        if ($request->amount > 5000_00) { // 5000 TL
            $score += 20;
            $flags[] = 'HIGH_AMOUNT';
        }

        // Kural 2: Hızlı ardışık işlemler (velocity check)
        $recentCount = $this->getRecentTransactionCount(
            $request->userId,
            windowMinutes: 10,
        );
        if ($recentCount > 3) {
            $score += 30;
            $flags[] = 'HIGH_VELOCITY';
        }

        // Kural 3: Farklı IP'den işlem
        $lastIp = $this->getLastTransactionIp($request->userId);
        if ($lastIp && $lastIp !== $request->ipAddress) {
            $score += 15;
            $flags[] = 'IP_CHANGE';
        }

        // Kural 4: Yüksek riskli ülke
        $country = $this->geoIp->getCountry($request->ipAddress);
        if (in_array($country, ['NG', 'RO', 'UA', 'RU'], true)) {
            $score += 25;
            $flags[] = 'HIGH_RISK_COUNTRY';
        }

        // Kural 5: Yeni hesap + yüksek tutar
        $accountAgeDays = $this->getAccountAgeDays($request->userId);
        if ($accountAgeDays < 7 && $request->amount > 1000_00) {
            $score += 20;
            $flags[] = 'NEW_ACCOUNT_HIGH_AMOUNT';
        }

        // Kural 6: Kart BIN kontrolü
        if ($this->isHighRiskBin($request->cardBin)) {
            $score += 15;
            $flags[] = 'HIGH_RISK_BIN';
        }

        $decision = match(true) {
            $score >= self::BLOCK_SCORE     => FraudDecision::BLOCK,
            $score >= self::HIGH_RISK_SCORE => FraudDecision::REVIEW,
            default                         => FraudDecision::ALLOW,
        };

        // Log (PCI DSS: kart numarası loglanmaz)
        $this->logFraudCheck($request->userId, $score, $flags, $decision);

        return new FraudScore(
            score:    $score,
            decision: $decision,
            flags:    $flags,
        );
    }

    private function getRecentTransactionCount(int $userId, int $windowMinutes): int
    {
        $key = "fraud:velocity:{$userId}";
        $now = time();
        $window = $now - ($windowMinutes * 60);

        $timestamps = $this->cache->get($key) ?? [];
        $timestamps = array_filter($timestamps, fn($t) => $t > $window);

        // Yeni timestamp ekle
        $timestamps[] = $now;
        $this->cache->set($key, $timestamps, $windowMinutes * 60 + 60);

        return count($timestamps);
    }
}
```

---

## IDEMPOTENCY PATTERN

```php
<?php
declare(strict_types=1);

/**
 * Idempotency: Aynı istek N kez yapılsa aynı sonuç döner.
 * Ödeme sistemlerinde kritik — duplicate charge önleme.
 */
final class IdempotencyMiddleware
{
    public function __construct(
        private readonly CacheService $cache,
        private readonly int $ttlSeconds = 86400, // 24 saat
    ) {}

    public function handle(Request $request, callable $next): Response
    {
        $idempotencyKey = $request->getHeader('Idempotency-Key');

        if (!$idempotencyKey) {
            // POST endpoint'lerde zorunlu
            if ($request->getMethod() === 'POST') {
                return Response::json([
                    'error' => 'Idempotency-Key header required for POST requests',
                ], 400);
            }
            return $next($request);
        }

        // Key format validate et (UUID v4)
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $idempotencyKey)) {
            return Response::json(['error' => 'Invalid Idempotency-Key format'], 400);
        }

        $cacheKey = "idempotency:{$idempotencyKey}";

        // Daha önce işlendi mi?
        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            // Aynı sonucu döndür
            return Response::fromArray($cached)
                ->withHeader('Idempotency-Replayed', 'true');
        }

        // İşle
        $response = $next($request);

        // Başarılı ise cache'e yaz
        if ($response->getStatusCode() < 500) {
            $this->cache->set($cacheKey, $response->toArray(), $this->ttlSeconds);
        }

        return $response;
    }
}
```

---

## ÖDEME GÜVENLİK KURALLARI

```
PCI DSS Zorunlu:
  ✅ Kart numarası loglanmaz (sadece son 4 hane)
  ✅ CVV/CVC loglanmaz, saklanmaz
  ✅ Kart verisi şifrelenir (AES-256)
  ✅ TLS 1.2+ zorunlu
  ✅ Tokenization (kart numarası yerine token sakla)
  ✅ Idempotency key (duplicate charge önleme)
  ✅ Webhook imza doğrulama (HMAC-SHA256)
  ✅ Fraud detection
  ✅ Rate limiting (ödeme endpoint'leri)
  ✅ Audit log (tüm ödeme işlemleri)

Yasaklar:
  ❌ Kart numarasını tam logla
  ❌ CVV/CVC sakla
  ❌ Şifrelenmemiş kart verisi ilet
  ❌ Idempotency key olmadan ödeme al
  ❌ Webhook imzasını doğrulamadan işle
```

---

## FINTECH CHECKLIST

```
PCI DSS:
  [ ] Kart numarası loglanmıyor mu?
  [ ] CVV/CVC saklanmıyor mu?
  [ ] TLS 1.2+ kullanılıyor mu?
  [ ] Tokenization var mı?
  [ ] Encryption at rest var mı?

Ödeme Güvenliği:
  [ ] Idempotency key var mı?
  [ ] Webhook imza doğrulama var mı?
  [ ] Fraud detection var mı?
  [ ] Rate limiting var mı?
  [ ] Audit log var mı?

Hata Yönetimi:
  [ ] Ödeme hatası kullanıcıya açıklanıyor mu? (kart bilgisi olmadan)
  [ ] Retry stratejisi var mı?
  [ ] Rollback mekanizması var mı?
  [ ] Reconciliation süreci var mı?
```

---

*Fintech & Payment Patterns v7.0.0 — 2026-05-29 | Kiro IDE Native*
*PCI DSS v4.0, iyzico, Stripe, Fraud Detection, Idempotency*
*Kaynak: pcisecuritystandards.org, stripe.com/docs, iyzico.com/api-docs*
