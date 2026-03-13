<?php
session_start();
include "config.php";

$screening_id = $_SESSION['active_screening_id']; // The ID we just generated

// Update the specific record for this session
$sql = "UPDATE screenings SET pulse_rate = ?, blood_pressure = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $pulse, $bp, $screening_id);
$stmt->execute();