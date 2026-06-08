<?php require_once 'app/helpers/SessionHelper.php'; SessionHelper::requireAdmin(); ?>
<?php include 'app/views/shares/header.php'; ?>

<style>
.admin-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.admin-title { font-size:1.35rem; font-weight:800; color:#222; }
.stat-card { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.07); padding:18px 24px; text-align:center; }
.stat-num { font-size:1.9rem; font-weight:800; }
.stat-label { font-size:.82rem; color:#888; margin-top:2px; }
.search-bar .form-control:focus { border-color:#e63946; box-shadow:0 0 0 .2rem rgba(230,57,70,.12); }
.user-table { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
.user-table table { margin:0; }
.user-table thead th { background:#f8f9fa; font-weight:700; font-size:.85rem; text-transform:uppercase; letter-spacing:.5px; color:#555; border:none; padding:14px 16px; }
.user-table tbody td { vertical-align:middle; padding:12px 16px; border-color:#f0f0f0; }
.avatar-sm { width:40px; height:40px; border-radius:50%; object-fit:cover; }
.avatar-sm-default { width:40px; height:40px; border-radius:50%; background:#e63946; display:inline-flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:1rem; }
.badge-admin { background:#fff3e0; color:#e65100; border:1px solid #ffcc80; font-weight:700; padding:3px 10px; border-radius:20px; font-size:.78rem; }
.badge-user { background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; font-weight:700; padding:3px 10px; border-radius:20px; font-size:.78rem; }
.badge-active { background:#e3f2fd; color:#1565c0; border:1px solid #90caf9; padding:3px 10px; border-radius:20px; font-size:.78rem; }
.badge-locked { background:#fce4ec; color:#b71c1c; border:1px solid #f48fb1; padding:3px 10px; border-radius:20px; font-size:.78rem; }
.btn-action { padding:4px 10px; font-size:.82rem; border-radius:6px; }
.filter-bar { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:16px 20px; margin-bottom:20px; }
</style>

<div class="admin-header">
    <div class="admin-title"><i class="fa fa-users-gear mr-2 text-danger"></i>Quản lý người dùng</div>
    <a href="/project1/Product" class="btn btn-outline-secondary btn-sm">
        <i class="fa fa-arrow-left mr-1"></i> Về trang chủ
    </a>
</div>

<!-- STATS -->
<?php
$total  = count($users);
$admins = count(array_filter($users, fn($u) => $u->role === 'admin'));
$locked = count(array_filter($users, fn($u) => ($u->is_active ?? 1) == 0));
?>
<div class="row mb-4">
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-num text-primary"><?= $total ?></div>
            <div class="stat-label">Tổng người dùng</div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-num text-warning"><?= $admins ?></div>
            <div class="stat-label">Quản trị viên</div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-num text-success"><?= $total - $admins ?></div>
            <div class="stat-label">Người dùng thường</div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-num text-danger"><?= $locked ?></div>
            <div class="stat-label">Tài khoản bị khóa</div>
        </div>
    </div>
</div>

<!-- ALERTS -->
<?php if (!empty($success)): ?>
    <div class="alert alert-success"><i class="fa fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div><i class="fa fa-exclamation-circle mr-1"></i><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- FILTER / SEARCH -->
<form method="GET" action="/project1/Admin/users" class="filter-bar">
    <div class="form-row align-items-end">
        <div class="col-md-5 mb-2 mb-md-0">
            <label class="font-weight-bold mb-1" style="font-size:.85rem;">Tìm kiếm</label>
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Tên hoặc email..." value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <label class="font-weight-bold mb-1" style="font-size:.85rem;">Vai trò</label>
            <select name="role" class="form-control form-control-sm">
                <option value="">Tất cả</option>
                <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="user"  <?= ($role ?? '') === 'user'  ? 'selected' : '' ?>>User</option>
            </select>
        </div>
        <div class="col-md-2 mb-2 mb-md-0">
            <label class="font-weight-bold mb-1" style="font-size:.85rem;">Trạng thái</label>
            <select name="status" class="form-control form-control-sm">
                <option value="">Tất cả</option>
                <option value="1" <?= ($status ?? '') === '1' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="0" <?= ($status ?? '') === '0' ? 'selected' : '' ?>>Bị khóa</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-danger btn-sm btn-block">
                <i class="fa fa-filter mr-1"></i> Lọc
            </button>
        </div>
    </div>
</form>

<!-- TABLE -->
<div class="user-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Người dùng</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($users)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Không tìm thấy người dùng nào.</td></tr>
        <?php else: ?>
            <?php foreach ($users as $i => $u): ?>
            <tr>
                <td class="text-muted"><?= $i + 1 ?></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($u->avatar)): ?>
                            <img src="/project1/<?= htmlspecialchars($u->avatar) ?>" class="avatar-sm mr-2">
                        <?php else: ?>
                            <div class="avatar-sm-default mr-2"><?= strtoupper(mb_substr($u->name,0,1)) ?></div>
                        <?php endif; ?>
                        <div>
                            <div class="font-weight-bold" style="font-size:.93rem;"><?= htmlspecialchars($u->name) ?></div>
                            <?php if (!empty($u->phone)): ?>
                                <small class="text-muted"><?= htmlspecialchars($u->phone) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td style="font-size:.88rem;"><?= htmlspecialchars($u->email) ?></td>
                <td>
                    <?php if ($u->id === SessionHelper::getUserId()): ?>
                        <span class="badge-<?= $u->role ?>"><?= $u->role === 'admin' ? '👑 Admin' : '👤 User' ?></span>
                    <?php else: ?>
                    <form action="/project1/Admin/updateRole/<?= $u->id ?>" method="POST" class="d-inline">
                        <select name="role" class="form-control form-control-sm d-inline-block w-auto"
                                style="font-size:.82rem;padding:2px 6px;"
                                onchange="this.form.submit()">
                            <option value="user"  <?= $u->role === 'user'  ? 'selected' : '' ?>>👤 User</option>
                            <option value="admin" <?= $u->role === 'admin' ? 'selected' : '' ?>>👑 Admin</option>
                        </select>
                    </form>
                    <?php endif; ?>
                </td>
                <td>
                    <?php $active = ($u->is_active ?? 1); ?>
                    <span class="<?= $active ? 'badge-active' : 'badge-locked' ?>">
                        <?= $active ? '✓ Hoạt động' : '✗ Bị khóa' ?>
                    </span>
                </td>
                <td style="font-size:.85rem;"><?= date('d/m/Y', strtotime($u->created_at)) ?></td>
                <td>
                    <?php if ($u->id !== SessionHelper::getUserId()): ?>
                    <div class="d-flex gap-1 flex-wrap">
                        <!-- Khóa / Mở khóa -->
                        <a href="/project1/Admin/toggleActive/<?= $u->id ?>"
                           class="btn btn-action <?= $active ? 'btn-outline-warning' : 'btn-outline-success' ?>"
                           onclick="return confirm('<?= $active ? 'Khóa' : 'Mở khóa' ?> tài khoản này?')">
                            <i class="fa <?= $active ? 'fa-lock' : 'fa-lock-open' ?>"></i>
                            <?= $active ? 'Khóa' : 'Mở khóa' ?>
                        </a>
                        <!-- Xóa -->
                        <a href="/project1/Admin/deleteUser/<?= $u->id ?>"
                           class="btn btn-action btn-outline-danger"
                           onclick="return confirm('Bạn chắc chắn muốn xóa người dùng này?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <?php else: ?>
                        <span class="text-muted" style="font-size:.82rem;">(Tài khoản của bạn)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'app/views/shares/footer.php'; ?>
