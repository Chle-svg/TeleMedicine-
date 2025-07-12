<?php
// doctor/payment_success.php

include '../includes/db.php';

// Get the transaction reference from Chapa (sent as a GET parameter or via webhook)
if (!isset($_GET['tx_ref'])) {
    die("Invalid payment verification request.");
}

$tx_ref = $_GET['tx_ref'];

// Chapa secret key
$chapa_secret_key = "CHASECK_TEST-ObOKGQ4vLV6nX2ELLeEPMZFCNTxJPS3i";

// Verify payment with Chapa
$verify_url = "https://api.chapa.co/v1/transaction/verify/" . urlencode($tx_ref);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verify_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $chapa_secret_key"
]);
$response = curl_exec($ch);
curl_close($ch);

// Parse the response
$result = json_decode($response, true);

// Check if payment was successful
if (isset($result['status']) && $result['status'] === 'success' && $result['data']['status'] === 'success') {
    $query = $conn->prepare("UPDATE appointments SET payment_status = 'paid' WHERE payment_tx_ref = ?");
    $query->bind_param("s", $tx_ref);
    if ($query->execute()) {
        echo "<h2 style='color:green;'>✅ Payment verified and status updated successfully!</h2>";
    } else {
        echo "<h2 style='color:orange;'>⚠️ Payment verified but failed to update status in the database.</h2>";
    }
} else {
    echo "<h2 style='color:red;'>❌ Payment verification failed or payment not successful.</h2>";
}
?>
