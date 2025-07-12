<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];
$appointment_id = isset($_GET['appointment_id']) ? intval($_GET['appointment_id']) : 0;

// Fetch appointment details
$stmt = $conn->prepare("
    SELECT a.id, a.status, a.payment_status, a.consult_fee, u.email
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ? AND a.user_id = ?
");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("ii", $appointment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$appointment = $result->fetch_assoc();
$stmt->close();

if (!$appointment || $appointment['status'] !== 'accepted') {
    die("Invalid or unapproved appointment.");
}

if ($appointment['payment_status'] === 'paid') {
    die("This appointment has already been paid.");
}

// Generate a new unique transaction reference
function generatePaymentTxRef($appointment_id) {
    return 'appt_' . $appointment_id . '_' . uniqid();
}
$new_tx_ref = generatePaymentTxRef($appointment_id);

// Set payment status to 'paid' immediately (as per your new logic)
$stmt = $conn->prepare("UPDATE appointments SET payment_tx_ref = ?, payment_status = 'paid' WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("si", $new_tx_ref, $appointment_id);
$stmt->execute();
$stmt->close();

// Prepare Chapa payment request
$secretKey = "CHASECK_TEST-ObOKGQ4vLV6nX2ELLeEPMZFCNTxJPS3i"; // Replace with your real/test key
$callbackUrl = "https://yourdomain.com/doctor/payment_callback.php"; // optional now
$returnUrl = "http://localhost/online_medication2/user/payment_receipt.php?appointment_id=" . $appointment_id;



$paymentData = [
    "amount" => $appointment['consult_fee'],
    "currency" => "ETB",
    "tx_ref" => $new_tx_ref,
    "customer" => [
        "email" => $appointment['email'] ?? "noemail@example.com",
    ],
    "customization" => [
        "title" => "TeleMedicine",  // max 16 characters
        "description" => "Consult Fee",
    ],
    "callback_url" => $callbackUrl,
    "return_url" => $returnUrl,
];

// Initialize curl
$ch = curl_init("https://api.chapa.co/v1/transaction/initialize");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer {$secretKey}",
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));

$response = curl_exec($ch);
if (curl_errno($ch)) {
    die("Payment initialization failed. Curl error: " . curl_error($ch));
}
curl_close($ch);

$responseData = json_decode($response, true);

if (!empty($responseData['status']) && $responseData['status'] === 'success' && !empty($responseData['data']['checkout_url'])) {
    $payment_link = $responseData['data']['checkout_url'];
    $stmt = $conn->prepare("UPDATE appointments SET chapa_payment_link = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("si", $payment_link, $appointment_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to Chapa
    header("Location: " . $payment_link);
    exit();
} else {
    echo "Payment initialization failed. Please try again.<br>";
    echo "<pre>";
    print_r($responseData);
    echo "</pre>";
}
