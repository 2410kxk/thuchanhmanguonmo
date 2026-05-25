<?php include 'app/views/shares/header.php'; ?>

<div class="page-header">

    <h1>
        <i class="fa-solid fa-computer"></i>
        Danh sách sản phẩm
    </h1>

    <a href="/project1/Product/add"
       class="btn btn-danger add-product-btn">

        <i class="fa-solid fa-plus"></i>
        Thêm sản phẩm mới

    </a>

</div>

<!-- PRODUCT GRID -->
<div class="product-grid">

<?php foreach ($products as $product): ?>

    <div class="product-card">

        <!-- IMAGE -->
        <div class="product-image">

            <?php if (!empty($product->image)): ?>

                <img src="/project1/<?php echo $product->image; ?>"
                     alt="Product Image">

            <?php else: ?>

                <img src="/project1/public/images/default-product.jpg"
                     alt="Default Product">

            <?php endif; ?>

        </div>

        <!-- CONTENT -->
        <div class="product-content">

            <h2>
                <a href="/project1/Product/show/<?php echo $product->id; ?>">

                    <?php
                        echo htmlspecialchars(
                            $product->name,
                            ENT_QUOTES,
                            'UTF-8'
                        );
                    ?>

                </a>
            </h2>

            <p class="product-description">

                <?php
                    echo htmlspecialchars(
                        $product->description,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>

            </p>

            <div class="product-category">

                <i class="fa-solid fa-layer-group"></i>

                <?php
                    echo htmlspecialchars(
                        $product->category_name,
                        ENT_QUOTES,
                        'UTF-8'
                    );
                ?>

            </div>

            <div class="product-price">

                <?php
                    echo number_format(
                        $product->price,
                        0,
                        ',',
                        '.'
                    );
                ?> đ

            </div>

            <!-- BUTTONS -->
            <div class="product-actions">

                <button class="btn btn-success add-cart-btn"
                        data-id="<?php echo $product->id; ?>"
                        data-name="<?php echo $product->name; ?>"
                        data-price="<?php echo $product->price; ?>">

                    <i class="fa-solid fa-cart-plus"></i>
                    Thêm giỏ hàng

                </button>

                <a href="/project1/Product/edit/<?php echo $product->id; ?>"
                   class="btn btn-warning">

                    <i class="fa-solid fa-pen"></i>
                    Sửa

                </a>

                <a href="/project1/Product/delete/<?php echo $product->id; ?>"
                   class="btn btn-danger"
                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">

                    <i class="fa-solid fa-trash"></i>
                    Xóa

                </a>

            </div>

        </div>

    </div>

<?php endforeach; ?>

</div>

<!-- MINI CART POPUP -->
<div class="cart-popup-overlay" id="cartOverlay">

    <div class="cart-popup">

        <h2 id="popupProductName">

            Sản phẩm

        </h2>

        <div class="popup-qty-box">

            <button class="popup-btn" id="minusBtn">

                -

            </button>

            <span id="popupQty">

                1

            </span>

            <button class="popup-btn" id="plusBtn">

                +

            </button>

        </div>

        <div class="popup-total">

            Tổng:
            <span id="popupTotal">

                0 đ

            </span>

        </div>

        <div class="popup-actions">

            <button class="confirm-btn"
                    id="confirmAdd">

                Đồng ý

            </button>

            <button class="cancel-btn"
                    id="cancelPopup">

                Huỷ

            </button>

        </div>

    </div>

</div>
<script>

let currentPrice = 0;

let currentQty = 1;

let currentProductId = 0;

let overlay =
    document.getElementById('cartOverlay');

let popupQty =
    document.getElementById('popupQty');

let popupTotal =
    document.getElementById('popupTotal');

let popupProductName =
    document.getElementById('popupProductName');

function updateTotal(){

    popupQty.innerText = currentQty;

    popupTotal.innerText =
        (currentPrice * currentQty)
        .toLocaleString('vi-VN') + ' đ';
}

document.querySelectorAll('.add-cart-btn')
.forEach(button => {

    button.addEventListener('click', function(){

        currentProductId = this.dataset.id;

        currentPrice = parseInt(this.dataset.price);

        currentQty = 1;

        popupProductName.innerText =
            this.dataset.name;

        updateTotal();

        overlay.style.display = 'flex';

    });

});

document.getElementById('plusBtn')
.addEventListener('click', function(){

    currentQty++;

    updateTotal();

});

document.getElementById('minusBtn')
.addEventListener('click', function(){

    currentQty--;

    if(currentQty < 0){

        currentQty = 0;
    }

    updateTotal();

});

document.getElementById('cancelPopup')
.addEventListener('click', function(){

    overlay.style.display = 'none';

});

overlay.addEventListener('click', function(e){

    if(e.target === overlay){

        overlay.style.display = 'none';
    }

});

document.getElementById('confirmAdd')
.addEventListener('click', function(){

    fetch('/project1/Product/addToCart/' +
        currentProductId +
        '?quantity=' + currentQty)

    .then(response => response.json())

    .then(data => {

overlay.style.display = 'none';

document.getElementById('cartCount')
    .innerText = data.totalQuantity;

});

});

</script>

<?php include 'app/views/shares/footer.php'; ?>