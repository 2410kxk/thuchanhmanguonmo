<?php include 'app/views/shares/header.php'; ?>

<h1>

    <i class="fa-solid fa-credit-card"></i>
    Thanh toán đơn hàng

</h1>

<?php

$total = 0;

foreach($cart as $item){

    $total +=
        $item['price'] * $item['quantity'];
}

?>

<div class="checkout-container">

    <div class="checkout-form">

        <div class="form-group">

            <label>

                Họ và tên

            </label>

            <input type="text"
                   class="form-control">

        </div>

        <div class="form-group">

            <label>

                Số điện thoại

            </label>

            <input type="text"
                   class="form-control">

        </div>

        <div class="form-group">

            <label>

                Địa chỉ

            </label>

            <textarea class="form-control"></textarea>

        </div>

        <div class="form-group">

            <label>

                Ghi chú

            </label>

            <textarea class="form-control"></textarea>

        </div>

        <div class="form-group">

            <label>

                Mã giảm giá

            </label>

            <input type="text"
                   id="couponCode"
                   class="form-control"
                   placeholder="Nhập mã giảm giá">

        </div>

    </div>

    <div class="checkout-summary">

        <h3>

            Đơn hàng của bạn

        </h3>

        <?php foreach($cart as $item): ?>

            <div class="summary-item">

                <span>

                    <?php echo $item['name']; ?>
                    x<?php echo $item['quantity']; ?>

                </span>

                <span>

                    <?php
                        echo number_format(
                            $item['price']
                            * $item['quantity'],
                            0,
                            ',',
                            '.'
                        );
                    ?> đ

                </span>

            </div>

        <?php endforeach; ?>

        <div class="summary-total">

            Tổng tiền:
            <span id="finalTotal">

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

        

<div class="payment-method">

    <h3>
        Chọn phương thức thanh toán
    </h3>

    <label class="payment-option">

        <input type="radio"
               name="payment"
               value="qr"
               checked>

        Thanh toán QR

    </label>

    <div class="qr-box" id="qrBox">

    <img src="/project1/public/images/qr.png"
         alt="QR Code">

    <p>

        Quét mã QR để thanh toán

    </p>

</div>

    <label class="payment-option">

        <input type="radio"
               name="payment"
               value="cash">

        Thanh toán tiền mặt

    </label>

    <label class="payment-option">

        <input type="radio"
               name="payment"
               value="atm">

        Thanh toán ATM

    </label>

</div>

<button type="button"
        class="place-order-btn"
        id="openOrderPopup">

    Đặt hàng

</button>

    </div>

</div>


<div class="order-popup-overlay"
     id="orderPopup">

    <div class="order-popup">

        <h2>

            Bạn có chắc chắn muốn
            đặt đơn hàng này không?

        </h2>

        <div class="popup-actions">

            <button class="confirm-btn"
                    id="confirmOrderBtn">

                Đồng ý

            </button>

            <button class="cancel-btn"
                    id="cancelOrderBtn">

                Huỷ

            </button>

        </div>

    </div>

</div>




<div class="success-popup-overlay"
     id="successPopup">

    <div class="success-popup">

        <div class="success-icon">

            ✓

        </div>

        <h2>

            Bạn đã đặt hàng thành công

        </h2>

        <button class="confirm-btn"
                id="closeSuccessBtn">

            OK

        </button>

    </div>

</div>
<script>

let originalTotal = <?php echo $total; ?>;

document.getElementById('couponCode')
.addEventListener('input', function(){

    let code =
        this.value.trim();

    let finalTotal =
        originalTotal;

    if(code === '2410kxk'){

        finalTotal =
            originalTotal * 0.8;
    }

    document.getElementById('finalTotal')
    .innerText =
        finalTotal.toLocaleString('vi-VN')
        + ' đ';

});


const orderPopup =
    document.getElementById('orderPopup');

const successPopup =
    document.getElementById('successPopup');

const openOrderPopup =
    document.getElementById('openOrderPopup');

const confirmOrderBtn =
    document.getElementById('confirmOrderBtn');

const cancelOrderBtn =
    document.getElementById('cancelOrderBtn');

const closeSuccessBtn =
    document.getElementById('closeSuccessBtn');


/* OPEN CONFIRM */

openOrderPopup.addEventListener('click', () => {

    orderPopup.style.display = 'flex';

});


/* CANCEL */

cancelOrderBtn.addEventListener('click', () => {

    orderPopup.style.display = 'none';

});


/* CONFIRM */

confirmOrderBtn.addEventListener('click', () => {

fetch('/project1/Product/placeOrder', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        coupon: document.getElementById('couponCode').value,
        total: document.getElementById('finalTotal').innerText
    })
})
.then(response => response.json())
.then(data => {

    orderPopup.style.display = 'none';
    successPopup.style.display = 'flex';

});

});


/* CLOSE SUCCESS */

closeSuccessBtn.addEventListener('click', () => {

    successPopup.style.display = 'none';

    window.location.href =
        '/project1/Product';

});
const paymentMethods =
    document.querySelectorAll(
        'input[name="payment"]'
    );

const qrBox =
    document.getElementById('qrBox');

paymentMethods.forEach(method => {

    method.addEventListener('change', () => {

        if(method.value === 'qr'){

            qrBox.style.display = 'block';

        }else{

            qrBox.style.display = 'none';

        }

    });

});
</script>



<?php include 'app/views/shares/footer.php'; ?>