<?php include 'app/views/shares/header.php'; ?>

<h1>

    <i class="fa-solid fa-cart-shopping"></i>
    Giỏ hàng

</h1>

<?php if(empty($cart)): ?>

    <div class="alert alert-warning">

        Chưa có sản phẩm trong giỏ hàng.

    </div>

<?php else: ?>

    <div class="cart-container">

        <?php
            $total = 0;
        ?>

        <?php foreach($cart as $id => $item): ?>

            <?php
                $subtotal =
                    $item['price'] * $item['quantity'];

                $total += $subtotal;
            ?>

            <div class="cart-item"
                 data-id="<?php echo $id; ?>">

                <img src="/project1/<?php echo $item['image']; ?>"
                     alt="">

                <div class="cart-info">

                    <h3>

                        <?php echo $item['name']; ?>

                    </h3>

                    <p>

                        Giá:
                        <?php
                            echo number_format(
                                $item['price'],
                                0,
                                ',',
                                '.'
                            );
                        ?> đ

                    </p>

                    <!-- QUANTITY -->
                    <div class="cart-qty-box">

                        <button class="cart-qty-btn minus-btn">

                            -

                        </button>

                        <span class="cart-qty">

                            <?php echo $item['quantity']; ?>

                        </span>

                        <button class="cart-qty-btn plus-btn">

                            +

                        </button>

                    </div>

                    <p class="cart-subtotal">

                        Thành tiền:
                        <span class="subtotal-text">

                            <?php
                                echo number_format(
                                    $subtotal,
                                    0,
                                    ',',
                                    '.'
                                );
                            ?> đ

                        </span>

                    </p>

                    <!-- DELETE -->
                    <button class="remove-btn">

                        <i class="fa-solid fa-trash"></i>
                        Xóa sản phẩm

                    </button>

                </div>

            </div>

        <?php endforeach; ?>

        <div class="cart-total">

            Tổng tiền:
            <span id="cartTotal">

                <?php
                    echo number_format(
                        $total,
                        0,
                        ',',
                        '.'
                    );
                ?> đ

            </span>

        </div>
        <a href="/project1/Product/checkout"
   class="checkout-btn">

    <i class="fa-solid fa-credit-card"></i>
    Thanh toán

</a>

    </div>

<?php endif; ?>

<script>

function updateCartTotal(){

    let total = 0;

    document.querySelectorAll('.cart-item')
    .forEach(item => {

        let qty = parseInt(
            item.querySelector('.cart-qty').innerText
        );

        let priceText =
            item.querySelector('.cart-info p')
            .innerText;

        let price =
            parseInt(
                priceText.replace(/[^\d]/g,'')
            );

        total += qty * price;

    });

    document.getElementById('cartTotal')
    .innerText =
        total.toLocaleString('vi-VN') + ' đ';
}

document.querySelectorAll('.cart-item')
.forEach(item => {

    let id = item.dataset.id;

    let qtyText =
        item.querySelector('.cart-qty');

    let subtotalText =
        item.querySelector('.subtotal-text');

    let plusBtn =
        item.querySelector('.plus-btn');

    let minusBtn =
        item.querySelector('.minus-btn');

    let removeBtn =
        item.querySelector('.remove-btn');

    let priceText =
        item.querySelector('.cart-info p')
        .innerText;

    let price =
        parseInt(
            priceText.replace(/[^\d]/g,'')
        );

    // PLUS
    plusBtn.addEventListener('click', () => {

        fetch('/project1/Product/updateCart?id='
            + id + '&action=plus')

        .then(response => response.json())

        .then(data => {

            let qty =
                parseInt(qtyText.innerText);

            qty++;

            qtyText.innerText = qty;

            subtotalText.innerText =
                (qty * price)
                .toLocaleString('vi-VN') + ' đ';

            document.getElementById('cartCount')
            .innerText = data.totalQuantity;

            updateCartTotal();

        });

    });

    // MINUS
    minusBtn.addEventListener('click', () => {

        fetch('/project1/Product/updateCart?id='
            + id + '&action=minus')

        .then(response => response.json())

        .then(data => {

            let qty =
                parseInt(qtyText.innerText);

            qty--;

            if(qty <= 0){

                item.remove();

                document.getElementById('cartCount')
                .innerText = data.totalQuantity;

                updateCartTotal();

                return;
            }

            qtyText.innerText = qty;

            subtotalText.innerText =
                (qty * price)
                .toLocaleString('vi-VN') + ' đ';

            document.getElementById('cartCount')
            .innerText = data.totalQuantity;

            updateCartTotal();

        });

    });

    // REMOVE
    removeBtn.addEventListener('click', () => {

        fetch('/project1/Product/removeCart?id='
            + id)

        .then(response => response.json())

        .then(data => {

            item.remove();

            document.getElementById('cartCount')
            .innerText = data.totalQuantity;

            updateCartTotal();

        });

    });

});

</script>

<?php include 'app/views/shares/footer.php'; ?>