<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Pastikan email pengguna tersedia di session
    if (!isset($_SESSION['email'])) {
        die("Session email tidak ditemukan. Silakan login kembali.");
    }

    $user_email = $_SESSION['email'];

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

    // Query untuk mendapatkan data pengguna berdasarkan email
    $sql_user = "SELECT * FROM user WHERE email = ?";
    $stmt_user = $conn->prepare($sql_user);
    if (!$stmt_user) {
        die("Query gagal: " . $conn->error);
    }
    $stmt_user->bind_param("s", $user_email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows === 0) {
        die("Data pengguna tidak ditemukan.");
    }

    $user = $result_user->fetch_assoc();
    $mahasiswa_id = $user['id'];

    // Query untuk menghitung rata-rata nilai berdasarkan mahasiswa_id
    $sql_avg_nilai = "
    SELECT ROUND(AVG((nilai_tugas + nilai_uts + nilai_uas) / 3), 2) AS rata_rata_nilai 
    FROM nilai WHERE mahasiswa_id = ?
    ";
    $stmt_avg_nilai = $conn->prepare($sql_avg_nilai);
    if (!$stmt_avg_nilai) {
        die("Query gagal: " . $conn->error);
    }
    $stmt_avg_nilai->bind_param("i", $mahasiswa_id);
    $stmt_avg_nilai->execute();
    $result_avg_nilai = $stmt_avg_nilai->get_result();
    $rata_rata_nilai = $result_avg_nilai->fetch_assoc()['rata_rata_nilai'];

    // Query untuk menghitung persentase kelulusan berdasarkan mahasiswa_id
    $sql_kelulusan = "
    SELECT 
        COUNT(CASE 
            WHEN ROUND((nilai_tugas + nilai_uts + nilai_uas) / 3, 2) >= 60 THEN 1 
        END) AS jumlah_lulus,
        COUNT(*) AS total_mata_kuliah
    FROM nilai WHERE mahasiswa_id = ?
    ";
    $stmt_kelulusan = $conn->prepare($sql_kelulusan);
    if (!$stmt_kelulusan) {
        die("Query gagal: " . $conn->error);
    }
    $stmt_kelulusan->bind_param("i", $mahasiswa_id);
    $stmt_kelulusan->execute();
    $result_kelulusan = $stmt_kelulusan->get_result();
    $data_kelulusan = $result_kelulusan->fetch_assoc();
    // Cek jika total_mata_kuliah lebih dari 0 sebelum melakukan pembagian
    if ($data_kelulusan['total_mata_kuliah'] > 0) {
        $persentase_kelulusan = round(($data_kelulusan['jumlah_lulus'] / $data_kelulusan['total_mata_kuliah']) * 100, 2);
    } else {
        // Jika tidak ada mata kuliah, set persentase kelulusan ke 0
        $persentase_kelulusan = 0;
    }
    ?>

    <!-- Profil dan statistik -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3 bg-primary text-white" style="max-width: 80%;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="<?php echo base_url('assets/img/profile/') . $user['image']; ?>" class="img-fluid rounded-start">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h5 class="card-title"><?= $user['nama']; ?></h5>
                            <p class="card-text"><?= $user['email']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-lg rounded text-center p-5 bg-success text-white">
                <h5 class="card-title font-weight-bold">Status Mahasiswa</h5>
                <p class="display-6">Aktif</p>
            </div>
        </div>
    </div>
    <div class="row text-center mb-5">
        <div class="col-md-6">
            <div class="card shadow-lg rounded p-4">
                <h5 class="card-title text-primary font-weight-bold">Rata-rata Nilai</h5>
                <p class="display-4 text-dark"><?= $rata_rata_nilai; ?></p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-lg rounded p-4">
                <h5 class="card-title text-primary font-weight-bold">Persentase Kelulusan</h5>
                <p class="display-4 text-dark"><?= $persentase_kelulusan; ?>%</p>
            </div>
        </div>
    </div>
</div>