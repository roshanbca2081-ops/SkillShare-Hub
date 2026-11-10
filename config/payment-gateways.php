<?php
/** Provider keys are read from ignored local config or environment variables. */
$paymentConfig = [
    'environment' => 'sandbox',
    'base_url' => '',
    'esewa_product_code' => '',
    'esewa_secret_key' => '',
    'khalti_secret_key' => '',
    'npr_per_usd' => '130',
];

$localFiles = [__DIR__ . '/payment.local.php.php', __DIR__ . '/payment.local.php'];
foreach ($localFiles as $localFile) {
    if (!is_file($localFile)) {
        continue;
    }
    $local = require $localFile;
    if (is_array($local)) {
        foreach (array_keys($paymentConfig) as $key) {
            if (isset($local[$key]) && is_string($local[$key]) && trim($local[$key]) !== '') {
                $paymentConfig[$key] = trim($local[$key]);
            }
        }
    }
}

$environmentNames = [
    'environment' => 'SKILLSHARE_PAYMENT_ENV',
    'base_url' => 'SKILLSHARE_APP_URL',
    'esewa_product_code' => 'SKILLSHARE_ESEWA_PRODUCT_CODE',
    'esewa_secret_key' => 'SKILLSHARE_ESEWA_SECRET_KEY',
    'khalti_secret_key' => 'SKILLSHARE_KHALTI_SECRET_KEY',
    'npr_per_usd' => 'SKILLSHARE_NPR_PER_USD',
];
foreach ($environmentNames as $key => $environmentName) {
    $value = getenv($environmentName);
    if ($value === false || trim((string)$value) === '') {
        $value = $_SERVER[$environmentName] ?? $_ENV[$environmentName] ?? '';
    }
    if (is_string($value) && trim($value) !== '') {
        $paymentConfig[$key] = trim($value);
    }
}

$paymentConfig['environment'] = strtolower($paymentConfig['environment']) === 'production' ? 'production' : 'sandbox';
$paymentConfig['base_url'] = rtrim($paymentConfig['base_url'], '/');
$paymentConfig['npr_per_usd'] = is_numeric($paymentConfig['npr_per_usd']) && (float)$paymentConfig['npr_per_usd'] > 0
    ? (string)$paymentConfig['npr_per_usd']
    : '130';

/** Ensure the configured callback origin cannot be attacker-controlled. */
function paymentAppUrl($path)
{
    global $paymentConfig;
    $parts = parse_url($paymentConfig['base_url']);
    $allowed = is_array($parts)
        && isset($parts['scheme'], $parts['host'])
        && in_array(strtolower($parts['scheme']), ['http', 'https'], true)
        && (strtolower($parts['scheme']) === 'https' || in_array(strtolower($parts['host']), ['localhost', '127.0.0.1'], true))
        && !isset($parts['user']) && !isset($parts['pass']) && !isset($parts['query']) && !isset($parts['fragment']);
    if (!$allowed) {
        throw new RuntimeException('Configure a valid application URL for payment callbacks.');
    }
    return $paymentConfig['base_url'] . '/' . ltrim($path, '/');
}

/** Make a JSON API request to a configured payment provider. */
function paymentProviderJsonRequest($url, $secret, array $payload)
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('PHP cURL extension is required for payment verification.');
    }
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_THROW_ON_ERROR),
        CURLOPT_HTTPHEADER => ['Authorization: Key ' . $secret, 'Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $body = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);
    if (!is_string($body) || $status < 200 || $status >= 300) {
        error_log('Payment provider API request failed; HTTP ' . $status . '; ' . $curlError);
        throw new RuntimeException('The payment provider could not confirm the request.');
    }
    $data = json_decode($body, true);
    if (!is_array($data)) {
        throw new RuntimeException('The payment provider returned an invalid response.');
    }
    return $data;
}

function paymentNprForUsd($usdAmount)
{
    global $paymentConfig;
    return (int)round((float)$usdAmount * (float)$paymentConfig['npr_per_usd'], 0, PHP_ROUND_HALF_UP);
}

function paymentGatewayReady($provider)
{
    global $paymentConfig;
    if ($provider === 'esewa') {
        return $paymentConfig['esewa_product_code'] !== '' && $paymentConfig['esewa_secret_key'] !== '';
    }
    if ($provider === 'khalti') {
        return $paymentConfig['khalti_secret_key'] !== '';
    }
    return false;
}

/** Confirm a Khalti payment via the official server-side lookup API. */
function khaltiLookupPayment($pidx)
{
    global $paymentConfig;
    if (!paymentGatewayReady('khalti')) {
        throw new RuntimeException('Khalti is not configured.');
    }
    $base = $paymentConfig['environment'] === 'production' ? 'https://khalti.com/api/v2' : 'https://dev.khalti.com/api/v2';
    return paymentProviderJsonRequest($base . '/epayment/lookup/', $paymentConfig['khalti_secret_key'], ['pidx' => $pidx]);
}

/** Verify eSewa's signed callback data and confirm status server-to-server. */
function verifyEsewaResponse(array $data)
{
    global $paymentConfig;
    if (!paymentGatewayReady('esewa') || empty($data['signed_field_names']) || empty($data['signature'])) {
        throw new RuntimeException('Invalid eSewa response.');
    }
    $fieldNames = explode(',', $data['signed_field_names']);
    if (!in_array('signed_field_names', $fieldNames, true) || !in_array('transaction_uuid', $fieldNames, true) || !in_array('total_amount', $fieldNames, true) || !in_array('product_code', $fieldNames, true)) {
        throw new RuntimeException('Invalid eSewa signature fields.');
    }
    $messageParts = [];
    foreach ($fieldNames as $fieldName) {
        $fieldName = trim($fieldName);
        if ($fieldName === '' || !array_key_exists($fieldName, $data) || !is_scalar($data[$fieldName])) {
            throw new RuntimeException('Incomplete eSewa response.');
        }
        $messageParts[] = $fieldName . '=' . $data[$fieldName];
    }
    $expectedSignature = base64_encode(hash_hmac('sha256', implode(',', $messageParts), $paymentConfig['esewa_secret_key'], true));
    if (!hash_equals($expectedSignature, (string)$data['signature'])) {
        throw new RuntimeException('eSewa signature validation failed.');
    }

    return lookupEsewaTransaction($data['total_amount'], $data['transaction_uuid']);
}

/** Use eSewa's status API for a known merchant order (including failure returns). */
function lookupEsewaTransaction($totalAmount, $transactionUuid)
{
    global $paymentConfig;
    if (!paymentGatewayReady('esewa') || !is_scalar($totalAmount) || !is_string($transactionUuid) || !preg_match('/\A[A-Za-z0-9-]{1,100}\z/', $transactionUuid)) {
        throw new RuntimeException('Invalid eSewa status lookup request.');
    }
    $base = $paymentConfig['environment'] === 'production' ? 'https://esewa.com.np' : 'https://rc.esewa.com.np';
    $query = http_build_query(['product_code' => $paymentConfig['esewa_product_code'], 'total_amount' => (string)$totalAmount, 'transaction_uuid' => $transactionUuid]);
    $url = $base . '/api/epay/transaction/status/?' . $query;
    if (!function_exists('curl_init')) {
        throw new RuntimeException('PHP cURL extension is required for eSewa verification.');
    }
    $curl = curl_init($url);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_TIMEOUT => 20, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_FOLLOWLOCATION => false]);
    $body = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);
    if (!is_string($body) || $status < 200 || $status >= 300) {
        error_log('eSewa status check failed; HTTP ' . $status . '; ' . $curlError);
        throw new RuntimeException('eSewa could not confirm the transaction.');
    }
    $result = json_decode($body, true);
    if (!is_array($result)) {
        throw new RuntimeException('eSewa returned an invalid verification response.');
    }
    return $result;
}

/** Record a gateway attempt against the fresher's pending session booking. */
function createSessionPaymentAttempt(PDO $pdo, $userId, array $session, $provider)
{
    $pdo->beginTransaction();
    try {
        $bookingQuery = $pdo->prepare('SELECT id, status FROM bookings WHERE fresher_id = ? AND session_id = ? LIMIT 1 FOR UPDATE');
        $bookingQuery->execute([$userId, $session['id']]);
        $booking = $bookingQuery->fetch();
        if ($booking && in_array($booking['status'], ['approved', 'completed'], true)) {
            throw new RuntimeException('You already have an approved booking for this session.');
        }
        if (!$booking) {
            $createBooking = $pdo->prepare("INSERT INTO bookings (fresher_id, session_id, status, booking_date) VALUES (?, ?, 'pending', NOW())");
            $createBooking->execute([$userId, $session['id']]);
            $bookingId = (int)$pdo->lastInsertId();
        } else {
            $bookingId = (int)$booking['id'];
            if ($booking['status'] === 'rejected') {
                $retryBooking = $pdo->prepare("UPDATE bookings SET status = 'pending' WHERE id = ? AND status = 'rejected'");
                $retryBooking->execute([$bookingId]);
            }
        }

        $pendingQuery = $pdo->prepare("SELECT id, created_at FROM payments WHERE fresher_id = ? AND status = 'pending' AND JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.booking_id')) = ? ORDER BY id DESC LIMIT 1 FOR UPDATE");
        $pendingQuery->execute([$userId, (string)$bookingId]);
        $existingAttempt = $pendingQuery->fetch();
        if ($existingAttempt) {
            throw new RuntimeException('A previous payment is still awaiting provider confirmation. Contact support before trying again to avoid a duplicate charge.');
        }

        $insert = $pdo->prepare("INSERT INTO payments (fresher_id, course_id, amount, payment_method, status, transaction_id, metadata, payment_date) VALUES (?, ?, ?, ?, 'pending', ?, ?, NOW())");
        $placeholder = 'INIT-' . bin2hex(random_bytes(12));
        $courseId = !empty($session['course_id']) ? (int)$session['course_id'] : null;
        $metadata = json_encode(['booking_id' => $bookingId, 'session_id' => (int)$session['id']], JSON_THROW_ON_ERROR);
        $insert->execute([$userId, $courseId, (float)$session['price'], $provider, $placeholder, $metadata]);
        $paymentId = (int)$pdo->lastInsertId();
        $orderReference = 'P' . $paymentId . '-B' . $bookingId . '-' . bin2hex(random_bytes(6));
        $metadata = json_encode(['booking_id' => $bookingId, 'session_id' => (int)$session['id'], 'order_reference' => $orderReference], JSON_THROW_ON_ERROR);
        $updateReference = $pdo->prepare('UPDATE payments SET transaction_id = ?, metadata = ? WHERE id = ?');
        $updateReference->execute([$orderReference, $metadata, $paymentId]);
        $pdo->commit();
        return ['payment_id' => $paymentId, 'booking_id' => $bookingId, 'order_reference' => $orderReference];
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }
}

/** Decode the internally generated payment and booking ids from a provider order reference. */
function parsePaymentOrderReference($reference)
{
    if (!is_string($reference) || !preg_match('/\AP([1-9][0-9]*)-B([1-9][0-9]*)-[a-f0-9]{12}\z/', $reference, $matches)) {
        return null;
    }
    return ['payment_id' => (int)$matches[1], 'booking_id' => (int)$matches[2]];
}

/** Only this helper transitions a pending payment after provider verification. */
function finalizeSessionPayment(PDO $pdo, $paymentId, $bookingId, $provider, $orderReference, $success)
{
    $pdo->beginTransaction();
    try {
        $lookup = $pdo->prepare('SELECT p.id, p.fresher_id, p.status, p.payment_method, p.transaction_id, b.id AS booking_id, b.status AS booking_status FROM payments p JOIN bookings b ON b.id = ? AND b.fresher_id = p.fresher_id WHERE p.id = ? AND JSON_UNQUOTE(JSON_EXTRACT(p.metadata, \'$.booking_id\')) = ? AND p.transaction_id = ? AND p.payment_method = ? LIMIT 1 FOR UPDATE');
        $lookup->execute([$bookingId, $paymentId, (string)$bookingId, $orderReference, $provider]);
        $record = $lookup->fetch();
        if (!$record) {
            $pdo->rollBack();
            return false;
        }
        if ($record['status'] === 'completed') {
            $pdo->commit();
            return (bool)$success;
        }
        if ($record['status'] !== 'pending') {
            $pdo->commit();
            return false;
        }
        if ($record['booking_status'] !== 'pending') {
            $pdo->commit();
            return false;
        }
        if ($success) {
            $updatePayment = $pdo->prepare("UPDATE payments SET status = 'completed', payment_date = NOW() WHERE id = ? AND status = 'pending'");
            $updatePayment->execute([$paymentId]);
            $updateBooking = $pdo->prepare("UPDATE bookings SET status = 'approved' WHERE id = ? AND fresher_id = ? AND status = 'pending'");
            $updateBooking->execute([$bookingId, $record['fresher_id']]);
        } else {
            $updatePayment = $pdo->prepare("UPDATE payments SET status = 'failed' WHERE id = ? AND status = 'pending'");
            $updatePayment->execute([$paymentId]);
            $updateBooking = $pdo->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ? AND fresher_id = ? AND status = 'pending'");
            $updateBooking->execute([$bookingId, $record['fresher_id']]);
        }
        $pdo->commit();
        return (bool)$success;
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }
}

return $paymentConfig;
