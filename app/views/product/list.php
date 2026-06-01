<?php
require_once 'app/helpers/SessionHelper.php';
include 'app/views/shares/header.php';
?>

<div class="page-header">

    <h1>
        <i class="fa-solid fa-computer"></i>
        Danh sách sản phẩm
    </h1>

    <?php if (SessionHelper::isAdmin()): ?>
    <a href="/project1/Product/add" class="btn btn-danger add-product-btn">
        <i class="fa-solid fa-plus"></i>
        Thêm sản phẩm mới
    </a>
    <?php endif; ?>

</div>

<!-- PRODUCT GRID -->
<div class="product-grid">

<?php foreach ($products as $product): ?>

    <div class="product-card">

        <!-- IMAGE -->
        <div class="product-image">
            <?php if (!empty($product->image)): ?>
                <img src="/project1/<?= $product->image ?>" alt="Product Image">
            <?php else: ?>
                <img src="/project1/public/images/default-product.jpg" alt="Default Product">
            <?php endif; ?>
        </div>

        <!-- CONTENT -->
        <div class="product-content">

            <h2>
                <a href="/project1/Product/show/<?= $product->id ?>">
                    <?= htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </h2>

            <p class="product-description">
                <?= htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8') ?>
            </p>

            <div class="product-category">
                <i class="fa-solid fa-layer-group"></i>
                <?= htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8') ?>
            </div>

            <div class="product-price">
                <?= number_format($product->price, 0, ',', '.') ?> đ
            </div>

            <!-- BUTTONS -->
            <div class="product-actions">

                <!-- Thêm giỏ hàng: tất cả user đã đăng nhập -->
                <?php if (SessionHelper::isLoggedIn()): ?>
                <button class="btn btn-success add-cart-btn"
                        data-id="<?= $product->id ?>"
                        data-name="<?= htmlspecialchars($product->name, ENT_QUOTES) ?>"
                        data-price="<?= $product->price ?>">
                    <i class="fa-solid fa-cart-plus"></i>
                    Thêm giỏ hàng
                </button>
                <?php else: ?>
                <a href="/project1/User/login" class="btn btn-outline-success">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Đăng nhập để mua
                </a>
                <?php endif; ?>

                <!-- Sửa / Xóa: chỉ Admin -->
                <?php if (SessionHelper::isAdmin()): ?>
                <a href="/project1/Product/edit/<?= $product->id ?>" class="btn btn-warning">
                    <i class="fa-solid fa-pen"></i> Sửa
                </a>
                <a href="/project1/Product/delete/<?= $product->id ?>"
                   class="btn btn-danger"
                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                    <i class="fa-solid fa-trash"></i> Xóa
                </a>
                <?php endif; ?>

            </div>

        </div>

    </div>

<?php endforeach; ?>

</div>

<!-- MINI CART POPUP -->
<div class="cart-popup-overlay" id="cartOverlay">
    <div class="cart-popup">
        <h2 id="popupProductName">Sản phẩm</h2>
        <div class="popup-qty-box">
            <button class="popup-btn" id="minusBtn">-</button>
            <span id="popupQty">1</span>
            <button class="popup-btn" id="plusBtn">+</button>
        </div>
        <div class="popup-total">Tổng: <span id="popupTotal">0 đ</span></div>
        <div class="popup-actions">
            <button class="confirm-btn" id="confirmAdd">Đồng ý</button>
            <button class="cancel-btn" id="cancelPopup">Huỷ</button>
        </div>
    </div>
</div>

<script>
let currentPrice = 0, currentQty = 1, currentProductId = 0;
const overlay          = document.getElementById('cartOverlay');
const popupQty         = document.getElementById('popupQty');
const popupTotal       = document.getElementById('popupTotal');
const popupProductName = document.getElementById('popupProductName');

function updateTotal(){
    popupQty.innerText   = currentQty;
    popupTotal.innerText = (currentPrice * currentQty).toLocaleString('vi-VN') + ' đ';
}

document.querySelectorAll('.add-cart-btn').forEach(btn => {
    btn.addEventListener('click', function(){
        currentProductId = this.dataset.id;
        currentPrice     = parseInt(this.dataset.price);
        currentQty       = 1;
        popupProductName.innerText = this.dataset.name;
        updateTotal();
        overlay.style.display = 'flex';
    });
});

document.getElementById('plusBtn').addEventListener('click',  () => { currentQty++; updateTotal(); });
document.getElementById('minusBtn').addEventListener('click', () => { if(currentQty > 1) currentQty--; updateTotal(); });
document.getElementById('cancelPopup').addEventListener('click', () => overlay.style.display = 'none');
overlay.addEventListener('click', e => { if(e.target === overlay) overlay.style.display = 'none'; });

document.getElementById('confirmAdd').addEventListener('click', function(){
    fetch('/project1/Product/addToCart/' + currentProductId + '?quantity=' + currentQty)
    .then(r => r.json())
    .then(data => {
        overlay.style.display = 'none';
        document.getElementById('cartCount').innerText = data.totalQuantity;
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
