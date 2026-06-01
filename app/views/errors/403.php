<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>403 – Không có quyền truy cập</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/project1/public/css/style.css">
    <style>
        body { background: #f4f6fb; }
        .err-wrap { min-height: 80vh; display: flex; align-items: center; justify-content: center; }
        .err-code { font-size: 6rem; font-weight: 900; color: #e63946; line-height: 1; }
        .err-msg  { font-size: 1.3rem; color: #444; margin-bottom: 24px; }
    </style>
</head>
<body>
<?php require_once 'app/helpers/SessionHelper.php'; SessionHelper::start(); ?>
<?php include 'app/views/shares/header.php'; ?>
<div class="err-wrap text-center">
    <div>
        <div class="err-code">403</div>
        <div class="err-msg">Bạn không có quyền thực hiện thao tác này.</div>
        <a href="/project1/Product" class="btn btn-danger px-4">
            <i class="fa fa-house mr-1"></i> Về trang chủ
        </a>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>
</body>
</html>
