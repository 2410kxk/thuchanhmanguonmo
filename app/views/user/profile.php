<?php require_once 'app/helpers/SessionHelper.php'; SessionHelper::requireLogin(); ?>
<?php include 'app/views/shares/header.php'; ?>

<style>
.profile-card { background:#fff; border-radius:16px; box-shadow:0 2px 20px rgba(0,0,0,.08); overflow:hidden; }
.profile-sidebar { background: linear-gradient(135deg,#e63946,#c1121f); color:#fff; padding:40px 24px; text-align:center; }
.avatar-wrap { position:relative; display:inline-block; margin-bottom:16px; }
.avatar-img { width:110px; height:110px; border-radius:50%; object-fit:cover; border:4px solid rgba(255,255,255,.4); }
.avatar-default { width:110px; height:110px; border-radius:50%; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:2.8rem; border:4px solid rgba(255,255,255,.4); }
.avatar-edit-btn { position:absolute; bottom:4px; right:4px; background:#fff; border:none; border-radius:50%; width:32px; height:32px; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(0,0,0,.2); }
.avatar-edit-btn i { color:#e63946; font-size:.85rem; }
.role-badge { display:inline-block; padding:3px 12px; border-radius:20px; font-size:.78rem; font-weight:700; margin-top:6px; }
.role-admin { background:rgba(255,255,255,.25); }
.role-user  { background:rgba(255,255,255,.15); }
.profile-body { padding:32px; }
.section-title { font-size:1rem; font-weight:700; color:#444; border-bottom:2px solid #f0f0f0; padding-bottom:8px; margin-bottom:20px; }
.form-control:focus { border-color:#e63946; box-shadow:0 0 0 .2rem rgba(230,57,70,.15); }
.btn-save { background:#e63946; color:#fff; border:none; border-radius:8px; padding:10px 28px; font-weight:700; }
.btn-save:hover { background:#c1121f; color:#fff; }
.info-item { display:flex; align-items:center; gap:8px; font-size:.93rem; margin-bottom:6px; color:rgba(255,255,255,.85); }
.side-links a { display:flex; align-items:center; gap:10px; padding:10px 16px; border-radius:8px; text-decoration:none; color:#555; font-size:.93rem; transition:background .15s; margin-bottom:4px; }
.side-links a:hover, .side-links a.active { background:#fff0f0; color:#e63946; text-decoration:none; }
.side-links a i { width:18px; text-align:center; }
</style>

<div class="row">
    <!-- SIDEBAR -->
    <div class="col-lg-3 mb-4">
        <div class="profile-card">
            <div class="profile-sidebar">
                <div class="avatar-wrap">
                    <?php if (!empty($user->avatar)): ?>
                        <img src="/project1/<?= htmlspecialchars($user->avatar) ?>" class="avatar-img" alt="Avatar">
                    <?php else: ?>
                        <div class="avatar-default"><i class="fa-solid fa-user"></i></div>
                    <?php endif; ?>
                    <button class="avatar-edit-btn" onclick="document.getElementById('avatarInput').click()" title="Đổi ảnh đại diện">
                        <i class="fa-solid fa-camera"></i>
                    </button>
                    <form id="avatarForm" action="/project1/User/avatarPost" method="POST" enctype="multipart/form-data" style="display:none">
                        <input type="file" id="avatarInput" name="avatar" accept="image/*"
                               onchange="document.getElementById('avatarForm').submit()">
                    </form>
                </div>
                <div style="font-size:1.15rem; font-weight:800;"><?= htmlspecialchars($user->name) ?></div>
                <div class="role-badge <?= $user->role === 'admin' ? 'role-admin' : 'role-user' ?>">
                    <?= $user->role === 'admin' ? '👑 Admin' : '👤 User' ?>
                </div>
                <?php if (!empty($user->email)): ?>
                <div class="info-item mt-3"><i class="fa fa-envelope"></i><?= htmlspecialchars($user->email) ?></div>
                <?php endif; ?>
                <?php if (!empty($user->phone)): ?>
                <div class="info-item"><i class="fa fa-phone"></i><?= htmlspecialchars($user->phone) ?></div>
                <?php endif; ?>
                <div class="info-item"><i class="fa fa-calendar"></i>Tham gia: <?= date('d/m/Y', strtotime($user->created_at)) ?></div>
            </div>
            <div class="p-3 side-links">
                <a href="/project1/User/profile" class="active">
                    <i class="fa fa-user-pen"></i> Hồ sơ cá nhân
                </a>
                <a href="/project1/User/changePassword">
                    <i class="fa fa-lock"></i> Đổi mật khẩu
                </a>
                <a href="/project1/Product/orders">
                    <i class="fa fa-box"></i> Đơn hàng của tôi
                </a>
                <?php if (SessionHelper::isAdmin()): ?>
                <hr class="my-2">
                <a href="/project1/Admin/users">
                    <i class="fa fa-users-gear"></i> Quản lý người dùng
                </a>
                <?php endif; ?>
                <hr class="my-2">
                <a href="/project1/User/logout" style="color:#dc3545;">
                    <i class="fa fa-right-from-bracket"></i> Đăng xuất
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="col-lg-9">
        <div class="profile-card profile-body">
            <div class="section-title"><i class="fa fa-user-pen mr-2 text-danger"></i>Cập nhật hồ sơ</div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><i class="fa fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e): ?><div><i class="fa fa-exclamation-circle mr-1"></i><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="/project1/User/profilePost" method="POST">

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($user->name) ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" class="form-control"
                               value="<?= htmlspecialchars($user->email) ?>" disabled>
                        <small class="text-muted">Email không thể thay đổi.</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= htmlspecialchars($user->phone ?? '') ?>"
                               placeholder="0901 234 567">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-bold">Vai trò</label>
                        <input type="text" class="form-control"
                               value="<?= $user->role === 'admin' ? 'Quản trị viên' : 'Người dùng' ?>" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Địa chỉ</label>
                    <textarea name="address" class="form-control" rows="2"
                              placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"><?= htmlspecialchars($user->address ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-save">
                    <i class="fa fa-floppy-disk mr-1"></i> Lưu thay đổi
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
