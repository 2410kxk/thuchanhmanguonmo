<?php
require_once 'app/helpers/SessionHelper.php';
include 'app/views/shares/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">
        <i class="fa-solid fa-computer"></i>
        Danh sách sản phẩm
    </h1>
    <?php if (SessionHelper::isAdmin()): ?>
    <a href="/project1/Product/add" class="btn add-product-btn">
        <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới
    </a>
    <?php endif; ?>
</div>

<!-- LOADING -->
<div id="loading-spinner" class="text-center py-5">
    <div class="spinner-border text-danger" role="status" style="width:3rem;height:3rem;">
        <span class="sr-only">Đang tải...</span>
    </div>
    <p class="mt-3" style="color:#94a3b8;">Đang tải danh sách sản phẩm...</p>
</div>

<!-- SEARCH & FILTER BAR -->
<div id="filter-bar" class="mb-4" style="display:none;">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text" style="background:#1e293b;border-color:rgba(255,255,255,.1);color:#fff;">
                    <i class="fa-solid fa-search"></i>
                </span>
                <input type="text" id="search-input" class="form-control" placeholder="Tìm kiếm sản phẩm...">
            </div>
        </div>
        <div class="col-md-3">
            <select id="category-filter" class="form-control">
                <option value="">-- Tất cả danh mục --</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="sort-filter" class="form-control">
                <option value="">-- Sắp xếp --</option>
                <option value="price_asc">Giá tăng dần</option>
                <option value="price_desc">Giá giảm dần</option>
                <option value="name_asc">Tên A-Z</option>
                <option value="name_desc">Tên Z-A</option>
            </select>
        </div>
    </div>
</div>

<!-- STATS BAR -->
<div id="stats-bar" class="mb-3" style="display:none;">
    <span style="color:#94a3b8;font-size:14px;">
        Hiển thị <strong id="product-count" style="color:#00ff99;">0</strong> sản phẩm
    </span>
</div>

<!-- PRODUCT GRID -->
<div class="product-grid" id="product-grid"></div>

<!-- EMPTY STATE -->
<div id="empty-state" class="text-center py-5" style="display:none;">
    <i class="fa-solid fa-box-open" style="font-size:60px;color:#334155;"></i>
    <p class="mt-3" style="color:#94a3b8;font-size:18px;">Không tìm thấy sản phẩm nào</p>
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

<!-- DELETE CONFIRM MODAL -->
<div class="cart-popup-overlay" id="deleteOverlay">
    <div class="cart-popup">
        <i class="fa-solid fa-triangle-exclamation" style="font-size:48px;color:#ff003c;margin-bottom:15px;"></i>
        <h2>Xác nhận xóa</h2>
        <p style="color:#94a3b8;margin:10px 0 25px;">Bạn có chắc chắn muốn xóa sản phẩm này không?</p>
        <div class="popup-actions">
            <button class="confirm-btn" id="confirmDelete" style="background:#ff003c;">Xóa ngay</button>
            <button class="cancel-btn" id="cancelDelete">Hủy bỏ</button>
        </div>
    </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:99999;"></div>

<style>
.product-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    background: rgba(255,0,60,0.15);
    color: #ff4d4d;
    margin-bottom: 10px;
}
.admin-actions { display: flex; gap: 8px; margin-top: 8px; }
.btn-edit { background: #f59e0b !important; color: #fff !important; padding: 8px 14px !important; font-size: 13px !important; }
.btn-delete { background: #ff003c !important; color: #fff !important; padding: 8px 14px !important; font-size: 13px !important; }
.btn-edit:hover { background: #d97706 !important; }
.btn-delete:hover { background: #cc0030 !important; }
.toast-msg { background:#1e293b; color:#fff; border-left:4px solid #00ff99; padding:15px 20px; border-radius:10px; margin-bottom:10px; box-shadow:0 4px 15px rgba(0,0,0,.4); animation: slideIn .3s ease; min-width:280px; }
.toast-msg.error { border-left-color: #ff003c; }
@keyframes slideIn { from { transform: translateX(100px); opacity:0; } to { transform: translateX(0); opacity:1; } }
.input-group-text { border-radius: 10px 0 0 10px !important; }
#search-input { border-radius: 0 10px 10px 0 !important; }
</style>

<script>
$(function () {
    // BASE URL - tự động lấy đúng host + port
    const BASE = window.location.protocol + '//' + window.location.host + '/project1';
    
    let allProducts = [];
    let deleteTargetId = null;
    let currentPrice = 0, currentQty = 1, currentProductId = 0;
    const isAdmin = <?= SessionHelper::isAdmin() ? 'true' : 'false' ?>;
    const isLoggedIn = <?= SessionHelper::isLoggedIn() ? 'true' : 'false' ?>;

    // ========== TOAST ==========
    function showToast(msg, type = 'success') {
        const t = $('<div>').addClass('toast-msg' + (type === 'error' ? ' error' : '')).html(
            `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-xmark'}" style="margin-right:8px;"></i>${msg}`
        );
        $('#toast-container').append(t);
        setTimeout(() => t.fadeOut(400, () => t.remove()), 3000);
    }

    // ========== FORMAT ==========
    function formatPrice(p) {
        return parseInt(p).toLocaleString('vi-VN') + ' đ';
    }

    // ========== RENDER CARD ==========
    function renderProduct(p) {
        const editBtn = isAdmin
            ? `<a href="/project1/Product/edit/${p.id}" class="btn btn-edit"><i class="fa-solid fa-pen"></i> Sửa</a>
               <button class="btn btn-delete delete-btn" data-id="${p.id}" data-name="${p.name}"><i class="fa-solid fa-trash"></i> Xóa</button>`
            : '';
        const cartBtn = isLoggedIn
            ? `<button class="btn btn-success add-cart-btn w-100" data-id="${p.id}" data-name="${p.name}" data-price="${p.price}">
                    <i class="fa-solid fa-cart-plus"></i> Thêm giỏ hàng
               </button>`
            : `<a href="/project1/User/login" class="btn btn-outline-success w-100">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập để mua
               </a>`;

        return `
        <div class="product-card" data-name="${p.name.toLowerCase()}" data-category="${(p.category_name||'').toLowerCase()}" data-price="${p.price}">
            <div class="product-image">
                <img src="${p.image ? '/project1/' + p.image : '/project1/public/images/default-product.jpg'}" alt="${p.name}" onerror="this.src='/project1/public/images/default-product.jpg'">
            </div>
            <div class="product-content">
                <span class="product-badge"><i class="fa-solid fa-tag"></i> ${p.category_name || 'Chưa phân loại'}</span>
                <h2><a href="/project1/Product/show/${p.id}">${p.name}</a></h2>
                <p class="product-description">${p.description}</p>
                <div class="product-price">${formatPrice(p.price)}</div>
                <div class="product-actions flex-column" style="gap:8px;">
                    ${cartBtn}
                    <div class="admin-actions">${editBtn}</div>
                </div>
            </div>
        </div>`;
    }

    // ========== FILTER & RENDER ==========
    function filterAndRender() {
        const search = $('#search-input').val().toLowerCase();
        const cat = $('#category-filter').val().toLowerCase();
        const sort = $('#sort-filter').val();

        let filtered = allProducts.filter(p => {
            const matchName = p.name.toLowerCase().includes(search) || p.description.toLowerCase().includes(search);
            const matchCat = !cat || (p.category_name || '').toLowerCase() === cat;
            return matchName && matchCat;
        });

        if (sort === 'price_asc') filtered.sort((a, b) => a.price - b.price);
        else if (sort === 'price_desc') filtered.sort((a, b) => b.price - a.price);
        else if (sort === 'name_asc') filtered.sort((a, b) => a.name.localeCompare(b.name));
        else if (sort === 'name_desc') filtered.sort((a, b) => b.name.localeCompare(a.name));

        $('#product-grid').html(filtered.map(renderProduct).join(''));
        $('#product-count').text(filtered.length);
        $('#empty-state').toggle(filtered.length === 0);
        $('#product-grid').toggle(filtered.length > 0);
        bindEvents();
    }

    // ========== LOAD PRODUCTS ==========
    $.ajax({
        url: BASE + '/api/product',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            if (!Array.isArray(data)) {
                $('#loading-spinner').html('<p style="color:#ff4d4d;">Lỗi: API không trả về dữ liệu hợp lệ.<br><small style="color:#94a3b8;">Kiểm tra kết nối database trong database.php</small></p>');
                return;
            }
            allProducts = data;
            $('#loading-spinner').hide();
            $('#filter-bar, #stats-bar').show();

            if (data.length === 0) {
                $('#loading-spinner').hide();
                $('#empty-state').show();
                return;
            }

            // Populate category filter
            const cats = [...new Set(data.map(p => p.category_name).filter(Boolean))];
            cats.forEach(c => $('#category-filter').append(`<option value="${c.toLowerCase()}">${c}</option>`));

            filterAndRender();
        },
        error: function (xhr, status, err) {
            $('#loading-spinner').html(`
                <i class="fa-solid fa-triangle-exclamation" style="font-size:48px;color:#ff003c;"></i>
                <p class="mt-3" style="color:#ff4d4d;font-size:18px;">Không thể tải sản phẩm!</p>
                <p style="color:#94a3b8;font-size:14px;">Kiểm tra: <br>
                1. Database <strong style="color:#fff;">my_store</strong> đã tồn tại chưa?<br>
                2. Apache và MySQL đang chạy chưa?<br>
                3. File .htaccess đã đúng chưa?</p>
                <button onclick="location.reload()" class="btn add-product-btn mt-3">
                    <i class="fa-solid fa-rotate-right"></i> Thử lại
                </button>
            `);
        }
    });

    // ========== SEARCH & FILTER EVENTS ==========
    $('#search-input').on('input', filterAndRender);
    $('#category-filter, #sort-filter').on('change', filterAndRender);

    // Đọc tham số ?q= từ URL (từ thanh tìm kiếm header)
    const urlParams = new URLSearchParams(window.location.search);
    const qParam = urlParams.get('q');
    if (qParam) {
        $('#search-input').val(qParam);
        // filterAndRender sẽ tự chạy sau khi AJAX load xong products
    }

    // ========== BIND CARD EVENTS ==========
    function bindEvents() {
        // Cart popup
        $('.add-cart-btn').off('click').on('click', function () {
            currentProductId = $(this).data('id');
            currentPrice = parseInt($(this).data('price'));
            currentQty = 1;
            $('#popupProductName').text($(this).data('name'));
            updatePopupTotal();
            $('#cartOverlay').css('display', 'flex');
        });

        // Delete btn
        $('.delete-btn').off('click').on('click', function () {
            deleteTargetId = $(this).data('id');
            $('#deleteOverlay').css('display', 'flex');
        });
    }

    // ========== CART POPUP ==========
    function updatePopupTotal() {
        $('#popupQty').text(currentQty);
        $('#popupTotal').text(formatPrice(currentPrice * currentQty));
    }
    $('#plusBtn').on('click', () => { currentQty++; updatePopupTotal(); });
    $('#minusBtn').on('click', () => { if (currentQty > 1) { currentQty--; updatePopupTotal(); } });
    $('#cancelPopup').on('click', () => $('#cartOverlay').hide());
    $('#cartOverlay').on('click', function (e) { if ($(e.target).is('#cartOverlay')) $(this).hide(); });

    $('#confirmAdd').on('click', function () {
        $.ajax({
            url: `${BASE}/Product/addToCart/${currentProductId}?quantity=${currentQty}`,
            method: 'GET',
            success: function (data) {
                $('#cartOverlay').hide();
                $('#cartCount').text(data.totalQuantity);
                showToast('Đã thêm sản phẩm vào giỏ hàng!');
            },
            error: function () {
                showToast('Thêm giỏ hàng thất bại!', 'error');
            }
        });
    });

    // ========== DELETE ==========
    $('#cancelDelete').on('click', () => $('#deleteOverlay').hide());
    $('#deleteOverlay').on('click', function (e) { if ($(e.target).is('#deleteOverlay')) $(this).hide(); });

    $('#confirmDelete').on('click', function () {
        if (!deleteTargetId) return;
        $.ajax({
            url: `${BASE}/api/product/${deleteTargetId}`,
            method: 'DELETE',
            success: function (data) {
                $('#deleteOverlay').hide();
                if (data.message === 'Product deleted successfully') {
                    allProducts = allProducts.filter(p => p.id != deleteTargetId);
                    filterAndRender();
                    showToast('Đã xóa sản phẩm thành công!');
                } else {
                    showToast('Xóa sản phẩm thất bại!', 'error');
                }
                deleteTargetId = null;
            },
            error: function () {
                $('#deleteOverlay').hide();
                showToast('Xóa sản phẩm thất bại!', 'error');
            }
        });
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
