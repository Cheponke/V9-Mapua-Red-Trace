<?php
session_start();
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donor_id = $_POST['donor_id'];
    $volume = $_POST['volume'];

    if (empty($volume)) {
        echo json_encode(["status" => "error", "message" => "Please enter blood volume."]);
        exit;
    }

    $query = "SELECT u.blood_type, s.id as screening_id 
              FROM users u 
              JOIN screenings s ON u.id = s.donor_id 
              WHERE u.id = '$donor_id' 
              ORDER BY s.id DESC LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        echo json_encode(["status" => "error", "message" => "No screening record found."]);
        exit;
    }

    $blood_type = $data['blood_type'];
    $donation_id = $data['screening_id'];

    mysqli_begin_transaction($conn);

    try {
        $stmt = $conn->prepare("INSERT INTO inventory (DonationID, Inventory_BloodType, Inventory_Volume, Inventory_Status) VALUES (?, ?, ?, 'Available')");
        $stmt->bind_param("isi", $donation_id, $blood_type, $volume);
        $stmt->execute();

        mysqli_query($conn, "UPDATE users SET status = 'Inactive' WHERE id = '$donor_id'");

        mysqli_commit($conn);
        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}