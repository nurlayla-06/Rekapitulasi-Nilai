<!-- Begin Page Content -->
<div class="container-fluid">

  <!-- Page Heading -->
  <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>
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

  // Query untuk mendapatkan daftar mahasiswa dengan nilai
  $sql_daftar_mahasiswa = "
    SELECT n.id, u.nama AS mahasiswa, mk.nama_matkul AS mata_kuliah, 
        n.nilai_tugas, n.nilai_uts, n.nilai_uas,
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

  // Proses Edit Nilai
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_nilai = $_POST['id_nilai'];
    $nilai_tugas = $_POST['nilai_tugas'];
    $nilai_uts = $_POST['nilai_uts'];
    $nilai_uas = $_POST['nilai_uas'];

    if (is_numeric($nilai_tugas) && is_numeric($nilai_uts) && is_numeric($nilai_uas)) {
      // Menggunakan prepared statement untuk mencegah SQL Injection
      $stmt = $conn->prepare("
            UPDATE nilai 
            SET nilai_tugas = ?, nilai_uts = ?, nilai_uas = ? 
            WHERE id = ?
        ");
      $stmt->bind_param("dddi", $nilai_tugas, $nilai_uts, $nilai_uas, $id_nilai);

      if ($stmt->execute()) {
        echo "<script>
                    alert('Nilai berhasil diperbarui!');
                    window.location.href = '';
                </script>";
      } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui data!');</script>";
      }
      $stmt->close();
    } else {
      echo "<script>alert('Nilai harus berupa angka!');</script>";
    }
  }
  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  </head>

  <body>
    <div class="container-fluid mt-4">

      <!-- Tabel Daftar Mahasiswa -->
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
              <th>Aksi</th>
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
                echo "<td>
                                <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editNilaiModal' 
                                    data-id='{$row['id']}'
                                    data-mahasiswa='{$row['mahasiswa']}'
                                    data-mata_kuliah='{$row['mata_kuliah']}'
                                    data-tugas='{$row['nilai_tugas']}'
                                    data-uts='{$row['nilai_uts']}'
                                    data-uas='{$row['nilai_uas']}'>
                                    Edit
                                </button>
                            </td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='9' class='text-center'>Tidak ada data tersedia.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Edit Nilai -->
    <div class="modal fade" id="editNilaiModal" tabindex="-1" role="dialog" aria-labelledby="editNilaiModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form method="POST" action="">
            <div class="modal-header">
              <h5 class="modal-title" id="editNilaiModalLabel">Edit Nilai</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="id_nilai" name="id_nilai">
              <div class="form-group">
                <label>Nilai Tugas</label>
                <input type="number" id="nilai_tugas" name="nilai_tugas" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Nilai UTS</label>
                <input type="number" id="nilai_uts" name="nilai_uts" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Nilai UAS</label>
                <input type="number" id="nilai_uas" name="nilai_uas" class="form-control" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script>
      $('#editNilaiModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var tugas = button.data('tugas');
        var uts = button.data('uts');
        var uas = button.data('uas');

        var modal = $(this);
        modal.find('#id_nilai').val(id);
        modal.find('#nilai_tugas').val(tugas);
        modal.find('#nilai_uts').val(uts);
        modal.find('#nilai_uas').val(uas);
      });
    </script>
  </body>

  </html>