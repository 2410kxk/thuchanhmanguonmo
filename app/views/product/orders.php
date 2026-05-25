<?php include 'app/views/shares/header.php'; ?>

<h1>

    <i class="fa-solid fa-truck"></i>
    Tra cứu đơn hàng

</h1>

<?php if(empty($orders)): ?>

    <div class="alert alert-warning">

        Chưa có đơn hàng nào.

    </div>

<?php else: ?>

    <div class="orders-container">

        <?php foreach($orders as $order): ?>

            <div class="order-card">

                <h3>

                    Mã đơn:
                    #<?php echo $order['code']; ?>

                </h3>

                <p>

                    Ngày đặt:
                    <?php echo $order['created_at']; ?>

                </p>

                <p>

                    Trạng thái:
                    <span class="order-status">

                        <?php echo $order['status']; ?>

                    </span>

                </p>

                <hr>

                <?php foreach($order['items'] as $item): ?>

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

                    Tổng:
                    <?php
                        echo number_format(
                            $order['total'],
                            0,
                            ',',
                            '.'
                        );
                    ?> đ

                </div>

                <div class="order-actions">

<a href="/project1/Product/deleteOrder?code=<?php echo $order['code']; ?>"
   class="remove-btn">

    <i class="fa-solid fa-trash"></i>
    Xóa đơn hàng

</a>

</div>

            </div>

        <?php endforeach; ?>

    </div>

<?php endif; ?>

<?php include 'app/views/shares/footer.php'; ?>