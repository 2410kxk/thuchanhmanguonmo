<?php require_once 'app/helpers/SessionHelper.php'; SessionHelper::start(); ?>
</div>

<footer class="custom-footer">

    <div class="container">

        <div class="row">

            <!-- LEFT -->
            <div class="col-lg-6 mb-4">
                <h5>QUẢN LÝ SẢN PHẨM</h5>
                <p>
                    Hệ thống quản lý sản phẩm giúp bạn theo dõi
                    và cập nhật thông tin sản phẩm dễ dàng.
                </p>
            </div>

            <!-- CENTER -->
            <div class="col-lg-3 mb-4">
                <h5>LIÊN KẾT NHANH</h5>
                <ul class="footer-links">
                    <li>
                        <a href="/project1/Product/">Danh sách sản phẩm</a>
                    </li>
                    <?php if (SessionHelper::isAdmin()): ?>
                    <li>
                        <a href="/project1/Product/add">Thêm sản phẩm</a>
                    </li>
                    <?php endif; ?>
                    <?php if (SessionHelper::isLoggedIn()): ?>
                    <li>
                        <a href="/project1/Product/orders">Đơn hàng của tôi</a>
                    </li>
                    <li>
                        <a href="/project1/User/logout" class="text-danger">Đăng xuất</a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="/project1/User/login">Đăng nhập</a>
                    </li>
                    <li>
                        <a href="/project1/User/register">Đăng ký</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- RIGHT -->
            <div class="col-lg-3 mb-4">
                <h5>KẾT NỐI VỚI CHÚNG TÔI</h5>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

        </div>

    </div>

    <!-- COPYRIGHT -->
    <div class="footer-bottom">
        © 2025 2410KXK. All rights reserved.
    </div>

</footer>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
