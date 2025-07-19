<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

    <?php

    $servername = "localhost";
    $username = "root"; 
    $password = ""; 
    $dbname = "bd_5097"; 

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Ambil daftar mata kuliah untuk dropdown
    $sql_mata_kuliah = "SELECT kode_matkul, nama_matkul FROM mata_kuliah";
    $result_mata_kuliah = $conn->query($sql_mata_kuliah);

    // Menyimpan atau memperbarui nilai
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nim = $conn->real_escape_string($_POST['nim']);
        $kode_matkul = $conn->real_escape_string($_POST['kode_matkul']);
        $nilai_tugas = floatval($_POST['nilai_tugas']);
        $nilai_uts = floatval($_POST['nilai_uts']);
        $nilai_uas = floatval($_POST['nilai_uas']);

        // Ambil ID mahasiswa berdasarkan NIM
        $sql_mahasiswa = "SELECT id FROM user WHERE nim_nip = ? AND role_id = 3";
        $stmt_mahasiswa = $conn->prepare($sql_mahasiswa);
        $stmt_mahasiswa->bind_param("s", $nim);
        $stmt_mahasiswa->execute();
        $result_mahasiswa = $stmt_mahasiswa->get_result();

        // Ambil ID mata kuliah berdasarkan kode mata kuliah
        $sql_matkul = "SELECT id FROM mata_kuliah WHERE kode_matkul = ?";
        $stmt_matkul = $conn->prepare($sql_matkul);
        $stmt_matkul->bind_param("s", $kode_matkul);
        $stmt_matkul->execute();
        $result_matkul = $stmt_matkul->get_result();

        // Cek apakah data mahasiswa dan mata kuliah ditemukan
        if ($result_mahasiswa->num_rows > 0 && $result_matkul->num_rows > 0) {
            $mahasiswa_row = $result_mahasiswa->fetch_assoc();
            $matkul_row = $result_matkul->fetch_assoc();

            $mahasiswa_id = $mahasiswa_row["id"];
            $mata_kuliah_id = $matkul_row["id"];

            // Cek apakah nilai sudah ada
            $sql_check = "SELECT * FROM nilai WHERE mahasiswa_id = ? AND mata_kuliah_id = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("ii", $mahasiswa_id, $mata_kuliah_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            // Logika untuk update atau insert nilai
            if ($result_check->num_rows > 0) {
                // Update nilai jika sudah ada
                $sql_update = "UPDATE nilai SET nilai_tugas = ?, nilai_uts = ?, nilai_uas = ? WHERE mahasiswa_id = ? AND mata_kuliah_id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("ddiii", $nilai_tugas, $nilai_uts, $nilai_uas, $mahasiswa_id, $mata_kuliah_id);
                if ($stmt_update->execute()) {
                    echo "<div class='alert alert-success'>Nilai berhasil diperbarui.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Gagal memperbarui nilai: " . $stmt_update->error . "</div>";
                }
            } else {
                // Insert nilai baru jika belum ada
                $sql_insert = "INSERT INTO nilai (mahasiswa_id, mata_kuliah_id, nilai_tugas, nilai_uts, nilai_uas) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("iiddi", $mahasiswa_id, $mata_kuliah_id, $nilai_tugas, $nilai_uts, $nilai_uas);
                if ($stmt_insert->execute()) {
                    echo "<div class='alert alert-success'>Nilai berhasil disimpan.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Gagal menyimpan nilai: " . $stmt_insert->error . "</div>";
                }
            }
        } else {
            echo "<div class='alert alert-danger'>NIM atau kode mata kuliah tidak ditemukan.</div>";
        }
    }

    // Mengambil daftar nilai mahasiswa
    $sql_daftar_mahasiswa = "
        SELECT u.nama AS mahasiswa, mk.nama_matkul AS mata_kuliah, n.nilai_tugas, n.nilai_uts, n.nilai_uas
        FROM nilai n
        INNER JOIN user u ON n.mahasiswa_id = u.id
        INNER JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
    ";
    $result_daftar_mahasiswa = $conn->query($sql_daftar_mahasiswa);
    ?>

    <!-- Form Input Nilai -->
    <form method="post" action="" class="mt-3">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nim" class="form-label">NIM Mahasiswa:</label>
                <input type="text" name="nim" id="nim" class="form-control" placeholder="Masukkan NIM Mahasiswa" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="kode_matkul" class="form-label">Kode Mata Kuliah:</label>
                <select name="kode_matkul" id="kode_matkul" class="form-control" required>
                    <option value="">Pilih Mata Kuliah</option>
                    <?php
                    if ($result_mata_kuliah->num_rows > 0) {
                        while ($row = $result_mata_kuliah->fetch_assoc()) {
                            echo "<option value='" . $row['kode_matkul'] . "'>" . $row['kode_matkul'] . " - " . $row['nama_matkul'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="nilai_tugas" class="form-label">Nilai Tugas:</label>
                <input type="number" name="nilai_tugas" id="nilai_tugas" class="form-control" step="0.01" placeholder="Masukkan Nilai Tugas" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="nilai_uts" class="form-label">Nilai UTS:</label>
                <input type="number" name="nilai_uts" id="nilai_uts" class="form-control" step="0.01" placeholder="Masukkan Nilai UTS" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="nilai_uas" class="form-label">Nilai UAS:</label>
                <input type="number" name="nilai_uas" id="nilai_uas" class="form-control" step="0.01" placeholder="Masukkan Nilai UAS" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Nilai</button>
    </form>

    <!-- Daftar Nilai -->
    <h2 class="mt-5">Daftar Nilai Mahasiswa</h2>
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
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada data tersedia.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <?php
    $conn->close();
    ?>
</div>
