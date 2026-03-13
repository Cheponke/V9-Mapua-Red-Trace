<?php
session_start();
header('Content-Type: application/json');
include "config.php";

// 1. Security Check
if(!isset($_SESSION['user_id'])){
    echo json_encode([
        'status' => 'error', 
        'message' => 'Unauthenticated: No session found. Please re-login.'
    ]);
    exit();
}

if($_SESSION['role'] !== "staff"){
    echo json_encode([
        'status' => 'error', 
        'message' => 'Unauthorized: Role is ' . ($_SESSION['role'] ?? 'None')
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // These names MUST match the formData.append() keys in your JavaScript
    $phone     = $_POST['phone'] ?? '';
    $name      = $_POST['name'] ?? '';
    $bloodType = $_POST['bloodType'] ?? '';

    if (empty($phone) || empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Recipient details are incomplete.']);
        exit;
    }

    // 2. Prepare the Message 
    // (Since we are passing data from the row, we don't need the extra SQL query here)
    $message = "Hello $name! This is Mapúa RedTrace. Your blood donation (Type $bloodType) has been processed and is now available in our inventory. Thank you for your life-saving gift!";

    // 3. PhilSMS API Integration
    $api_key = "1775|eZqhMCVfg2mi4nPgtpEUuUQCW1kdqdTnG4kgoQBMea783599";
    $sender_id = "PhilSMS";

    $payload = [
        "recipient" => $phone,
        "sender_id" => $sender_id,
        "message"   => $message,
    ];

    $ch = curl_init("https://app.philsms.com/api/v3/sms/send");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $api_key",
        "Content-Type: application/json",
        "Accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(['status' => 'error', 'message' => 'CURL Error: ' . $err]);
    } else {
        $api_res = json_decode($response, true);
        
        // Check if PhilSMS actually accepted the message
        if (isset($api_res['data']) || (isset($api_res['status']) && $api_res['status'] == 'success')) {
            echo json_encode(['status' => 'success']);
        } else {
            $errorMsg = $api_res['message'] ?? 'API Provider Error';
            echo json_encode(['status' => 'error', 'message' => $errorMsg]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>