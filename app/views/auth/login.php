<?php require_once 'app/helpers/SessionHelper.php'; SessionHelper::start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập – 2410KXK Store</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/project1/public/css/style.css">
    <style>
        body { background: #f4f6fb; }
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 32px rgba(0,0,0,.10);
            padding: 40px 36px;
            width: 100%;
            max-width: 440px;
        }
        .auth-logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: #e63946;
            letter-spacing: 1px;
            text-decoration: none;
        }
        .auth-logo:hover { color: #c1121f; text-decoration: none; }
        .auth-title { font-size: 1.3rem; font-weight: 700; color: #222; margin: 8px 0 24px; }
        .form-control:focus { border-color: #e63946; box-shadow: 0 0 0 .2rem rgba(230,57,70,.18); }
        .btn-auth {
            background: #e63946; color: #fff; font-weight: 700;
            border: none; border-radius: 8px; padding: 10px;
            width: 100%; font-size: 1rem; transition: background .2s;
        }
        .btn-auth:hover { background: #c1121f; color: #fff; }
        .input-group-text { background: #f4f6fb; border-right: none; }
        .form-control { border-left: none; }
        .input-group .form-control:focus { border-color: #e63946; }
        .divider { border-top: 1px solid #eee; margin: 20px 0; }
        .auth-link { color: #e63946; font-weight: 600; }
        .auth-link:hover { color: #c1121f; }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">

        <a class="auth-logo" href="/project1/Product">2410KXK</a>
        <div class="auth-title">Đăng nhập tài khoản</div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $err): ?>
                    <div><i class="fa fa-circle-exclamation mr-1"></i><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/project1/User/loginPost" method="POST" novalidate>

            <div class="form-group">
                <label for="email" class="font-weight-600">Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-envelope text-muted"></i></span>
                    </div>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="example@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="font-weight-600">Mật khẩu</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-lock text-muted"></i></span>
                    </div>
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="Nhập mật khẩu" required>
                </div>
            </div>

            <button type="submit" class="btn-auth mt-2">
                <i class="fa fa-right-to-bracket mr-1"></i> Đăng nhập
            </button>

        </form>

        <div class="divider"></div>

        <div class="text-center" style="font-size:.95rem;">
            Chưa có tài khoản?
            <a href="/project1/User/register" class="auth-link">Đăng ký ngay</a>
        </div>

        <div class="text-center mt-2">
            <a href="/project1/Product" class="text-muted" style="font-size:.88rem;">
                <i class="fa fa-arrow-left mr-1"></i>Quay lại cửa hàng
            </a>
        </div>

    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
