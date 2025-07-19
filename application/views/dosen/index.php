<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <?php
    // Koneksi ke database
    $servername = "localhost";
    $username = "root"; // Ganti dengan username database Anda
    $password = ""; // Ganti dengan password database Anda
    $dbname = "bd_5097"; // Ganti dengan nama database Anda

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Query untuk mendapatkan jumlah mahasiswa
    $sql_jumlah_mahasiswa = "SELECT COUNT(*) AS total_mahasiswa FROM user WHERE role_id = 3";
    $result_jumlah_mahasiswa = $conn->query($sql_jumlah_mahasiswa);
    if (!$result_jumlah_mahasiswa) {
        die("Query gagal: " . $conn->error);
    }
    $jumlah_mahasiswa = $result_jumlah_mahasiswa->fetch_assoc()['total_mahasiswa'];

    // Query untuk mendapatkan jumlah mahasiswa yang sudah diinput nilai
    $sql_mahasiswa_nilai = "SELECT COUNT(DISTINCT mahasiswa_id) AS total_sudah_dinilai FROM nilai";
    $result_mahasiswa_nilai = $conn->query($sql_mahasiswa_nilai);
    if (!$result_mahasiswa_nilai) {
        die("Query gagal: " . $conn->error);
    }
    $jumlah_sudah_dinilai = $result_mahasiswa_nilai->fetch_assoc()['total_sudah_dinilai'];

    // Hitung jumlah mahasiswa yang belum diinput nilai
    $jumlah_belum_dinilai = $jumlah_mahasiswa - $jumlah_sudah_dinilai;

    // Query untuk mendapatkan daftar mahasiswa yang sudah memiliki nilai
    $sql_daftar_mahasiswa = "
    SELECT u.nama AS mahasiswa, mk.nama_matkul AS mata_kuliah, n.nilai_tugas, n.nilai_uts, n.nilai_uas,
        ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) AS nilai_rata_rata,
        CASE
            WHEN ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) >= 85 THEN 'A'
            WHEN ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) >= 75 THEN 'B'
            WHEN ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) >= 60 THEN 'C'
            WHEN ROUND((n.nilai_tugas + n.nilai_uts + n.nilai_uas) / 3, 2) >= 50 THEN 'D'
            ELSE 'E'
        END AS nilai_huruf
    FROM nilai n
    INNER JOIN user u ON n.mahasiswa_id = u.id
    INNER JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
";
    $result_daftar_mahasiswa = $conn->query($sql_daftar_mahasiswa);
    if (!$result_daftar_mahasiswa) {
        die("Query gagal: " . $conn->error);
    }
    ?>

    <!-- Card Statistik -->
    <div class="row">
        <!-- Card Jumlah Mahasiswa -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Jumlah Mahasiswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_mahasiswa; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Jumlah Mahasiswa yang Sudah Dinilai -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Sudah Dinilai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_sudah_dinilai; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Jumlah Mahasiswa yang Belum Dinilai -->
        <div class="col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Belum Dinilai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $jumlah_belum_dinilai; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Mahasiswa -->
    <h2 class="mt-4">Daftar Mahasiswa dengan Nilai</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Nama Mahasiswa</th>
                    <th>Mata Kuliah</th>
                    <th>Nilai Tugas</th>
                    <th>Nilai UTS</th>
                    <th>Nilai UAS</th>
                    <th>Nilai Rata-rata</th>
                    <th>Nilai Huruf</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if ($result_daftar_mahasiswa->num_rows > 0) {
                    while ($row = $result_daftar_mahasiswa->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['mahasiswa'] . "</td>";
                        echo "<td>" . $row['mata_kuliah'] . "</td>";
                        echo "<td>" . $row['nilai_tugas'] . "</td>";
                        echo "<td>" . $row['nilai_uts'] . "</td>";
                        echo "<td>" . $row['nilai_uas'] . "</td>";
                        echo "<td>" . $row['nilai_rata_rata'] . "</td>";
                        echo "<td>" . $row['nilai_huruf'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Tidak ada data tersedia.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>