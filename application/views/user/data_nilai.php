<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd_5097";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data pengguna yang sedang login
$user_email = $user['email']; // Diasumsikan email pengguna yang login tersedia di variabel $user['email']

// Ambil data mahasiswa berdasarkan email pengguna
$sql_mahasiswa = "SELECT id, nama, nim_nip FROM user WHERE email = ? AND role_id = 3 LIMIT 1";
$stmt_mahasiswa = $conn->prepare($sql_mahasiswa);
$stmt_mahasiswa->bind_param("s", $user_email);
$stmt_mahasiswa->execute();
$result_mahasiswa = $stmt_mahasiswa->get_result();

if (!$result_mahasiswa) {
    die("Query gagal: " . $conn->error);
}

$mahasiswa = $result_mahasiswa->fetch_assoc();

// Query untuk menghitung nilai rata-rata, IPK, dan IPK berdasarkan mahasiswa
$sql_avg_nilai = "
    SELECT 
        ROUND(AVG((nilai_tugas + nilai_uts + nilai_uas) / 3), 2) AS rata_rata_nilai,
        ROUND(SUM(CASE
            WHEN (nilai_tugas + nilai_uts + nilai_uas) / 3 >= 85 THEN 4.0
            WHEN (nilai_tugas + nilai_uts + nilai_uas) / 3 >= 75 THEN 3.0
            WHEN (nilai_tugas + nilai_uts + nilai_uas) / 3 >= 60 THEN 2.0
            WHEN (nilai_tugas + nilai_uts + nilai_uas) / 3 >= 50 THEN 1.0
            ELSE 0.0
        END) / COUNT(*), 2) AS ipk
    FROM nilai
    WHERE mahasiswa_id = ?
";
$stmt_avg_nilai = $conn->prepare($sql_avg_nilai);
$stmt_avg_nilai->bind_param("i", $mahasiswa['id']);
$stmt_avg_nilai->execute();
$result_avg_nilai = $stmt_avg_nilai->get_result();

if (!$result_avg_nilai) {
    die("Query gagal: " . $conn->error);
}

$data_avg_nilai = $result_avg_nilai->fetch_assoc();

$rata_rata_nilai = $data_avg_nilai['rata_rata_nilai'] ?? 0;
$ipk = $data_avg_nilai['ipk'] ?? 0;

// Query untuk data nilai per mata kuliah berdasarkan mahasiswa
$sql_nilai_mahasiswa = "
    SELECT 
        m.kode_matkul,
        m.nama_matkul,
        n.nilai_tugas,
        n.nilai_uts,
        n.nilai_uas,
        ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) AS rata_rata,
        CASE
            WHEN ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) >= 85 THEN 'A'
            WHEN ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) >= 75 THEN 'B'
            WHEN ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) >= 60 THEN 'C'
            WHEN ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) >= 50 THEN 'D'
            ELSE 'E'
        END AS nilai_huruf
    FROM nilai n
    INNER JOIN mata_kuliah m ON n.mata_kuliah_id = m.id
    WHERE n.mahasiswa_id = ?
    ORDER BY m.nama_matkul
";
$stmt_nilai_mahasiswa = $conn->prepare($sql_nilai_mahasiswa);
$stmt_nilai_mahasiswa->bind_param("i", $mahasiswa['id']);
$stmt_nilai_mahasiswa->execute();
$result_nilai_mahasiswa = $stmt_nilai_mahasiswa->get_result();

if (!$result_nilai_mahasiswa) {
    die("Query gagal: " . $conn->error);
}

?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?? 'Dashboard'; ?></h1>

    <!-- Informasi Mahasiswa -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <h5 class="font-weight-bold">Nama: <?= $mahasiswa['nama'] ?? 'Tidak diketahui'; ?></h5>
            <h5 class="font-weight-bold">NIM: <?= $mahasiswa['nim_nip'] ?? 'Tidak diketahui'; ?></h5>
        </div>
    </div>

    <!-- Data Nilai Mahasiswa -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Nilai Mahasiswa</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kode Mata Kuliah</th>
                                <th>Mata Kuliah</th>
                                <th>Nilai Tugas</th>
                                <th>Nilai UTS</th>
                                <th>Nilai UAS</th>
                                <th>Nilai Huruf</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_nilai_mahasiswa && $result_nilai_mahasiswa->num_rows > 0) {
                                while ($row = $result_nilai_mahasiswa->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['kode_matkul']}</td>
                                            <td>{$row['nama_matkul']}</td>
                                            <td>{$row['nilai_tugas']}</td>
                                            <td>{$row['nilai_uts']}</td>
                                            <td>{$row['nilai_uas']}</td>
                                            <td>{$row['nilai_huruf']}</td>
                                        </tr>";
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center">Tidak ada data tersedia.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="row text-center mb-5">
        <div class="col-md-4">
            <div class="card shadow-lg rounded p-4">
                <h5 class="card-title text-primary font-weight-bold">Nilai Rata-rata</h5>
                <p class="display-4 text-dark"><?= $rata_rata_nilai; ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-lg rounded p-4">
                <h5 class="card-title text-primary font-weight-bold">IPK</h5>
                <p class="display-4 text-dark"><?= $ipk; ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-lg rounded p-4">
                <h5 class="card-title text-primary font-weight-bold">IP</h5>
                <p class="display-4 text-dark"><?= $ipk; ?></p>
            </div>
        </div>
    </div>
</div>