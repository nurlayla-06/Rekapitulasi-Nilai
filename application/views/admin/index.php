<?php
// Koneksi ke database
$host = 'localhost';
$dbname = 'bd_5097';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hitung total users
    $totalUsersStmt = $pdo->query("SELECT COUNT(*) AS total_users FROM user");
    $totalUsers = $totalUsersStmt->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Hitung users aktif
    $activeUsersStmt = $pdo->query("SELECT COUNT(*) AS active_users FROM user WHERE is_active = 1");
    $activeUsers = $activeUsersStmt->fetch(PDO::FETCH_ASSOC)['active_users'];

    // Hitung users baru (misalnya yang mendaftar dalam 7 hari terakhir)
    $newUsersStmt = $pdo->query("SELECT COUNT(*) AS new_users FROM user WHERE DATE(date_created) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
    $newUsers = $newUsersStmt->fetch(PDO::FETCH_ASSOC)['new_users'];

    // Ambil data users dengan role_id 2 dan 3
    $usersStmt = $pdo->prepare("SELECT id, nama, email, role_id, is_active FROM user WHERE role_id IN (1, 2, 3)");
    $usersStmt->execute();
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-primary"><?= $title; ?></h1>

    <!-- Overview Section -->
    <div id="overview" class="my-4">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="card shadow p-3 border-primary">
                    <h5 class="card-title text-primary">Total User</h5>
                    <p class="display-6 text-warning"><?= htmlspecialchars($totalUsers); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow p-3 border-primary">
                    <h5 class="card-title text-primary">User Aktif</h5>
                    <p class="display-6 text-warning"><?= htmlspecialchars($activeUsers); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Management Section -->
    <div id="data" class="my-4">
        <h2 class="text-primary">Data Management</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary style=" color: black;">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)) : ?>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td class="text-primary"><?= htmlspecialchars($user['id']); ?></td>
                                <td class="text-primary"><?= htmlspecialchars($user['nama']); ?></td>
                                <td class="text-primary"><?= htmlspecialchars($user['email']); ?></td>
                                <td class="text-<?= ($user['is_active'] == 1) ? 'success' : 'danger'; ?>">
                                    <?= ($user['is_active'] == 1) ? 'Active' : 'Inactive'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center text-primary">No data found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</div>
<!-- /.container-fluid -->