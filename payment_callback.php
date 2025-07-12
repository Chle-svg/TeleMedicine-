<?php
// user/payment_callback.php

include '../includes/db.php';

// Read raw POST data from Chapa callback
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Log raw input (optional for debugging)
// file_put_contents("chapa_callback_log.txt", print_r($data, true), FILE_APPEND);

// Extract tx_ref
$tx_ref = $data['tx_ref'] ?? '';
$status = $data['status'] ?? '';

if (!$tx_ref || $status !== 'success') {
    http_response_code(400); // Bad request
    exit("Invalid or incomplete callback data");
}

// Verify the payment with Chapa
$secretKey = "CHASECK_TEST-ObOKGQ4vLV6nX2ELLeEPMZFCNTxJPS3i"; // your test/live key

$verifyUrl = "https://api.chapa.co/v1/transaction/verify/" . $tx_ref;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verifyUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer {$secretKey}"
]);

$response = curl_exec($ch);
curl_close($ch);

$verifyData = json_decode($response, true);

if (
    !empty($verifyData['status']) && $verifyData['status'] === 'success' &&
    $verifyData['data']['status'] === 'success'
) {
    // Update DB: set payment_status = 'paid' where payment_tx_ref matches
    $stmt = $conn->prepare("UPDATE appointments SET payment_status = 'paid' WHERE payment_tx_ref = ?");
    $stmt->bind_param("s", $tx_ref);
    $stmt->execute();
    $stmt->close();

    http_response_code(200);
    echo "Payment verified and status updated.";
} else {
    http_response_code(400);
    echo "Payment verification failed.";
}
