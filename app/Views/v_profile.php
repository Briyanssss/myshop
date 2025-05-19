<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h2>Profil Pengguna</h2>
<div class="card p-3">
    <h4>Data Profil</h4>
    <table class="table table-bordered">
        <tr><th>Username</th><td><?= esc($username) ?: '-' ?></td></tr>
        <tr><th>Role</th><td><?= esc($role) ?: '-' ?></td></tr>
        <tr><th>Email</th><td><?= esc($email) ?: '-' ?></td></tr>
        <tr><th>Waktu Login</th><td><?= esc($login_time) ?: '-' ?></td></tr>
        <tr><th>Status</th><td><?= esc($status) ?: '-' ?></td></tr>
    </table>
</div>

<?= $this->endSection() ?>
