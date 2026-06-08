<?php require_once 'app/helpers/SessionHelper.php'; SessionHelper::start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu – 2410KXK Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/project1/public/css/style.css">
    <style>
        body { background:#f4f6fb; }
        .auth-wrapper { min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .auth-card { background:#fff; border-radius:16px; box-shadow:0 4px 32px rgba(0,0,0,.10); padding:40px 36px; width:100%; max-width:460px; }
        .auth-logo { font-size:1.8rem; font-weight:800; color:#e63946; letter-spacing:1px; text-decoration:none; }
        .auth-logo:hover { color:#c1121f; text-decoration:none; }
        .auth-title { font-size:1.2rem; font-weight:700; color:#222; margin:8px 0 8px; }
        .auth-subtitle { font-size:.9rem; color:#888; margin-bottom:24px; }
        .form-control:focus { border-color:#e63946; box-shadow:0 0 0 .2rem rgba(230,57,70,.18); }
        .btn-auth { background:#e63946; color:#fff; font-weight:700; border:none; border-radius:8px; padding:10px; width:100%; font-size:1rem; }
        .btn-auth:hover { background:#c1121f; color:#fff; }
        .input-group-text { background:#f4f6fb; border-right:none; }
        .form-control { border-left:none; }
        .divider { border-top:1px solid #eee; margin:20px 0; }
        .auth-link { color:#e63946; font-weight:600; }
        .demo-box { background:#fff8e1; border:1px solid #ffd54f; border-radius:8px; padding:12px 16px; font-size:.88rem; }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <a class="auth-logo" href="/project1/Product">2410KXK</a>
        <div class="auth-title">Quên mật khẩu?</div>
        <div class="auth-subtitle">Nhập email đã đăng ký. Chúng tôi sẽ gửi link đặt lại mật khẩu cho bạn.</div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success py-2">
                <i class="fa fa-check-circle mr-1"></i><?= htmlspecialchars($success) ?>
            </div>
            <?php if (!empty($_SESSION['demo_reset_token'])): ?>
            <div class="demo-box mb-3">
                <strong>⚠️ Demo Mode:</strong> Trong môi trường thực, link sẽ gửi qua email.<br>
                <strong>Link test:</strong>
                <a href="/project1/User/resetPassword/<?= urlencode($_SESSION['demo_reset_token']) ?>" class="auth-link">
                    Đặt lại mật khẩu ngay
                </a>
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $err): ?>
                    <div><i class="fa fa-circle-exclamation mr-1"></i><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/project1/User/forgotPasswordPost" method="POST" novalidate>
            <div class="form-group">
                <label class="font-weight-bold">Email đăng ký</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-envelope text-muted"></i></span>
                    </div>
                    <input type="email" name="email" class="form-control"
                           placeholder="example@email.com" required autofocus>
                </div>
            </div>
            <button type="submit" class="btn-auth mt-1">
                <i class="fa fa-paper-plane mr-1"></i> Gửi link đặt lại
            </button>
        </form>

        <div class="divider"></div>
        <div class="text-center" style="font-size:.95rem;">
            Nhớ mật khẩu rồi?
            <a href="/project1/User/login" class="auth-link">Đăng nhập</a>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
