<?php
require_once 'app/helpers/SessionHelper.php';
include 'app/views/shares/header.php';
?>

<div class="d-flex align-items-center mb-4" style="gap:15px;">
    <a href="/project1/Product/" class="back-btn btn" style="width:auto;padding:12px 18px;">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <h1 class="mb-0" style="flex:1;">
        <i class="fa-solid fa-pen-to-square"></i>
        Chỉnh sửa sản phẩm
    </h1>
</div>

<!-- LOADING STATE -->
<div id="loading-form" class="text-center py-5">
    <div class="spinner-border text-danger" style="width:3rem;height:3rem;"></div>
    <p class="mt-3" style="color:#94a3b8;">Đang tải thông tin sản phẩm...</p>
</div>

<!-- FORM CONTENT (hidden until loaded) -->
<div id="form-content" style="display:none;">
    <div class="row">
        <div class="col-lg-8">
            <form id="edit-product-form" novalidate>
                <input type="hidden" id="product-id">

                <!-- STATUS BAR -->
                <div class="mb-3 p-3" style="background:#1e293b;border-radius:12px;border-left:4px solid #f59e0b;">
                    <small style="color:#94a3b8;">Đang chỉnh sửa sản phẩm ID: </small>
                    <strong id="display-id" style="color:#f59e0b;"></strong>
                </div>

                <!-- TÊN -->
                <div class="form-group mb-3">
                    <label for="name">
                        <i class="fa-solid fa-box" style="color:#ff4d4d;margin-right:6px;"></i>
                        Tên sản phẩm <span style="color:#ff003c;">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Nhập tên sản phẩm...">
                    <div class="invalid-feedback" id="err-name"></div>
                </div>

                <!-- MÔ TẢ -->
                <div class="form-group mb-3">
                    <label for="description">
                        <i class="fa-solid fa-file-lines" style="color:#ff4d4d;margin-right:6px;"></i>
                        Mô tả <span style="color:#ff003c;">*</span>
                    </label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Nhập mô tả..."></textarea>
                    <div class="invalid-feedback" id="err-description"></div>
                </div>

                <div class="row">
                    <!-- GIÁ -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="price">
                                <i class="fa-solid fa-tag" style="color:#ff4d4d;margin-right:6px;"></i>
                                Giá (VND) <span style="color:#ff003c;">*</span>
                            </label>
                            <input type="number" id="price" name="price" class="form-control" min="0" step="1000">
                            <div class="invalid-feedback" id="err-price"></div>
                            <small id="price-preview" style="color:#00ff99;margin-top:5px;display:block;"></small>
                        </div>
                    </div>

                    <!-- DANH MỤC -->
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="category_id">
                                <i class="fa-solid fa-layer-group" style="color:#ff4d4d;margin-right:6px;"></i>
                                Danh mục <span style="color:#ff003c;">*</span>
                            </label>
                            <select id="category_id" name="category_id" class="form-control">
                                <option value="">-- Đang tải... --</option>
                            </select>
                            <div class="invalid-feedback" id="err-category"></div>
                        </div>
                    </div>
                </div>

                <!-- CHANGE INDICATOR -->
                <div id="change-notice" style="display:none;" class="mb-3 p-3" style="background:#1e293b;border-radius:12px;border-left:4px solid #00ff99;">
                    <i class="fa-solid fa-circle-info" style="color:#00ff99;margin-right:8px;"></i>
                    <small style="color:#00ff99;">Có thay đổi chưa được lưu</small>
                </div>

                <!-- SUBMIT -->
                <div class="d-flex" style="gap:12px;margin-top:10px;">
                    <button type="submit" class="btn add-product-btn" id="submit-btn" style="flex:1;">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Lưu thay đổi
                    </button>
                    <button type="button" id="reset-btn" class="btn back-btn" style="flex:0 0 auto;padding:12px 20px;">
                        <i class="fa-solid fa-rotate-left"></i> Hoàn tác
                    </button>
                    <a href="/project1/Product/" class="btn" style="background:#334155;color:#fff;flex:0 0 auto;padding:12px 20px;border-radius:12px;font-weight:bold;">
                        Hủy
                    </a>
                </div>

            </form>
        </div>

        <!-- PREVIEW PANEL -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div style="background:#1e293b;border-radius:20px;padding:25px;position:sticky;top:20px;">
                <h5 style="color:#f59e0b;margin-bottom:20px;">
                    <i class="fa-solid fa-eye"></i> Xem trước thay đổi
                </h5>
                <div style="background:#0f172a;border-radius:15px;padding:20px;">
                    <div id="preview-name" style="font-size:18px;font-weight:bold;color:#ff4d4d;margin-bottom:10px;"></div>
                    <div id="preview-cat" style="color:#94a3b8;font-size:13px;margin-bottom:10px;"></div>
                    <div id="preview-desc" style="color:#cbd5e1;font-size:14px;margin-bottom:15px;min-height:60px;"></div>
                    <div id="preview-price" style="color:#00ff99;font-size:28px;font-weight:bold;"></div>
                </div>

                <!-- ORIGINAL VALUES -->
                <div class="mt-3">
                    <h6 style="color:#64748b;font-size:13px;margin-bottom:10px;">
                        <i class="fa-solid fa-clock-rotate-left"></i> Giá trị ban đầu
                    </h6>
                    <div id="original-values" style="color:#475569;font-size:12px;line-height:1.8;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOAST -->
<div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:99999;"></div>

<style>
.form-control.is-invalid { border-color: #ff003c !important; box-shadow: 0 0 8px rgba(255,0,60,.3) !important; }
.invalid-feedback { display:none; color:#ff4d4d; font-size:13px; margin-top:5px; }
.is-invalid ~ .invalid-feedback { display:block; }
.toast-msg { background:#1e293b; color:#fff; border-left:4px solid #00ff99; padding:15px 20px; border-radius:10px; margin-bottom:10px; box-shadow:0 4px 15px rgba(0,0,0,.4); animation: slideIn .3s ease; min-width:280px; }
.toast-msg.error { border-left-color: #ff003c; }
@keyframes slideIn { from { transform: translateX(100px); opacity:0; } to { transform: translateX(0); opacity:1; } }
#submit-btn:disabled { opacity:.6; cursor:not-allowed; }
</style>

<script>
$(function () {
    // BASE URL - tự động lấy đúng host + port
    const BASE = window.location.protocol + '//' + window.location.host + '/project1';
    
    const productId = <?= isset($editId) ? intval($editId) : 0 ?>;
    let originalData = {};
    let categoriesLoaded = false;
    let productLoaded = false;

    // ========== TOAST ==========
    function showToast(msg, type = 'success') {
        const t = $('<div>').addClass('toast-msg' + (type === 'error' ? ' error' : '')).html(
            `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-xmark'}" style="margin-right:8px;"></i>${msg}`
        );
        $('#toast-container').append(t);
        setTimeout(() => t.fadeOut(400, () => t.remove()), 3500);
    }

    function tryShowForm() {
        if (categoriesLoaded && productLoaded) {
            $('#loading-form').hide();
            $('#form-content').fadeIn(300);
        }
    }

    // ========== LOAD CATEGORIES ==========
    $.ajax({
        url: BASE + '/api/category',
        method: 'GET',
        success: function (cats) {
            const sel = $('#category_id').empty().append('<option value="">-- Chọn danh mục --</option>');
            cats.forEach(c => sel.append(`<option value="${c.id}">${c.name}</option>`));
            categoriesLoaded = true;
            tryShowForm();
        },
        error: function () {
            showToast('Không thể tải danh mục!', 'error');
            categoriesLoaded = true;
            tryShowForm();
        }
    });

    // ========== LOAD PRODUCT ==========
    $.ajax({
        url: `${BASE}/api/product/${productId}`,
        method: 'GET',
        success: function (p) {
            originalData = { name: p.name, description: p.description, price: p.price, category_id: p.category_id };

            $('#product-id').val(p.id);
            $('#display-id').text('#' + p.id);
            $('#name').val(p.name);
            $('#description').val(p.description);
            $('#price').val(p.price);

            // Set category after options are loaded
            const trySetCat = setInterval(function () {
                if ($('#category_id option').length > 1) {
                    $('#category_id').val(p.category_id);
                    clearInterval(trySetCat);
                    updatePreview();
                }
            }, 100);

            // Original values display
            $('#original-values').html(`
                <div>📦 ${p.name}</div>
                <div>💰 ${parseInt(p.price).toLocaleString('vi-VN')} đ</div>
                <div>🏷️ Danh mục ID: ${p.category_id}</div>
            `);

            updatePreview();
            productLoaded = true;
            tryShowForm();
        },
        error: function () {
            $('#loading-form').html(`
                <div class="text-center py-5">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size:60px;color:#ff003c;"></i>
                    <p class="mt-3" style="color:#94a3b8;">Không tìm thấy sản phẩm!</p>
                    <a href="/project1/Product/" class="btn add-product-btn mt-3">Quay lại danh sách</a>
                </div>
            `);
        }
    });

    // ========== LIVE PREVIEW ==========
    function updatePreview() {
        const name = $('#name').val();
        const desc = $('#description').val();
        const price = parseInt($('#price').val()) || 0;
        const catText = $('#category_id option:selected').text();

        $('#preview-name').text(name || 'Tên sản phẩm');
        $('#preview-desc').text(desc || 'Mô tả...');
        $('#preview-price').text(price.toLocaleString('vi-VN') + ' đ');
        $('#preview-cat').html(`<i class="fa-solid fa-layer-group"></i> ${catText || 'Chưa chọn'}`);
        $('#price-preview').text(price > 0 ? '≈ ' + price.toLocaleString('vi-VN') + ' đ' : '');

        // Show change indicator
        const hasChange = (
            name !== originalData.name ||
            desc !== originalData.description ||
            String(price) !== String(originalData.price) ||
            $('#category_id').val() !== String(originalData.category_id)
        );
        $('#change-notice').toggle(hasChange);
    }

    $('#name, #description, #price').on('input', updatePreview);
    $('#category_id').on('change', updatePreview);

    // ========== RESET ==========
    $('#reset-btn').on('click', function () {
        $('#name').val(originalData.name);
        $('#description').val(originalData.description);
        $('#price').val(originalData.price);
        $('#category_id').val(originalData.category_id);
        updatePreview();
        showToast('Đã hoàn tác về giá trị ban đầu!');
    });

    // ========== VALIDATE ==========
    function clearErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('').hide();
    }

    function setError(field, msg) {
        $(`#${field}`).addClass('is-invalid');
        $(`#err-${field}`).text(msg).show();
    }

    function validateForm(data) {
        clearErrors();
        let valid = true;
        if (!data.name.trim()) { setError('name', 'Tên sản phẩm không được để trống'); valid = false; }
        if (!data.description.trim()) { setError('description', 'Mô tả không được để trống'); valid = false; }
        if (!data.price || isNaN(data.price) || Number(data.price) < 0) { setError('price', 'Giá sản phẩm không hợp lệ'); valid = false; }
        if (!data.category_id) { setError('category', 'Vui lòng chọn danh mục'); valid = false; }
        return valid;
    }

    // ========== SUBMIT ==========
    $('#edit-product-form').on('submit', function (e) {
        e.preventDefault();

        const id = $('#product-id').val();
        const data = {
            name: $('#name').val(),
            description: $('#description').val(),
            price: $('#price').val(),
            category_id: $('#category_id').val()
        };

        if (!validateForm(data)) return;

        const btn = $('#submit-btn');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...');

        $.ajax({
            url: `${BASE}/api/product/${id}`,
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function (res) {
                if (res.message === 'Product updated successfully') {
                    showToast('Cập nhật sản phẩm thành công!');
                    originalData = { ...data };
                    $('#change-notice').hide();
                    setTimeout(() => { window.location.href = BASE + '/Product/'; }, 1200);
                } else {
                    showToast('Cập nhật thất bại!', 'error');
                    btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi');
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi');
                const res = xhr.responseJSON;
                if (res && res.errors) {
                    $.each(res.errors, (field, msg) => setError(field, msg));
                    showToast('Vui lòng kiểm tra lại!', 'error');
                } else {
                    showToast('Đã xảy ra lỗi!', 'error');
                }
            }
        });
    });

    $('.form-control').on('input change', function () {
        $(this).removeClass('is-invalid');
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
