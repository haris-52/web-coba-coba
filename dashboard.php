<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_darurat";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses Hapus Data
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $conn->query("DELETE FROM log_lokasi WHERE id = $id_hapus");
    header("Location: dashboard.php");
    exit();
}

// Proses Update Deskripsi
if (isset($_POST['update_deskripsi'])) {
    $id_edit = intval($_POST['id_data']);
    $deskripsi_baru = $conn->real_escape_string($_POST['deskripsi']);
    $conn->query("UPDATE log_lokasi SET deskripsi = '$deskripsi_baru' WHERE id = $id_edit");
    header("Location: dashboard.php");
    exit();
}

$result = $conn->query("SELECT * FROM log_lokasi ORDER BY waktu_kejadian DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Monitoring Darurat</title>
    <!-- Memanggil CSS Terpisah -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Dashboard Riwayat Koordinat Darurat</h2>
<p>Nomor urut tampil otomatis secara runtut meskipun ada baris yang dihapus.</p>

<table>
    <tr>
        <th>ID</th>
        <th>Latitude, Longitude</th>
        <th>Date</th>
        <th>Time</th>
        <th>Description</th>
        <th>Location</th>
        <th>Action</th>
    </tr>
    <?php 
    if ($result && $result->num_rows > 0) {
        $nomor_urut = 1; 
        while($row = $result->fetch_assoc()) {
            $dateTime = new DateTime($row['waktu_kejadian'], new DateTimeZone('Asia/Jakarta'));
            $date = $dateTime->format('Y-m-d');
            $time = $dateTime->format('H:i');
    ?>
    <tr>
        <form action="dashboard.php" method="POST">
            <td><strong>#<?php echo $nomor_urut++; ?></strong></td>
            <td><?php echo $row['latitude'] . ", " . $row['longitude']; ?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo $time; ?></td>
            <td>
                <input type="hidden" name="id_data" value="<?php echo $row['id']; ?>">
                <textarea name="deskripsi"><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
            </td>
            <td>
                <a class="map-link" href="https://www.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>" target="_blank">Buka Peta</a>
            </td>
            <td>
                <button type="submit" name="update_deskripsi" class="btn btn-primary">Simpan</button>
                <a href="dashboard.php?hapus=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus riwayat ini?')">Hapus</a>
            </td>
        </form>
    </tr>
    <?php 
        }
    } else {
        echo "<tr><td colspan='7' style='text-align:center;'>Belum ada data riwayat yang masuk.</td></tr>";
    }
    ?>
</table>

</body>
</html>
<?php $conn->close(); ?>