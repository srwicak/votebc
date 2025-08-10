<?= $this->extend('layout/admin_template') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Reset Password Pengguna</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Reset Password</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->has('error')) : ?>
                        <div class="alert alert-danger">
                            <?= session('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('success')) : ?>
                        <div class="alert alert-success">
                            <?= session('success') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('api/auth/reset-password') ?>" method="post" id="resetPasswordForm">
                        <div class="form-group">
                            <label for="user_id">Pilih Pengguna</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">-- Pilih Pengguna --</option>
                                <?php foreach ($users as $user) : ?>
                                    <option value="<?= $user['id'] ?>"><?= $user['nim'] ?> - <?= $user['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                            <small class="form-text text-muted">Password minimal 6 karakter</small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm_password" required minlength="6">
                            <div class="invalid-feedback">Password tidak sama</div>
                        </div>
                        <div class="form-group">
                            <label for="reason">Alasan Reset Password</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            <small class="form-text text-muted">Berikan alasan mengapa password direset (akan dicatat dalam sistem)</small>
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('resetPasswordForm');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const submitBtn = document.getElementById('submitBtn');

    // Validasi password sama
    function validatePassword() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.classList.add('is-invalid');
            submitBtn.disabled = true;
            return false;
        } else {
            confirmPassword.classList.remove('is-invalid');
            submitBtn.disabled = false;
            return true;
        }
    }

    newPassword.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', validatePassword);

    // Validasi form sebelum submit
    form.addEventListener('submit', function(e) {
        if (!validatePassword()) {
            e.preventDefault();
            return false;
        }
        
        // Konfirmasi sebelum reset password
        if (!confirm('Apakah Anda yakin ingin mereset password pengguna ini?')) {
            e.preventDefault();
            return false;
        }
        
        return true;
    });
});
</script>

<?= $this->endSection() ?>