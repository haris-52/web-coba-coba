<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_darurat";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Koneksi database gagal"]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['latitude']) && isset($data['longitude'])) {
    $latitude  = $conn->real_escape_string($data['latitude']);
    $longitude = $conn->real_escape_string($data['longitude']);
    
    // Set zona waktu ke Indonesia agar akurat saat data disimpan
    date_default_timezone_set('Asia/Jakarta');
    $waktu = date("Y-m-d H:i:s");

    $sql = "INSERT INTO log_lokasi (latitude, longitude, waktu_kejadian, deskripsi) 
            VALUES ('$latitude', '$longitude', '$waktu', '')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data koordinat tidak lengkap"]);
}
$conn->close();
?>