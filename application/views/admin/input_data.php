<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?php echo $title; ?></h1>

    <body>
        <div class="container mt-5">
            <h1 class="text-center mb-4" style="color: black;">Daftar Mata Kuliah</h1>

            <?php
            // Koneksi ke database
            $conn = new mysqli('localhost', 'root', '', 'bd_5097');

            // Cek koneksi
            if ($conn->connect_error) {
                die("Koneksi gagal: " . $conn->connect_error);
            }

            // Logika untuk menyimpan data
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nama_matkul = $_POST['nama_matkul'];
                $kode_matkul = $_POST['kode_matkul'];
                $sks = $_POST['sks'];
                $jenis_matkul = $_POST['jenis_matkul'];

                $sql = "INSERT INTO mata_kuliah (nama_matkul, kode_matkul, sks, jenis_matkul) 
                        VALUES ('$nama_matkul', '$kode_matkul', $sks, '$jenis_matkul')";

                if ($conn->query($sql) === TRUE) {
                    echo "<div class='alert alert-success'>Data mata kuliah berhasil disimpan!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                }
            }
            ?>


            <!-- Input Data Mata Kuliah -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>Tambah Mata Kuliah</h5>
                </div>
                <div class="card-body" style="color: black;">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="namaMatkul" class="form-label">Nama Mata Kuliah</label>
                            <input type="text" class="form-control" id="namaMatkul" name="nama_matkul" placeholder="Masukkan nama mata kuliah" required>
                        </div>
                        <div class="mb-3">
                            <label for="kodeMatkul" class="form-label">Kode Mata Kuliah</label>
                            <input type="text" class="form-control" id="kodeMatkul" name="kode_matkul" placeholder="Masukkan kode mata kuliah" required>
                        </div>
                        <div class="mb-3">
                            <label for="sksMatkul" class="form-label">Jumlah SKS</label>
                            <input type="number" class="form-control" id="sksMatkul" name="sks" placeholder="Masukkan jumlah SKS" required>
                        </div>
                        <div class="mb-3">
                            <label for="jenisMatkul" class="form-label">Jenis Mata Kuliah</label>
                            <select class="form-control" id="jenisMatkul" name="jenis_matkul" required>
                                <option value="">Pilih Jenis Mata Kuliah</option>
                                <option value="Wajib">Wajib</option>
                                <option value="Pilihan">Pilihan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Simpan Data Mata Kuliah</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Mata Kuliah -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Daftar Mata Kuliah</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mata Kuliah</th>
                            <th>Kode Mata Kuliah</th>
                            <th>Jumlah SKS</th>
                            <th>Jenis Mata Kuliah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil data dari tabel mata_kuliah
                        $sql = "SELECT * FROM mata_kuliah";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $no++ . "</td>";
                                echo "<td>" . $row['nama_matkul'] . "</td>";
                                echo "<td>" . $row['kode_matkul'] . "</td>";
                                echo "<td>" . $row['sks'] . "</td>";
                                echo "<td>" . $row['jenis_matkul'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Tidak ada data</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</div>