<?php
$host = 'localhost'; 
$dbname = 'bd_5097'; 
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hapus data jika diminta
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
        $deleteId = $_POST['delete_id'];
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
        $stmt->execute([$deleteId]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Perbarui data jika diminta
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
        $editId = $_POST['edit_id'];
        $editName = $_POST['edit_name'];
        $editEmail = $_POST['edit_email'];
        $editStatus = $_POST['edit_status'];

        $stmt = $pdo->prepare("UPDATE user SET nama = ?, email = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$editName, $editEmail, $editStatus, $editId]);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Ambil data pengguna dari tabel "users"
    $stmt = $pdo->query("SELECT id, nama, email, is_active FROM user");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-primary"><?= $title; ?></h1>

    <div id="data" class="my-4">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)) : ?>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']); ?></td>
                                <td><?= htmlspecialchars($user['nama']); ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td><?= ($user['is_active'] == 1) ? 'Active' : 'Inactive'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editUser(<?= htmlspecialchars(json_encode($user)); ?>)">Edit</button>
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($user['id']); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">No data found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="edit_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="edit_status" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function editUser(user) {
        const modal = new bootstrap.Modal(document.getElementById('editModal'));
        document.getElementById('edit_id').value = user.id;
        document.getElementById('edit_name').value = user.nama;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_status').value = user.is_active;
        modal.show();
    }
</script>
