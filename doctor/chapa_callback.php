<?php
// doctor/chapa_callback.php

include '../includes/db.php';

// Chapa secret key (test or live)
$secretKey = "CHASECK_TEST-ObOKGQ4vLV6nX2ELLeEPMZFCNTxJPS3i";

// Get tx_ref from GET or POST
$tx_ref = $_GET['tx_ref'] ?? $_POST['tx_ref'] ?? null;

if (!$tx_ref) {
    http_response_code(400);
    echo "Missing transaction reference (tx_ref)";
    exit;
}

// Function to verify transaction from Chapa
function verifyChapaPayment($tx_ref, $secretKey) {
    $url = "https://api.chapa.co/v1/transaction/verify/" . $tx_ref;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $secretKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return false;
    }

    return json_decode($response, true);
}

// Call Chapa verification
$response = verifyChapaPayment($tx_ref, $secretKey);

// Check validity
if (!$response || $response['status'] !== 'success' || !isset($response['data']['status'])) {
    http_response_code(500);
    echo "Chapa verification failed.";
    exit;
}

// Check if the payment was successful
$paymentStatusFromChapa = $response['data']['status'];  // should be "success"
$reference = $response['data']['tx_ref'];
$paymentStatusToSave = ($paymentStatusFromChapa === 'success') ? 'paid' : 'failed';

// Update appointments table
$stmt = $conn->prepare("UPDATE appointments SET payment_status = ? WHERE payment_tx_ref = ?");
if (!$stmt) {
    http_response_code(500);
    echo "Database error: " . $conn->error;
    exit;
}
$stmt->bind_param("ss", $paymentStatusToSave, $reference);

if ($stmt->execute()) {
    http_response_code(200);
    echo "Payment status updated to '$paymentStatusToSave'";
} else {
    http_response_code(500);
    echo "Failed to update payment status.";
}
$stmt->close();
?>
