<?php
require_once 'app/helpers/SessionHelper.php';
SessionHelper::start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2410KXK Store</title>

    <!-- Bootstrap -->
    <link rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="/project1/public/css/style.css">
</head>

<body>

<!-- TOPBAR -->
<div class="topbar">
    <div class="container d-flex justify-content-between align-items-center">

        <div class="topbar-left">
            <i class="fa-solid fa-phone"></i>
            Hotline: 1900 2410
        </div>

        <div class="topbar-right">
            <a href="#">
                <i class="fa-solid fa-location-dot"></i>
                Hệ thống cửa hàng
            </a>

            <a href="/project1/Product/orders">
                <i class="fa-solid fa-truck-fast"></i>
                Tra cứu đơn hàng
            </a>
        </div>

    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg custom-navbar">

    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand logo-text"
            href="/project1/Product/">
            2410KXK
        </a>

        <!-- MOBILE BUTTON -->
        <button class="navbar-toggler custom-toggler"
            type="button"
            data-toggle="collapse"
            data-target="#navbarNav">
            <i class="fa-solid fa-bars text-white"></i>
        </button>

        <!-- MENU -->
        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- SEARCH -->
            <form class="search-box mx-auto">
                <input type="text"
                    placeholder="Bạn cần tìm gì hôm nay...?">
                <button type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <!-- RIGHT NAV -->
            <ul class="navbar-nav ml-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link custom-link" href="/project1/Product/">
                        <i class="fa-solid fa-computer"></i>
                        Sản phẩm
                    </a>
                </li>

                <!-- GIỎ HÀNG -->
                <li class="nav-item">
                    <a class="nav-link cart-btn" href="/project1/Product/cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Giỏ hàng
                        <?php
                            $cartCount = 0;
                            if (isset($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $cartCount += $item['quantity'];
                                }
                            }
                        ?>
                        <span class="cart-count" id="cartCount"><?= $cartCount ?></span>
                    </a>
                </li>

                <?php if (SessionHelper::isLoggedIn()): ?>

                    <!-- ADMIN: nút quản lý -->
                    <?php if (SessionHelper::isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link custom-link" href="/project1/Product/add"
                           title="Thêm sản phẩm">
                            <i class="fa-solid fa-circle-plus"></i>
                            <span class="d-lg-none ml-1">Thêm SP</span>
                        </a>
                    </li>
                    <?php endif; ?>

                    <!-- DROPDOWN TÀI KHOẢN -->
                    <li class="nav-item dropdown">
                        <a class="nav-link custom-link dropdown-toggle" href="#"
                           id="userDropdown" data-toggle="dropdown">
                            <i class="fa-solid fa-circle-user"></i>
                            <?= htmlspecialchars(SessionHelper::getUserName()) ?>
                            <?php if (SessionHelper::isAdmin()): ?>
                                <span class="badge badge-warning ml-1" style="font-size:.7rem;">Admin</span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <?php if (SessionHelper::isAdmin()): ?>
                                <a class="dropdown-item" href="/project1/Product/add">
                                    <i class="fa-solid fa-plus mr-2 text-success"></i>Thêm sản phẩm
                                </a>
                                <div class="dropdown-divider"></div>
                            <?php endif; ?>
                            <a class="dropdown-item" href="/project1/Product/orders">
                                <i class="fa-solid fa-box mr-2 text-primary"></i>Đơn hàng của tôi
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="/project1/User/logout">
                                <i class="fa-solid fa-right-from-bracket mr-2"></i>Đăng xuất
                            </a>
                        </div>
                    </li>

                <?php else: ?>

                    <!-- CHƯA ĐĂNG NHẬP -->
                    <li class="nav-item">
                        <a class="nav-link custom-link" href="/project1/User/login">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Đăng nhập
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link custom-link" href="/project1/User/register">
                            <i class="fa-solid fa-user-plus"></i>
                            Đăng ký
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<div class="category-menu">
    <div class="container">
        <a href="/project1/Product/category/1">Điện thoại</a>
        <a href="/project1/Product/category/2">Laptop</a>
        <a href="/project1/Product/category/3">Máy tính bảng</a>
        <a href="/project1/Product/category/4">Phụ kiện</a>
        <a href="/project1/Product/category/5">Âm thanh</a>
        <a href="/project1/Product/category/6">RAM</a>
        <a href="/project1/Product/category/7">Fan LED</a>
        <a href="/project1/Product/category/8">VGA</a>
        <a href="/project1/Product/category/9">Nguồn</a>
        <a href="/project1/Product/category/10">Tản nhiệt</a>
        <a href="/project1/Product/category/11">Mainboard</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container py-4">
